<?php
/**
 * API Authentication - Change Password Endpoint
 * Allows user to change their password with old password, new password, and confirm new password.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle preflight request (CORS)
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
    if (empty($data['user_id']) || empty($data['old_password']) || empty($data['new_password']) || empty($data['confirm_password'])) {
        http_response_code(400);
        echo json_encode([ 
            'success' => false, 
            'message' => 'All fields are required'
        ]);
        exit;
    }

    if ($data['new_password'] !== $data['confirm_password']) {
        http_response_code(400);
        echo json_encode([ 
            'success' => false, 
            'message' => 'Password confirmation does not match'
        ]);
        exit;
    }

    if (strlen($data['new_password']) < 6) {
        http_response_code(400);
        echo json_encode([ 
            'success' => false, 
            'message' => 'Password must be at least 6 characters long'
        ]);
        exit;
    }

    // Verify the old password by checking it in the database
    $query = "SELECT password_hash FROM users WHERE id = ? AND is_active = 1";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 's', $data['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result || mysqli_num_rows($result) === 0) {
        http_response_code(400);
        echo json_encode([ 
            'success' => false, 
            'message' => 'Invalid user or user not active'
        ]);
        exit;
    }

    $user = mysqli_fetch_assoc($result);
    $stored_password = $user['password_hash'];

    // Check if the old password matches the stored password
    if (!password_verify($data['old_password'], $stored_password)) {
        http_response_code(400);
        echo json_encode([ 
            'success' => false, 
            'message' => 'Old password is incorrect'
        ]);
        exit;
    }

    // Hash the new password and update it in the database
    $new_password_hash = password_hash($data['new_password'], PASSWORD_DEFAULT);

    // Update the user's password in the database
    $update_query = "UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $update_query);
    mysqli_stmt_bind_param($stmt, 'ss', $new_password_hash, $data['user_id']);
    $update_result = mysqli_stmt_execute($stmt);

    if (!$update_result) {
        http_response_code(500);
        echo json_encode([ 
            'success' => false, 
            'message' => 'Failed to update password. Please try again.'
        ]);
        exit;
    }

    // Log the API access for auditing
    logAPIAccess($koneksi, $data['user_id'], '/api/auth/change-password', 'POST', true);

    // Return successful response
    http_response_code(200);
    echo json_encode([ 
        'success' => true, 
        'message' => 'Password has been successfully changed.'
    ]);

} catch (Exception $e) {
    // Handle any unexpected errors
    http_response_code(500);
    echo json_encode([ 
        'success' => false, 
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ]);
}

/**
 * Log API access for audit
 */
function logAPIAccess($koneksi, $user_id, $endpoint, $method, $success)
{
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'API Client';

    $stmt = mysqli_prepare($koneksi, "
        INSERT INTO api_access_log (user_id, endpoint, method, success, ip_address, user_agent) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    if ($stmt) {
        $success_int = $success ? 1 : 0;
        mysqli_stmt_bind_param($stmt, "ssisss", $user_id, $endpoint, $method, $success_int, $ip_address, $user_agent);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}
?>
