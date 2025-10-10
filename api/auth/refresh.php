<?php
/**
 * API Authentication - Token Refresh Endpoint
 * Refreshes access tokens using refresh tokens
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
    if (empty($data['refresh_token'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Refresh token is required'
        ]);
        exit;
    }

    $refresh_token = $data['refresh_token'];

    // Check if token is blacklisted
    if (JWTHelper::isTokenBlacklisted($refresh_token)) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Refresh token has been revoked'
        ]);
        exit;
    }

    // Validate refresh token
    if (!JWTHelper::isRefreshToken($refresh_token)) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid refresh token'
        ]);
        exit;
    }

    // Get user data from refresh token
    $user_data = JWTHelper::getUserFromToken($refresh_token);
    if (!$user_data) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid refresh token payload'
        ]);
        exit;
    }

    // Generate new access token
    $new_tokens = JWTHelper::refreshAccessToken($refresh_token, $user_data['user_id']);

    if (!$new_tokens) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to refresh token'
        ]);
        exit;
    }

    // Return new access token
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Token refreshed successfully',
        'access_token' => $new_tokens['access_token'],
        'token_type' => $new_tokens['token_type'],
        'expires_in' => $new_tokens['expires_in']
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