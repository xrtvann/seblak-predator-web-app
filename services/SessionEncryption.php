<?php
/**
 * Session Encryption Class
 * Provides encryption/decryption for sensitive session data
 */

class SessionEncryption
{
    private static $encryption_key = 'SeblakPredator2024SessionKey!@#';
    private static $cipher_method = 'AES-256-CBC';

    /**
     * Set custom encryption key
     * @param string $key Encryption key
     */
    public static function setEncryptionKey($key)
    {
        self::$encryption_key = $key;
    }

    /**
     * Encrypt data for session storage
     * @param mixed $data Data to encrypt
     * @return string Encrypted data
     */
    public static function encrypt($data)
    {
        $json_data = json_encode($data);
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($json_data, self::$cipher_method, self::$encryption_key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt data from session storage
     * @param string $encrypted_data Encrypted data
     * @return mixed Decrypted data
     */
    public static function decrypt($encrypted_data)
    {
        try {
            $data = base64_decode($encrypted_data);
            $iv = substr($data, 0, 16);
            $encrypted = substr($data, 16);
            $decrypted = openssl_decrypt($encrypted, self::$cipher_method, self::$encryption_key, 0, $iv);
            return json_decode($decrypted, true);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Store encrypted data in session
     * @param string $key Session key
     * @param mixed $data Data to store
     */
    public static function setSecureSession($key, $data)
    {
        $_SESSION['secure_' . $key] = self::encrypt($data);
    }

    /**
     * Retrieve encrypted data from session
     * @param string $key Session key
     * @return mixed Decrypted data or null
     */
    public static function getSecureSession($key)
    {
        if (!isset($_SESSION['secure_' . $key])) {
            return null;
        }
        return self::decrypt($_SESSION['secure_' . $key]);
    }

    /**
     * Remove encrypted data from session
     * @param string $key Session key
     */
    public static function removeSecureSession($key)
    {
        unset($_SESSION['secure_' . $key]);
    }
}

/**
 * Secure Cookie Handler Class
 * Manages encrypted cookies with security features
 */
class SecureCookie
{
    private static $encryption_key = 'SeblakPredator2024CookieKey!@#';
    private static $cookie_prefix = 'seblak_';
    private static $default_options = [
        'expires' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false,    // Set to true in production with HTTPS
        'httponly' => true,
        'samesite' => 'Strict'
    ];

    /**
     * Set encrypted cookie
     * @param string $name Cookie name
     * @param mixed $value Cookie value
     * @param array $options Cookie options
     * @return bool Success status
     */
    public static function set($name, $value, $options = [])
    {
        $options = array_merge(self::$default_options, $options);
        $encrypted_value = self::encrypt($value);
        $cookie_name = self::$cookie_prefix . $name;

        return setcookie(
            $cookie_name,
            $encrypted_value,
            $options['expires'],
            $options['path'],
            $options['domain'],
            $options['secure'],
            $options['httponly']
        );
    }

    /**
     * Get encrypted cookie
     * @param string $name Cookie name
     * @return mixed Decrypted value or null
     */
    public static function get($name)
    {
        $cookie_name = self::$cookie_prefix . $name;

        if (!isset($_COOKIE[$cookie_name])) {
            return null;
        }

        return self::decrypt($_COOKIE[$cookie_name]);
    }

    /**
     * Delete cookie
     * @param string $name Cookie name
     * @param array $options Cookie options
     * @return bool Success status
     */
    public static function delete($name, $options = [])
    {
        $options = array_merge(self::$default_options, $options);
        $options['expires'] = time() - 3600; // Set expiry to past
        $cookie_name = self::$cookie_prefix . $name;

        return setcookie(
            $cookie_name,
            '',
            $options['expires'],
            $options['path'],
            $options['domain'],
            $options['secure'],
            $options['httponly']
        );
    }

    /**
     * Check if cookie exists
     * @param string $name Cookie name
     * @return bool
     */
    public static function exists($name)
    {
        $cookie_name = self::$cookie_prefix . $name;
        return isset($_COOKIE[$cookie_name]);
    }

    /**
     * Encrypt data for cookie storage
     * @param mixed $data Data to encrypt
     * @return string Encrypted data
     */
    private static function encrypt($data)
    {
        $json_data = json_encode($data);
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($json_data, 'AES-256-CBC', self::$encryption_key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt data from cookie storage
     * @param string $encrypted_data Encrypted data
     * @return mixed Decrypted data
     */
    private static function decrypt($encrypted_data)
    {
        try {
            $data = base64_decode($encrypted_data);
            $iv = substr($data, 0, 16);
            $encrypted = substr($data, 16);
            $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', self::$encryption_key, 0, $iv);
            return json_decode($decrypted, true);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Set cookie encryption key
     * @param string $key Encryption key
     */
    public static function setEncryptionKey($key)
    {
        self::$encryption_key = $key;
    }

    /**
     * Set cookie prefix
     * @param string $prefix Cookie prefix
     */
    public static function setCookiePrefix($prefix)
    {
        self::$cookie_prefix = $prefix;
    }

    /**
     * Set default cookie options
     * @param array $options Default options
     */
    public static function setDefaultOptions($options)
    {
        self::$default_options = array_merge(self::$default_options, $options);
    }
}
?>