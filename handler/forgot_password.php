<?php
/**
 * Forgot Password Handler
 * Handles OTP generation, verification, and password reset
 * With CSRF protection, rate limiting, and HTTPS enforcement
 */

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/EmailService.php';

// Set JSON header
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSONResponse(false, 'Invalid request method');
}

// Get action from POST data
$action = isset($_POST['action']) ? sanitizeInput($_POST['action']) : '';

// Verify CSRF token for all actions
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    logSecurityEvent('CSRF_TOKEN_INVALID', [
        'action' => $action,
        'ip' => $_SERVER['REMOTE_ADDR']
    ]);
    sendJSONResponse(false, 'Invalid security token. Please refresh the page and try again.');
}

// Get user IP for rate limiting
$user_ip = $_SERVER['REMOTE_ADDR'];

// Route to appropriate handler
switch ($action) {
    case 'send_otp':
        handleSendOTP($koneksi, $user_ip);
        break;

    case 'verify_otp':
        handleVerifyOTP($koneksi, $user_ip);
        break;

    case 'reset_password':
        handleResetPassword($koneksi, $user_ip);
        break;

    case 'resend_otp':
        handleResendOTP($koneksi, $user_ip);
        break;

    default:
        sendJSONResponse(false, 'Invalid action');
}

/**
 * Handle Send OTP Request
 */
function handleSendOTP($koneksi, $user_ip)
{
    // Check rate limiting
    if (checkRateLimit($user_ip, 'send_otp')) {
        $remaining = getRateLimitRemaining($user_ip, 'send_otp');
        logSecurityEvent('RATE_LIMIT_EXCEEDED', [
            'action' => 'send_otp',
            'ip' => $user_ip,
            'remaining_time' => $remaining
        ]);
        sendJSONResponse(false, "Terlalu banyak percobaan. Silakan coba lagi dalam " . ceil($remaining / 60) . " menit.", [
            'remaining_seconds' => $remaining
        ]);
    }

    // Validate email
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';

    if (empty($email) || !isValidEmail($email)) {
        sendJSONResponse(false, 'Email tidak valid');
    }

    // Check if user exists
    $query = "SELECT id_user, name, email FROM users WHERE email = ? LIMIT 1";
    $result = executePreparedStatement($koneksi, $query, 's', [$email]);

    if (!$result || mysqli_num_rows($result) === 0) {
        // Don't reveal if email exists or not (security best practice)
        logSecurityEvent('PASSWORD_RESET_EMAIL_NOT_FOUND', [
            'email' => $email,
            'ip' => $user_ip
        ]);

        // Still show success message to prevent email enumeration
        sendJSONResponse(true, 'Jika email terdaftar, kode OTP akan dikirim ke email Anda.');
    }

    $user = mysqli_fetch_assoc($result);
    $user_id = $user['id_user'];
    $user_name = $user['name'];

    // Generate OTP
    $otp = generateOTP();
    $otp_hash = hashOTP($otp);

    // Calculate expiry time
    $expires_at = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRE_MINUTES . ' minutes'));

    // Get user agent
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';

    // Delete old unused tokens for this user
    $delete_query = "DELETE FROM password_reset_tokens WHERE user_id = ? AND used = 0";
    executeUpdate($koneksi, $delete_query, 'i', [$user_id]);

    // Insert new token
    $insert_query = "INSERT INTO password_reset_tokens (user_id, email, otp_hash, ip_address, user_agent, expires_at) VALUES (?, ?, ?, ?, ?, ?)";
    $insert_result = executeUpdate($koneksi, $insert_query, 'isssss', [
        $user_id,
        $email,
        $otp_hash,
        $user_ip,
        $user_agent,
        $expires_at
    ]);

    if (!$insert_result) {
        logSecurityEvent('OTP_INSERT_FAILED', [
            'email' => $email,
            'user_id' => $user_id
        ]);
        sendJSONResponse(false, 'Terjadi kesalahan. Silakan coba lagi.');
    }

    // Send email
    try {
        $emailService = new EmailService();
        $email_sent = $emailService->sendPasswordResetOTP($email, $user_name, $otp);

        if ($email_sent) {
            logSecurityEvent('OTP_SENT_SUCCESS', [
                'email' => $email,
                'user_id' => $user_id,
                'expires_at' => $expires_at
            ]);

            // Store email in session for verification
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_step'] = 'otp_verification';

            sendJSONResponse(true, 'Kode OTP telah dikirim ke email Anda.', [
                'email' => $email,
                'expires_in_minutes' => OTP_EXPIRE_MINUTES
            ]);
        } else {
            logSecurityEvent('OTP_EMAIL_SEND_FAILED', [
                'email' => $email,
                'user_id' => $user_id
            ]);
            sendJSONResponse(false, 'Gagal mengirim email. Silakan coba lagi.');
        }
    } catch (Exception $e) {
        logSecurityEvent('OTP_EMAIL_EXCEPTION', [
            'email' => $email,
            'error' => $e->getMessage()
        ]);
        sendJSONResponse(false, 'Terjadi kesalahan saat mengirim email.');
    }
}

