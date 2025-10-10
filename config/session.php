<?php
/**
 * Secure Session Configuration
 * Enhanced session security with encryption and secure cookie settings
 */

// Session security configuration
ini_set('session.cookie_httponly', 1);  // Prevent JavaScript access to session cookies
ini_set('session.cookie_secure', 0);    // Set to 1 in production with HTTPS
ini_set('session.cookie_samesite', 'Strict'); // CSRF protection
ini_set('session.use_strict_mode', 1);  // Reject uninitialized session IDs
ini_set('session.use_cookies', 1);      // Use cookies for session ID
ini_set('session.use_only_cookies', 1); // Only use cookies, not URL parameters
ini_set('session.use_trans_sid', 0);    // Don't use transparent session ID

// Set session name (avoid default PHPSESSID)
session_name('SEBLAK_SESSION');

// Enhanced session configuration
$session_config = [
    'lifetime' => 3600,          // 1 hour session lifetime
    'path' => '/',
    'domain' => '',              // Set your domain in production
    'secure' => false,           // Set to true in production with HTTPS
    'httponly' => true,
    'samesite' => 'Strict'
];

session_set_cookie_params($session_config);

// Start session with security checks
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session security checks
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
}

// Validate session against hijacking
if (isset($_SESSION['user_ip']) && $_SESSION['user_ip'] !== ($_SERVER['REMOTE_ADDR'] ?? 'unknown')) {
    session_unset();
    session_destroy();
    session_start();
    session_regenerate_id(true);
}

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_config['lifetime'])) {
    session_unset();
    session_destroy();
    session_start();
    session_regenerate_id(true);
}

$_SESSION['last_activity'] = time();

// Regenerate session ID periodically for security
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) { // 30 minutes
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']) &&
        isset($_SESSION['user_authenticated']) &&
        $_SESSION['user_authenticated'] === true;
}

/**
 * Get current logged-in user data
 * @return array|null
 */
function getCurrentSessionUser()
{
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'name' => $_SESSION['name'] ?? null,
        'role_id' => $_SESSION['role_id'] ?? null,
        'role_name' => $_SESSION['role_name'] ?? null
    ];
}

/**
 * Require login for protected pages
 * @param string $redirect_url URL to redirect to after login
 */
function requireLogin($redirect_url = null)
{
    if (!isLoggedIn()) {
        if ($redirect_url) {
            $_SESSION['redirect_after_login'] = $redirect_url;
        }
        header('Location: pages/auth/login.php');
        exit();
    }
}

/**
 * Require specific role for protected pages
 * @param array $allowed_roles Array of allowed role names
 * @param string $error_message Custom error message
 */
function requireRole($allowed_roles, $error_message = 'Access denied')
{
    requireLogin();

    $user = getCurrentSessionUser();
    if (!$user || !in_array($user['role_name'], $allowed_roles)) {
        http_response_code(403);
        die($error_message);
    }
}

/**
 * Logout user and clean session
 */
function logoutUser()
{
    // Clear all session variables
    $_SESSION = [];

    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Destroy the session
    session_destroy();

    // Start a new session for flash messages
    session_start();
    session_regenerate_id(true);
}

/**
 * Set flash message
 * @param string $type Message type (success, error, warning, info)
 * @param string $message Message content
 */
function setFlashMessage($type, $message)
{
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash messages
 * @return array
 */
function getFlashMessages()
{
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCSRFToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 * @param string $token Token to validate
 * @return bool
 */
function validateCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>