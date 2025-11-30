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
        getAllUsers();
        break;
    case 'POST':
        createUser();
        break;
    case 'PUT':
        updateUser();
        break;
    case 'DELETE':
        deleteUser();
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
            restoreUser();
            break;
        case 'toggle-status':
            toggleUserStatus();
            break;
        case 'permanent-delete':
            permanentlyDeleteUser();
            break;
        case 'permanent-delete-inactive':
            permanentlyDeleteInactiveUsers();
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid PATCH action']);
            break;
    }
}

function getAllUsers()
{
    global $koneksi;

    try {
        // Get filter parameters
        $status = $_GET['status'] ?? 'all'; // active, inactive, all
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $per_page = max(1, min(100, (int) ($_GET['per_page'] ?? 20)));
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? ''; // role_owner, role_admin, role_customer

        // Build WHERE clause
        $whereConditions = [];
        $params = [];
        $types = '';

        if ($status === 'active') {
            $whereConditions[] = "u.is_active = TRUE";
        } elseif ($status === 'inactive') {
            $whereConditions[] = "u.is_active = FALSE";
        }
        // For 'all', no status filter

        if (!empty($search)) {
            $whereConditions[] = "(u.name LIKE ? OR u.email LIKE ? OR u.username LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $types .= 'sss';
        }

        if (!empty($role)) {
            $whereConditions[] = "u.role_id = ?";
            $params[] = $role;
            $types .= 's';
        }

        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM users u $whereClause";
        if (!empty($params)) {
            $countStmt = mysqli_prepare($koneksi, $countQuery);
            if (!empty($types)) {
                mysqli_stmt_bind_param($countStmt, $types, ...$params);
            }
            mysqli_stmt_execute($countStmt);
            $countResult = mysqli_stmt_get_result($countStmt);
            $total = mysqli_fetch_assoc($countResult)['total'];
            mysqli_stmt_close($countStmt);
        } else {
            $countResult = mysqli_query($koneksi, $countQuery);
            $total = mysqli_fetch_assoc($countResult)['total'];
        }

        // Get paginated data
        $offset = ($page - 1) * $per_page;
        $query = "SELECT u.id, u.name, u.email, u.username, u.role_id,
                         r.name as role_name, u.is_active,
                         u.created_at, u.updated_at
                  FROM users u
                  LEFT JOIN roles r ON u.role_id = r.id
                  $whereClause
                  ORDER BY u.created_at DESC
                  LIMIT ? OFFSET ?";

        $stmt = mysqli_prepare($koneksi, $query);

        // Add limit and offset to params
        $params[] = $per_page;
        $params[] = $offset;
        $types .= 'ii';

        if (!empty($types)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['is_active'] = (bool) $row['is_active'];
            $users[] = $row;
        }

        mysqli_stmt_close($stmt);

        // Get statistics
        $statsQuery = "SELECT
                        COUNT(*) as total,
                        SUM(CASE WHEN is_active = TRUE THEN 1 ELSE 0 END) as active,
                        SUM(CASE WHEN is_active = FALSE THEN 1 ELSE 0 END) as inactive,
                        SUM(CASE WHEN role_id = 'role_admin' AND is_active = TRUE THEN 1 ELSE 0 END) as admin_count,
                        SUM(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as new_users
                       FROM users";
        $statsResult = mysqli_query($koneksi, $statsQuery);
        $stats = mysqli_fetch_assoc($statsResult);

        echo json_encode([
            'success' => true,
            'data' => $users,
            'pagination' => [
                'page' => $page,
                'per_page' => $per_page,
                'total' => (int) $total,
                'total_pages' => ceil($total / $per_page)
            ],
            'statistics' => [
                'total' => (int) $stats['total'],
                'active' => (int) $stats['active'],
                'inactive' => (int) $stats['inactive'],
                'admin_count' => (int) $stats['admin_count'],
                'new_users' => (int) $stats['new_users']
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch users: ' . $e->getMessage()
        ]);
    }
}

function createUser()
{
    global $koneksi;

    try {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        $requiredFields = ['name', 'email', 'username', 'password', 'role_id'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Check if email already exists
        $checkEmail = mysqli_prepare($koneksi, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($checkEmail, 's', $data['email']);
        mysqli_stmt_execute($checkEmail);
        $emailResult = mysqli_stmt_get_result($checkEmail);
        if (mysqli_num_rows($emailResult) > 0) {
            throw new Exception("Email already exists");
        }
        mysqli_stmt_close($checkEmail);

        // Check if username already exists
        $checkUsername = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($checkUsername, 's', $data['username']);
        mysqli_stmt_execute($checkUsername);
        $usernameResult = mysqli_stmt_get_result($checkUsername);
        if (mysqli_num_rows($usernameResult) > 0) {
            throw new Exception("Username already exists");
        }
        mysqli_stmt_close($checkUsername);

        // Generate unique ID
        $id = 'user_' . uniqid() . '_' . time();

        // Hash password
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        // Set default active status
        $isActive = isset($data['is_active']) ? (bool) $data['is_active'] : true;

        // Insert new user
        $query = "INSERT INTO users (id, name, email, username, password_hash, role_id, is_active, created_at, updated_at)
                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param(
            $stmt,
            'ssssssi',
            $id,
            $data['name'],
            $data['email'],
            $data['username'],
            $passwordHash,
            $data['role_id'],
            $isActive
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);

            // Get the created user
            $getUser = mysqli_prepare($koneksi, "SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.id = ?");
            mysqli_stmt_bind_param($getUser, 's', $id);
            mysqli_stmt_execute($getUser);
            $result = mysqli_stmt_get_result($getUser);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($getUser);

            // Remove password hash from response
            unset($user['password_hash']);
            $user['is_active'] = (bool) $user['is_active'];

            echo json_encode([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ]);
        } else {
            throw new Exception('Failed to create user: ' . mysqli_error($koneksi));
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

function updateUser()
{
    global $koneksi;

    try {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            throw new Exception('User ID is required');
        }

        $data = json_decode(file_get_contents('php://input'), true);

        // Check if user exists
        $checkUser = mysqli_prepare($koneksi, "SELECT id FROM users WHERE id = ?");
        mysqli_stmt_bind_param($checkUser, 's', $id);
        mysqli_stmt_execute($checkUser);
        $userResult = mysqli_stmt_get_result($checkUser);
        if (mysqli_num_rows($userResult) === 0) {
            throw new Exception("User not found");
        }
        mysqli_stmt_close($checkUser);

        // Build update query dynamically
        $updateFields = [];
        $params = [];
        $types = '';

        if (isset($data['name'])) {
            $updateFields[] = "name = ?";
            $params[] = $data['name'];
            $types .= 's';
        }

        if (isset($data['email'])) {
            // Validate email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format");
            }
            // Check if email exists for other users
            $checkEmail = mysqli_prepare($koneksi, "SELECT id FROM users WHERE email = ? AND id != ?");
            mysqli_stmt_bind_param($checkEmail, 'ss', $data['email'], $id);
            mysqli_stmt_execute($checkEmail);
            $emailResult = mysqli_stmt_get_result($checkEmail);
            if (mysqli_num_rows($emailResult) > 0) {
                throw new Exception("Email already exists");
            }
            mysqli_stmt_close($checkEmail);

            $updateFields[] = "email = ?";
            $params[] = $data['email'];
            $types .= 's';
        }

        if (isset($data['username'])) {
            // Check if username exists for other users
            $checkUsername = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ? AND id != ?");
            mysqli_stmt_bind_param($checkUsername, 'ss', $data['username'], $id);
            mysqli_stmt_execute($checkUsername);
            $usernameResult = mysqli_stmt_get_result($checkUsername);
            if (mysqli_num_rows($usernameResult) > 0) {
                throw new Exception("Username already exists");
            }
            mysqli_stmt_close($checkUsername);

            $updateFields[] = "username = ?";
            $params[] = $data['username'];
            $types .= 's';
        }

        if (isset($data['password']) && !empty($data['password'])) {
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
            $updateFields[] = "password_hash = ?";
            $params[] = $passwordHash;
            $types .= 's';
        }

        if (isset($data['role_id'])) {
            $updateFields[] = "role_id = ?";
            $params[] = $data['role_id'];
            $types .= 's';
        }

        if (isset($data['is_active'])) {
            $updateFields[] = "is_active = ?";
            $params[] = (bool) $data['is_active'];
            $types .= 'i';
        }

        if (empty($updateFields)) {
            throw new Exception('No fields to update');
        }

        $updateFields[] = "updated_at = NOW()";

        $query = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $params[] = $id;
        $types .= 's';

        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$params);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);

            // Get updated user
            $getUser = mysqli_prepare($koneksi, "SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.id = ?");
            mysqli_stmt_bind_param($getUser, 's', $id);
            mysqli_stmt_execute($getUser);
            $result = mysqli_stmt_get_result($getUser);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($getUser);

            // Remove password hash from response
            unset($user['password_hash']);
            $user['is_active'] = (bool) $user['is_active'];

            echo json_encode([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ]);
        } else {
            throw new Exception('Failed to update user: ' . mysqli_error($koneksi));
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

function deleteUser()
{
    global $koneksi;

    try {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            throw new Exception('User ID is required');
        }

        // Check if user exists
        $checkUser = mysqli_prepare($koneksi, "SELECT id FROM users WHERE id = ?");
        mysqli_stmt_bind_param($checkUser, 's', $id);
        mysqli_stmt_execute($checkUser);
        $userResult = mysqli_stmt_get_result($checkUser);
        if (mysqli_num_rows($userResult) === 0) {
            throw new Exception("User not found");
        }
        mysqli_stmt_close($checkUser);

        // Soft delete by setting is_active to false
        $query = "UPDATE users SET is_active = FALSE, updated_at = NOW() WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 's', $id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);

            echo json_encode([
                'success' => true,
                'message' => 'User deactivated successfully'
            ]);
        } else {
            throw new Exception('Failed to deactivate user: ' . mysqli_error($koneksi));
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

function restoreUser()
{
    global $koneksi;

    try {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            throw new Exception('User ID is required');
        }

        // Check if user exists
        $checkUser = mysqli_prepare($koneksi, "SELECT id FROM users WHERE id = ?");
        mysqli_stmt_bind_param($checkUser, 's', $id);
        mysqli_stmt_execute($checkUser);
        $userResult = mysqli_stmt_get_result($checkUser);
        if (mysqli_num_rows($userResult) === 0) {
            throw new Exception("User not found");
        }
        mysqli_stmt_close($checkUser);

        // Restore by setting is_active to true
        $query = "UPDATE users SET is_active = TRUE, updated_at = NOW() WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 's', $id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);

            echo json_encode([
                'success' => true,
                'message' => 'User activated successfully'
            ]);
        } else {
            throw new Exception('Failed to activate user: ' . mysqli_error($koneksi));
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

function toggleUserStatus()
{
    global $koneksi;

    try {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            throw new Exception('User ID is required');
        }

        // Check if user exists and get current status
        $checkUser = mysqli_prepare($koneksi, "SELECT is_active FROM users WHERE id = ?");
        mysqli_stmt_bind_param($checkUser, 's', $id);
        mysqli_stmt_execute($checkUser);
        $userResult = mysqli_stmt_get_result($checkUser);
        if (mysqli_num_rows($userResult) === 0) {
            throw new Exception("User not found");
        }
        $currentStatus = mysqli_fetch_assoc($userResult)['is_active'];
        mysqli_stmt_close($checkUser);

        // Toggle status
        $newStatus = !$currentStatus;
        $query = "UPDATE users SET is_active = ?, updated_at = NOW() WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'is', $newStatus, $id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);

            $statusText = $newStatus ? 'activated' : 'deactivated';
            echo json_encode([
                'success' => true,
                'message' => "User $statusText successfully",
                'is_active' => (bool) $newStatus
            ]);
        } else {
            throw new Exception('Failed to toggle user status: ' . mysqli_error($koneksi));
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

function permanentlyDeleteUser()
{
    global $koneksi;

    try {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            throw new Exception('User ID is required');
        }

        // Check if user exists
        $checkUser = mysqli_prepare($koneksi, "SELECT id, is_active FROM users WHERE id = ?");
        mysqli_stmt_bind_param($checkUser, 's', $id);
        mysqli_stmt_execute($checkUser);
        $userResult = mysqli_stmt_get_result($checkUser);
        if (mysqli_num_rows($userResult) === 0) {
            throw new Exception("User not found");
        }

        $user = mysqli_fetch_assoc($userResult);
        mysqli_stmt_close($checkUser);

        // Only allow permanent deletion of inactive users
        if ($user['is_active']) {
            throw new Exception("Cannot permanently delete an active user. User must be inactive first.");
        }

        // Start transaction for data consistency
        mysqli_autocommit($koneksi, FALSE);

        // Delete related records in dependent tables
        $relatedTables = [
            ['table' => 'user_sessions', 'column' => 'user_id'],
            ['table' => 'password_resets', 'column' => 'user_id']
        ];

        foreach ($relatedTables as $relatedTable) {
            $query = "DELETE FROM {$relatedTable['table']} WHERE {$relatedTable['column']} = ?";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, 's', $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // Delete related login attempts (these might use username instead of ID)
        $loginAttemptsQuery = "DELETE FROM login_attempts WHERE username IN (
            SELECT username FROM users WHERE id = ?
        )";
        $loginStmt = mysqli_prepare($koneksi, $loginAttemptsQuery);
        mysqli_stmt_bind_param($loginStmt, 's', $id);
        mysqli_stmt_execute($loginStmt);
        mysqli_stmt_close($loginStmt);

        // Permanently delete the user
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 's', $id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_commit($koneksi);
            mysqli_autocommit($koneksi, TRUE);

            echo json_encode([
                'success' => true,
                'message' => 'User permanently deleted successfully'
            ]);
        } else {
            mysqli_rollback($koneksi);
            mysqli_autocommit($koneksi, TRUE);
            throw new Exception('Failed to permanently delete user: ' . mysqli_error($koneksi));
        }
    } catch (Exception $e) {
        if (mysqli_connect_errno() === 0) { // Only rollback if connection is still valid
            mysqli_rollback($koneksi);
            mysqli_autocommit($koneksi, TRUE);
        }
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

function permanentlyDeleteInactiveUsers()
{
    global $koneksi;

    try {
        // Start transaction for data consistency
        mysqli_autocommit($koneksi, FALSE);

        // Get inactive user IDs first to process related records properly
        $getInactiveQuery = "SELECT id, username FROM users WHERE is_active = FALSE";
        $result = mysqli_query($koneksi, $getInactiveQuery);
        $inactiveUsers = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $inactiveUsers[] = $row;
        }

        $inactiveCount = count($inactiveUsers);

        if ($inactiveCount === 0) {
            echo json_encode([
                'success' => true,
                'message' => 'No inactive users to delete',
                'deleted_count' => 0
            ]);
            mysqli_autocommit($koneksi, TRUE);
            return;
        }

        // Delete related records in dependent tables for inactive users
        $relatedTables = [
            ['table' => 'user_sessions', 'column' => 'user_id'],
            ['table' => 'password_resets', 'column' => 'user_id']
        ];

        foreach ($relatedTables as $relatedTable) {
            $userIds = [];
            $userParams = [];
            $types = '';
            foreach ($inactiveUsers as $user) {
                $userParams[] = $user['id'];
                $types .= 's';
            }

            if (!empty($userParams)) {
                $placeholders = str_repeat('?,', count($userParams) - 1) . '?';
                $query = "DELETE FROM {$relatedTable['table']} WHERE {$relatedTable['column']} IN ($placeholders)";
                $stmt = mysqli_prepare($koneksi, $query);
                mysqli_stmt_bind_param($stmt, $types, ...$userParams);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }

        // Delete related login attempts
        $loginUsernames = [];
        $usernameParams = [];
        $types = '';
        foreach ($inactiveUsers as $user) {
            $usernameParams[] = $user['username'];
            $types .= 's';
        }

        if (!empty($usernameParams)) {
            $placeholders = str_repeat('?,', count($usernameParams) - 1) . '?';
            $loginQuery = "DELETE FROM login_attempts WHERE username IN ($placeholders)";
            $loginStmt = mysqli_prepare($koneksi, $loginQuery);
            mysqli_stmt_bind_param($loginStmt, $types, ...$usernameParams);
            mysqli_stmt_execute($loginStmt);
            mysqli_stmt_close($loginStmt);
        }

        // Permanently delete all inactive users
        $deleteQuery = "DELETE FROM users WHERE is_active = FALSE";
        if (mysqli_query($koneksi, $deleteQuery)) {
            mysqli_commit($koneksi);
            mysqli_autocommit($koneksi, TRUE);

            echo json_encode([
                'success' => true,
                'message' => "Successfully permanently deleted {$inactiveCount} inactive user(s)",
                'deleted_count' => $inactiveCount
            ]);
        } else {
            mysqli_rollback($koneksi);
            mysqli_autocommit($koneksi, TRUE);
            throw new Exception('Failed to permanently delete inactive users: ' . mysqli_error($koneksi));
        }
    } catch (Exception $e) {
        if (mysqli_connect_errno() === 0) { // Only rollback if connection is still valid
            mysqli_rollback($koneksi);
            mysqli_autocommit($koneksi, TRUE);
        }
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}