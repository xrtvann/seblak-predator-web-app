<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/koneksi.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            getProductById();
        } else {
            getAllProducts();
        }
        break;
    case 'POST':
        createProduct();
        break;
    case 'PUT':
        updateProduct();
        break;
    case 'DELETE':
        deleteProduct();
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

function getAllProducts()
{
    global $koneksi;

    try {
        // Build query with optional filters
        $whereConditions = ["1=1"]; // Always true condition to allow flexible filtering
        $params = [];
        $types = "";

        if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
            $whereConditions[] = "p.category_id = ?";
            $params[] = $_GET['category_id'];
            $types .= "s";
        }

        if (isset($_GET['is_topping'])) {
            $whereConditions[] = "p.is_topping = ?";
            $params[] = $_GET['is_topping'] === 'true' ? 1 : 0;
            $types .= "i";
        }

        // Optional is_active filter (now as parameter instead of default)
        if (isset($_GET['is_active'])) {
            $whereConditions[] = "p.is_active = ?";
            $params[] = $_GET['is_active'] === 'true' ? 1 : 0;
            $types .= "i";
        }

        // Pagination - if no pagination parameters provided, return all data
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $per_page = isset($_GET['per_page']) ? max(1, min(1000, intval($_GET['per_page']))) : (isset($_GET['page']) ? 20 : 1000);
        $offset = ($page - 1) * $per_page;

        $whereClause = implode(" AND ", $whereConditions);

        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM products p WHERE " . $whereClause;
        if (!empty($params)) {
            $countStmt = mysqli_prepare($koneksi, $countQuery);
            if (!empty($types)) {
                mysqli_stmt_bind_param($countStmt, $types, ...$params);
            }
            mysqli_stmt_execute($countStmt);
            $countResult = mysqli_stmt_get_result($countStmt);
        } else {
            $countResult = mysqli_query($koneksi, $countQuery);
        }

        $totalRow = mysqli_fetch_assoc($countResult);
        $total = $totalRow['total'];

        // Get products
        $query = "SELECT p.id, p.category_id, p.name, p.description, p.image_url, 
                         p.price, p.is_topping, p.is_active, p.created_at, p.updated_at,
                         c.name as category_name, c.type as category_type
                  FROM products p 
                  JOIN categories c ON p.category_id = c.id 
                  WHERE " . $whereClause . "
                  ORDER BY p.created_at DESC, p.updated_at DESC, p.name ASC 
                  LIMIT ? OFFSET ?";

        $allParams = array_merge($params, [$per_page, $offset]);
        $allTypes = $types . "ii";

        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, $allTypes, ...$allParams);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            throw new Exception('Database query failed: ' . mysqli_error($koneksi));
        }

        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['price'] = floatval($row['price']);
            $row['is_topping'] = (bool) $row['is_topping'];
            $row['is_active'] = (bool) $row['is_active'];
            $products[] = $row;
        }

        $last_page = ceil($total / $per_page);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $products,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $per_page,
                'last_page' => $last_page
            ],
            'message' => 'Produk berhasil diambil'
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function getProductById()
{
    global $koneksi;

    $product_id = $_GET['id'];

    try {
        $query = "SELECT p.id, p.category_id, p.name, p.description, p.image_url, 
                         p.price, p.is_topping, p.is_active, p.created_at, p.updated_at,
                         c.name as category_name, c.type as category_type
                  FROM products p 
                  JOIN categories c ON p.category_id = c.id 
                  WHERE p.id = ?";

        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            throw new Exception('Database query failed: ' . mysqli_error($koneksi));
        }

        if ($row = mysqli_fetch_assoc($result)) {
            $row['price'] = floatval($row['price']);
            $row['is_topping'] = (bool) $row['is_topping'];
            $row['is_active'] = (bool) $row['is_active'];

            // Get product variants
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
            mysqli_stmt_bind_param($variantStmt, "s", $product_id);
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

            // Get available toppings
            $toppingQuery = "SELECT t.id, t.name, t.price 
                            FROM product_toppings pt
                            JOIN products t ON pt.topping_id = t.id
                            WHERE pt.product_id = ? AND t.is_active = TRUE AND t.is_topping = TRUE
                            ORDER BY t.name";

            $toppingStmt = mysqli_prepare($koneksi, $toppingQuery);
            mysqli_stmt_bind_param($toppingStmt, "s", $product_id);
            mysqli_stmt_execute($toppingStmt);
            $toppingResult = mysqli_stmt_get_result($toppingStmt);

            $toppings = [];
            while ($toppingRow = mysqli_fetch_assoc($toppingResult)) {
                $toppingRow['price'] = floatval($toppingRow['price']);
                $toppings[] = $toppingRow;
            }

            $row['toppings'] = $toppings;

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $row,
                'message' => 'Produk berhasil diambil'
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan']);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function createProduct()
{
    global $koneksi;

    $input = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Format JSON tidak valid']);
        return;
    }

    // Validate required fields
    $required_fields = ['name', 'category_id', 'price'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required']);
            return;
        }
    }

    try {
        // Validate category exists
        $categoryQuery = "SELECT id FROM categories WHERE id = ? AND is_active = TRUE";
        $categoryStmt = mysqli_prepare($koneksi, $categoryQuery);
        mysqli_stmt_bind_param($categoryStmt, "s", $input['category_id']);
        mysqli_stmt_execute($categoryStmt);
        $categoryResult = mysqli_stmt_get_result($categoryStmt);

        if (mysqli_num_rows($categoryResult) === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID kategori tidak valid']);
            return;
        }

        // Validate price
        $price = floatval($input['price']);
        if ($price < 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Price must be non-negative']);
            return;
        }

        $id = 'prod_' . uniqid();
        $name = mysqli_real_escape_string($koneksi, trim($input['name']));
        $description = isset($input['description']) ? mysqli_real_escape_string($koneksi, trim($input['description'])) : null;
        $image_url = isset($input['image_url']) ? mysqli_real_escape_string($koneksi, trim($input['image_url'])) : null;
        $is_topping = isset($input['is_topping']) ? (bool) $input['is_topping'] : false;

        // Insert product
        $insertQuery = "INSERT INTO products (id, category_id, name, description, image_url, price, is_topping, is_active, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, TRUE, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        $insertStmt = mysqli_prepare($koneksi, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "ssssidi", $id, $input['category_id'], $name, $description, $image_url, $price, $is_topping);

        if (mysqli_stmt_execute($insertStmt)) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Produk berhasil dibuat',
                'product_id' => $id
            ]);
        } else {
            throw new Exception('Failed to create product: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function updateProduct()
{
    global $koneksi;

    $product_id = $_GET['id'] ?? '';
    if (empty($product_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Product ID is required']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Format JSON tidak valid']);
        return;
    }

    try {
        // Check if product exists
        $checkQuery = "SELECT id FROM products WHERE id = ? AND is_active = TRUE";
        $checkStmt = mysqli_prepare($koneksi, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "s", $product_id);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan']);
            return;
        }

        $updateFields = [];
        $types = "";
        $values = [];

        if (isset($input['name'])) {
            $updateFields[] = "name = ?";
            $types .= "s";
            $values[] = mysqli_real_escape_string($koneksi, trim($input['name']));
        }

        if (isset($input['description'])) {
            $updateFields[] = "description = ?";
            $types .= "s";
            $values[] = mysqli_real_escape_string($koneksi, trim($input['description']));
        }

        if (isset($input['image_url'])) {
            $updateFields[] = "image_url = ?";
            $types .= "s";
            $values[] = mysqli_real_escape_string($koneksi, trim($input['image_url']));
        }

        if (isset($input['price'])) {
            $price = floatval($input['price']);
            if ($price < 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Price must be non-negative']);
                return;
            }
            $updateFields[] = "price = ?";
            $types .= "d";
            $values[] = $price;
        }

        if (isset($input['category_id'])) {
            // Validate category exists
            $categoryQuery = "SELECT id FROM categories WHERE id = ? AND is_active = TRUE";
            $categoryStmt = mysqli_prepare($koneksi, $categoryQuery);
            mysqli_stmt_bind_param($categoryStmt, "s", $input['category_id']);
            mysqli_stmt_execute($categoryStmt);
            $categoryResult = mysqli_stmt_get_result($categoryStmt);

            if (mysqli_num_rows($categoryResult) === 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID kategori tidak valid']);
                return;
            }

            $updateFields[] = "category_id = ?";
            $types .= "s";
            $values[] = $input['category_id'];
        }

        if (isset($input['is_topping'])) {
            $updateFields[] = "is_topping = ?";
            $types .= "i";
            $values[] = (bool) $input['is_topping'] ? 1 : 0;
        }

        if (empty($updateFields)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
            return;
        }

        $updateFields[] = "updated_at = CURRENT_TIMESTAMP";
        $types .= "s";
        $values[] = $product_id;

        $updateQuery = "UPDATE products SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $updateStmt = mysqli_prepare($koneksi, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, $types, ...$values);

        if (mysqli_stmt_execute($updateStmt)) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Produk berhasil diupdate'
            ]);
        } else {
            throw new Exception('Failed to update product: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function deleteProduct()
{
    global $koneksi;

    $product_id = $_GET['id'] ?? '';
    if (empty($product_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Product ID is required']);
        return;
    }

    try {
        // Soft delete product
        $deleteQuery = "UPDATE products SET is_active = FALSE, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $deleteStmt = mysqli_prepare($koneksi, $deleteQuery);
        mysqli_stmt_bind_param($deleteStmt, "s", $product_id);

        if (mysqli_stmt_execute($deleteStmt)) {
            if (mysqli_affected_rows($koneksi) > 0) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Produk berhasil dihapus'
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan']);
            }
        } else {
            throw new Exception('Failed to delete product: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}
?>