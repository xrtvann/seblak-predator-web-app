<?php
/**
 * Web Authentication Service
 * Handles login/logout for web interface using secure sessions and cookies
 */

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/SessionEncryption.php';

class WebAuthService
{
    private $connection;

    public function __construct($db_connection)
    {
        $this->connection = $db_connection;
    }

    /**
     * Authenticate user with username/email and password
     * @param string $username Username or email
     * @param string $password Plain text password
     * @param bool $remember_me Whether to set remember me cookie
     * @return array Result with success status and user data
     */
    public function login($username, $password, $remember_me = false)
    {
        try {
            // Check for rate limiting
            $rate_limit_check = $this->getRateLimitStatus($username);
            if ($rate_limit_check['is_limited']) {
                return [
                    'success' => false,
                    'message' => 'Too many failed login attempts. Please try again in ' . $rate_limit_check['remaining_time_text'] . '.',
                    'code' => 'RATE_LIMITED',
                    'remaining_seconds' => $rate_limit_check['remaining_seconds'],
                    'remaining_time_text' => $rate_limit_check['remaining_time_text']
                ];
            }

            // Get user from database
            $user = $this->getUserByUsernameOrEmail($username);

            if (!$user) {
                $this->logLoginAttempt($username, false);
                return [
                    'success' => false,
                    'message' => 'Invalid username or password',
                    'code' => 'INVALID_CREDENTIALS'
                ];
            }

            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                $this->logLoginAttempt($username, false);
                return [
                    'success' => false,
                    'message' => 'Invalid username or password',
                    'code' => 'INVALID_CREDENTIALS'
                ];
            }

            // Check if user is active
            if (!$user['is_active']) {
                return [
                    'success' => false,
                    'message' => 'Account is disabled. Please contact administrator.',
                    'code' => 'ACCOUNT_DISABLED'
                ];
            }

            // Get user role
            $role = $this->getUserRole($user['role_id']);

            // Create secure session
            $this->createUserSession($user, $role);

            // Set remember me cookie if requested
            if ($remember_me) {
                $this->setRememberMeCookie($user['id']);
            }

            // Update last login
            $this->updateLastLogin($user['id']);

            // Log successful login
            $this->logLoginAttempt($username, true);

            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'role_name' => $role['name'] ?? 'Customer'
                ]
            ];

        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred during login',
                'code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Logout user and clean session/cookies
     * @return array Result with success status
     */
    public function logout()
    {
        try {
            $user_id = $_SESSION['user_id'] ?? null;

            // Remove remember me cookie
            SecureCookie::delete('remember_token');

            // Clear user session in database
            if ($user_id) {
                $this->clearUserSessions($user_id);
            }

            // Logout user (from session.php)
            logoutUser();

            return [
                'success' => true,
                'message' => 'Logged out successfully'
            ];

        } catch (Exception $e) {
            error_log("Logout error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred during logout'
            ];
        }
    }

    /**
     * Check remember me cookie and auto-login if valid
     * @return bool Success status
     */
    public function checkRememberMe()
    {
        try {
            if (isLoggedIn()) {
                return true;
            }

            $remember_token = SecureCookie::get('remember_token');
            if (!$remember_token) {
                return false;
            }

            // Validate remember token from database
            $user = $this->getUserByRememberToken($remember_token);
            if (!$user) {
                SecureCookie::delete('remember_token');
                return false;
            }

            // Get user role
            $role = $this->getUserRole($user['role_id']);

            // Create secure session
            $this->createUserSession($user, $role);

            // Regenerate remember token for security
            $this->setRememberMeCookie($user['id']);

            return true;

        } catch (Exception $e) {
            error_log("Remember me check error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create secure user session
     * @param array $user User data
     * @param array $role Role data
     */
    private function createUserSession($user, $role)
    {
        // Regenerate session ID for security
        session_regenerate_id(true);

        // Set basic session data
        $_SESSION['user_authenticated'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['role_name'] = $role['name'] ?? 'Customer';
        $_SESSION['login_time'] = time();

        // Store encrypted sensitive data
        SessionEncryption::setSecureSession('user_details', [
            'phone_number' => $user['phone_number'] ?? null,
            'created_at' => $user['created_at'],
            'permissions' => $this->getUserPermissions($user['role_id'])
        ]);
    }

    /**
     * Set remember me cookie with secure token
     * @param string $user_id User ID
     */
    private function setRememberMeCookie($user_id)
    {
        $token = bin2hex(random_bytes(32));
        $expires = time() + (30 * 24 * 60 * 60); // 30 days

        // Store token in database
        $this->storeRememberToken($user_id, $token, $expires);

        // Set encrypted cookie
        SecureCookie::set('remember_token', $token, [
            'expires' => $expires,
            'secure' => false, // Set to true in production with HTTPS
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }

    /**
     * Get user by username or email
     * @param string $username Username or email
     * @return array|false User data or false
     */
    private function getUserByUsernameOrEmail($username)
    {
        $query = "SELECT id, username, email, password_hash, name, role_id, phone_number, is_active, created_at 
                  FROM users 
                  WHERE (username = ? OR email = ?) AND is_active = TRUE";

        $stmt = mysqli_prepare($this->connection, $query);
        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "ss", $username, $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }

        return false;
    }

    /**
     * Get user role by role ID
     * @param string $role_id Role ID
     * @return array|false Role data or false
     */
    private function getUserRole($role_id)
    {
        $query = "SELECT id, name FROM roles WHERE id = ?";
        $stmt = mysqli_prepare($this->connection, $query);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "s", $role_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }

        return false;
    }

    /**
     * Get user permissions (placeholder for future implementation)
     * @param string $role_id Role ID
     * @return array User permissions
     */
    private function getUserPermissions($role_id)
    {
        // Basic role-based permissions
        $permissions = [
            'role_admin' => ['*'], // Admin can do everything
            'role_staff' => ['menu.read', 'menu.write', 'orders.read', 'orders.write'],
            'role_customer' => ['orders.read']
        ];

        return $permissions[$role_id] ?? [];
    }

    /**
     * Check rate limit status and get remaining time
     * @param string $username Username
     * @return array Rate limit status with remaining time
     */
    private function getRateLimitStatus($username)
    {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // Get failed attempts in last 5 minutes
        $query = "SELECT COUNT(*) as attempt_count, 
                         MAX(attempted_at) as last_attempt
                  FROM login_attempts 
                  WHERE (username = ? OR ip_address = ?) 
                  AND success = FALSE 
                  AND attempted_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)";

        $stmt = mysqli_prepare($this->connection, $query);
        if (!$stmt) {
            return ['is_limited' => false, 'remaining_seconds' => 0, 'remaining_time_text' => ''];
        }

        mysqli_stmt_bind_param($stmt, "ss", $username, $ip_address);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $attempt_count = (int) $row['attempt_count'];

            if ($attempt_count >= 5) { // Max 5 attempts in 5 minutes
                // Calculate remaining lockout time
                $last_attempt = new DateTime($row['last_attempt']);
                $lockout_end = clone $last_attempt;
                $lockout_end->add(new DateInterval('PT5M')); // Add 5 minutes
                $now = new DateTime();

                if ($now < $lockout_end) {
                    $remaining_seconds = $lockout_end->getTimestamp() - $now->getTimestamp();
                    $minutes = floor($remaining_seconds / 60);
                    $seconds = $remaining_seconds % 60;
                    $remaining_time_text = sprintf('%d:%02d', $minutes, $seconds);

                    return [
                        'is_limited' => true,
                        'remaining_seconds' => $remaining_seconds,
                        'remaining_time_text' => $remaining_time_text,
                        'attempt_count' => $attempt_count
                    ];
                }
            }
        }

        return ['is_limited' => false, 'remaining_seconds' => 0, 'remaining_time_text' => '', 'attempt_count' => $attempt_count ?? 0];
    }

    /**
     * Check if user is rate limited (legacy method for backward compatibility)
     * @param string $username Username
     * @return bool True if rate limited
     */
    private function isRateLimited($username)
    {
        $status = $this->getRateLimitStatus($username);
        return $status['is_limited'];
    }

    /**
     * Log login attempt
     * @param string $username Username
     * @param bool $success Success status
     */
    private function logLoginAttempt($username, $success)
    {
        $query = "INSERT INTO login_attempts (username, success, ip_address, user_agent, attempted_at) 
                  VALUES (?, ?, ?, ?, NOW())";

        $stmt = mysqli_prepare($this->connection, $query);
        if ($stmt) {
            $success_int = $success ? 1 : 0;
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

            mysqli_stmt_bind_param($stmt, "siss", $username, $success_int, $ip_address, $user_agent);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    /**
     * Update user's last login timestamp
     * @param string $user_id User ID
     */
    private function updateLastLogin($user_id)
    {
        $query = "UPDATE users SET updated_at = NOW() WHERE id = ?";
        $stmt = mysqli_prepare($this->connection, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    /**
     * Store remember token in database
     * @param string $user_id User ID
     * @param string $token Remember token
     * @param int $expires Expiry timestamp
     */
    private function storeRememberToken($user_id, $token, $expires)
    {
        // First, clear old remember tokens for this user
        $delete_query = "DELETE FROM user_sessions WHERE user_id = ? AND session_token LIKE 'remember_%'";
        $delete_stmt = mysqli_prepare($this->connection, $delete_query);
        if ($delete_stmt) {
            mysqli_stmt_bind_param($delete_stmt, "s", $user_id);
            mysqli_stmt_execute($delete_stmt);
            mysqli_stmt_close($delete_stmt);
        }

        // Store new remember token
        $session_id = 'session_' . uniqid() . '_' . mt_rand(1000, 9999);
        $session_token = 'remember_' . $token;
        $expires_datetime = date('Y-m-d H:i:s', $expires);
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        $query = "INSERT INTO user_sessions (id, user_id, session_token, ip_address, user_agent, expires_at, is_active) 
                  VALUES (?, ?, ?, ?, ?, ?, TRUE)";

        $stmt = mysqli_prepare($this->connection, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssss", $session_id, $user_id, $session_token, $ip_address, $user_agent, $expires_datetime);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    /**
     * Get user by remember token
     * @param string $token Remember token
     * @return array|false User data or false
     */
    private function getUserByRememberToken($token)
    {
        $session_token = 'remember_' . $token;

        $query = "SELECT u.id, u.username, u.email, u.name, u.role_id, u.phone_number, u.is_active, u.created_at
                  FROM users u
                  INNER JOIN user_sessions s ON u.id = s.user_id
                  WHERE s.session_token = ? AND s.expires_at > NOW() AND s.is_active = TRUE AND u.is_active = TRUE";

        $stmt = mysqli_prepare($this->connection, $query);
        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "s", $session_token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }

        return false;
    }

    /**
     * Clear user sessions from database
     * @param string $user_id User ID
     */
    private function clearUserSessions($user_id)
    {
        $query = "UPDATE user_sessions SET is_active = FALSE WHERE user_id = ?";
        $stmt = mysqli_prepare($this->connection, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    /**
     * Get current rate limit status for display (public method)
     * @param string $username Username
     * @return array Rate limit status
     */
    public function getLoginRateLimit($username = '')
    {
        return $this->getRateLimitStatus($username);
    }
}
?>