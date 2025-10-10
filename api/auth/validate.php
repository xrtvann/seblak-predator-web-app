<?php
/**
 * API Authentication - Token Validation Endpoint
 * Validates JWT tokens and returns user information
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Allow both GET and POST requests
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
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

    // Get token from Authorization header or request body
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (json_last_error() === JSON_ERROR_NONE && isset($data['token'])) {
            $token = $data['token'];
        }
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

    // Check if token is blacklisted
    if (JWTHelper::isTokenBlacklisted($token)) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Token has been revoked'
        ]);
        exit;
    }

    // Validate token
    $validation = JWTHelper::validateToken($token);

    if (!$validation['valid']) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => $validation['error'],
            'code' => 'INVALID_TOKEN'
        ]);
        exit;
    }

    // Return successful validation
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Token is valid',
        'data' => [
            'user' => $validation['user_data'],
            'token_type' => $validation['token_type'],
            'expires_at' => $validation['expires_at'],
            'issued_at' => $validation['issued_at'],
            'jwt_id' => $validation['jwt_id']
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