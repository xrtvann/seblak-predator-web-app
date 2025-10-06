<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/koneksi.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $since = $_GET['since'] ?? '1970-01-01 00:00:00';

    // Validate timestamp format
    $timestamp = DateTime::createFromFormat('Y-m-d H:i:s', $since);
    if (!$timestamp) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid timestamp format. Use YYYY-MM-DD HH:MM:SS']);
        exit;
    }

    $query = "SELECT p.id, p.category_id, p.name, p.description, p.image_url, 
                     p.price, p.is_topping, p.is_active, p.created_at, p.updated_at,
                     c.name as category_name, c.type as category_type
              FROM products p 
              JOIN categories c ON p.category_id = c.id 
              WHERE p.updated_at > ?
              ORDER BY p.updated_at";

    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $since);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        throw new Exception('Database query failed: ' . mysqli_error($koneksi));
    }

    $products = [];
    $lastSync = $since;

    while ($row = mysqli_fetch_assoc($result)) {
        $row['price'] = floatval($row['price']);
        $row['is_topping'] = (bool) $row['is_topping'];
        $row['is_active'] = (bool) $row['is_active'];

        // Get product variants for each product
        $variantQuery = "SELECT vg.id as group_id, vg.name as group_name, 
                                vg.is_required, vg.allow_multiple, vg.sort_order,
                                vo.id as option_id, vo.name as option_name, 
                                vo.price_adjustment, vo.is_active as option_active,
                                vo.sort_order as option_sort_order
                         FROM product_variant_groups vg
                         LEFT JOIN product_variant_options vo ON vg.id = vo.variant_group_id AND vo.is_active = TRUE
                         WHERE vg.product_id = ?
                         ORDER BY vg.sort_order, vo.sort_order";

        $variantStmt = mysqli_prepare($koneksi, $variantQuery);
        mysqli_stmt_bind_param($variantStmt, "s", $row['id']);
        mysqli_stmt_execute($variantStmt);
        $variantResult = mysqli_stmt_get_result($variantStmt);

        $variants = [];
        while ($variantRow = mysqli_fetch_assoc($variantResult)) {
            $groupId = $variantRow['group_id'];
            if (!isset($variants[$groupId])) {
                $variants[$groupId] = [
                    'id' => $variantRow['group_id'],
                    'name' => $variantRow['group_name'],
                    'is_required' => (bool) $variantRow['is_required'],
                    'allow_multiple' => (bool) $variantRow['allow_multiple'],
                    'sort_order' => $variantRow['sort_order'],
                    'options' => []
                ];
            }

            if ($variantRow['option_id']) {
                $variants[$groupId]['options'][] = [
                    'id' => $variantRow['option_id'],
                    'name' => $variantRow['option_name'],
                    'price_adjustment' => floatval($variantRow['price_adjustment']),
                    'is_active' => (bool) $variantRow['option_active'],
                    'sort_order' => $variantRow['option_sort_order']
                ];
            }
        }

        $row['variants'] = array_values($variants);

        // Get available toppings for each product
        $toppingQuery = "SELECT t.id, t.name, t.price 
                        FROM product_toppings pt
                        JOIN products t ON pt.topping_id = t.id
                        WHERE pt.product_id = ? AND t.is_active = TRUE AND t.is_topping = TRUE
                        ORDER BY t.name";

        $toppingStmt = mysqli_prepare($koneksi, $toppingQuery);
        mysqli_stmt_bind_param($toppingStmt, "s", $row['id']);
        mysqli_stmt_execute($toppingStmt);
        $toppingResult = mysqli_stmt_get_result($toppingStmt);

        $toppings = [];
        while ($toppingRow = mysqli_fetch_assoc($toppingResult)) {
            $toppingRow['price'] = floatval($toppingRow['price']);
            $toppings[] = $toppingRow;
        }

        $row['toppings'] = $toppings;

        $products[] = $row;
        $lastSync = max($lastSync, $row['updated_at']);
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $products,
        'last_sync' => $lastSync,
        'sync_timestamp' => date('Y-m-d H:i:s'),
        'total' => count($products),
        'message' => 'Products synchronized successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error: ' . $e->getMessage()
    ]);
}
?>