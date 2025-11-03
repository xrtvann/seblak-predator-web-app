<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/koneksi.php';
require_once '../../api/auth/middleware.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON format']);
    exit();
}

// Simple validation
if (empty($input['items']) || !is_array($input['items'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Order items are required']);
    exit();
}

// Get user from token
$user = authenticate();
$user_id = $user ? $user['id'] : null;

global $koneksi;

mysqli_begin_transaction($koneksi);

try {
    $order_id = 'order_' . uniqid();
    $total_amount = 0;

    // First, calculate total amount
    foreach ($input['items'] as $item) {
        // Get base product price
        $product_id = $item['base_product_id'];
        $prod_stmt = mysqli_prepare($koneksi, "SELECT price FROM products WHERE id = ?");
        mysqli_stmt_bind_param($prod_stmt, "s", $product_id);
        mysqli_stmt_execute($prod_stmt);
        $prod_result = mysqli_stmt_get_result($prod_stmt);
        $product_data = mysqli_fetch_assoc($prod_result);
        $item_price = $product_data['price'];

        // Add variant prices
        if (!empty($item['variants'])) {
            $variants_placeholders = implode(',', array_fill(0, count($item['variants']), '?'));
            $var_stmt = mysqli_prepare($koneksi, "SELECT SUM(price_adjustment) as total_variant_price FROM product_variant_options WHERE id IN ($variants_placeholders)");
            mysqli_stmt_bind_param($var_stmt, str_repeat('s', count($item['variants'])), ...$item['variants']);
            mysqli_stmt_execute($var_stmt);
            $var_result = mysqli_stmt_get_result($var_stmt);
            $variant_price = mysqli_fetch_assoc($var_result)['total_variant_price'];
            $item_price += $variant_price;
        }

        // Add topping prices
        if (!empty($item['toppings'])) {
            $toppings_placeholders = implode(',', array_fill(0, count($item['toppings']), '?'));
            $top_stmt = mysqli_prepare($koneksi, "SELECT SUM(price) as total_topping_price FROM products WHERE id IN ($toppings_placeholders)");
            mysqli_stmt_bind_param($top_stmt, str_repeat('s', count($item['toppings'])), ...$item['toppings']);
            mysqli_stmt_execute($top_stmt);
            $top_result = mysqli_stmt_get_result($top_stmt);
            $topping_price = mysqli_fetch_assoc($top_result)['total_topping_price'];
            $item_price += $topping_price;
        }
        $total_amount += $item_price;
    }

    // Insert into orders table
    $order_stmt = mysqli_prepare($koneksi, "INSERT INTO orders (id, user_id, customer_name, total_amount, status) VALUES (?, ?, ?, ?, 'pending')");
    $customer_name = $user ? $user['username'] : 'Guest';
    mysqli_stmt_bind_param($order_stmt, "sssd", $order_id, $user_id, $customer_name, $total_amount);
    mysqli_stmt_execute($order_stmt);

    // Insert into order_items and order_item_details
    foreach ($input['items'] as $item) {
        // Recalculate price for subtotal
        $product_id = $item['base_product_id'];
        $prod_stmt = mysqli_prepare($koneksi, "SELECT price FROM products WHERE id = ?");
        mysqli_stmt_bind_param($prod_stmt, "s", $product_id);
        mysqli_stmt_execute($prod_stmt);
        $prod_result = mysqli_stmt_get_result($prod_stmt);
        $product_data = mysqli_fetch_assoc($prod_result);
        $subtotal = $product_data['price'];

        $item_name = $item['name'];
        $quantity = 1; // Assuming quantity is 1 for each seblak

        $order_item_stmt = mysqli_prepare($koneksi, "INSERT INTO order_items (order_id, product_id, item_name, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($order_item_stmt, "sssiid", $order_id, $product_id, $item_name, $quantity, $product_data['price'], $subtotal);
        mysqli_stmt_execute($order_item_stmt);
        $order_item_id = mysqli_insert_id($koneksi);

        // Insert variants
        if (!empty($item['variants'])) {
            foreach ($item['variants'] as $variant_id) {
                $var_detail_stmt = mysqli_prepare($koneksi, "SELECT name, price_adjustment FROM product_variant_options WHERE id = ?");
                mysqli_stmt_bind_param($var_detail_stmt, "s", $variant_id);
                mysqli_stmt_execute($var_detail_stmt);
                $var_detail_result = mysqli_stmt_get_result($var_detail_stmt);
                $variant_detail = mysqli_fetch_assoc($var_detail_result);

                $detail_stmt = mysqli_prepare($koneksi, "INSERT INTO order_item_details (order_item_id, type, item_id, item_name, price_adjustment) VALUES (?, 'variant', ?, ?, ?)");
                mysqli_stmt_bind_param($detail_stmt, "issd", $order_item_id, $variant_id, $variant_detail['name'], $variant_detail['price_adjustment']);
                mysqli_stmt_execute($detail_stmt);
            }
        }

        // Insert toppings
        if (!empty($item['toppings'])) {
            foreach ($item['toppings'] as $topping_id) {
                $top_detail_stmt = mysqli_prepare($koneksi, "SELECT name, price FROM products WHERE id = ?");
                mysqli_stmt_bind_param($top_detail_stmt, "s", $topping_id);
                mysqli_stmt_execute($top_detail_stmt);
                $top_detail_result = mysqli_stmt_get_result($top_detail_stmt);
                $topping_detail = mysqli_fetch_assoc($top_detail_result);

                $detail_stmt = mysqli_prepare($koneksi, "INSERT INTO order_item_details (order_item_id, type, item_id, item_name, price_adjustment) VALUES (?, 'topping', ?, ?, ?)");
                mysqli_stmt_bind_param($detail_stmt, "issd", $order_item_id, $topping_id, $topping_detail['name'], $topping_detail['price']);
                mysqli_stmt_execute($detail_stmt);
            }
        }
    }

    mysqli_commit($koneksi);

    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Order created successfully', 'order_id' => $order_id]);

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create order: ' . $e->getMessage()]);
}

?>