<?php
/**
 * API Authentication - Logout Endpoint
 * Blacklists JWT tokens to prevent further use
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

require_once __DIR__ . '/JWTHelper.php';

try {
    $token = null;

    // Get JSON input for explicit token
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() === JSON_ERROR_NONE && isset($data['token'])) {
        $token = $data['token'];
    }

    // If no token in body, try Authorization header
    if (!$token) {
        $token = JWTHelper::extractTokenFromHeader();
    }

    if (!$token) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'No token provided'
        ]);
        exit;
    }

    // Validate token first
    $validation = JWTHelper::validateToken($token);

    if (!$validation['valid']) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid token'
        ]);
        exit;
    }

    // Get user data from token
    $user_data = $validation['user_data'];
    if (!$user_data || !isset($user_data['user_id'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid token payload'
        ]);
        exit;
    }

    // Blacklist the token
    $blacklisted = JWTHelper::blacklistToken($token, $user_data['user_id'], 'logout');

    if (!$blacklisted) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to logout - could not blacklist token'
        ]);
        exit;
    }

    // Also blacklist refresh token if provided
    if (isset($data['refresh_token']) && !empty($data['refresh_token'])) {
        JWTHelper::blacklistToken($data['refresh_token'], $user_data['user_id'], 'logout');
    }

    // Return successful logout
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Logged out successfully',
        'data' => [
            'user_id' => $user_data['user_id'],
            'logged_out_at' => date('Y-m-d H:i:s')
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