<?php
/**
 * API untuk mengelola kategori pengeluaran
 * CRUD operations untuk expense categories
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/koneksi.php';
require_once '../config/session.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            getCategoryById();
        } else {
            getAllCategories();
        }
        break;
    case 'POST':
        createCategory();
        break;
    case 'PUT':
        updateCategory();
        break;
    case 'DELETE':
        deleteCategory();
        break;
    case 'PATCH':
        // Handle restore action
        if (isset($_GET['action']) && $_GET['action'] === 'restore') {
            restoreCategory();
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

function getAllCategories()
{
    global $koneksi;

    try {
        // Build query with optional filters
        $whereConditions = ["1=1"]; // Always true condition to allow flexible filtering
        $params = [];
        $types = "";

        // Filter by is_deleted (inverse of is_active)
        if (isset($_GET['is_active'])) {
            $whereConditions[] = "is_deleted = ?";
            // If requesting active items (is_active=true), filter where is_deleted=0
            $is_active_value = ($_GET['is_active'] === 'true' || $_GET['is_active'] === '1' || $_GET['is_active'] === 1) ? 1 : 0;
            $is_deleted_value = $is_active_value === 1 ? 0 : 1; // Invert the logic
            $params[] = $is_deleted_value;
            $types .= "i";
        }

        // Pagination - if no pagination parameters provided, return all data
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $per_page = isset($_GET['per_page']) ? max(1, min(1000, intval($_GET['per_page']))) : (isset($_GET['page']) ? 20 : 1000);
        $offset = ($page - 1) * $per_page;

        $whereClause = implode(" AND ", $whereConditions);

        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM expense_categories WHERE " . $whereClause;
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

        // Get categories
        $query = "SELECT id, name, description, color, icon, 
                  (CASE WHEN is_deleted = 0 THEN 1 ELSE 0 END) as is_active,
                  created_at, updated_at
                  FROM expense_categories
                  WHERE " . $whereClause . "
                  ORDER BY created_at DESC
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

        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['is_active'] = (bool) $row['is_active'];
            $categories[] = $row;
        }

        $last_page = ceil($total / $per_page);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $categories,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $per_page,
                'last_page' => $last_page
            ],
            'message' => 'Kategori pengeluaran berhasil diambil'
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function getCategoryById()
{
    global $koneksi;

    $category_id = $_GET['id'];

    try {
        $query = "SELECT id, name, description, color, icon, 
                  (CASE WHEN is_deleted = 0 THEN 1 ELSE 0 END) as is_active,
                  created_at, updated_at
                  FROM expense_categories
                  WHERE id = ?";

        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $category_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            throw new Exception('Database query failed: ' . mysqli_error($koneksi));
        }

        if ($row = mysqli_fetch_assoc($result)) {
            $row['is_active'] = (bool) $row['is_active'];

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $row,
                'message' => 'Kategori pengeluaran berhasil diambil'
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Kategori pengeluaran tidak ditemukan']);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function createCategory()
{
    global $koneksi;

    $input = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Format JSON tidak valid']);
        return;
    }

    // Validate required fields
    $required_fields = ['name'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required']);
            return;
        }
    }

    try {
        $name = mysqli_real_escape_string($koneksi, trim($input['name']));
        $description = isset($input['description']) ? mysqli_real_escape_string($koneksi, trim($input['description'])) : null;
        $color = isset($input['color']) ? mysqli_real_escape_string($koneksi, trim($input['color'])) : '#A8A8A8';
        $icon = isset($input['icon']) ? mysqli_real_escape_string($koneksi, trim($input['icon'])) : 'ti ti-dots';

        // Insert category (is_deleted = 0 means active)
        $insertQuery = "INSERT INTO expense_categories (name, description, color, icon, is_deleted, created_at, updated_at)
                        VALUES (?, ?, ?, ?, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        $insertStmt = mysqli_prepare($koneksi, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "ssss", $name, $description, $color, $icon);

        if (mysqli_stmt_execute($insertStmt)) {
            $id = mysqli_insert_id($koneksi);
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Kategori pengeluaran berhasil dibuat',
                'category_id' => $id
            ]);
        } else {
            throw new Exception('Failed to create category: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function updateCategory()
{
    global $koneksi;

    $category_id = $_GET['id'] ?? '';
    if (empty($category_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Category ID is required']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Format JSON tidak valid']);
        return;
    }

    try {
        // Check if category exists
        $checkQuery = "SELECT id FROM expense_categories WHERE id = ?";
        $checkStmt = mysqli_prepare($koneksi, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "i", $category_id);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Kategori pengeluaran tidak ditemukan']);
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

        if (isset($input['color'])) {
            $updateFields[] = "color = ?";
            $types .= "s";
            $values[] = mysqli_real_escape_string($koneksi, trim($input['color']));
        }

        if (isset($input['icon'])) {
            $updateFields[] = "icon = ?";
            $types .= "s";
            $values[] = mysqli_real_escape_string($koneksi, trim($input['icon']));
        }

        if (empty($updateFields)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
            return;
        }

        $updateFields[] = "updated_at = CURRENT_TIMESTAMP";
        $types .= "i";
        $values[] = $category_id;

        $updateQuery = "UPDATE expense_categories SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $updateStmt = mysqli_prepare($koneksi, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, $types, ...$values);

        if (mysqli_stmt_execute($updateStmt)) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Kategori pengeluaran berhasil diupdate'
            ]);
        } else {
            throw new Exception('Failed to update category: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function deleteCategory()
{
    global $koneksi;

    $category_id = $_GET['id'] ?? '';
    if (empty($category_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Category ID is required']);
        return;
    }

    try {
        // Check if category is being used by expenses
        $checkQuery = "SELECT COUNT(*) as count FROM expenses WHERE category_id = ? AND is_deleted = 0";
        $checkStmt = mysqli_prepare($koneksi, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "i", $category_id);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);
        $checkRow = mysqli_fetch_assoc($checkResult);

        if ($checkRow['count'] > 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh pengeluaran']);
            return;
        }

        // Soft delete category (set is_deleted = 1)
        $deleteQuery = "UPDATE expense_categories SET is_deleted = 1, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $deleteStmt = mysqli_prepare($koneksi, $deleteQuery);
        mysqli_stmt_bind_param($deleteStmt, "i", $category_id);

        if (mysqli_stmt_execute($deleteStmt)) {
            if (mysqli_affected_rows($koneksi) > 0) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Kategori pengeluaran berhasil dihapus'
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Kategori pengeluaran tidak ditemukan']);
            }
        } else {
            throw new Exception('Failed to delete category: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function restoreCategory()
{
    global $koneksi;

    $category_id = $_GET['id'] ?? '';
    if (empty($category_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Category ID is required']);
        return;
    }

    try {
        // Check if category exists and is deleted
        $checkQuery = "SELECT id, is_deleted FROM expense_categories WHERE id = ?";
        $checkStmt = mysqli_prepare($koneksi, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "i", $category_id);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Kategori pengeluaran tidak ditemukan']);
            return;
        }

        $category = mysqli_fetch_assoc($checkResult);
        if ($category['is_deleted'] == 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Kategori pengeluaran sudah aktif']);
            return;
        }

        // Restore category (set is_deleted = 0)
        $restoreQuery = "UPDATE expense_categories SET is_deleted = 0, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $restoreStmt = mysqli_prepare($koneksi, $restoreQuery);
        mysqli_stmt_bind_param($restoreStmt, "i", $category_id);

        if (mysqli_stmt_execute($restoreStmt)) {
            if (mysqli_affected_rows($koneksi) > 0) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Kategori pengeluaran berhasil dipulihkan'
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Kategori pengeluaran tidak ditemukan']);
            }
        } else {
            throw new Exception('Failed to restore category: ' . mysqli_error($koneksi));
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