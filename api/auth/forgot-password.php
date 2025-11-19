<?php
/**
 * API Authentication - Forgot Password Endpoint
 * Handles password reset process: send OTP, verify OTP, and reset password
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
require_once __DIR__ . '/../../config/config.php';

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

    // Get action
    $action = $data['action'] ?? '';

    // Route to appropriate handler
    switch ($action) {
        case 'send_otp':
            handleSendOTP($koneksi, $data);
            break;

        case 'verify_otp':
            handleVerifyOTP($koneksi, $data);
            break;

        case 'reset_password':
            handleResetPassword($koneksi, $data);
            break;

        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action. Use: send_otp, verify_otp, or reset_password'
            ]);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ]);
}

/**
 * Handle sending OTP to email
 */
function handleSendOTP($koneksi, $data)
{
    $user_ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    // Rate limiting check
    if (checkRateLimit($user_ip, 'forgot_password')) {
        http_response_code(429);
        echo json_encode([
            'success' => false,
            'message' => 'Too many attempts. Please try again in ' . ceil(RATE_LIMIT_PERIOD / 60) . ' minutes.',
            'retry_after' => RATE_LIMIT_PERIOD
        ]);
        exit;
    }

    $email = isset($data['email']) ? sanitizeInput($data['email']) : '';

    if (empty($email) || !isValidEmail($email)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email address'
        ]);
        exit;
    }

    // Verify if email is registered in the system
    $query = "SELECT id, name, email FROM users WHERE email = ? AND is_active = 1";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result || mysqli_num_rows($result) === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Email is not registered in the system. Please check your email or create a new account.'
        ]);
        exit;
    }

    $user = mysqli_fetch_assoc($result);
    $user_id = $user['id'];
    $user_name = $user['name'];

    // Generate OTP and hash it for secure storage
    $otp = generateOTP();
    $otp_hash = hashOTP($otp);
    $expires_at = date('Y-m-d H:i:s', time() + 600); // 10 minutes

    // Create unique ID for password reset record
    $reset_id = uniqid('pwd_reset_', true);

    // Delete any existing password reset requests for this user
    $delete_query = "DELETE FROM password_resets WHERE user_id = ?";
    $stmt = mysqli_prepare($koneksi, $delete_query);
    mysqli_stmt_bind_param($stmt, 's', $user_id);
    mysqli_stmt_execute($stmt);

    // Insert new reset record (storing hashed OTP for security)
    $insert_query = "INSERT INTO password_resets (id, user_id, otp_code, expires_at) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $insert_query);
    mysqli_stmt_bind_param($stmt, 'ssss', $reset_id, $user_id, $otp_hash, $expires_at);
    $insert_result = mysqli_stmt_execute($stmt);

    if (!$insert_result) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred. Please try again.'
        ]);
        exit;
    }

    // Send OTP via email
    require_once __DIR__ . '/../../services/EmailService.php';
    $email_service = new EmailService();

    try {
        $sent = $email_service->sendPasswordResetOTP($email, $user_name, $otp);

        if ($sent) {
            // Log API access
            logAPIAccess($koneksi, $user_id, '/api/auth/forgot-password (send_otp)', 'POST', true);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'OTP code has been sent to your email (' . $email . '). Please check your email and enter the OTP code.',
                'expires_in' => 600 // 10 minutes in seconds
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to send email. Please try again.'
            ]);
        }
    } catch (Exception $e) {
        error_log("Email service error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred while sending email. Please try again.'
        ]);
    }
}

/**
 * Handle OTP verification
 */
