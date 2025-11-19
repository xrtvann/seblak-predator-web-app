<?php
/**
 * API Authentication - Register Endpoint
 * Handles user registration
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

require_once __DIR__ . '/../../config/koneksi.php';

try {
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON input'
        ]);
        exit;
    }

    // Validate required fields
    if (empty($data['name']) || empty($data['username']) || empty($data['email']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'All fields are required (name, username, email, password)'
        ]);
        exit;
    }

    // Sanitize input
    $name = htmlspecialchars(trim($data['name']));
    $username = htmlspecialchars(trim($data['username']));
    $email = htmlspecialchars(trim($data['email']));
    $password = $data['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email format'
        ]);
        exit;
    }

    // Validate password length (minimum 6 characters)
    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Password must be at least 6 characters long'
        ]);
        exit;
    }

    // Check if username already exists
    $stmt = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Username already exists. Please choose a different username.'
        ]);
        exit;
    }
    mysqli_stmt_close($stmt);

    // Check if email already exists
    $stmt = mysqli_prepare($koneksi, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Email already exists. Please use a different email address.'
        ]);
        exit;
    }
    mysqli_stmt_close($stmt);

    // Get or create 'Customer' role (default role for new users)
    $role_id = null;
    $result = mysqli_query($koneksi, "SELECT id FROM roles WHERE name = 'Customer' LIMIT 1");

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $role_id = $row['id'];
        mysqli_free_result($result);
    } else {
        // Use default customer role ID
        $role_id = 'role_customer';
    }

    // Generate unique user ID
    $user_id = 'user_' . uniqid();
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user
    $stmt = mysqli_prepare($koneksi, "
        INSERT INTO users (id, name, email, username, password_hash, role_id, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    mysqli_stmt_bind_param($stmt, "ssssss", $user_id, $name, $email, $username, $hashed_password, $role_id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);

        // Log API access
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'API Client';

        $stmt = mysqli_prepare($koneksi, "
            INSERT INTO api_access_log (user_id, endpoint, method, success, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        if ($stmt) {
            $endpoint = '/api/auth/register';
            $method = 'POST';
            $success = 1;
            mysqli_stmt_bind_param($stmt, "ssisss", $user_id, $endpoint, $method, $success, $ip_address, $user_agent);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // Return successful response
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully! You can now log in.',
            'user' => [
                'id' => $user_id,
                'username' => $username,
                'name' => $name,
                'email' => $email,
                'role_id' => $role_id
            ]
        ]);
    } else {
        mysqli_stmt_close($stmt);
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Registration failed. Please try again.'
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ]);
}
?>