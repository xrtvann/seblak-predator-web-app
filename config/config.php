<?php
/**
 * Configuration File
 * Contains application-wide settings and security configurations
 */

// Load environment variables
require_once __DIR__ . '/env.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration - Load from environment
define('DB_HOST', EnvLoader::get('DB_HOST', 'localhost'));
define('DB_USERNAME', EnvLoader::get('DB_USER', 'root'));
define('DB_PASSWORD', EnvLoader::get('DB_PASSWORD', ''));
define('DB_NAME', EnvLoader::get('DB_NAME', 'seblak_app'));
define('DB_PORT', EnvLoader::get('DB_PORT', 3306));

// Application Configuration
define('APP_NAME', EnvLoader::get('APP_NAME', 'Seblak Predator'));
define('APP_URL', EnvLoader::get('APP_URL', 'http://localhost/seblak-predator'));
define('APP_ENV', EnvLoader::get('APP_ENV', 'development'));
define('APP_DEBUG', EnvLoader::get('APP_DEBUG', true));

// Email Configuration (PHPMailer) - Load from environment
define('SMTP_HOST', EnvLoader::get('MAIL_HOST', 'smtp.gmail.com'));
define('SMTP_PORT', EnvLoader::get('MAIL_PORT', 587));
define('SMTP_USERNAME', EnvLoader::get('MAIL_USERNAME', 'your-email@gmail.com'));
define('SMTP_PASSWORD', EnvLoader::get('MAIL_PASSWORD', 'your-app-password'));
define('SMTP_FROM_EMAIL', EnvLoader::get('MAIL_FROM_ADDRESS', 'noreply@seblakpredator.com'));
define('SMTP_FROM_NAME', EnvLoader::get('MAIL_FROM_NAME', APP_NAME));

// Security Configuration - Load from environment
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_TIME_NAME', 'csrf_token_time');
define('CSRF_TOKEN_EXPIRE', EnvLoader::get('CSRF_TOKEN_EXPIRY', 3600));

// Rate Limiting Configuration - Load from environment
define('RATE_LIMIT_REQUESTS', EnvLoader::get('MAX_LOGIN_ATTEMPTS', 5));
define('RATE_LIMIT_PERIOD', EnvLoader::get('LOGIN_ATTEMPT_WINDOW', 300)); // 5 minutes (300 seconds)

// OTP Configuration
define('OTP_LENGTH', 6);
define('OTP_EXPIRE_MINUTES', 15);
define('OTP_RESEND_COOLDOWN', 60);

// Password Configuration
define('PASSWORD_MIN_LENGTH', EnvLoader::get('PASSWORD_MIN_LENGTH', 8));

// Development/Production mode detection
define('DEVELOPMENT_MODE', EnvLoader::get('APP_ENV', 'development') !== 'production');

// Force HTTPS in production
if (!DEVELOPMENT_MODE && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on')) {
    header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}

/**
 * Check Rate Limiting
 * Returns true if rate limit is exceeded
 */
function checkRateLimit($identifier, $action = 'default')
{
    $key = 'rate_limit_' . $action . '_' . $identifier;

    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [
            'count' => 1,
            'first_request' => time()
        ];
        return false;
    }

    $data = $_SESSION[$key];
    $elapsed_time = time() - $data['first_request'];

    // Reset if period has passed
    if ($elapsed_time > RATE_LIMIT_PERIOD) {
        $_SESSION[$key] = [
            'count' => 1,
            'first_request' => time()
        ];
        return false;
    }

    // Check if limit exceeded
    if ($data['count'] >= RATE_LIMIT_REQUESTS) {
        return true;
    }

    // Increment counter
    $_SESSION[$key]['count']++;
    return false;
}

/**
 * Get remaining time for rate limit
 */
function getRateLimitRemaining($identifier, $action = 'default')
{
    $key = 'rate_limit_' . $action . '_' . $identifier;

    if (!isset($_SESSION[$key])) {
        return 0;
    }

    $data = $_SESSION[$key];
    $elapsed_time = time() - $data['first_request'];
    $remaining = RATE_LIMIT_PERIOD - $elapsed_time;

    return max(0, $remaining);
}

/**
 * Sanitize input data
 */
function sanitizeInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email format
 */
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate secure random OTP
 */
function generateOTP($length = OTP_LENGTH)
{
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= random_int(0, 9);
    }
    return $otp;
}

/**
 * Hash OTP for storage (prevents rainbow table attacks)
 */
function hashOTP($otp)
{
    return password_hash($otp, PASSWORD_BCRYPT);
}

/**
 * Verify OTP
 */
function verifyOTP($otp, $hashedOTP)
{
    return password_verify($otp, $hashedOTP);
}

/**
 * Log security events
 */
function logSecurityEvent($event, $details = [])
{
    $log_file = __DIR__ . '/../logs/security.log';
    $log_dir = dirname($log_file);

    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

    $log_entry = sprintf(
        "[%s] %s | IP: %s | User-Agent: %s | Details: %s\n",
        $timestamp,
        $event,
        $ip,
        $user_agent,
        json_encode($details)
    );

    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

/**
 * Send JSON response
 */
function sendJSONResponse($success, $message, $data = [])
{
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}
?>