function handleVerifyOTP($koneksi, $data)
{
    $email = isset($data['email']) ? sanitizeInput($data['email']) : '';
    $otp = isset($data['otp']) ? sanitizeInput($data['otp']) : '';

    if (empty($email) || empty($otp)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Email and OTP code are required'
        ]);
        exit;
    }

    if (!isValidEmail($email)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email address'
        ]);
        exit;
    }

    if (strlen($otp) !== 6 || !ctype_digit($otp)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'OTP code must be 6 digits'
        ]);
        exit;
    }

    // Get user and check OTP
    $query = "SELECT u.id, pr.id as reset_id, pr.otp_code, pr.expires_at, pr.used_at 
              FROM users u 
              JOIN password_resets pr ON u.id = pr.user_id 
              WHERE u.email = ? AND u.is_active = 1 
              ORDER BY pr.created_at DESC 
              LIMIT 1";

    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result || mysqli_num_rows($result) === 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid or expired OTP code'
        ]);
        exit;
    }

    $row = mysqli_fetch_assoc($result);

    // Check if OTP has already been used
    if (!empty($row['used_at'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'OTP code has already been used'
        ]);
        exit;
    }

    // Check if OTP has expired
    if (strtotime($row['expires_at']) < time()) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'OTP code has expired. Please request a new code.'
        ]);
        exit;
    }

    // Verify OTP
    if (!verifyOTP($otp, $row['otp_code'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid OTP code'
        ]);
        exit;
    }

    // Mark OTP as used
    $update_query = "UPDATE password_resets SET used_at = NOW() WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $update_query);
    mysqli_stmt_bind_param($stmt, 's', $row['reset_id']);
    mysqli_stmt_execute($stmt);

    // Log API access
    logAPIAccess($koneksi, $row['id'], '/api/auth/forgot-password (verify_otp)', 'POST', true);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'OTP code verified successfully. You can now reset your password.'
    ]);
}

/**
 * Handle password reset
 */
function handleResetPassword($koneksi, $data)
{
    $email = isset($data['email']) ? sanitizeInput($data['email']) : '';
    $otp = isset($data['otp']) ? sanitizeInput($data['otp']) : '';
    $new_password = $data['new_password'] ?? '';
    $confirm_password = $data['confirm_password'] ?? '';

    if (empty($email) || empty($otp) || empty($new_password) || empty($confirm_password)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'All fields are required'
        ]);
        exit;
    }

    if ($new_password !== $confirm_password) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Password confirmation does not match'
        ]);
        exit;
    }

    if (strlen($new_password) < 6) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Password must be at least 6 characters long'
        ]);
        exit;
    }

    // Verify OTP one more time and check if it's been used for password reset
    $query = "SELECT u.id, pr.id as reset_id, pr.otp_code, pr.expires_at, pr.used_at 
              FROM users u 
              JOIN password_resets pr ON u.id = pr.user_id 
              WHERE u.email = ? AND u.is_active = 1 
              ORDER BY pr.created_at DESC 
              LIMIT 1";

    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result || mysqli_num_rows($result) === 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid password reset session'
        ]);
        exit;
    }

    $row = mysqli_fetch_assoc($result);

    // Check if OTP has expired
    if (strtotime($row['expires_at']) < time()) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Password reset session has expired'
        ]);
        exit;
    }

    // Check if OTP has not been verified (used_at should be set by verify step)
    if (empty($row['used_at'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Please verify OTP code first'
        ]);
        exit;
    }

    // Verify OTP one more time
    if (!verifyOTP($otp, $row['otp_code'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid OTP code'
        ]);
        exit;
    }

    // Update user password
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $update_query = "UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $update_query);
    mysqli_stmt_bind_param($stmt, 'ss', $password_hash, $row['id']);
    $update_result = mysqli_stmt_execute($stmt);

    if (!$update_result) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update password. Please try again.'
        ]);
        exit;
    }

    // Delete the password reset record after successful reset
    $delete_query = "DELETE FROM password_resets WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $delete_query);
    mysqli_stmt_bind_param($stmt, 's', $row['reset_id']);
    mysqli_stmt_execute($stmt);

    // Send success email
    try {
        require_once __DIR__ . '/../../services/EmailService.php';
        $email_service = new EmailService();

        // Get user name for success email
        $name_query = "SELECT name FROM users WHERE email = ? AND is_active = 1";
        $stmt = mysqli_prepare($koneksi, $name_query);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $name_result = mysqli_stmt_get_result($stmt);
        $user_name = 'User';
        if ($name_result && mysqli_num_rows($name_result) > 0) {
            $user_data = mysqli_fetch_assoc($name_result);
            $user_name = $user_data['name'];
        }

        $email_service->sendPasswordResetSuccess($email, $user_name);
    } catch (Exception $e) {
        error_log("Failed to send success email: " . $e->getMessage());
    }

    // Log API access
    logAPIAccess($koneksi, $row['id'], '/api/auth/forgot-password (reset_password)', 'POST', true);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Password has been successfully changed. You can now log in with your new password.'
    ]);
}

/**
 * Log API access
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