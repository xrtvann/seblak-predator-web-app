<?php
session_start();
require_once '../config/koneksi.php';
require_once '../config/session.php';
require_once '../config/config.php';

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSONResponse(false, 'Invalid request method');
}

$action = isset($_POST['action']) ? sanitizeInput($_POST['action']) : '';

// CSRF Token validation
if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    sendJSONResponse(false, 'Invalid security token. Please refresh the page and try again.');
}

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

    default:
        sendJSONResponse(false, 'Invalid action');
        break;
}

/**
 * Handle sending OTP to email
 */
function handleSendOTP($koneksi, $user_ip)
{
    // Rate limiting check - use session-based rate limiting from config.php
    if (checkRateLimit($user_ip, 'forgot_password')) {
        sendJSONResponse(false, "Terlalu banyak percobaan. Silakan coba lagi dalam " . ceil(RATE_LIMIT_PERIOD / 60) . " menit.", [
            'retry_after' => RATE_LIMIT_PERIOD
        ]);
    }

    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';

    if (empty($email) || !isValidEmail($email)) {
        sendJSONResponse(false, 'Email tidak valid');
    }

    // First, verify if email is registered in the system
    $query = "SELECT id, name, email FROM users WHERE email = ? AND is_active = 1";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result || mysqli_num_rows($result) === 0) {
        // Email not found - be explicit about it
        error_log("DEBUG: Email not found in database: " . $email);
        sendJSONResponse(false, 'Email tidak terdaftar dalam sistem. Silakan periksa kembali email Anda atau daftar akun baru.');
    }

    $user = mysqli_fetch_assoc($result);
    $user_id = $user['id'];
    $user_name = $user['name'];

    error_log("DEBUG: Email found in database: " . $email . " (User ID: " . $user_id . ")");

    // Email is verified and registered, proceed with OTP generation

    // Generate OTP and hash it
    $otp = generateOTP();
    $otp_hash = hashOTP($otp);
    $expires_at = date('Y-m-d H:i:s', time() + 600); // 10 minutes

    // Create unique ID for password reset record
    $reset_id = uniqid('pwd_reset_', true);

    // Delete any existing reset records for this user
    $delete_query = "DELETE FROM password_resets WHERE user_id = ?";
    $stmt = mysqli_prepare($koneksi, $delete_query);
    mysqli_stmt_bind_param($stmt, 's', $user_id);
    mysqli_stmt_execute($stmt);

    // Insert new reset record
    $insert_query = "INSERT INTO password_resets (id, user_id, otp_code, expires_at) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $insert_query);
    mysqli_stmt_bind_param($stmt, 'ssss', $reset_id, $user_id, $otp_hash, $expires_at);
    $insert_result = mysqli_stmt_execute($stmt);

    if (!$insert_result) {
        sendJSONResponse(false, 'Terjadi kesalahan. Silakan coba lagi.');
    }

    // Send OTP via email
    $email_service = getEmailService();

    try {
        // We already have user_name from the verification query above
        $sent = $email_service->sendPasswordResetOTP($email, $user_name, $otp);

        if ($sent) {
            // Rate limiting is automatically handled by the session-based checkRateLimit function
            sendJSONResponse(true, 'Kode OTP telah dikirim ke email Anda (' . $email . '). Silakan periksa email dan masukkan kode OTP.');
        } else {
            sendJSONResponse(false, 'Gagal mengirim email. Silakan coba lagi.');
        }
    } catch (Exception $e) {
        error_log("Email service error: " . $e->getMessage());
        sendJSONResponse(false, 'Terjadi kesalahan saat mengirim email. Silakan coba lagi.');
    }
}

/**
 * Handle OTP verification
 */
function handleVerifyOTP($koneksi, $user_ip)
{
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $otp = isset($_POST['otp']) ? sanitizeInput($_POST['otp']) : '';

    if (empty($email) || empty($otp)) {
        sendJSONResponse(false, 'Email dan kode OTP wajib diisi');
    }

    if (!isValidEmail($email)) {
        sendJSONResponse(false, 'Email tidak valid');
    }

    if (strlen($otp) !== 6 || !ctype_digit($otp)) {
        sendJSONResponse(false, 'Kode OTP harus 6 digit angka');
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
        sendJSONResponse(false, 'Kode OTP tidak valid atau sudah kedaluwarsa');
    }

    $row = mysqli_fetch_assoc($result);

    // Check if OTP has already been used
    if (!empty($row['used_at'])) {
        sendJSONResponse(false, 'Kode OTP sudah pernah digunakan');
    }

    // Check if OTP has expired
    if (strtotime($row['expires_at']) < time()) {
        sendJSONResponse(false, 'Kode OTP sudah kedaluwarsa. Silakan minta kode baru.');
    }

    // Verify OTP
    if (!verifyOTP($otp, $row['otp_code'])) {
        sendJSONResponse(false, 'Kode OTP tidak valid');
    }

    // Mark OTP as used
    $update_query = "UPDATE password_resets SET used_at = NOW() WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $update_query);
    mysqli_stmt_bind_param($stmt, 's', $row['reset_id']);
    mysqli_stmt_execute($stmt);

    sendJSONResponse(true, 'Kode OTP berhasil diverifikasi. Silakan masukkan password baru.');
}

/**
 * Handle password reset
 */
function handleResetPassword($koneksi, $user_ip)
{
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $otp = isset($_POST['otp']) ? sanitizeInput($_POST['otp']) : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if (empty($email) || empty($otp) || empty($new_password) || empty($confirm_password)) {
        sendJSONResponse(false, 'Semua field wajib diisi');
    }

    if ($new_password !== $confirm_password) {
        sendJSONResponse(false, 'Konfirmasi password tidak sesuai');
    }

    if (strlen($new_password) < 6) {
        sendJSONResponse(false, 'Password minimal 6 karakter');
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
        sendJSONResponse(false, 'Sesi reset password tidak valid');
    }

    $row = mysqli_fetch_assoc($result);

    // Check if OTP has expired
    if (strtotime($row['expires_at']) < time()) {
        sendJSONResponse(false, 'Sesi reset password sudah kedaluwarsa');
    }

    // Check if OTP has not been verified (used_at should be set by verify step)
    if (empty($row['used_at'])) {
        sendJSONResponse(false, 'Silakan verifikasi kode OTP terlebih dahulu');
    }

    // Verify OTP one more time
    if (!verifyOTP($otp, $row['otp_code'])) {
        sendJSONResponse(false, 'Kode OTP tidak valid');
    }

    // Update user password
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $update_query = "UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $update_query);
    mysqli_stmt_bind_param($stmt, 'ss', $password_hash, $row['id']);
    $update_result = mysqli_stmt_execute($stmt);

    if (!$update_result) {
        sendJSONResponse(false, 'Gagal mengupdate password. Silakan coba lagi.');
    }

    // Delete the password reset record after successful reset
    $delete_query = "DELETE FROM password_resets WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $delete_query);
    mysqli_stmt_bind_param($stmt, 's', $row['reset_id']);
    mysqli_stmt_execute($stmt);

    // Send success email
    $email_service = getEmailService();
    try {
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

    sendJSONResponse(true, 'Password berhasil diubah. Silakan login dengan password baru.');
}

// Email service functions - specific to this handler
function getEmailService()
{
    require_once '../config/env.php';

    if (APP_ENV === 'development') {
        require_once '../services/DevelopmentEmailService.php';
        return new DevelopmentEmailService();
    } else {
        require_once '../services/EmailService.php';
        return new EmailService();
    }
}

?>

?>