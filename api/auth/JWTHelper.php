<?php
/**
 * JWT Helper Class for Authentication using Firebase JWT
 * Handles JWT token creation, validation, and decoding
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/env.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

class JWTHelper
{
    // Load configuration from environment variables
    private static function getSecretKey()
    {
        $key = EnvLoader::get('JWT_SECRET_KEY');

        if (empty($key) || $key === 'SeblakPredator2025SecretKey!@#$%^&*()_FirebaseJWT_CHANGE_THIS_IN_PRODUCTION') {
            if (EnvLoader::isProduction()) {
                throw new Exception('JWT_SECRET_KEY must be set to a secure value in production!');
            }
            // Use a warning in development
            error_log('WARNING: Using default JWT secret key. Change JWT_SECRET_KEY in .env file!');
        }

        return $key ?: 'SeblakPredator2025SecretKey!@#$%^&*()_FirebaseJWT';
    }

    private static function getAlgorithm()
    {
        return EnvLoader::get('JWT_ALGORITHM', 'HS256');
    }

    private static function getTokenExpiry()
    {
        return EnvLoader::get('JWT_ACCESS_TOKEN_EXPIRY', 3600);
    }

    private static function getRefreshTokenExpiry()
    {
        return EnvLoader::get('JWT_REFRESH_TOKEN_EXPIRY', 604800);
    }

    private static function getIssuer()
    {
        return EnvLoader::get('JWT_ISSUER', 'seblak-predator');
    }

    private static function getAudience()
    {
        return EnvLoader::get('JWT_AUDIENCE', 'seblak-predator-users');
    }

    /**
     * Generate JWT token for user using Firebase JWT
     */
    public static function generateToken($payload, $is_refresh_token = false)
    {
        $expiry = $is_refresh_token ? self::getRefreshTokenExpiry() : self::getTokenExpiry();
        $current_time = time();

        $token_payload = [
            'iss' => self::getIssuer(),                    // Issuer
            'aud' => self::getAudience(),                  // Audience
            'iat' => $current_time,                        // Issued at
            'nbf' => $current_time,                        // Not before
            'exp' => $current_time + $expiry,              // Expiration
            'jti' => uniqid('jwt_', true),                 // JWT ID (unique identifier)
            'data' => $payload,                            // User data
            'token_type' => $is_refresh_token ? 'refresh' : 'access'
        ];

        try {
            return JWT::encode($token_payload, self::getSecretKey(), self::getAlgorithm());
        } catch (Exception $e) {
            error_log("JWT Generation Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate and decode JWT token using Firebase JWT
     */
    public static function validateToken($token)
    {
        if (empty($token)) {
            return [
                'valid' => false,
                'error' => 'Token is empty',
                'payload' => null
            ];
        }

        try {
            $decoded = JWT::decode($token, new Key(self::getSecretKey(), self::getAlgorithm()));

            return [
                'valid' => true,
                'payload' => (array) $decoded,
                'user_data' => isset($decoded->data) ? (array) $decoded->data : null,
                'token_type' => $decoded->token_type ?? 'access',
                'expires_at' => $decoded->exp ?? null,
                'issued_at' => $decoded->iat ?? null,
                'jwt_id' => $decoded->jti ?? null
            ];

        } catch (ExpiredException $e) {
            return [
                'valid' => false,
                'error' => 'Token has expired',
                'payload' => null
            ];
        } catch (SignatureInvalidException $e) {
            return [
                'valid' => false,
                'error' => 'Invalid token signature',
                'payload' => null
            ];
        } catch (BeforeValidException $e) {
            return [
                'valid' => false,
                'error' => 'Token not yet valid',
                'payload' => null
            ];
        } catch (Exception $e) {
            return [
                'valid' => false,
                'error' => 'Invalid token: ' . $e->getMessage(),
                'payload' => null
            ];
        }
    }

    /**
     * Extract token from Authorization header
     */
    public static function extractTokenFromHeader($header = null)
    {
        if ($header === null) {
            $headers = getallheaders();
            $header = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        }

        if (empty($header)) {
            return false;
        }

        if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }

        return false;
    }

    /**
     * Get user data from valid token
     */
    public static function getUserFromToken($token)
    {
        $validation = self::validateToken($token);

        if ($validation['valid'] && isset($validation['user_data'])) {
            return $validation['user_data'];
        }

        return false;
    }

    /**
     * Check if token is refresh token
     */
    public static function isRefreshToken($token)
    {
        $validation = self::validateToken($token);
        return $validation['valid'] && $validation['token_type'] === 'refresh';
    }

    /**
     * Check if token is access token
     */
    public static function isAccessToken($token)
    {
        $validation = self::validateToken($token);
        return $validation['valid'] && $validation['token_type'] === 'access';
    }

    /**
     * Generate both access and refresh tokens
     */
    public static function generateTokenPair($user_data)
    {
        // Clean user data for token (remove sensitive info)
        $token_payload = [
            'user_id' => $user_data['id'],
            'username' => $user_data['username'],
            'email' => $user_data['email'],
            'role_id' => $user_data['role_id'],
            'name' => $user_data['name']
        ];

        return [
            'access_token' => self::generateToken($token_payload, false),
            'refresh_token' => self::generateToken($token_payload, true),
            'token_type' => 'Bearer',
            'expires_in' => self::getTokenExpiry()
        ];
    }

    /**
     * Blacklist a token (for logout)
     */
    public static function blacklistToken($token, $user_id, $reason = 'logout')
    {
        $validation = self::validateToken($token);

        if (!$validation['valid'] || !isset($validation['jwt_id'])) {
            return false;
        }

        // Validate reason - must be one of the enum values
        $valid_reasons = ['logout', 'security', 'admin'];
        if (!in_array($reason, $valid_reasons)) {
            $reason = 'logout'; // Default to logout if invalid reason
        }

        // Add to blacklist table
        global $koneksi;
        if (!$koneksi) {
            require_once __DIR__ . '/../../config/koneksi.php';
        }

        $stmt = mysqli_prepare($koneksi, "
            INSERT INTO blacklisted_tokens (token_jti, user_id, expires_at, reason) 
            VALUES (?, ?, FROM_UNIXTIME(?), ?)
        ");

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "ssis",
                $validation['jwt_id'],
                $user_id,
                $validation['expires_at'],
                $reason
            );
            $result = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $result;
        }

        return false;
    }

    /**
     * Check if token is blacklisted
     */
    public static function isTokenBlacklisted($token)
    {
        $validation = self::validateToken($token);

        if (!$validation['valid'] || !isset($validation['jwt_id'])) {
            return true; // Invalid tokens are considered blacklisted
        }

        global $koneksi;
        if (!$koneksi) {
            require_once __DIR__ . '/../../config/koneksi.php';
        }

        $stmt = mysqli_prepare($koneksi, "
            SELECT id FROM blacklisted_tokens 
            WHERE token_jti = ? AND expires_at > NOW()
        ");

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $validation['jwt_id']);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $blacklisted = mysqli_num_rows($result) > 0;
            mysqli_stmt_close($stmt);
            return $blacklisted;
        }

        return false;
    }

    /**
     * Refresh an access token using a refresh token
     */
    public static function refreshAccessToken($refresh_token, $user_id)
    {
        // Validate refresh token
        if (!self::isRefreshToken($refresh_token) || self::isTokenBlacklisted($refresh_token)) {
            return false;
        }

        $user_data = self::getUserFromToken($refresh_token);
        if (!$user_data || $user_data['user_id'] !== $user_id) {
            return false;
        }

        // Generate new access token
        $new_access_token = self::generateToken($user_data, false);

        // Log refresh activity
        global $koneksi;
        if (!$koneksi) {
            require_once __DIR__ . '/../../config/koneksi.php';
        }

        $stmt = mysqli_prepare($koneksi, "
            INSERT INTO token_refresh_log (user_id, ip_address, user_agent) 
            VALUES (?, ?, ?)
        ");

        if ($stmt) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            mysqli_stmt_bind_param($stmt, "sss", $user_id, $ip, $user_agent);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        return [
            'access_token' => $new_access_token,
            'token_type' => 'Bearer',
            'expires_in' => self::getTokenExpiry()
        ];
    }
}
?>