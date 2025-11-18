<?php
/**
 * API Authentication - Login Endpoint
 * Handles user authentication and JWT token generation
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
require_once __DIR__ . '/../../services/WebAuthService.php';
require_once __DIR__ . '/JWTHelper.php';

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
    if (empty($data['username']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Username and password are required'
        ]);
        exit;
    }

    // Initialize authentication service
    $auth = new WebAuthService($koneksi);
    $jwt = new JWTHelper();

    // Get client information
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'API Client';

    // Attempt login
    $login_result = $auth->login(
        $data['username'],
        $data['password'],
        false // API login doesn't use remember me
    );

    if (!$login_result['success']) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => $login_result['message']
        ]);
        exit;
    }

    // Generate JWT tokens
    $user = $login_result['user'];
    $tokens = $jwt->generateTokenPair([
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'role_id' => $user['role_id'] ?? null,
        'name' => $user['name']
    ]);

    if (!$tokens) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to generate authentication tokens'
        ]);
        exit;
    }

    // Log API access
    $stmt = mysqli_prepare($koneksi, "
        INSERT INTO api_access_log (user_id, endpoint, method, success, ip_address, user_agent) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    if ($stmt) {
        $endpoint = '/api/auth/login';
        $method = 'POST';
        $success = 1;
        mysqli_stmt_bind_param($stmt, "ssisss", $user['id'], $endpoint, $method, $success, $ip_address, $user_agent);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Return successful response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'access_token' => $tokens['access_token'],
        'refresh_token' => $tokens['refresh_token'],
        'token_type' => $tokens['token_type'],
        'expires_in' => $tokens['expires_in'],
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role_name']
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ]);
}
?>