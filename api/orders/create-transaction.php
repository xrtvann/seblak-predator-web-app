<?php
/**
 * API untuk membuat transaksi baru (Seblak Order)
 * Endpoint: POST /api/orders/create-transaction.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/database.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON format']);
    exit();
}

// Validation
if (empty($input['customer_name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nama pelanggan wajib diisi']);
    exit();
}

// Validate order type
$order_type = $input['order_type'] ?? 'dine_in';
if (!in_array($order_type, ['dine_in', 'take_away'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tipe pesanan tidak valid']);
    exit();
}

// Validate table number for dine in
if ($order_type === 'dine_in' && empty($input['table_number'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nomor meja wajib diisi untuk dine in']);
    exit();
}

if (empty($input['items']) || !is_array($input['items']) || count($input['items']) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Pesanan harus memiliki minimal 1 item seblak']);
    exit();
}

// Validate each item has spice level
foreach ($input['items'] as $index => $item) {
    if (empty($item['spice_level'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Seblak #' . ($index + 1) . ' harus memiliki level pedas']);
        exit();
    }
}

try {
    $conn = DatabaseConnection::getInstance();

    // Start transaction
    $conn->begin_transaction();

    // Generate IDs
    $order_id = 'ORD_' . date('YmdHis') . '_' . substr(uniqid(), -4);
    $order_number = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

    // Calculate totals
    $subtotal = 0;

    foreach ($input['items'] as $item) {
        // Base price is 0
        $item_price = 0;

        // Add spice level price
        if (!empty($item['spice_level'])) {
            $spice_stmt = $conn->prepare("SELECT price FROM spice_levels WHERE id = ?");
            $spice_stmt->bind_param("s", $item['spice_level']);
            $spice_stmt->execute();
            $spice_result = $spice_stmt->get_result();
            if ($spice_data = $spice_result->fetch_assoc()) {
                $item_price += floatval($spice_data['price']);
            }
            $spice_stmt->close();
        }

        // Add customizations price
        if (!empty($item['customizations'])) {
            foreach ($item['customizations'] as $type => $cust_id) {
                $cust_stmt = $conn->prepare("SELECT price FROM customization_options WHERE id = ?");
                $cust_stmt->bind_param("s", $cust_id);
                $cust_stmt->execute();
                $cust_result = $cust_stmt->get_result();
                if ($cust_data = $cust_result->fetch_assoc()) {
                    $item_price += floatval($cust_data['price']);
                }
                $cust_stmt->close();
            }
        }

        // Add toppings price
        if (!empty($item['toppings'])) {
            foreach ($item['toppings'] as $topping) {
                $item_price += floatval($topping['unit_price']) * intval($topping['quantity']);
            }
        }

        $subtotal += $item_price * intval($item['quantity']);
    }

    $tax = 0; // No tax for now
    $discount = 0; // No discount for now
    $total_amount = $subtotal + $tax - $discount;

    // Insert into orders table
    $order_stmt = $conn->prepare("
        INSERT INTO orders (
            id, order_number, customer_name, order_type, table_number, phone,
            pickup_time, delivery_address,
            subtotal, tax, discount, total_amount, 
            payment_method, payment_status, order_status, notes, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', ?, NOW())
    ");

    $payment_method = $input['payment_method'] ?? 'cash';
    $notes = $input['notes'] ?? null;
    $table_number = $input['table_number'] ?? null;
    $pickup_time = $input['pickup_time'] ?? null;
    $delivery_address = $input['delivery_address'] ?? null;

    $order_stmt->bind_param(
        "ssssssssdddsss",
        $order_id,
        $order_number,
        $input['customer_name'],
        $order_type,
        $table_number,
        $input['phone'],
        $pickup_time,
        $delivery_address,
        $subtotal,
        $tax,
        $discount,
        $total_amount,
        $payment_method,
        $notes
    );

    if (!$order_stmt->execute()) {
        throw new Exception("Failed to insert order: " . $order_stmt->error);
    }
    $order_stmt->close();

    // Insert order items
    $customer_number = 1; // Start from customer 1

    foreach ($input['items'] as $item) {
        $item_id = 'ITEM_' . uniqid();

        // Get spice level details
        $spice_level_id = $item['spice_level'];
        $spice_level_name = null;
        $spice_level_price = 0;

        if (!empty($spice_level_id)) {
            $spice_stmt = $conn->prepare("SELECT name, price FROM spice_levels WHERE id = ?");
            $spice_stmt->bind_param("s", $spice_level_id);
            $spice_stmt->execute();
            $spice_result = $spice_stmt->get_result();
            if ($spice_data = $spice_result->fetch_assoc()) {
                $spice_level_name = $spice_data['name'];
                $spice_level_price = floatval($spice_data['price']);
            }
            $spice_stmt->close();
        }

        // Calculate item subtotal
        $item_subtotal = $spice_level_price;

        // Add customizations price to subtotal
        if (!empty($item['customizations'])) {
            foreach ($item['customizations'] as $type => $cust_id) {
                $cust_stmt = $conn->prepare("SELECT price FROM customization_options WHERE id = ?");
                $cust_stmt->bind_param("s", $cust_id);
                $cust_stmt->execute();
                $cust_result = $cust_stmt->get_result();
                if ($cust_data = $cust_result->fetch_assoc()) {
                    $item_subtotal += floatval($cust_data['price']);
                }
                $cust_stmt->close();
            }
        }

        // Add toppings price to subtotal
        if (!empty($item['toppings'])) {
            foreach ($item['toppings'] as $topping) {
                $item_subtotal += floatval($topping['unit_price']) * intval($topping['quantity']);
            }
        }

        // Multiply by quantity
        $item_subtotal *= intval($item['quantity']);

        // Insert order item
        $item_stmt = $conn->prepare("
            INSERT INTO order_items (
                id, order_id, customer_number, quantity,
                spice_level_id, spice_level_name, spice_level_price,
                subtotal, notes, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $item_notes = $item['notes'] ?? null;
        $item_quantity = intval($item['quantity']);

        $item_stmt->bind_param(
            "ssiissdds",
            $item_id,
            $order_id,
            $customer_number,
            $item_quantity,
            $spice_level_id,
            $spice_level_name,
            $spice_level_price,
            $item_subtotal,
            $item_notes
        );

        if (!$item_stmt->execute()) {
            throw new Exception("Failed to insert order item: " . $item_stmt->error);
        }
        $item_stmt->close();

        // Insert customizations
        if (!empty($item['customizations'])) {
            foreach ($item['customizations'] as $type => $cust_id) {
                $cust_detail_stmt = $conn->prepare("SELECT name, price FROM customization_options WHERE id = ?");
                $cust_detail_stmt->bind_param("s", $cust_id);
                $cust_detail_stmt->execute();
                $cust_detail_result = $cust_detail_stmt->get_result();

                if ($cust_detail = $cust_detail_result->fetch_assoc()) {
                    $cust_custom_id = 'CUST_' . uniqid();

                    $insert_cust_stmt = $conn->prepare("
                        INSERT INTO order_item_customizations (
                            id, order_item_id, customization_type, 
                            customization_id, customization_name, price, created_at
                        ) VALUES (?, ?, ?, ?, ?, ?, NOW())
                    ");

                    $cust_price = floatval($cust_detail['price']);

                    $insert_cust_stmt->bind_param(
                        "sssssd",
                        $cust_custom_id,
                        $item_id,
                        $type,
                        $cust_id,
                        $cust_detail['name'],
                        $cust_price
                    );

                    if (!$insert_cust_stmt->execute()) {
                        throw new Exception("Failed to insert customization: " . $insert_cust_stmt->error);
                    }
                    $insert_cust_stmt->close();
                }
                $cust_detail_stmt->close();
            }
        }

        // Insert toppings
        if (!empty($item['toppings'])) {
            foreach ($item['toppings'] as $topping) {
                $topping_id = 'TOPP_' . uniqid();
                $topping_qty = intval($topping['quantity']);
                $topping_price = floatval($topping['unit_price']);
                $topping_subtotal = $topping_price * $topping_qty;

                $topping_stmt = $conn->prepare("
                    INSERT INTO order_item_toppings (
                        id, order_item_id, topping_id, topping_name,
                        quantity, unit_price, subtotal, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                ");

                $topping_stmt->bind_param(
                    "ssssidd",
                    $topping_id,
                    $item_id,
                    $topping['topping_id'],
                    $topping['topping_name'],
                    $topping_qty,
                    $topping_price,
                    $topping_subtotal
                );

                if (!$topping_stmt->execute()) {
                    throw new Exception("Failed to insert topping: " . $topping_stmt->error);
                }
                $topping_stmt->close();
            }
        }

        $customer_number++;
    }

    // Commit transaction
    $conn->commit();

    // Return success response
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Transaksi berhasil dibuat',
        'data' => [
            'order_id' => $order_id,
            'order_number' => $order_number,
            'total_amount' => $total_amount,
            'payment_method' => $payment_method
        ]
    ]);

} catch (Exception $e) {
    // Rollback on error
    if (isset($conn)) {
        $conn->rollback();
    }

    error_log("Order creation error: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal membuat transaksi: ' . $e->getMessage()
    ]);
}
?>