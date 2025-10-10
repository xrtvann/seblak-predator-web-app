<?php
/**
 * Environment Configuration Loader
 * Loads environment variables from .env file for secure configuration
 */

class EnvLoader
{
    private static $env_vars = [];
    private static $loaded = false;

    /**
     * Load environment variables from .env file
     */
    public static function load($env_file = null)
    {
        if (self::$loaded) {
            return;
        }

        $env_file = $env_file ?: __DIR__ . '/../.env';

        if (!file_exists($env_file)) {
            // Fallback to .env.example if .env doesn't exist
            $env_file = __DIR__ . '/../.env.example';
        }

        if (file_exists($env_file)) {
            $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                // Skip comments
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }

                // Parse key=value pairs
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);

                    // Remove quotes if present
                    if (
                        (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                        (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)
                    ) {
                        $value = substr($value, 1, -1);
                    }

                    self::$env_vars[$key] = $value;

                    // Also set as environment variable if not already set
                    if (!getenv($key)) {
                        putenv("$key=$value");
                    }
                }
            }
        }

        self::$loaded = true;
    }

    /**
     * Get environment variable with optional default
     */
    public static function get($key, $default = null)
    {
        self::load();

        // Check PHP environment first
        $value = getenv($key);
        if ($value !== false) {
            return self::parseValue($value);
        }

        // Check loaded .env file
        if (isset(self::$env_vars[$key])) {
            return self::parseValue(self::$env_vars[$key]);
        }

        return $default;
    }

    /**
     * Parse environment value (convert string booleans, numbers)
     */
    private static function parseValue($value)
    {
        // Boolean values
        if (strtolower($value) === 'true') {
            return true;
        }
        if (strtolower($value) === 'false') {
            return false;
        }

        // Null value
        if (strtolower($value) === 'null') {
            return null;
        }

        // Numeric values
        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float) $value : (int) $value;
        }

        return $value;
    }

    /**
     * Check if running in production environment
     */
    public static function isProduction()
    {
        return self::get('APP_ENV') === 'production';
    }

    /**
     * Check if debug mode is enabled
     */
    public static function isDebug()
    {
        return self::get('APP_DEBUG', false);
    }

    /**
     * Generate a secure random key for JWT or encryption
     */
    public static function generateSecureKey($length = 64)
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Validate required environment variables
     */
    public static function validateRequired($required_vars)
    {
        $missing = [];

        foreach ($required_vars as $var) {
            if (self::get($var) === null) {
                $missing[] = $var;
            }
        }

        if (!empty($missing)) {
            throw new Exception('Missing required environment variables: ' . implode(', ', $missing));
        }
    }
}

// Auto-load environment on include
EnvLoader::load();
?>