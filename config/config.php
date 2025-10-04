<?php
/**
 * Configuration File
 * Contains application-wide settings and security configurations
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'seblak_app');

// Application Configuration
define('APP_NAME', 'Seblak Predator');
define('APP_URL', 'http://localhost/seblak-predator'); // Change to https:// in production

// Email Configuration (PHPMailer)
define('SMTP_HOST', 'smtp.gmail.com'); // Change to your SMTP host
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com'); // Change to your email
define('SMTP_PASSWORD', 'your-app-password'); // Use app password, not regular password
define('SMTP_FROM_EMAIL', 'noreply@seblakpredator.com');
define('SMTP_FROM_NAME', APP_NAME);

// Security Configuration
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_TIME_NAME', 'csrf_token_time');
define('CSRF_TOKEN_EXPIRE', 3600); // 1 hour in seconds

// Rate Limiting Configuration
define('RATE_LIMIT_REQUESTS', 3); // Maximum 3 requests
define('RATE_LIMIT_PERIOD', 900); // Within 15 minutes (900 seconds)

// OTP Configuration
define('OTP_LENGTH', 6);
define('OTP_EXPIRE_MINUTES', 15);
define('OTP_RESEND_COOLDOWN', 60); // 60 seconds between resend requests

// Password Configuration
define('PASSWORD_MIN_LENGTH', 8);

// Force HTTPS in production
if (!defined('DEVELOPMENT_MODE')) {
    define('DEVELOPMENT_MODE', true); // Set to false in production
}

// Redirect to HTTPS if not in development mode
if (!DEVELOPMENT_MODE && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on')) {
    header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}

/**
 * Generate CSRF Token
 */
function generateCSRFToken()
{
    if (
        empty($_SESSION[CSRF_TOKEN_NAME]) || empty($_SESSION[CSRF_TOKEN_TIME_NAME]) ||
        (time() - $_SESSION[CSRF_TOKEN_TIME_NAME]) > CSRF_TOKEN_EXPIRE
    ) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        $_SESSION[CSRF_TOKEN_TIME_NAME] = time();
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verify CSRF Token
 */
function verifyCSRFToken($token)
{
    if (empty($_SESSION[CSRF_TOKEN_NAME]) || empty($_SESSION[CSRF_TOKEN_TIME_NAME])) {
        return false;
    }

    // Check if token is expired
    if ((time() - $_SESSION[CSRF_TOKEN_TIME_NAME]) > CSRF_TOKEN_EXPIRE) {
        return false;
    }

    // Verify token
    return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
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