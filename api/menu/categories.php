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
        getAllCategories();
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
            restoreCategory();
            break;
        case 'permanent-delete':
            permanentDeleteCategory();
            break;
        case 'permanent-delete-inactive':
            permanentlyDeleteInactiveCategories();
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid PATCH action']);
            break;
    }
}

function getAllCategories()
{
    global $koneksi;

    try {
        // Get filter parameters
        $status = $_GET['status'] ?? 'active'; // active, deleted, all
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $per_page = max(1, min(100, (int) ($_GET['per_page'] ?? 20)));
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? ''; // product, topping

        // Build WHERE clause
        $whereConditions = [];
        $params = [];
        $types = '';

        if ($status === 'active') {
            $whereConditions[] = "is_active = TRUE";
        } elseif ($status === 'deleted') {
            $whereConditions[] = "is_active = FALSE";
        }
        // For 'all', no status filter

        if (!empty($search)) {
            $whereConditions[] = "name LIKE ?";
            $params[] = "%$search%";
            $types .= 's';
        }

        if (!empty($type) && in_array($type, ['seblak_base', 'topping'])) {
            $whereConditions[] = "type = ?";
            $params[] = $type;
            $types .= 's';
        }

        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM categories $whereClause";
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

        if (!$countResult) {
            throw new Exception('Count query failed: ' . mysqli_error($koneksi));
        }

        $totalRow = mysqli_fetch_assoc($countResult);
        $total = (int) $totalRow['total'];

        // Calculate pagination
        $offset = ($page - 1) * $per_page;
        $last_page = ceil($total / $per_page);

        // Get categories with pagination
        $query = "SELECT id, name, type, is_active, created_at, updated_at 
                  FROM categories 
                  $whereClause 
                  ORDER BY type, name 
                  LIMIT ? OFFSET ?";

        $params[] = $per_page;
        $params[] = $offset;
        $types .= 'ii';

        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            throw new Exception('Database query failed: ' . mysqli_error($koneksi));
        }

        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $categories,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $per_page,
                'last_page' => $last_page,
                'from' => $total > 0 ? $offset + 1 : 0,
                'to' => min($offset + $per_page, $total)
            ],
            'message' => 'Categories retrieved successfully'
        ]);

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
        echo json_encode(['success' => false, 'message' => 'Invalid JSON format']);
        return;
    }

    // Validate required fields
    if (empty($input['name'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Category name is required']);
        return;
    }

    try {
        $id = 'cat_' . uniqid();
        $name = mysqli_real_escape_string($koneksi, trim($input['name']));
        $type = isset($input['type']) ? $input['type'] : 'seblak_base';

        // Validate type
        if (!in_array($type, ['seblak_base', 'topping'])) {
            throw new Exception('Invalid category type. Must be seblak_base or topping');
        }

        // Check if category name already exists
        $checkQuery = "SELECT id FROM categories WHERE name = ? AND type = ?";
        $checkStmt = mysqli_prepare($koneksi, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "ss", $name, $type);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) > 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Category name already exists for this type']);
            return;
        }

        // Insert new category
        $insertQuery = "INSERT INTO categories (id, name, type, is_active, created_at, updated_at) 
                        VALUES (?, ?, ?, TRUE, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        $insertStmt = mysqli_prepare($koneksi, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "sss", $id, $name, $type);

        if (mysqli_stmt_execute($insertStmt)) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Category created successfully',
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
        echo json_encode(['success' => false, 'message' => 'Invalid JSON format']);
        return;
    }

    try {
        // Check if category exists
        $checkQuery = "SELECT id FROM categories WHERE id = ? AND is_active = TRUE";
        $checkStmt = mysqli_prepare($koneksi, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "s", $category_id);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Category not found']);
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

        if (isset($input['type']) && in_array($input['type'], ['seblak_base', 'topping'])) {
            $updateFields[] = "type = ?";
            $types .= "s";
            $values[] = $input['type'];
        }

        if (empty($updateFields)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
            return;
        }

        $updateFields[] = "updated_at = CURRENT_TIMESTAMP";
        $types .= "s";
        $values[] = $category_id;

        $updateQuery = "UPDATE categories SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $updateStmt = mysqli_prepare($koneksi, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, $types, ...$values);

        if (mysqli_stmt_execute($updateStmt)) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Category updated successfully'
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
        // Get category info first
        $getCategoryQuery = "SELECT id, name FROM categories WHERE id = ?";
        $getCategoryStmt = mysqli_prepare($koneksi, $getCategoryQuery);
        mysqli_stmt_bind_param($getCategoryStmt, "s", $category_id);
        mysqli_stmt_execute($getCategoryStmt);
        $getCategoryResult = mysqli_stmt_get_result($getCategoryStmt);

        if (mysqli_num_rows($getCategoryResult) === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Category not found']);
            return;
        }

        $category = mysqli_fetch_assoc($getCategoryResult);

        // Check if category has associated active toppings
        $toppingCheckQuery = "SELECT COUNT(*) as topping_count FROM toppings WHERE category_id = ? AND is_available = TRUE";
        $toppingCheckStmt = mysqli_prepare($koneksi, $toppingCheckQuery);
        mysqli_stmt_bind_param($toppingCheckStmt, "s", $category_id);
        mysqli_stmt_execute($toppingCheckStmt);
        $toppingCheckResult = mysqli_stmt_get_result($toppingCheckStmt);
        $toppingRow = mysqli_fetch_assoc($toppingCheckResult);

        if ($toppingRow['topping_count'] > 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "Cannot delete category '{$category['name']}'. It has {$toppingRow['topping_count']} active topping(s). Please deactivate or reassign all toppings first."
            ]);
            return;
        }

        // Check if category has associated active customization options
        $customizationCheckQuery = "SELECT COUNT(*) as customization_count FROM customization_options WHERE category_id = ? AND is_active = TRUE";
        $customizationCheckStmt = mysqli_prepare($koneksi, $customizationCheckQuery);
        mysqli_stmt_bind_param($customizationCheckStmt, "s", $category_id);
        mysqli_stmt_execute($customizationCheckStmt);
        $customizationCheckResult = mysqli_stmt_get_result($customizationCheckStmt);
        $customizationRow = mysqli_fetch_assoc($customizationCheckResult);

        if ($customizationRow['customization_count'] > 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "Cannot delete category '{$category['name']}'. It has {$customizationRow['customization_count']} active customization option(s). Please deactivate or reassign them first."
            ]);
            return;
        }

        // Check if category has associated active spice levels
        $spiceLevelCheckQuery = "SELECT COUNT(*) as spice_count FROM spice_levels WHERE category_id = ? AND is_active = TRUE";
        $spiceLevelCheckStmt = mysqli_prepare($koneksi, $spiceLevelCheckQuery);
        mysqli_stmt_bind_param($spiceLevelCheckStmt, "s", $category_id);
        mysqli_stmt_execute($spiceLevelCheckStmt);
        $spiceLevelCheckResult = mysqli_stmt_get_result($spiceLevelCheckStmt);
        $spiceLevelRow = mysqli_fetch_assoc($spiceLevelCheckResult);

        if ($spiceLevelRow['spice_count'] > 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "Cannot delete category '{$category['name']}'. It has {$spiceLevelRow['spice_count']} active spice level(s). Please deactivate or reassign them first."
            ]);
            return;
        }

        // Soft delete category (set is_active to FALSE)
        $deleteQuery = "UPDATE categories SET is_active = FALSE, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $deleteStmt = mysqli_prepare($koneksi, $deleteQuery);
        mysqli_stmt_bind_param($deleteStmt, "s", $category_id);

        if (mysqli_stmt_execute($deleteStmt)) {
            if (mysqli_affected_rows($koneksi) > 0) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => "Category '{$category['name']}' has been deactivated successfully"
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Category not found or already inactive']);
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
        // Check if category exists and is currently deleted
        $checkQuery = "SELECT id, name FROM categories WHERE id = ? AND is_active = FALSE";
        $checkStmt = mysqli_prepare($koneksi, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "s", $category_id);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Deleted category not found']);
            return;
        }

        $category = mysqli_fetch_assoc($checkResult);

        // Restore category
        $restoreQuery = "UPDATE categories SET is_active = TRUE, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $restoreStmt = mysqli_prepare($koneksi, $restoreQuery);
        mysqli_stmt_bind_param($restoreStmt, "s", $category_id);

        if (mysqli_stmt_execute($restoreStmt)) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => "Category '{$category['name']}' has been restored successfully"
            ]);
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

