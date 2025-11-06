<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/koneksi.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            getOptionById();
        } else {
            getAllOptions();
        }
        break;
    case 'POST':
        createOption();
        break;
    case 'PUT':
        updateOption();
        break;
    case 'DELETE':
        deleteOption();
        break;
    case 'PATCH':
        handlePatchRequest();
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

function handlePatchRequest()
{
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'restore':
            restoreOption();
            break;
        case 'permanent-delete':
            permanentDeleteOption();
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid PATCH action']);
            break;
    }
}

function getAllOptions()
{
    global $koneksi;

    try {
        // Get filter parameters
        $status = $_GET['status'] ?? 'active'; // active, inactive, all
        $type = $_GET['type'] ?? ''; // egg_type, broth_flavor, kencur_level
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $per_page = max(1, min(100, (int) ($_GET['per_page'] ?? 50)));
        $search = $_GET['search'] ?? '';

        // Build WHERE clause
        $whereConditions = [];
        $params = [];
        $types = '';

        if ($status === 'active') {
            $whereConditions[] = "co.is_active = TRUE";
        } elseif ($status === 'inactive') {
            $whereConditions[] = "co.is_active = FALSE";
        }

        if (!empty($type)) {
            $whereConditions[] = "c.type = ?";
            $params[] = $type;
            $types .= "s";
        }

        if (!empty($search)) {
            $whereConditions[] = "(co.name LIKE ? OR c.name LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "ss";
        }

        $whereClause = empty($whereConditions) ? "1=1" : implode(" AND ", $whereConditions);

        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM customization_options co LEFT JOIN categories c ON co.category_id = c.id WHERE " . $whereClause;
        if (!empty($params)) {
            $countStmt = mysqli_prepare($koneksi, $countQuery);
            mysqli_stmt_bind_param($countStmt, $types, ...$params);
            mysqli_stmt_execute($countStmt);
            $countResult = mysqli_stmt_get_result($countStmt);
        } else {
            $countResult = mysqli_query($koneksi, $countQuery);
        }

        $totalRow = mysqli_fetch_assoc($countResult);
        $total = $totalRow['total'];
        $last_page = ceil($total / $per_page);

        // Get paginated data with category information using JOIN
        $offset = ($page - 1) * $per_page;
        $query = "SELECT co.*, c.name as category_name, c.type as category_type 
                  FROM customization_options co 
                  LEFT JOIN categories c ON co.category_id = c.id 
                  WHERE " . $whereClause . " 
                  ORDER BY co.sort_order ASC, co.name ASC 
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

        $options = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['price'] = floatval($row['price']);
            $row['is_active'] = (bool) $row['is_active'];
            $row['sort_order'] = (int) $row['sort_order'];
            $options[] = $row;
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $options,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $per_page,
                'last_page' => $last_page,
                'from' => $offset + 1,
                'to' => min($offset + $per_page, $total)
            ],
            'message' => 'Customization options retrieved successfully'
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function getOptionById()
{
    global $koneksi;

    $option_id = $_GET['id'] ?? '';
    if (empty($option_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Option ID is required']);
        return;
    }

    try {
        $query = "SELECT * FROM customization_options WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $option_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $row['price'] = floatval($row['price']);
            $row['is_active'] = (bool) $row['is_active'];
            $row['sort_order'] = (int) $row['sort_order'];

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $row,
                'message' => 'Option retrieved successfully'
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Option not found']);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function createOption()
{
    global $koneksi;

    $input = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    $required_fields = ['name', 'category_id'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required']);
            return;
        }
    }

    try {
        // Generate ID
        $id = 'opt_' . uniqid();

        // Validate and set price
        $price = isset($input['price']) ? floatval($input['price']) : 0.00;
        if ($price < 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Price must be non-negative']);
            return;
        }

        $name = mysqli_real_escape_string($koneksi, trim($input['name']));
        $category_id = mysqli_real_escape_string($koneksi, trim($input['category_id']));
        $image = isset($input['image']) ? mysqli_real_escape_string($koneksi, trim($input['image'])) : null;
        $sort_order = isset($input['sort_order']) ? (int) $input['sort_order'] : 0;

        // Insert option
        $insertQuery = "INSERT INTO customization_options (id, name, price, image, category_id, is_active, sort_order, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, TRUE, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        $insertStmt = mysqli_prepare($koneksi, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "ssdssi", $id, $name, $price, $image, $category_id, $sort_order);

        if (mysqli_stmt_execute($insertStmt)) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $id,
                    'category_id' => $category_id,
                    'name' => $name,
                    'price' => $price,
                    'image' => $image,
                    'sort_order' => $sort_order
                ],
                'message' => 'Option created successfully'
            ]);
        } else {
            throw new Exception('Failed to create option: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function updateOption()
{
    global $koneksi;

    $option_id = $_GET['id'] ?? '';
    if (empty($option_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Option ID is required']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    try {
        // Check if option exists
        $checkQuery = "SELECT id FROM customization_options WHERE id = ?";
        $checkStmt = mysqli_prepare($koneksi, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "s", $option_id);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Option not found']);
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

        if (isset($input['category_id'])) {
            $updateFields[] = "category_id = ?";
            $types .= "s";
            $values[] = mysqli_real_escape_string($koneksi, trim($input['category_id']));
        }

        if (isset($input['image'])) {
            $updateFields[] = "image = ?";
            $types .= "s";
            $values[] = $input['image'] ? mysqli_real_escape_string($koneksi, trim($input['image'])) : null;
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

        if (isset($input['is_active'])) {
            $updateFields[] = "is_active = ?";
            $types .= "i";
            $values[] = $input['is_active'] ? 1 : 0;
        }

        if (isset($input['sort_order'])) {
            $updateFields[] = "sort_order = ?";
            $types .= "i";
            $values[] = (int) $input['sort_order'];
        }

        if (empty($updateFields)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
            return;
        }

        $updateFields[] = "updated_at = CURRENT_TIMESTAMP";
        $types .= "s";
        $values[] = $option_id;

        $updateQuery = "UPDATE customization_options SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $updateStmt = mysqli_prepare($koneksi, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, $types, ...$values);

        if (mysqli_stmt_execute($updateStmt)) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Option updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update option: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function deleteOption()
{
    global $koneksi;

    $option_id = $_GET['id'] ?? '';
    if (empty($option_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Option ID is required']);
        return;
    }

    try {
        // Soft delete option
        $deleteQuery = "UPDATE customization_options SET is_active = FALSE, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $deleteStmt = mysqli_prepare($koneksi, $deleteQuery);
        mysqli_stmt_bind_param($deleteStmt, "s", $option_id);

        if (mysqli_stmt_execute($deleteStmt)) {
            if (mysqli_affected_rows($koneksi) > 0) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Option deleted successfully (soft delete)'
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Option not found']);
            }
        } else {
            throw new Exception('Failed to delete option: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function restoreOption()
{
    global $koneksi;

    $option_id = $_GET['id'] ?? '';
    if (empty($option_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Option ID is required']);
        return;
    }

    try {
        $restoreQuery = "UPDATE customization_options SET is_active = TRUE, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $restoreStmt = mysqli_prepare($koneksi, $restoreQuery);
        mysqli_stmt_bind_param($restoreStmt, "s", $option_id);

        if (mysqli_stmt_execute($restoreStmt)) {
            if (mysqli_affected_rows($koneksi) > 0) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Option restored successfully'
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Option not found']);
            }
        } else {
            throw new Exception('Failed to restore option: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function permanentDeleteOption()
{
    global $koneksi;

    $option_id = $_GET['id'] ?? '';
    if (empty($option_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Option ID is required']);
        return;
    }

    try {
        $deleteQuery = "DELETE FROM customization_options WHERE id = ?";
        $deleteStmt = mysqli_prepare($koneksi, $deleteQuery);
        mysqli_stmt_bind_param($deleteStmt, "s", $option_id);

        if (mysqli_stmt_execute($deleteStmt)) {
            if (mysqli_affected_rows($koneksi) > 0) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Option permanently deleted'
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Option not found']);
            }
        } else {
            throw new Exception('Failed to delete option: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}
