<?php
/**
 * Secure Database Connection Manager
 * Uses environment variables and provides enhanced security
 */

require_once __DIR__ . '/env.php';

class DatabaseConnection
{
    private static $connection = null;
    private static $config = [];

    /**
     * Get database configuration from environment
     */
    private static function getConfig()
    {
        if (empty(self::$config)) {
            // Ensure environment variables are loaded
            EnvLoader::load();

            self::$config = [
                'host' => EnvLoader::get('DB_HOST', 'localhost'),
                'username' => EnvLoader::get('DB_USER', 'root'),
                'password' => EnvLoader::get('DB_PASSWORD', ''),
                'database' => EnvLoader::get('DB_NAME', 'seblak_app'),
                'port' => EnvLoader::get('DB_PORT', 3306),
                'charset' => 'utf8mb4'
            ];

            // Validate required configuration
            if (empty(self::$config['database'])) {
                throw new Exception('Database name (DB_NAME) is required');
            }
        }

        return self::$config;
    }

    /**
     * Get database connection (singleton pattern)
     */
    public static function getInstance()
    {
        if (self::$connection === null) {
            self::connect();
        }

        // Check if connection is still alive
        if (!mysqli_ping(self::$connection)) {
            self::connect();
        }

        return self::$connection;
    }

    /**
     * Create database connection
     */
    private static function connect()
    {
        $config = self::getConfig();

        try {
            // Create connection
            self::$connection = mysqli_connect(
                $config['host'],
                $config['username'],
                $config['password'],
                $config['database'],
                $config['port']
            );

            // Check connection
            if (!self::$connection) {
                throw new Exception('Database connection failed: ' . mysqli_connect_error());
            }

            // Set charset for security and emoji support
            if (!mysqli_set_charset(self::$connection, $config['charset'])) {
                throw new Exception('Error setting charset: ' . mysqli_error(self::$connection));
            }

            // Set SQL modes for enhanced security
            $sql_mode = "STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO";
            mysqli_query(self::$connection, "SET sql_mode = '$sql_mode'");

            // Set timezone - use offset format for better compatibility
            try {
                $timezone_offset = date('P'); // Get current timezone offset like +07:00
                mysqli_query(self::$connection, "SET time_zone = '$timezone_offset'");
            } catch (Exception $tz_error) {
                // Fallback: don't set timezone if there's an issue
                if (EnvLoader::get('APP_DEBUG')) {
                    error_log("Warning: Could not set MySQL timezone: " . $tz_error->getMessage());
                }
            }

            // Log successful connection in development
            if (EnvLoader::get('APP_ENV') === 'development' && EnvLoader::get('APP_DEBUG')) {
                error_log("Database connected successfully to {$config['database']} on {$config['host']}");
            }

        } catch (Exception $e) {
            // Log connection error
            error_log("Database connection error: " . $e->getMessage());

            // In production, don't reveal database details
            if (EnvLoader::isProduction()) {
                throw new Exception('Database connection failed');
            } else {
                throw $e;
            }
        }
    }

    /**
     * Execute prepared statement safely
     */
    public static function executeQuery($query, $types = '', $params = [])
    {
        $conn = self::getInstance();

        if (empty($params)) {
            return mysqli_query($conn, $query);
        }

        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . mysqli_error($conn));
        }

        if (!empty($types) && !empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        $result = mysqli_stmt_execute($stmt);
        if (!$result) {
            $error = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            throw new Exception('Execute failed: ' . $error);
        }

        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }

    /**
     * Get database configuration info (for debugging)
     */
    public static function getConnectionInfo()
    {
        $config = self::getConfig();

        return [
            'host' => $config['host'],
            'database' => $config['database'],
            'port' => $config['port'],
            'charset' => $config['charset'],
            'connected' => self::$connection !== null && mysqli_ping(self::$connection)
        ];
    }

    /**
     * Test database connection
     */
    public static function testConnection()
    {
        try {
            $conn = self::getInstance();
            $result = mysqli_query($conn, "SELECT 1 as test");

            if ($result) {
                $row = mysqli_fetch_assoc($result);
                // Use loose comparison since MySQL returns string values
                return $row['test'] == 1;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Close database connection
     */
    public static function close()
    {
        if (self::$connection) {
            mysqli_close(self::$connection);
            self::$connection = null;
        }
    }
}

// For backward compatibility, create the global $koneksi variable
$koneksi = DatabaseConnection::getInstance();
?>