<?php
/**
 * API untuk mengelola pengeluaran
 * CRUD operations untuk expenses
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

// Get current user
$current_user = getCurrentSessionUser();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            getExpenseById();
        } else {
            getAllExpenses();
        }
        break;
    case 'POST':
        createExpense();
        break;
    case 'PUT':
        updateExpense();
        break;
    case 'DELETE':
        deleteExpense();
        break;
    case 'PATCH':
        // Handle restore action
        if (isset($_GET['action']) && $_GET['action'] === 'restore') {
            restoreExpense();
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

function getAllExpenses()
{
    global $koneksi;

    try {
        // Build query with optional filters
        $whereConditions = ["1=1"]; // Always true condition to allow flexible filtering
        $params = [];
        $types = "";

        if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
            $whereConditions[] = "e.category_id = ?";
            $params[] = $_GET['category_id'];
            $types .= "s";
        }

        if (isset($_GET['is_active'])) {
            $whereConditions[] = "e.is_deleted = ?";
            // is_active=true means is_deleted=0, is_active=false means is_deleted=1
            $is_active_value = ($_GET['is_active'] === 'true' || $_GET['is_active'] === '1' || $_GET['is_active'] === 1) ? 1 : 0;
            $is_deleted_value = $is_active_value === 1 ? 0 : 1;
            $params[] = $is_deleted_value;
            $types .= "i";
        }

        // Date range filter
        if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
            $whereConditions[] = "e.expense_date >= ?";
            $params[] = $_GET['start_date'];
            $types .= "s";
        }

        if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
            $whereConditions[] = "e.expense_date <= ?";
            $params[] = $_GET['end_date'];
            $types .= "s";
        }

        // Search
        if (isset($_GET['search'])) {
            $searchTerm = '%' . $_GET['search'] . '%';
            $whereConditions[] = "(e.title LIKE ? OR e.description LIKE ? OR ec.name LIKE ?)";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "sss";
        }

        // Pagination - if no pagination parameters provided, return all data
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $per_page = isset($_GET['per_page']) ? max(1, min(1000, intval($_GET['per_page']))) : (isset($_GET['page']) ? 20 : 1000);
        $offset = ($page - 1) * $per_page;

        $whereClause = implode(" AND ", $whereConditions);

        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM expenses e JOIN expense_categories ec ON e.category_id = ec.id WHERE " . $whereClause;
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

        // Get expenses
        $query = "SELECT e.id, e.category_id, e.title, e.description, e.amount, e.expense_date,
                         e.created_by, 
                         (CASE WHEN e.is_deleted = 0 THEN 1 ELSE 0 END) as is_active,
                         e.created_at, e.updated_at,
                         ec.name as category_name,
                         u.username as created_by_name
                  FROM expenses e
                  JOIN expense_categories ec ON e.category_id = ec.id
                  LEFT JOIN users u ON e.created_by = u.id
                  WHERE " . $whereClause . "
                  ORDER BY e.expense_date DESC, e.created_at DESC
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

        $expenses = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['amount'] = floatval($row['amount']);
            $row['is_active'] = (bool) $row['is_active'];
            $expenses[] = $row;
        }

        $last_page = ceil($total / $per_page);

        // Get summary statistics
        $statsQuery = "SELECT
                        SUM(CASE WHEN DATE(expense_date) = CURDATE() THEN amount ELSE 0 END) as today_expenses,
                        SUM(CASE WHEN YEAR(expense_date) = YEAR(CURDATE()) AND MONTH(expense_date) = MONTH(CURDATE()) THEN amount ELSE 0 END) as month_expenses,
                        AVG(amount) as avg_expense
                       FROM expenses WHERE is_deleted = 0";
        $statsResult = mysqli_query($koneksi, $statsQuery);
        $stats = mysqli_fetch_assoc($statsResult);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $expenses,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $per_page,
                'last_page' => $last_page
            ],
            'statistics' => [
                'today_expenses' => floatval($stats['today_expenses'] ?? 0),
                'month_expenses' => floatval($stats['month_expenses'] ?? 0),
                'avg_expense' => floatval($stats['avg_expense'] ?? 0)
            ],
            'message' => 'Pengeluaran berhasil diambil'
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function getExpenseById()
{
    global $koneksi;

    $expense_id = $_GET['id'];

    try {
        $query = "SELECT e.id, e.category_id, e.title, e.description, e.amount, e.expense_date,
                         e.created_by,
                         (CASE WHEN e.is_deleted = 0 THEN 1 ELSE 0 END) as is_active,
                         e.created_at, e.updated_at,
                         ec.name as category_name,
                         u.username as created_by_name
                  FROM expenses e
                  JOIN expense_categories ec ON e.category_id = ec.id
                  LEFT JOIN users u ON e.created_by = u.id
                  WHERE e.id = ?";

        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $expense_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            throw new Exception('Database query failed: ' . mysqli_error($koneksi));
        }

        if ($row = mysqli_fetch_assoc($result)) {
            $row['amount'] = floatval($row['amount']);
            $row['is_active'] = (bool) $row['is_active'];

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $row,
                'message' => 'Pengeluaran berhasil diambil'
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Pengeluaran tidak ditemukan']);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function createExpense()
{
    global $koneksi, $current_user;

    $input = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Format JSON tidak valid']);
        return;
    }

    // Validate required fields
    $required_fields = ['title', 'category_id', 'amount', 'expense_date'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
            return;
        }
    }

    try {
        // Validate category exists
        $categoryQuery = "SELECT id FROM expense_categories WHERE id = ? AND is_deleted = 0";
        $categoryStmt = mysqli_prepare($koneksi, $categoryQuery);
        mysqli_stmt_bind_param($categoryStmt, "i", $input['category_id']);
        mysqli_stmt_execute($categoryStmt);
        $categoryResult = mysqli_stmt_get_result($categoryStmt);

        if (mysqli_num_rows($categoryResult) === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID kategori tidak valid']);
            return;
        }

        // Validate amount
        $amount = floatval($input['amount']);
        if ($amount <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Amount must be greater than 0']);
            return;
        }

        $title = mysqli_real_escape_string($koneksi, trim($input['title']));
        $description = isset($input['description']) ? mysqli_real_escape_string($koneksi, trim($input['description'])) : null;
        $expense_date = date('Y-m-d', strtotime($input['expense_date']));
        $created_by = $current_user['id'] ?? null;

        // Insert expense (id auto increment)
        $insertQuery = "INSERT INTO expenses (category_id, title, description, amount, expense_date, created_by, is_deleted, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, ?, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        $insertStmt = mysqli_prepare($koneksi, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "issdss", $input['category_id'], $title, $description, $amount, $expense_date, $created_by);

        if (mysqli_stmt_execute($insertStmt)) {
            $expense_id = mysqli_insert_id($koneksi);
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Pengeluaran berhasil dibuat',
                'expense_id' => $expense_id
            ]);
        } else {
            throw new Exception('Failed to create expense: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function updateExpense()
{
    global $koneksi;

    $expense_id = $_GET['id'] ?? '';
    if (empty($expense_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Expense ID is required']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Format JSON tidak valid']);
        return;
    }

    try {
        // Check if expense exists
        $checkQuery = "SELECT id FROM expenses WHERE id = ? AND is_deleted = 0";
        $checkStmt = mysqli_prepare($koneksi, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "i", $expense_id);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Pengeluaran tidak ditemukan']);
            return;
        }

        $updateFields = [];
        $types = "";
        $values = [];

        if (isset($input['title'])) {
            $updateFields[] = "title = ?";
            $types .= "s";
            $values[] = mysqli_real_escape_string($koneksi, trim($input['title']));
        }

        if (isset($input['description'])) {
            $updateFields[] = "description = ?";
            $types .= "s";
            $values[] = mysqli_real_escape_string($koneksi, trim($input['description']));
        }

        if (isset($input['category_id'])) {
            // Validate category exists
            $categoryQuery = "SELECT id FROM expense_categories WHERE id = ? AND is_deleted = 0";
            $categoryStmt = mysqli_prepare($koneksi, $categoryQuery);
            mysqli_stmt_bind_param($categoryStmt, "i", $input['category_id']);
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

        if (isset($input['amount'])) {
            $amount = floatval($input['amount']);
            if ($amount <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Amount must be greater than 0']);
                return;
            }
            $updateFields[] = "amount = ?";
            $types .= "d";
            $values[] = $amount;
        }

        if (isset($input['expense_date'])) {
            $updateFields[] = "expense_date = ?";
            $types .= "s";
            $values[] = date('Y-m-d', strtotime($input['expense_date']));
        }

        if (empty($updateFields)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
            return;
        }

        $updateFields[] = "updated_at = CURRENT_TIMESTAMP";
        $types .= "i";
        $values[] = $expense_id;

        $updateQuery = "UPDATE expenses SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $updateStmt = mysqli_prepare($koneksi, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, $types, ...$values);

        if (mysqli_stmt_execute($updateStmt)) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Pengeluaran berhasil diupdate'
            ]);
        } else {
            throw new Exception('Failed to update expense: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function deleteExpense()
{
    global $koneksi;

    $expense_id = $_GET['id'] ?? '';
    if (empty($expense_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Expense ID is required']);
        return;
    }

    try {
        // Soft delete expense
        $deleteQuery = "UPDATE expenses SET is_deleted = 1, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $deleteStmt = mysqli_prepare($koneksi, $deleteQuery);
        mysqli_stmt_bind_param($deleteStmt, "i", $expense_id);

        if (mysqli_stmt_execute($deleteStmt)) {
            if (mysqli_affected_rows($koneksi) > 0) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Pengeluaran berhasil dihapus'
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Pengeluaran tidak ditemukan']);
            }
        } else {
            throw new Exception('Failed to delete expense: ' . mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage()
        ]);
    }
}

function restoreExpense()
{
    global $koneksi;

    $expense_id = $_GET['id'] ?? '';
    if (empty($expense_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Expense ID is required']);
        return;
    }

    try {
        // Check if expense exists and is deleted
        $checkQuery = "SELECT id, is_deleted FROM expenses WHERE id = ?";
        $checkStmt = mysqli_prepare($koneksi, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "i", $expense_id);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Pengeluaran tidak ditemukan']);
            return;
        }

        $expense = mysqli_fetch_assoc($checkResult);
        if ($expense['is_deleted'] == 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Pengeluaran sudah aktif']);
            return;
        }

        // Restore expense (set is_deleted = 0)
        $restoreQuery = "UPDATE expenses SET is_deleted = 0, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $restoreStmt = mysqli_prepare($koneksi, $restoreQuery);
        mysqli_stmt_bind_param($restoreStmt, "i", $expense_id);

        if (mysqli_stmt_execute($restoreStmt)) {
            if (mysqli_affected_rows($koneksi) > 0) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Pengeluaran berhasil dipulihkan'
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Pengeluaran tidak ditemukan']);
            }
        } else {
            throw new Exception('Failed to restore expense: ' . mysqli_error($koneksi));
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