/**
 * Handle Verify OTP Request
 */
function handleVerifyOTP($koneksi, $user_ip)
{
    // Check rate limiting
    if (checkRateLimit($user_ip, 'verify_otp')) {
        $remaining = getRateLimitRemaining($user_ip, 'verify_otp');
        sendJSONResponse(false, "Terlalu banyak percobaan. Silakan coba lagi dalam " . ceil($remaining / 60) . " menit.");
    }

    // Check if email is in session
    if (!isset($_SESSION['reset_email']) || $_SESSION['reset_step'] !== 'otp_verification') {
        sendJSONResponse(false, 'Sesi tidak valid. Silakan mulai dari awal.');
    }

    $email = $_SESSION['reset_email'];

    // Validate OTP
    $otp = isset($_POST['otp']) ? sanitizeInput($_POST['otp']) : '';

    if (empty($otp) || strlen($otp) !== OTP_LENGTH || !ctype_digit($otp)) {
        sendJSONResponse(false, 'Kode OTP tidak valid');
    }

    // Get the latest unused token for this email
    $query = "SELECT id, user_id, otp_hash, expires_at FROM password_reset_tokens 
              WHERE email = ? AND used = 0 AND expires_at > NOW() 
              ORDER BY created_at DESC LIMIT 1";
    $result = executePreparedStatement($koneksi, $query, 's', [$email]);

    if (!$result || mysqli_num_rows($result) === 0) {
        logSecurityEvent('OTP_VERIFICATION_NO_TOKEN', [
            'email' => $email,
            'ip' => $user_ip
        ]);
        sendJSONResponse(false, 'Kode OTP tidak valid atau sudah kadaluarsa.');
    }

    $token = mysqli_fetch_assoc($result);

    // Verify OTP
    if (!verifyOTP($otp, $token['otp_hash'])) {
        logSecurityEvent('OTP_VERIFICATION_FAILED', [
            'email' => $email,
            'ip' => $user_ip,
            'token_id' => $token['id']
        ]);
        sendJSONResponse(false, 'Kode OTP tidak valid.');
    }

    // OTP is valid, update session
    $_SESSION['reset_token_id'] = $token['id'];
    $_SESSION['reset_user_id'] = $token['user_id'];
    $_SESSION['reset_step'] = 'set_password';

    logSecurityEvent('OTP_VERIFICATION_SUCCESS', [
        'email' => $email,
        'user_id' => $token['user_id'],
        'token_id' => $token['id']
    ]);

    sendJSONResponse(true, 'Kode OTP berhasil diverifikasi.');
}

/**
 * Handle Reset Password Request
 */
