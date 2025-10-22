<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
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
            getRole();
        } else {
            getAllRoles();
        }
        break;
    case 'POST':
        createRole();
        break;
    case 'PUT':
        updateRole();
        break;
    case 'DELETE':
        deleteRole();
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

function getAllRoles()
{
    global $koneksi;

    try {
        // Get filter parameters
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $per_page = max(1, min(100, (int) ($_GET['per_page'] ?? 20)));
        $search = $_GET['search'] ?? '';

        // Build WHERE clause
        $whereConditions = [];
        $params = [];
        $types = '';

        if (!empty($search)) {
            $whereConditions[] = "(name LIKE ? OR id LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'ss';
        }

        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM roles $whereClause";
        if (!empty($params)) {
            $stmt = mysqli_prepare($koneksi, $countQuery);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            $countResult = mysqli_stmt_get_result($stmt);
            $total = mysqli_fetch_assoc($countResult)['total'];
            mysqli_stmt_close($stmt);
        } else {
            $countResult = mysqli_query($koneksi, $countQuery);
            $total = mysqli_fetch_assoc($countResult)['total'];
        }

        // Get paginated data
        $offset = ($page - 1) * $per_page;
        $dataQuery = "SELECT r.id, r.name, r.created_at,
                      (SELECT COUNT(*) FROM users WHERE role_id = r.id AND is_active = TRUE) as user_count
                      FROM roles r 
                      $whereClause
                      ORDER BY r.created_at DESC
                      LIMIT ? OFFSET ?";

        $stmt = mysqli_prepare($koneksi, $dataQuery);

        if (!empty($params)) {
            $params[] = $per_page;
            $params[] = $offset;
            $types .= 'ii';
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        } else {
            mysqli_stmt_bind_param($stmt, 'ii', $per_page, $offset);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $roles = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $roles[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'user_count' => (int) $row['user_count'],
                'created_at' => $row['created_at']
            ];
        }
        mysqli_stmt_close($stmt);

        // Get statistics
        $statsQuery = "SELECT 
                        COUNT(*) as total,
                        (SELECT COUNT(*) FROM users WHERE role_id = 'role_owner' AND is_active = TRUE) as owner_users,
                        (SELECT COUNT(*) FROM users WHERE role_id = 'role_admin' AND is_active = TRUE) as admin_users,
                        (SELECT COUNT(*) FROM users WHERE role_id = 'role_customer' AND is_active = TRUE) as customer_users
                       FROM roles";
        $statsResult = mysqli_query($koneksi, $statsQuery);
        $stats = mysqli_fetch_assoc($statsResult);

        echo json_encode([
            'success' => true,
            'data' => $roles,
            'pagination' => [
                'page' => $page,
                'per_page' => $per_page,
                'total' => (int) $total,
                'total_pages' => ceil($total / $per_page)
            ],
            'statistics' => [
                'total' => (int) $stats['total'],
                'owner_users' => (int) $stats['owner_users'],
                'admin_users' => (int) $stats['admin_users'],
                'customer_users' => (int) $stats['customer_users']
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch roles: ' . $e->getMessage()
        ]);
    }
}

function getRole()
{
    global $koneksi;

    try {
        $id = $_GET['id'];

        $query = "SELECT r.id, r.name, r.created_at,
                  (SELECT COUNT(*) FROM users WHERE role_id = r.id AND is_active = TRUE) as user_count
                  FROM roles r
                  WHERE r.id = ?";

        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 's', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'user_count' => (int) $row['user_count'],
                    'created_at' => $row['created_at']
                ]
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Role not found']);
        }

        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch role: ' . $e->getMessage()
        ]);
    }
}

function createRole()
{
    global $koneksi;

    try {
        $input = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        if (empty($input['name'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Role name is required']);
            return;
        }

        $name = trim($input['name']);

        // Validate name length
        if (strlen($name) > 30) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Role name must not exceed 30 characters']);
            return;
        }

        // Check if role with same name already exists
        $checkQuery = "SELECT id FROM roles WHERE name = ?";
        $checkStmt = mysqli_prepare($koneksi, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, 's', $name);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) > 0) {
            mysqli_stmt_close($checkStmt);
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Role with this name already exists']);
            return;
        }
        mysqli_stmt_close($checkStmt);

        // Generate unique ID
        $id = 'role_' . strtolower(str_replace(' ', '_', $name));

        // Insert new role
        $query = "INSERT INTO roles (id, name, created_at) VALUES (?, ?, CURRENT_TIMESTAMP)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'ss', $id, $name);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode([
                'success' => true,
                'message' => 'Role created successfully',
                'data' => [
                    'id' => $id,
                    'name' => $name
                ]
            ]);
        } else {
            throw new Exception('Failed to create role');
        }

        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create role: ' . $e->getMessage()
        ]);
    }
}

