<?php
/**
 * API Transaksi/Orders untuk Sistem Seblak Prasmanan
 * Mendukung: Create, Read, Update, Delete orders dengan topping
 * Tanpa tracking stok (sesuai sistem prasmanan)
 */

header('Content-Type: application/json');
require_once '../config/koneksi.php';
require_once '../config/session.php';

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get current user
$current_user = getCurrentSessionUser();

// Generate UUID function
function generateUUID()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

// Generate order number
function generateOrderNumber($koneksi)
{
    $date = date('Ymd');
    $query = "SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    $number = str_pad($row['count'] + 1, 4, '0', STR_PAD_LEFT);
    return "ORD-{$date}-{$number}";
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            handleGet($koneksi);
            break;
        case 'POST':
            handlePost($koneksi, $current_user);
            break;
        case 'PUT':
            handlePut($koneksi);
            break;
        case 'DELETE':
            handleDelete($koneksi);
            break;
        case 'PATCH':
            handlePatch($koneksi);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

/**
 * GET - Retrieve orders
 */
function handleGet($koneksi)
{
    // Get single order by ID
    if (isset($_GET['id'])) {
        $id = mysqli_real_escape_string($koneksi, $_GET['id']);

        // Get order details
        $query = "SELECT o.*, u.username as created_by_name 
                  FROM orders o 
                  LEFT JOIN users u ON o.created_by = u.id 
                  WHERE o.id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $order = mysqli_fetch_assoc($result);

        if (!$order) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            return;
        }

        // Get order items
        $itemsQuery = "SELECT oi.*, p.image_url as product_image 
                       FROM order_items oi
                       LEFT JOIN products p ON oi.product_id = p.id
                       WHERE oi.order_id = ?";
        $itemsStmt = mysqli_prepare($koneksi, $itemsQuery);
        mysqli_stmt_bind_param($itemsStmt, "s", $id);
        mysqli_stmt_execute($itemsStmt);
        $itemsResult = mysqli_stmt_get_result($itemsStmt);

        $items = [];
        while ($item = mysqli_fetch_assoc($itemsResult)) {
            // Get toppings for this item
            $toppingsQuery = "SELECT * FROM order_item_toppings WHERE order_item_id = ?";
            $toppingsStmt = mysqli_prepare($koneksi, $toppingsQuery);
            mysqli_stmt_bind_param($toppingsStmt, "s", $item['id']);
            mysqli_stmt_execute($toppingsStmt);
            $toppingsResult = mysqli_stmt_get_result($toppingsStmt);

            $toppings = [];
            while ($topping = mysqli_fetch_assoc($toppingsResult)) {
                $toppings[] = $topping;
            }

            $item['toppings'] = $toppings;
            $items[] = $item;
        }

        $order['items'] = $items;

        echo json_encode(['success' => true, 'data' => $order]);
        return;
    }

    // Get all orders with filters
    $where = [];
    $params = [];
    $types = '';

    // Filter by status
    if (isset($_GET['status'])) {
        $where[] = "o.order_status = ?";
        $params[] = $_GET['status'];
        $types .= 's';
    }

    // Filter by payment status
    if (isset($_GET['payment_status'])) {
        $where[] = "o.payment_status = ?";
        $params[] = $_GET['payment_status'];
        $types .= 's';
    }

    // Filter by date
    if (isset($_GET['date'])) {
        $where[] = "DATE(o.created_at) = ?";
        $params[] = $_GET['date'];
        $types .= 's';
    }

    // Search
    if (isset($_GET['search'])) {
        $searchTerm = '%' . $_GET['search'] . '%';
        $where[] = "(o.order_number LIKE ? OR o.customer_name LIKE ?)";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'ss';
    }

    $whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

    // Pagination
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $perPage = isset($_GET['per_page']) ? (int) $_GET['per_page'] : 20;
    $offset = ($page - 1) * $perPage;

    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM orders o $whereClause";
    if (count($params) > 0) {
        $countStmt = mysqli_prepare($koneksi, $countQuery);
        mysqli_stmt_bind_param($countStmt, $types, ...$params);
        mysqli_stmt_execute($countStmt);
        $countResult = mysqli_stmt_get_result($countStmt);
    } else {
        $countResult = mysqli_query($koneksi, $countQuery);
    }
    $totalRow = mysqli_fetch_assoc($countResult);
    $total = $totalRow['total'];

    // Get orders
    $query = "SELECT o.*, u.username as created_by_name,
              (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as items_count
              FROM orders o 
              LEFT JOIN users u ON o.created_by = u.id 
              $whereClause 
              ORDER BY o.created_at DESC 
              LIMIT ? OFFSET ?";

    $params[] = $perPage;
    $params[] = $offset;
    $types .= 'ii';

    $stmt = mysqli_prepare($koneksi, $query);
    if (count($params) > 0) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }

    // Get statistics
    $statsQuery = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN order_status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
                    SUM(CASE WHEN order_status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                    SUM(CASE WHEN order_status = 'completed' THEN total_amount ELSE 0 END) as total_revenue,
                    SUM(CASE WHEN DATE(created_at) = CURDATE() AND order_status = 'completed' THEN total_amount ELSE 0 END) as today_revenue
                   FROM orders";
    $statsResult = mysqli_query($koneksi, $statsQuery);
    $stats = mysqli_fetch_assoc($statsResult);

    echo json_encode([
        'success' => true,
        'data' => $orders,
        'pagination' => [
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => ceil($total / $perPage)
        ],
        'statistics' => $stats
    ]);
}

/**
 * POST - Create new order
 */
function handlePost($koneksi, $current_user)
{
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['customer_name']) || !isset($input['items']) || empty($input['items'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Customer name and items are required']);
        return;
    }

    mysqli_begin_transaction($koneksi);

    try {
        $orderId = generateUUID();
        $orderNumber = generateOrderNumber($koneksi);

        // Calculate totals
        $subtotal = 0;
        foreach ($input['items'] as $item) {
            $itemSubtotal = $item['quantity'] * $item['unit_price'];

            // Add topping prices
            if (isset($item['toppings'])) {
                foreach ($item['toppings'] as $topping) {
                    $itemSubtotal += ($topping['quantity'] * $topping['unit_price']);
                }
            }

            $subtotal += $itemSubtotal;
        }

        $tax = isset($input['tax']) ? $input['tax'] : 0;
        $discount = isset($input['discount']) ? $input['discount'] : 0;
        $total = $subtotal + $tax - $discount;

        // Insert order
        $insertOrderQuery = "INSERT INTO orders (
            id, order_number, customer_name, table_number, phone, notes,
            subtotal, tax, discount, total_amount, payment_method, 
            payment_status, order_status, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $orderStmt = mysqli_prepare($koneksi, $insertOrderQuery);

        $customerName = $input['customer_name'];
        $tableNumber = $input['table_number'] ?? null;
        $phone = $input['phone'] ?? null;
        $notes = $input['notes'] ?? null;
        $paymentMethod = $input['payment_method'] ?? 'cash';
        $paymentStatus = $input['payment_status'] ?? 'pending';
        $orderStatus = 'pending';
        $createdBy = $current_user['id'] ?? null;

        mysqli_stmt_bind_param(
            $orderStmt,
            "ssssssdddsssss",
            $orderId,
            $orderNumber,
            $customerName,
            $tableNumber,
            $phone,
            $notes,
            $subtotal,
            $tax,
            $discount,
            $total,
            $paymentMethod,
            $paymentStatus,
            $orderStatus,
            $createdBy
        );

        mysqli_stmt_execute($orderStmt);

        // Insert order items
        foreach ($input['items'] as $item) {
            $itemId = generateUUID();
            $itemSubtotal = $item['quantity'] * $item['unit_price'];

            $insertItemQuery = "INSERT INTO order_items (
                id, order_id, product_id, product_name, quantity, unit_price, subtotal, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $itemStmt = mysqli_prepare($koneksi, $insertItemQuery);

            $productId = $item['product_id'];
            $productName = $item['product_name'];
            $quantity = $item['quantity'];
            $unitPrice = $item['unit_price'];
            $itemNotes = $item['notes'] ?? null;

            mysqli_stmt_bind_param(
                $itemStmt,
                "sssiddds",
                $itemId,
                $orderId,
                $productId,
                $productName,
                $quantity,
                $unitPrice,
                $itemSubtotal,
                $itemNotes
            );

            mysqli_stmt_execute($itemStmt);

            // Insert toppings if any
            if (isset($item['toppings'])) {
                foreach ($item['toppings'] as $topping) {
                    $toppingId = generateUUID();
                    $toppingSubtotal = $topping['quantity'] * $topping['unit_price'];

                    $insertToppingQuery = "INSERT INTO order_item_toppings (
                        id, order_item_id, topping_id, topping_name, quantity, unit_price, subtotal
                    ) VALUES (?, ?, ?, ?, ?, ?, ?)";

                    $toppingStmt = mysqli_prepare($koneksi, $insertToppingQuery);

                    mysqli_stmt_bind_param(
                        $toppingStmt,
                        "ssssidddd",
                        $toppingId,
                        $itemId,
                        $topping['topping_id'],
                        $topping['topping_name'],
                        $topping['quantity'],
                        $topping['unit_price'],
                        $toppingSubtotal
                    );

                    mysqli_stmt_execute($toppingStmt);
                }
            }
        }

        mysqli_commit($koneksi);

        echo json_encode([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => [
                'id' => $orderId,
                'order_number' => $orderNumber
            ]
        ]);

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        throw $e;
    }
}

