<?php
/**
 * Mobile API: Get User Profile
 * Protected API Endpoint for Android App
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
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
    // Authenticate user with JWT
    $user = JWTMiddleware::authenticate(); // Tidak memerlukan role khusus

    if (!$user) {
        exit; // middleware sudah mengirim response error
    }

    // Log API access
    JWTMiddleware::logApiAccess('/api/auth/profile_mobile', 'GET', true);

    // Koneksi database
    require_once __DIR__ . '/../../config/koneksi.php';

    $stmt = mysqli_prepare($koneksi, "
        SELECT u.id, u.username, u.name, u.email, u.phone_number, r.name as role_name
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
            // Kembalikan data yang relevan untuk mobile
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'User profile retrieved successfully',
                'data' => [
                    'id' => $user_details['id'],
                    'username' => $user_details['username'],
                    'name' => $user_details['name'],
                    'email' => $user_details['email'],
                    'phone_number' => $user_details['phone_number'],
                    'role' => $user_details['role_name']
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
    JWTMiddleware::logApiAccess('/api/auth/profile_mobile', 'GET', false);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ]);
}
?>