function updateRole()
{
    global $koneksi;

    try {
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Role ID is required']);
            return;
        }

        $id = $_GET['id'];
        $input = json_decode(file_get_contents('php://input'), true);

        // Check if role exists
        $checkQuery = "SELECT id FROM roles WHERE id = ?";
        $checkStmt = mysqli_prepare($koneksi, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, 's', $id);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) === 0) {
            mysqli_stmt_close($checkStmt);
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Role not found']);
            return;
        }
        mysqli_stmt_close($checkStmt);

        // Prevent updating system roles (owner, admin, customer)
        $systemRoles = ['role_owner', 'role_admin', 'role_customer'];
        if (in_array($id, $systemRoles)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Cannot modify system roles']);
            return;
        }

        // Validate and update name if provided
        if (isset($input['name'])) {
            $name = trim($input['name']);

            if (empty($name)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Role name cannot be empty']);
                return;
            }

            if (strlen($name) > 30) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Role name must not exceed 30 characters']);
                return;
            }

            // Check if name already exists for another role
            $checkNameQuery = "SELECT id FROM roles WHERE name = ? AND id != ?";
            $checkNameStmt = mysqli_prepare($koneksi, $checkNameQuery);
            mysqli_stmt_bind_param($checkNameStmt, 'ss', $name, $id);
            mysqli_stmt_execute($checkNameStmt);
            $checkNameResult = mysqli_stmt_get_result($checkNameStmt);

            if (mysqli_num_rows($checkNameResult) > 0) {
                mysqli_stmt_close($checkNameStmt);
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'Role name already exists']);
                return;
            }
            mysqli_stmt_close($checkNameStmt);

            // Update role name
            $updateQuery = "UPDATE roles SET name = ? WHERE id = ?";
            $updateStmt = mysqli_prepare($koneksi, $updateQuery);
            mysqli_stmt_bind_param($updateStmt, 'ss', $name, $id);

            if (mysqli_stmt_execute($updateStmt)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Role updated successfully'
                ]);
            } else {
                throw new Exception('Failed to update role');
            }

            mysqli_stmt_close($updateStmt);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'No changes made'
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update role: ' . $e->getMessage()
        ]);
    }
}

function deleteRole()
{
    global $koneksi;

    try {
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Role ID is required']);
            return;
        }

        $id = $_GET['id'];

        // Prevent deleting system roles
        $systemRoles = ['role_owner', 'role_admin', 'role_customer'];
        if (in_array($id, $systemRoles)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Cannot delete system roles']);
            return;
        }

        // Check if role has users
        $checkUsersQuery = "SELECT COUNT(*) as count FROM users WHERE role_id = ?";
        $checkUsersStmt = mysqli_prepare($koneksi, $checkUsersQuery);
        mysqli_stmt_bind_param($checkUsersStmt, 's', $id);
        mysqli_stmt_execute($checkUsersStmt);
        $checkUsersResult = mysqli_stmt_get_result($checkUsersStmt);
        $userCount = mysqli_fetch_assoc($checkUsersResult)['count'];
        mysqli_stmt_close($checkUsersStmt);

        if ($userCount > 0) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'message' => "Cannot delete role with {$userCount} assigned user(s). Please reassign users first."
            ]);
            return;
        }

        // Delete role
        $deleteQuery = "DELETE FROM roles WHERE id = ?";
        $deleteStmt = mysqli_prepare($koneksi, $deleteQuery);
        mysqli_stmt_bind_param($deleteStmt, 's', $id);

        if (mysqli_stmt_execute($deleteStmt)) {
            if (mysqli_stmt_affected_rows($deleteStmt) > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Role deleted successfully'
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Role not found']);
            }
        } else {
            throw new Exception('Failed to delete role');
        }

        mysqli_stmt_close($deleteStmt);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete role: ' . $e->getMessage()
        ]);
    }
}
?>