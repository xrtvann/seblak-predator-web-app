<?php
/**
 * Mobile API - Create Delivery Order
 * Endpoint khusus untuk aplikasi mobile
 * Tipe pesanan: delivery only
 * 
 * POST /api/mobile/create-order.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

date_default_timezone_set('Asia/Jakarta');

require_once '../../config/database.php';
require_once '../../config/session.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['user_id', 'delivery_address', 'items'];
foreach ($required_fields as $field) {
    if (!isset($input[$field]) || empty($input[$field])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Field '$field' is required"
        ]);
        exit;
    }
}

// Validate items array
if (!is_array($input['items']) || count($input['items']) === 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Items array cannot be empty'
    ]);
    exit;
}

try {
    $conn = DatabaseConnection::getInstance();

    // Start transaction
    mysqli_begin_transaction($conn);

    // Get user information
    $user_stmt = $conn->prepare("SELECT id, name, email, phone_number FROM users WHERE id = ? AND is_active = 1");
    $user_stmt->bind_param("s", $input['user_id']);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();

    if ($user_result->num_rows === 0) {
        throw new Exception('User not found or inactive');
    }

    $user = $user_result->fetch_assoc();

    // Generate unique order ID and number
    $order_id = 'ORD_' . date('Ymd') . '_' . substr(uniqid(), -4);
    $order_number = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);

    // Calculate totals
    $subtotal = 0;
    $items_data = [];

    foreach ($input['items'] as $item) {
        // Validate item structure
        if (!isset($item['quantity']) || !isset($item['spice_level'])) {
            throw new Exception('Invalid item structure. Quantity and spice_level are required.');
        }

        // Get base price (seblak predator base price)
        // Assuming there's a base product for seblak
        $base_price = 15000; // Default base price for seblak

        $item_total = $base_price * $item['quantity'];

        // Add customizations price
        if (isset($item['customizations']) && is_array($item['customizations'])) {
            foreach ($item['customizations'] as $custom_id => $custom_value) {
                if ($custom_value) {
                    // Get customization price
                    $custom_stmt = $conn->prepare("SELECT price FROM customization_options WHERE id = ?");
                    $custom_stmt->bind_param("s", $custom_id);
                    $custom_stmt->execute();
                    $custom_result = $custom_stmt->get_result();
                    if ($custom_row = $custom_result->fetch_assoc()) {
                        $item_total += ($custom_row['price'] * $item['quantity']);
                    }
                }
            }
        }

        // Add toppings price
        if (isset($item['toppings']) && is_array($item['toppings'])) {
            foreach ($item['toppings'] as $topping) {
                if (isset($topping['topping_id']) && isset($topping['quantity'])) {
                    // Get topping price
                    $topping_stmt = $conn->prepare("SELECT price FROM toppings WHERE id = ?");
                    $topping_stmt->bind_param("s", $topping['topping_id']);
                    $topping_stmt->execute();
                    $topping_result = $topping_stmt->get_result();
                    if ($topping_row = $topping_result->fetch_assoc()) {
                        $item_total += ($topping_row['price'] * $topping['quantity']);
                    }
                }
            }
        }

        $subtotal += $item_total;
        $items_data[] = array_merge($item, ['item_total' => $item_total]);
    }

    // Calculate tax and discount
    $tax = 0;
    $discount = 0;
    $total_amount = $subtotal + $tax - $discount;

    // Get payment method (default to pending for mobile orders)
    $payment_method = $input['payment_method'] ?? 'midtrans';
    $payment_status = 'pending';

    // Get phone number (from input or user profile)
    $phone = $input['phone'] ?? $user['phone_number'];

    // Get notes
    $notes = $input['notes'] ?? '';

    // Insert order
    $order_stmt = $conn->prepare("
        INSERT INTO orders (
            id, order_number, customer_name, order_type, phone,
            delivery_address, subtotal, tax, discount, total_amount, 
            payment_method, payment_status, order_status, created_by, notes, created_at
        ) VALUES (?, ?, ?, 'delivery', ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, NOW())
    ");

    $order_stmt->bind_param(
        "sssssddddssss",
        $order_id,
        $order_number,
        $user['name'],
        $phone,
        $input['delivery_address'],
        $subtotal,
        $tax,
        $discount,
        $total_amount,
        $payment_method,
        $payment_status,
        $input['user_id'],
        $notes
    );

    if (!$order_stmt->execute()) {
        throw new Exception('Failed to create order: ' . $order_stmt->error);
    }

    // Insert order items
    foreach ($items_data as $item) {
        $item_id = 'ITEM_' . uniqid();
        $item_price = $item['item_total'] / $item['quantity'];

        $item_stmt = $conn->prepare("
            INSERT INTO order_items (
                id, order_id, product_id, quantity, unit_price,
                subtotal, spice_level, created_at
            ) VALUES (?, ?, 'BASE_SEBLAK', ?, ?, ?, ?, NOW())
        ");

        $item_stmt->bind_param(
            "ssidds",
            $item_id,
            $order_id,
            $item['quantity'],
            $item_price,
            $item['item_total'],
            $item['spice_level']
        );

        if (!$item_stmt->execute()) {
            throw new Exception('Failed to insert order item: ' . $item_stmt->error);
        }

        // Insert customizations
        if (isset($item['customizations']) && is_array($item['customizations'])) {
            foreach ($item['customizations'] as $custom_id => $custom_value) {
                if ($custom_value) {
                    // Get customization details
                    $custom_stmt = $conn->prepare("SELECT name, price FROM customization_options WHERE id = ?");
                    $custom_stmt->bind_param("s", $custom_id);
                    $custom_stmt->execute();
                    $custom_result = $custom_stmt->get_result();

                    if ($custom_row = $custom_result->fetch_assoc()) {
                        $custom_item_id = 'CUSTOM_' . uniqid();
                        $custom_insert = $conn->prepare("
                            INSERT INTO order_item_customizations (
                                id, order_item_id, customization_id, customization_name,
                                price, created_at
                            ) VALUES (?, ?, ?, ?, ?, NOW())
                        ");

                        $custom_insert->bind_param(
                            "ssssd",
                            $custom_item_id,
                            $item_id,
                            $custom_id,
                            $custom_row['name'],
                            $custom_row['price']
                        );

                        $custom_insert->execute();
                    }
                }
            }
        }

        // Insert toppings
        if (isset($item['toppings']) && is_array($item['toppings'])) {
            foreach ($item['toppings'] as $topping) {
                if (isset($topping['topping_id']) && isset($topping['quantity'])) {
                    // Get topping details
                    $topping_stmt = $conn->prepare("SELECT name, price FROM toppings WHERE id = ?");
                    $topping_stmt->bind_param("s", $topping['topping_id']);
                    $topping_stmt->execute();
                    $topping_result = $topping_stmt->get_result();

                    if ($topping_row = $topping_result->fetch_assoc()) {
                        $topping_item_id = 'TOPPING_' . uniqid();
                        $topping_insert = $conn->prepare("
                            INSERT INTO order_toppings (
                                id, order_id, topping_id, topping_name,
                                quantity, unit_price, subtotal, created_at
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                        ");

                        $topping_subtotal = $topping_row['price'] * $topping['quantity'];

                        $topping_insert->bind_param(
                            "ssssidd",
                            $topping_item_id,
                            $order_id,
                            $topping['topping_id'],
                            $topping_row['name'],
                            $topping['quantity'],
                            $topping_row['price'],
                            $topping_subtotal
                        );

                        $topping_insert->execute();
                    }
                }
            }
        }
    }

    // Commit transaction
    mysqli_commit($conn);

    // Return success response
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Order created successfully',
        'data' => [
            'order_id' => $order_id,
            'order_number' => $order_number,
            'total_amount' => $total_amount,
            'payment_status' => $payment_status,
            'customer_name' => $user['name'],
            'delivery_address' => $input['delivery_address'],
            'created_at' => date('Y-m-d H:i:s')
        ]
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn)) {
        mysqli_rollback($conn);
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to create order: ' . $e->getMessage()
    ]);
}
