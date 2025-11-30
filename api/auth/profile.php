<?php
/**
 * Protected API Endpoint Example
 * Demonstrates how to use JWT middleware for protected routes
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

require_once __DIR__ . '/middleware.php';

try {
    // Authenticate request - require admin or Cashier roles
    $user = JWTMiddleware::authenticate(['role_admin', 'role_cashier']);

    if (!$user) {
        // Authentication failed - middleware already sent error response
        exit;
    }

    // Log API access
    JWTMiddleware::logApiAccess('/api/auth/profile', 'GET', true);

    // Get additional user information from database
    require_once __DIR__ . '/../../config/koneksi.php';

    $stmt = mysqli_prepare($koneksi, "
        SELECT u.*, r.name as role_name 
        FROM users u 
        LEFT JOIN roles r ON u.role_id = r.id 
        WHERE u.id = ? AND u.is_active = TRUE
    ");

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $user['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user_details = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($user_details) {
            // Remove sensitive information
            unset($user_details['password_hash']);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'User profile retrieved successfully',
                'data' => [
                    'user' => $user_details,
                    'token_info' => [
                        'expires_at' => $GLOBALS['jwt_validation']['expires_at'],
                        'issued_at' => $GLOBALS['jwt_validation']['issued_at'],
                        'jwt_id' => $GLOBALS['jwt_validation']['jwt_id']
                    ],
                    'permissions' => [
                        'is_admin' => JWTMiddleware::hasRole('role_admin'),
                        'is_cashier' => JWTMiddleware::hasRole('role_cashier'),
                        'is_customer' => JWTMiddleware::hasRole('role_customer')
                    ]
                ]
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'User not found or inactive'
            ]);
        }
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database error'
        ]);
    }

} catch (Exception $e) {
    // Log API access as failed
    JWTMiddleware::logApiAccess('/api/auth/profile', 'GET', false);

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ]);
}
?>