function permanentDeleteCategory()
{
    global $koneksi;

    $category_id = $_GET['id'] ?? '';
    if (empty($category_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Category ID is required']);
        return;
    }

    try {
        // Check if category exists
        $checkQuery = "SELECT id, name, is_active FROM categories WHERE id = ?";
        $checkStmt = mysqli_prepare($koneksi, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "s", $category_id);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Category not found']);
            return;
        }

        $category = mysqli_fetch_assoc($checkResult);

        // Check if category has associated toppings (RESTRICT constraint)
        $toppingCheckQuery = "SELECT COUNT(*) as topping_count FROM toppings WHERE category = ?";
        $toppingCheckStmt = mysqli_prepare($koneksi, $toppingCheckQuery);
        mysqli_stmt_bind_param($toppingCheckStmt, "s", $category_id);
        mysqli_stmt_execute($toppingCheckStmt);
        $toppingCheckResult = mysqli_stmt_get_result($toppingCheckStmt);
        $toppingRow = mysqli_fetch_assoc($toppingCheckResult);

        if ($toppingRow['topping_count'] > 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "Cannot permanently delete category '{$category['name']}'. It has {$toppingRow['topping_count']} associated topping(s). Please delete or reassign all toppings in this category first."
            ]);
            return;
        }

        // Check if category has associated customization options
        $customizationCheckQuery = "SELECT COUNT(*) as customization_count FROM customization_options WHERE category_id = ?";
        $customizationCheckStmt = mysqli_prepare($koneksi, $customizationCheckQuery);
        mysqli_stmt_bind_param($customizationCheckStmt, "s", $category_id);
        mysqli_stmt_execute($customizationCheckStmt);
        $customizationCheckResult = mysqli_stmt_get_result($customizationCheckStmt);
        $customizationRow = mysqli_fetch_assoc($customizationCheckResult);

        // Check if category has associated spice levels
        $spiceLevelCheckQuery = "SELECT COUNT(*) as spice_count FROM spice_levels WHERE category_id = ?";
        $spiceLevelCheckStmt = mysqli_prepare($koneksi, $spiceLevelCheckQuery);
        mysqli_stmt_bind_param($spiceLevelCheckStmt, "s", $category_id);
        mysqli_stmt_execute($spiceLevelCheckStmt);
        $spiceLevelCheckResult = mysqli_stmt_get_result($spiceLevelCheckStmt);
        $spiceLevelRow = mysqli_fetch_assoc($spiceLevelCheckResult);

        // Warning message if there are related records that will be affected
        $warnings = [];
        if ($customizationRow['customization_count'] > 0) {
            $warnings[] = "{$customizationRow['customization_count']} customization option(s) will have their category_id set to NULL";
        }
        if ($spiceLevelRow['spice_count'] > 0) {
            $warnings[] = "{$spiceLevelRow['spice_count']} spice level(s) will have their category_id set to NULL";
        }

        // Permanently delete category
        $deleteQuery = "DELETE FROM categories WHERE id = ?";
        $deleteStmt = mysqli_prepare($koneksi, $deleteQuery);
        mysqli_stmt_bind_param($deleteStmt, "s", $category_id);

        if (mysqli_stmt_execute($deleteStmt)) {
            if (mysqli_affected_rows($koneksi) > 0) {
                $message = "Category '{$category['name']}' has been permanently deleted";
                if (!empty($warnings)) {
                    $message .= ". Note: " . implode('; ', $warnings);
                }

                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => $message,
                    'warnings' => $warnings
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Category not found']);
            }
        } else {
            // Check if it's a foreign key constraint error
            $error = mysqli_error($koneksi);
            if (strpos($error, 'foreign key constraint') !== false || strpos($error, 'RESTRICT') !== false) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => "Cannot delete category '{$category['name']}' due to foreign key constraints. Please remove all associated records first."
                ]);
            } else {
                throw new Exception('Failed to permanently delete category: ' . $error);
            }
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function permanentlyDeleteInactiveCategories()
{
    global $koneksi;

    try {
        // Start transaction for data consistency
        mysqli_autocommit($koneksi, FALSE);

        // Get count of inactive categories first
        $countQuery = "SELECT COUNT(*) as total FROM categories WHERE is_active = FALSE";
        $countResult = mysqli_query($koneksi, $countQuery);
        $countRow = mysqli_fetch_assoc($countResult);
        $inactiveCount = $countRow['total'];

        if ($inactiveCount === 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Tidak ada kategori inaktif yang perlu dihapus',
                'deleted_count' => 0
            ]);
            mysqli_autocommit($koneksi, TRUE);
            return;
        }

        // Check if any inactive categories have dependencies
        $checkQuery = "SELECT c.id, c.name, 
                       (SELECT COUNT(*) FROM toppings WHERE category_id = c.id) as topping_count,
                       (SELECT COUNT(*) FROM customization_options WHERE category_id = c.id) as option_count
                       FROM categories c 
                       WHERE c.is_active = FALSE";
        $checkResult = mysqli_query($koneksi, $checkQuery);

        $hasConstraints = false;
        $constraintMessages = [];

        while ($row = mysqli_fetch_assoc($checkResult)) {
            if ($row['topping_count'] > 0 || $row['option_count'] > 0) {
                $hasConstraints = true;
                $constraintMessages[] = "Kategori '{$row['name']}' memiliki {$row['topping_count']} topping dan {$row['option_count']} opsi";
            }
        }

        if ($hasConstraints) {
            mysqli_autocommit($koneksi, TRUE);
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Tidak dapat menghapus kategori yang memiliki dependensi. ' . implode('; ', $constraintMessages)
            ]);
            return;
        }

        // Delete all inactive categories
        $deleteQuery = "DELETE FROM categories WHERE is_active = FALSE";
        $deleteResult = mysqli_query($koneksi, $deleteQuery);

        if ($deleteResult) {
            $deletedCount = mysqli_affected_rows($koneksi);

            // Commit transaction
            mysqli_commit($koneksi);
            mysqli_autocommit($koneksi, TRUE);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => "Berhasil menghapus {$deletedCount} kategori inaktif secara permanen",
                'deleted_count' => $deletedCount
            ]);
        } else {
            mysqli_rollback($koneksi);
            mysqli_autocommit($koneksi, TRUE);
            throw new Exception('Failed to delete inactive categories: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        mysqli_autocommit($koneksi, TRUE);
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
}
?>