/**
 * PUT - Update order
 */
function handlePut($koneksi)
{
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Order ID is required']);
        return;
    }

    $updateFields = [];
    $params = [];
    $types = '';

    if (isset($input['customer_name'])) {
        $updateFields[] = "customer_name = ?";
        $params[] = $input['customer_name'];
        $types .= 's';
    }

    if (isset($input['table_number'])) {
        $updateFields[] = "table_number = ?";
        $params[] = $input['table_number'];
        $types .= 's';
    }

    if (isset($input['phone'])) {
        $updateFields[] = "phone = ?";
        $params[] = $input['phone'];
        $types .= 's';
    }

    if (isset($input['notes'])) {
        $updateFields[] = "notes = ?";
        $params[] = $input['notes'];
        $types .= 's';
    }

    if (isset($input['payment_method'])) {
        $updateFields[] = "payment_method = ?";
        $params[] = $input['payment_method'];
        $types .= 's';
    }

    if (count($updateFields) === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No fields to update']);
        return;
    }

    $params[] = $input['id'];
    $types .= 's';

    $query = "UPDATE orders SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Order not found or no changes made']);
    }
}

/**
 * PATCH - Update order status
 */
function handlePatch($koneksi)
{
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['id']) || !isset($input['status'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Order ID and status are required']);
        return;
    }

    $allowedStatuses = ['pending', 'processing', 'completed', 'cancelled'];
    if (!in_array($input['status'], $allowedStatuses)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        return;
    }

    $query = "UPDATE orders SET order_status = ?";
    $params = [$input['status']];
    $types = 's';

    // Set completion/cancellation timestamp
    if ($input['status'] === 'completed') {
        $query .= ", completed_at = NOW(), payment_status = 'paid'";
    } elseif ($input['status'] === 'cancelled') {
        $query .= ", cancelled_at = NOW()";
        if (isset($input['cancel_reason'])) {
            $query .= ", cancel_reason = ?";
            $params[] = $input['cancel_reason'];
            $types .= 's';
        }
    }

    $query .= " WHERE id = ?";
    $params[] = $input['id'];
    $types .= 's';

    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
    }
}

/**
 * DELETE - Delete order
 */
function handleDelete($koneksi)
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Order ID is required']);
        return;
    }

    $id = mysqli_real_escape_string($koneksi, $_GET['id']);

    // Delete order (CASCADE will delete items and toppings)
    $query = "DELETE FROM orders WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo json_encode(['success' => true, 'message' => 'Order deleted successfully']);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Order not found']);
    }
}
