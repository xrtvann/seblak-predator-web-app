<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
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
            getToppingById();
        } else {
            getAllToppings();
        }
        break;
    case 'POST':
        createTopping();
        break;
    case 'PUT':
        updateTopping();
        break;
    case 'DELETE':
        deleteTopping();
        break;
    case 'PATCH':
        if (isset($_GET['action']) && $_GET['action'] === 'restore') {
            restoreTopping();
        } elseif (isset($_GET['action']) && $_GET['action'] === 'permanent_delete') {
            permanentDeleteTopping();
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

function getAllToppings()
{
    global $koneksi;

    try {
        $whereConditions = ["1=1"];
        $params = [];
        $types = "";

        if (isset($_GET['category']) && $_GET['category'] !== '') {
            $whereConditions[] = "t.category = ?";
            $params[] = $_GET['category'];
            $types .= "s";
        }

        if (isset($_GET['is_available'])) {
            $whereConditions[] = "t.is_available = ?";
            $is_val = ($_GET['is_available'] === 'true' || $_GET['is_available'] === '1' || $_GET['is_available'] === 1) ? 1 : 0;
            $params[] = $is_val;
            $types .= "i";
        }

        if (!empty($_GET['search'])) {
            $whereConditions[] = "(t.name LIKE ? OR c.name LIKE ?)";
            $search = '%' . $_GET['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $types .= "ss";
        }

        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $per_page = isset($_GET['per_page']) ? max(1, min(1000, intval($_GET['per_page']))) : (isset($_GET['page']) ? 20 : 1000);
        $offset = ($page - 1) * $per_page;

        $whereClause = implode(' AND ', $whereConditions);

        $countQuery = "SELECT COUNT(*) as total FROM toppings t 
                       LEFT JOIN categories c ON t.category = c.id 
                       WHERE " . $whereClause;
        if (!empty($params)) {
            $countStmt = mysqli_prepare($koneksi, $countQuery);
            if (!empty($types))
                mysqli_stmt_bind_param($countStmt, $types, ...$params);
            mysqli_stmt_execute($countStmt);
            $countResult = mysqli_stmt_get_result($countStmt);
        } else {
            $countResult = mysqli_query($koneksi, $countQuery);
        }

        $totalRow = mysqli_fetch_assoc($countResult);
        $total = $totalRow['total'];
        $last_page = ceil($total / $per_page);

        $query = "SELECT t.id, t.name, t.price, t.image, t.category, c.name as category_name, 
                         t.is_available, t.sort_order, t.created_at, t.updated_at 
                  FROM toppings t
                  LEFT JOIN categories c ON t.category = c.id
                  WHERE " . $whereClause . " 
                  ORDER BY t.sort_order ASC, t.name ASC 
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

        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['price'] = floatval($row['price']);
            $row['is_available'] = (bool) $row['is_available'];
            // Backwards compatibility with pages that expect is_active
            $row['is_active'] = $row['is_available'];
            // For backwards compatibility with old UI that used category_id
            $row['category_id'] = $row['category'];
            $row['sort_order'] = (int) $row['sort_order'];
            $items[] = $row;
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $items,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $per_page,
                'last_page' => $last_page,
                'from' => $offset + 1,
                'to' => min($offset + $per_page, $total)
            ],
            'message' => 'Toppings retrieved successfully'
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()]);
    }
}

function getToppingById()
{
    global $koneksi;
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Topping ID is required']);
        return;
    }

    try {
        $query = "SELECT t.id, t.name, t.price, t.image, t.category, c.name as category_name, 
                         t.is_available, t.sort_order, t.created_at, t.updated_at 
                  FROM toppings t
                  LEFT JOIN categories c ON t.category = c.id
                  WHERE t.id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 's', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $row['price'] = floatval($row['price']);
            $row['is_available'] = (bool) $row['is_available'];
            // Backwards compatibility
            $row['is_active'] = $row['is_available'];
            // For backwards compatibility with old UI that used category_id
            $row['category_id'] = $row['category'];
            $row['sort_order'] = (int) $row['sort_order'];
            http_response_code(200);
            echo json_encode(['success' => true, 'data' => $row, 'message' => 'Topping retrieved successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Topping not found']);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()]);
    }
}

