<?php
/**
 * Midtrans Create Transaction - Generate Snap Token
 * API untuk membuat transaksi dan mendapatkan Snap Token dari Midtrans
 */

header('Content-Type: application/json');
require_once '../../config/koneksi.php';
require_once '../../config/session.php';
require_once 'config.php';

// Database connection alias for consistency
$conn = $koneksi;

// Get current user (optional - for logging purposes)
$current_user = getCurrentSessionUser();

// Log request for debugging
error_log('=== Midtrans Create Transaction Request ===');
error_log('User: ' . ($current_user['username'] ?? 'Guest'));
error_log('Input: ' . file_get_contents('php://input'));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    // Get request body
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        throw new Exception('Invalid request data');
    }

    // Log input for debugging
    error_log('Input data: ' . json_encode($input));

    // Validate required fields
    if (empty($input['customer_name']) || empty($input['items']) || count($input['items']) === 0) {
        throw new Exception('Customer name and items are required');
    }

    // Calculate totals
    $subtotal = 0;
    $item_details = [];
    $item_counter = 1;

    foreach ($input['items'] as $item) {
        $item_price = 0;
        $item_name_parts = [];

        // Validate item has at least spice level
        if (empty($item['spice_level'])) {
            error_log("Warning: Item without spice level - using default base price");
            // You might want to set a default base price or skip this item
            // For now, we'll continue with 0 base price
        }

        // Calculate base item price from spice level
        if (!empty($item['spice_level'])) {
            // Fetch spice level price from database
            $stmt = $conn->prepare("SELECT name, price FROM spice_levels WHERE id = ?");
            $stmt->bind_param("s", $item['spice_level']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $item_price += floatval($row['price']);
                $item_name_parts[] = $row['name'];
            } else {
                error_log("Warning: Spice level ID {$item['spice_level']} not found in database");
            }
            $stmt->close();
        }

        // Add customizations price
        if (!empty($item['customizations'])) {
            // Customizations sent as object/associative array: {type: optionId, ...}
            // Convert to array of values
            $customization_ids = is_array($item['customizations'])
                ? array_values($item['customizations'])
                : [];

            foreach ($customization_ids as $customization_id) {
                if (empty($customization_id))
                    continue;

                $stmt = $conn->prepare("SELECT name, price FROM customization_options WHERE id = ?");
                $stmt->bind_param("s", $customization_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $item_price += floatval($row['price']);
                    $item_name_parts[] = $row['name'];
                }
                $stmt->close();
            }
        }

        // Add toppings price
        if (!empty($item['toppings']) && is_array($item['toppings'])) {
            foreach ($item['toppings'] as $topping) {
                $topping_price = floatval($topping['unit_price']) * intval($topping['quantity']);
                $item_price += $topping_price;
            }
        }

        $quantity = intval($item['quantity']) ?: 1;
        $item_total = $item_price * $quantity;
        $subtotal += $item_total;

        // Create descriptive item name (max 50 chars for Midtrans)
        $item_name = 'Seblak #' . $item_counter;
        if (!empty($item_name_parts)) {
            $extra = ' - ' . implode(', ', array_slice($item_name_parts, 0, 2));
            if (strlen($item_name . $extra) <= 50) {
                $item_name .= $extra;
            }
        }

        // Add main seblak item to item_details (ensure integer prices)
        $item_details[] = [
            'id' => 'SEBLAK-' . $item_counter,
            'price' => (int) round($item_price),
            'quantity' => $quantity,
            'name' => substr($item_name, 0, 50)  // Midtrans max 50 chars
        ];

        error_log("Item #$item_counter: price=" . round($item_price) . ", qty=$quantity, total=" . round($item_total));

        $item_counter++;
    }

    $tax = 0; // No tax
    $discount = 0; // No discount
    $total = (int) round($subtotal + $tax - $discount);

    // Validate we have items and total
    if (empty($item_details)) {
        throw new Exception('No valid items to process');
    }

    if ($total <= 0) {
        throw new Exception('Order total must be greater than zero');
    }

    error_log("Order Summary: subtotal=$subtotal, tax=$tax, discount=$discount, total=$total");
    error_log("Item details: " . json_encode($item_details));

    // Generate unique order ID
    $order_id = 'ORD-' . date('YmdHis') . '-' . rand(1000, 9999);

    // Prepare customer email (Midtrans requires valid email format)
    $customer_email = 'customer@seblakpredator.com'; // Default email

    // Prepare transaction data for Midtrans
    $transaction_data = [
        'transaction_details' => [
            'order_id' => $order_id,
            'gross_amount' => (int) $total
        ],
        'customer_details' => [
            'first_name' => $input['customer_name'],
            'email' => $customer_email,
            'phone' => '08123456789'
        ],
        'item_details' => $item_details,
        'callbacks' => [
            'finish' => 'https://yourdomain.com/payment-finish'
        ]
    ];

    // Add customer address if table number provided
    if (!empty($input['table_number'])) {
        $transaction_data['customer_details']['billing_address'] = [
            'first_name' => $input['customer_name'],
            'address' => 'Table Number: ' . $input['table_number'],
            'city' => 'Restaurant',
            'postal_code' => '00000',
            'country_code' => 'IDN'
        ];
    }

    // Persist order locally as PENDING so webhook can update it later
    try {
        // Generate unique order ID for database
        $order_uuid = 'ord_' . uniqid() . bin2hex(random_bytes(4));

        // Insert main order record
        $insert_query = "INSERT INTO orders 
            (id, order_number, customer_name, table_number, order_type, 
             subtotal, tax, discount, total_amount, payment_method, payment_status, order_status, 
             order_date, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $stmt = mysqli_prepare($koneksi, $insert_query);
        if (!$stmt) {
            error_log('DB Prepare failed: ' . mysqli_error($koneksi));
            throw new Exception('Database error while saving order');
        }

        $customer_name = $input['customer_name'];
        $table_number = $input['table_number'] ?? '';
        $order_type = $input['order_type'] ?? 'dine_in';
        $total_amount = (float) $total;
        $payment_method = 'midtrans';
        $payment_status = 'pending';
        $order_status = 'pending';

        mysqli_stmt_bind_param(
            $stmt,
            'sssssdddssss',
            $order_uuid,
            $order_id,
            $customer_name,
            $table_number,
            $order_type,
            $subtotal,
            $tax,
            $discount,
            $total_amount,
            $payment_method,
            $payment_status,
            $order_status
        );

        if (!mysqli_stmt_execute($stmt)) {
            $err = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            error_log('DB Execute failed: ' . $err);
            throw new Exception('Database error while saving order');
        }
        mysqli_stmt_close($stmt);

        // Save order items
        $customer_counter = 1;
        foreach ($input['items'] as $index => $item) {
            $item_uuid = 'item_' . uniqid() . bin2hex(random_bytes(4));
            $quantity = intval($item['quantity']) ?: 1;
            $spice_level_id = $item['spice_level'] ?? null;

            // Calculate item price and get spice level details
            $item_price = 0;
            $spice_level_name = '';
            $spice_level_price = 0;

            if (!empty($spice_level_id)) {
                $stmt = $koneksi->prepare("SELECT name, price FROM spice_levels WHERE id = ?");
                $stmt->bind_param("s", $spice_level_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $spice_level_price = floatval($row['price']);
                    $spice_level_name = $row['name'];
                    $item_price += $spice_level_price;
                }
                $stmt->close();
            }

            // Add customizations price
            if (!empty($item['customizations'])) {
                $customization_ids = is_array($item['customizations'])
                    ? array_values($item['customizations'])
                    : [];

                foreach ($customization_ids as $customization_id) {
                    if (empty($customization_id))
                        continue;

                    $stmt = $koneksi->prepare("SELECT price FROM customization_options WHERE id = ?");
                    $stmt->bind_param("s", $customization_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc()) {
                        $item_price += floatval($row['price']);
                    }
                    $stmt->close();
                }
            }

            // Add toppings price
            if (!empty($item['toppings']) && is_array($item['toppings'])) {
                foreach ($item['toppings'] as $topping) {
                    $topping_price = floatval($topping['unit_price']) * intval($topping['quantity']);
                    $item_price += $topping_price;
                }
            }

            $item_total = $item_price * $quantity;

            // Insert order item
            $item_insert = "INSERT INTO order_items 
                (id, order_id, customer_number, spice_level_id, spice_level_name, spice_level_price, quantity, subtotal, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $item_stmt = mysqli_prepare($koneksi, $item_insert);
            if ($item_stmt) {
                mysqli_stmt_bind_param(
                    $item_stmt,
                    'ssissdid',
                    $item_uuid,
                    $order_uuid,
                    $customer_counter,
                    $spice_level_id,
                    $spice_level_name,
                    $spice_level_price,
                    $quantity,
                    $item_total
                );
                mysqli_stmt_execute($item_stmt);
                mysqli_stmt_close($item_stmt);

                // Save customizations
                if (!empty($item['customizations'])) {
                    $customization_ids = is_array($item['customizations'])
                        ? $item['customizations']  // Keep as associative array
                        : [];

                    foreach ($customization_ids as $cust_type => $customization_id) {
                        if (empty($customization_id))
                            continue;

                        // Get customization details
                        $stmt = $koneksi->prepare("SELECT name, price FROM customization_options WHERE id = ?");
                        $stmt->bind_param("s", $customization_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($row = $result->fetch_assoc()) {
                            $cust_uuid = 'cust_' . uniqid() . bin2hex(random_bytes(4));
                            $cust_name = $row['name'];
                            $cust_price = floatval($row['price']);

                            $cust_insert = "INSERT INTO order_item_customizations 
                                (id, order_item_id, customization_type, customization_id, customization_name, price, created_at)
                                VALUES (?, ?, ?, ?, ?, ?, NOW())";

                            $cust_stmt = mysqli_prepare($koneksi, $cust_insert);
                            if ($cust_stmt) {
                                mysqli_stmt_bind_param($cust_stmt, 'sssssd', $cust_uuid, $item_uuid, $cust_type, $customization_id, $cust_name, $cust_price);
                                mysqli_stmt_execute($cust_stmt);
                                mysqli_stmt_close($cust_stmt);
                            }
                        }
                        $stmt->close();
                    }
                }

                // Save toppings
                if (!empty($item['toppings']) && is_array($item['toppings'])) {
                    foreach ($item['toppings'] as $topping) {
                        $topping_uuid = 'top_' . uniqid() . bin2hex(random_bytes(4));
                        $topping_id = $topping['topping_id'];
                        $topping_name = $topping['topping_name'];
                        $topping_qty = intval($topping['quantity']);
                        $topping_price = floatval($topping['unit_price']);
                        $topping_subtotal = $topping_price * $topping_qty;

                        $topping_insert = "INSERT INTO order_item_toppings 
                            (id, order_item_id, topping_id, topping_name, quantity, unit_price, subtotal, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

                        $topping_stmt = mysqli_prepare($koneksi, $topping_insert);
                        if ($topping_stmt) {
                            mysqli_stmt_bind_param(
                                $topping_stmt,
                                'ssssidd',
                                $topping_uuid,
                                $item_uuid,
                                $topping_id,
                                $topping_name,
                                $topping_qty,
                                $topping_price,
                                $topping_subtotal
                            );
                            mysqli_stmt_execute($topping_stmt);
                            mysqli_stmt_close($topping_stmt);
                        }
                    }
                }

                $customer_counter++;
            }
        }

        error_log('Order saved locally with order_number: ' . $order_id . ' (DB ID: ' . $order_uuid . ')');
    } catch (Exception $dbEx) {
        // If saving the order fails, abort the flow so we don't create a Midtrans transaction without a local record
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $dbEx->getMessage()]);
        exit();
    }

    // Call Midtrans Snap API
    $snap_result = createMidtransSnapToken($transaction_data);

    if ($snap_result['success']) {
        echo json_encode([
            'success' => true,
            'snap_token' => $snap_result['token'],
            'order_id' => $order_id,
            'total_amount' => $total
        ]);
    } else {
        throw new Exception($snap_result['message'] ?? 'Failed to create Snap token');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Create Midtrans Snap Token
 */
function createMidtransSnapToken($transaction_data)
{
    $url = MIDTRANS_SNAP_URL . '/transactions';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transaction_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode(MIDTRANS_SERVER_KEY . ':')
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    // Check for CURL errors
    if ($curl_error) {
        error_log('Midtrans CURL Error: ' . $curl_error);
        return [
            'success' => false,
            'message' => 'Connection error: ' . $curl_error
        ];
    }

    // Parse response
    $result = json_decode($response, true);

    if ($http_code === 201 || $http_code === 200) {
        if (isset($result['token'])) {
            return [
                'success' => true,
                'token' => $result['token']
            ];
        } else {
            error_log('Midtrans API Error: No token in response');
            return [
                'success' => false,
                'message' => 'No token received from Midtrans'
            ];
        }
    }

    // Log error for debugging
    error_log('Midtrans API Error (HTTP ' . $http_code . '): ' . $response);

    // Return detailed error
    $error_message = 'Midtrans API Error';
    if ($result && isset($result['error_messages'])) {
        $error_message = implode(', ', $result['error_messages']);
    } elseif ($result && isset($result['message'])) {
        $error_message = $result['message'];
    }

    return [
        'success' => false,
        'message' => $error_message . ' (HTTP ' . $http_code . ')',
        'http_code' => $http_code,
        'response' => $result
    ];
}