function handleResetPassword($koneksi, $user_ip)
{
    // Check if user passed OTP verification
    if (
        !isset($_SESSION['reset_token_id']) ||
        !isset($_SESSION['reset_user_id']) ||
        $_SESSION['reset_step'] !== 'set_password'
    ) {
        sendJSONResponse(false, 'Sesi tidak valid. Silakan mulai dari awal.');
    }

    $token_id = $_SESSION['reset_token_id'];
    $user_id = $_SESSION['reset_user_id'];
    $email = $_SESSION['reset_email'];

    // Validate passwords
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if (empty($new_password) || empty($confirm_password)) {
        sendJSONResponse(false, 'Password tidak boleh kosong');
    }

    if ($new_password !== $confirm_password) {
        sendJSONResponse(false, 'Password tidak cocok');
    }

    if (strlen($new_password) < PASSWORD_MIN_LENGTH) {
        sendJSONResponse(false, 'Password minimal ' . PASSWORD_MIN_LENGTH . ' karakter');
    }

    // Hash new password
    $password_hash = password_hash($new_password, PASSWORD_BCRYPT);

    // Begin transaction
    beginTransaction($koneksi);

    try {
        // Update user password
        $update_query = "UPDATE users SET password = ? WHERE id_user = ?";
        $update_result = executeUpdate($koneksi, $update_query, 'si', [$password_hash, $user_id]);

        if (!$update_result) {
            throw new Exception('Failed to update password');
        }

        // Mark token as used
        $mark_used_query = "UPDATE password_reset_tokens SET used = 1, used_at = NOW() WHERE id = ?";
        $mark_used_result = executeUpdate($koneksi, $mark_used_query, 'i', [$token_id]);

        if (!$mark_used_result) {
            throw new Exception('Failed to mark token as used');
        }

        // Commit transaction
        commitTransaction($koneksi);

        // Send success email
        try {
            $user_query = "SELECT name FROM users WHERE id_user = ?";
            $user_result = executePreparedStatement($koneksi, $user_query, 'i', [$user_id]);
            $user_data = mysqli_fetch_assoc($user_result);

            $emailService = new EmailService();
            $emailService->sendPasswordResetSuccess($email, $user_data['name']);
        } catch (Exception $e) {
            // Log but don't fail the request
            logSecurityEvent('PASSWORD_SUCCESS_EMAIL_FAILED', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
        }

        // Clear session data
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_token_id']);
        unset($_SESSION['reset_user_id']);
        unset($_SESSION['reset_step']);

        logSecurityEvent('PASSWORD_RESET_SUCCESS', [
            'email' => $email,
            'user_id' => $user_id,
            'ip' => $user_ip
        ]);

        sendJSONResponse(true, 'Password berhasil direset. Silakan login dengan password baru.');

    } catch (Exception $e) {
        // Rollback transaction
        rollbackTransaction($koneksi);

        logSecurityEvent('PASSWORD_RESET_FAILED', [
            'email' => $email,
            'user_id' => $user_id,
            'error' => $e->getMessage()
        ]);

        sendJSONResponse(false, 'Terjadi kesalahan. Silakan coba lagi.');
    }
}

/**
 * Handle Resend OTP Request
 */
function handleResendOTP($koneksi, $user_ip)
{
    // Check rate limiting (stricter for resend)
    if (checkRateLimit($user_ip, 'resend_otp')) {
        $remaining = getRateLimitRemaining($user_ip, 'resend_otp');
        sendJSONResponse(false, "Silakan tunggu " . $remaining . " detik sebelum mengirim ulang.");
    }

    // Check if email is in session
    if (!isset($_SESSION['reset_email'])) {
        sendJSONResponse(false, 'Sesi tidak valid. Silakan mulai dari awal.');
    }

    $email = $_SESSION['reset_email'];

    // Get user info
    $query = "SELECT id_user, name FROM users WHERE email = ? LIMIT 1";
    $result = executePreparedStatement($koneksi, $query, 's', [$email]);

    if (!$result || mysqli_num_rows($result) === 0) {
        sendJSONResponse(false, 'User tidak ditemukan.');
    }

    $user = mysqli_fetch_assoc($result);
    $user_id = $user['id_user'];
    $user_name = $user['name'];

    // Generate new OTP
    $otp = generateOTP();
    $otp_hash = hashOTP($otp);
    $expires_at = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRE_MINUTES . ' minutes'));
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';

    // Delete old unused tokens
    $delete_query = "DELETE FROM password_reset_tokens WHERE user_id = ? AND used = 0";
    executeUpdate($koneksi, $delete_query, 'i', [$user_id]);

    // Insert new token
    $insert_query = "INSERT INTO password_reset_tokens (user_id, email, otp_hash, ip_address, user_agent, expires_at) VALUES (?, ?, ?, ?, ?, ?)";
    $insert_result = executeUpdate($koneksi, $insert_query, 'isssss', [
        $user_id,
        $email,
        $otp_hash,
        $user_ip,
        $user_agent,
        $expires_at
    ]);

    if (!$insert_result) {
        sendJSONResponse(false, 'Terjadi kesalahan. Silakan coba lagi.');
    }

    // Send email
    try {
        $emailService = new EmailService();
        $email_sent = $emailService->sendPasswordResetOTP($email, $user_name, $otp);

        if ($email_sent) {
            logSecurityEvent('OTP_RESENT_SUCCESS', [
                'email' => $email,
                'user_id' => $user_id
            ]);

            sendJSONResponse(true, 'Kode OTP baru telah dikirim ke email Anda.');
        } else {
            sendJSONResponse(false, 'Gagal mengirim email. Silakan coba lagi.');
        }
    } catch (Exception $e) {
        sendJSONResponse(false, 'Terjadi kesalahan saat mengirim email.');
    }
}
?>