<?php
/**
 * JWT Authentication Middleware
 * Validates JWT tokens for protected API endpoints
 */

class JWTMiddleware
{
    /**
     * Authenticate request with JWT token
     */
    public static function authenticate($required_roles = [])
    {
        require_once __DIR__ . '/JWTHelper.php';

        try {
            // Get token from Authorization header
            $token = JWTHelper::extractTokenFromHeader();

            if (!$token) {
                self::respondWithError(401, 'Authorization token required');
                return false;
            }

            // Check if token is blacklisted
            if (JWTHelper::isTokenBlacklisted($token)) {
                self::respondWithError(401, 'Token has been revoked');
                return false;
            }

            // Validate token
            $validation = JWTHelper::validateToken($token);

            if (!$validation['valid']) {
                self::respondWithError(401, $validation['error']);
                return false;
            }

            // Check if it's an access token (not refresh token)
            if ($validation['token_type'] !== 'access') {
                self::respondWithError(401, 'Invalid token type - access token required');
                return false;
            }

            $user_data = $validation['user_data'];

            // Check role authorization if required
            if (!empty($required_roles) && isset($user_data['role_id'])) {
                if (!in_array($user_data['role_id'], $required_roles)) {
                    self::respondWithError(403, 'Insufficient permissions');
                    return false;
                }
            }

            // Store user data globally for use in protected endpoints
            $GLOBALS['authenticated_user'] = $user_data;
            $GLOBALS['jwt_token'] = $token;
            $GLOBALS['jwt_validation'] = $validation;

            return $user_data;

        } catch (Exception $e) {
            self::respondWithError(500, 'Authentication error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get current authenticated user
     */
    public static function getCurrentUser()
    {
        return $GLOBALS['authenticated_user'] ?? null;
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole($role_id)
    {
        $user = self::getCurrentUser();
        return $user && isset($user['role_id']) && $user['role_id'] === $role_id;
    }

    /**
     * Check if user has any of the specified roles
     */
    public static function hasAnyRole($role_ids)
    {
        $user = self::getCurrentUser();
        return $user && isset($user['role_id']) && in_array($user['role_id'], $role_ids);
    }

    /**
     * Respond with error and exit
     */
    private static function respondWithError($status_code, $message)
    {
        http_response_code($status_code);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $message,
            'code' => $status_code
        ]);
        exit;
    }

    /**
     * Log API access
     */
    public static function logApiAccess($endpoint, $method, $success = true)
    {
        $user = self::getCurrentUser();
        $user_id = $user['user_id'] ?? null;

        global $koneksi;
        if (!$koneksi) {
            require_once __DIR__ . '/../../config/koneksi.php';
        }

        $stmt = mysqli_prepare($koneksi, "
            INSERT INTO api_access_log (user_id, endpoint, method, success, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        if ($stmt) {
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            mysqli_stmt_bind_param($stmt, "ssisss", $user_id, $endpoint, $method, $success, $ip_address, $user_agent);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}
?>