function createTopping()
{
    global $koneksi;
    $input = json_decode(file_get_contents('php://input'), true);
    $required = ['name', 'price'];
    foreach ($required as $f) {
        if (!isset($input[$f]) || $input[$f] === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => ucfirst($f) . ' is required']);
            return;
        }
    }

    try {
        $id = 'top_' . uniqid();
        $name = mysqli_real_escape_string($koneksi, trim($input['name']));
        $price = floatval($input['price']);
        if ($price < 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Price must be non-negative']);
            return;
        }
        $image = isset($input['image']) ? mysqli_real_escape_string($koneksi, trim($input['image'])) : null;
        $category = isset($input['category']) ? mysqli_real_escape_string($koneksi, trim($input['category'])) : null;
        $is_available = isset($input['is_available']) ? ($input['is_available'] ? 1 : 0) : 1;
        $sort_order = isset($input['sort_order']) ? (int) $input['sort_order'] : 0;

        $insert = "INSERT INTO toppings (id, name, price, image, category, is_available, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        $stmt = mysqli_prepare($koneksi, $insert);
        mysqli_stmt_bind_param($stmt, 'ssdssii', $id, $name, $price, $image, $category, $is_available, $sort_order);

        if (mysqli_stmt_execute($stmt)) {
            http_response_code(201);
            echo json_encode(['success' => true, 'data' => ['id' => $id, 'name' => $name, 'price' => $price], 'message' => 'Topping created successfully']);
        } else {
            throw new Exception('Failed to create topping: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()]);
    }
}

function updateTopping()
{
    global $koneksi;
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Topping ID is required']);
        return;
    }
    $input = json_decode(file_get_contents('php://input'), true);

    try {
        $check = "SELECT id FROM toppings WHERE id = ?";
        $cstmt = mysqli_prepare($koneksi, $check);
        mysqli_stmt_bind_param($cstmt, 's', $id);
        mysqli_stmt_execute($cstmt);
        $cres = mysqli_stmt_get_result($cstmt);
        if (mysqli_num_rows($cres) === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Topping not found']);
            return;
        }

        $updateFields = [];
        $types = '';
        $values = [];

        if (isset($input['name'])) {
            $updateFields[] = 'name = ?';
            $types .= 's';
            $values[] = mysqli_real_escape_string($koneksi, trim($input['name']));
        }
        if (isset($input['price'])) {
            $updateFields[] = 'price = ?';
            $types .= 'd';
            $values[] = floatval($input['price']);
        }
        if (array_key_exists('image', $input)) {
            $updateFields[] = 'image = ?';
            $types .= 's';
            $values[] = $input['image'] ? mysqli_real_escape_string($koneksi, trim($input['image'])) : null;
        }
        if (array_key_exists('category', $input)) {
            $updateFields[] = 'category = ?';
            $types .= 's';
            $values[] = $input['category'] ? mysqli_real_escape_string($koneksi, trim($input['category'])) : null;
        }
        if (isset($input['is_available'])) {
            $updateFields[] = 'is_available = ?';
            $types .= 'i';
            $values[] = $input['is_available'] ? 1 : 0;
        }
        if (isset($input['sort_order'])) {
            $updateFields[] = 'sort_order = ?';
            $types .= 'i';
            $values[] = (int) $input['sort_order'];
        }

        if (empty($updateFields)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
            return;
        }

        $updateFields[] = 'updated_at = CURRENT_TIMESTAMP';
        $types .= 's';
        $values[] = $id;

        $query = "UPDATE toppings SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $ustmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($ustmt, $types, ...$values);

        if (mysqli_stmt_execute($ustmt)) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Topping updated successfully']);
        } else {
            throw new Exception('Failed to update topping: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()]);
    }
}

function deleteTopping()
{
    global $koneksi;
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Topping ID is required']);
        return;
    }

    try {
        $query = "UPDATE toppings SET is_available = FALSE, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 's', $id);
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_affected_rows($koneksi) > 0) {
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'Topping deleted successfully (soft delete)']);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Topping not found']);
            }
        } else {
            throw new Exception('Failed to delete topping: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()]);
    }
}

function restoreTopping()
{
    global $koneksi;
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Topping ID is required']);
        return;
    }

    try {
        $query = "UPDATE toppings SET is_available = TRUE, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 's', $id);
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_affected_rows($koneksi) > 0) {
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'Topping restored successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Topping not found']);
            }
        } else {
            throw new Exception('Failed to restore topping: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()]);
    }
}

function permanentDeleteTopping()
{
    global $koneksi;
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Topping ID is required']);
        return;
    }

    try {
        $query = "DELETE FROM toppings WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 's', $id);
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_affected_rows($koneksi) > 0) {
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'Topping permanently deleted']);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Topping not found']);
            }
        } else {
            throw new Exception('Failed to delete topping: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()]);
    }
}

?>