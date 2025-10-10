<?php
/**
 * Database Troubleshooting Script
 * Check MySQL connection and available databases
 */

require_once 'config/env.php';

echo "🔧 Database Troubleshooting\n";
echo "==========================\n\n";

try {
    // Load environment
    EnvLoader::load();

    $host = EnvLoader::get('DB_HOST');
    $user = EnvLoader::get('DB_USER');
    $password = EnvLoader::get('DB_PASSWORD');
    $database = EnvLoader::get('DB_NAME');
    $port = EnvLoader::get('DB_PORT');

    echo "1. Environment Configuration:\n";
    echo "   Host: $host\n";
    echo "   User: $user\n";
    echo "   Password: " . (empty($password) ? '(empty)' : '(set)') . "\n";
    echo "   Database: $database\n";
    echo "   Port: $port\n\n";

    echo "2. Testing MySQL Connection (without database):\n";

    // Try to connect to MySQL server without specifying database
    $connection = mysqli_connect($host, $user, $password, null, $port);

    if ($connection) {
        echo "   ✅ MySQL server connection: SUCCESSFUL\n";

        echo "\n3. Checking Available Databases:\n";
        $result = mysqli_query($connection, "SHOW DATABASES");
        if ($result) {
            echo "   Available databases:\n";
            while ($row = mysqli_fetch_assoc($result)) {
                $db_name = $row['Database'];
                echo "   - $db_name";
                if ($db_name === $database) {
                    echo " ← Target database ✅";
                }
                echo "\n";
            }
        }

        echo "\n4. Testing Target Database Connection:\n";
        $db_connection = mysqli_connect($host, $user, $password, $database, $port);

        if ($db_connection) {
            echo "   ✅ Target database connection: SUCCESSFUL\n";

            // Test a simple query
            $test_result = mysqli_query($db_connection, "SELECT 1 as test");
            if ($test_result) {
                echo "   ✅ Database query test: SUCCESSFUL\n";
            } else {
                echo "   ❌ Database query test: FAILED\n";
                echo "   Error: " . mysqli_error($db_connection) . "\n";
            }

            mysqli_close($db_connection);
        } else {
            echo "   ❌ Target database connection: FAILED\n";
            echo "   Error: " . mysqli_connect_error() . "\n";

            echo "\n5. Attempting to Create Database:\n";
            $create_result = mysqli_query($connection, "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            if ($create_result) {
                echo "   ✅ Database '$database' created successfully\n";
                echo "   📝 Try running the application again\n";
            } else {
                echo "   ❌ Failed to create database: " . mysqli_error($connection) . "\n";
            }
        }

        mysqli_close($connection);

    } else {
        echo "   ❌ MySQL server connection: FAILED\n";
        echo "   Error: " . mysqli_connect_error() . "\n";

        echo "\n🔧 TROUBLESHOOTING STEPS:\n";
        echo "1. Check if Laragon is running\n";
        echo "2. Verify MySQL service is started\n";
        echo "3. Check MySQL port (default: 3306)\n";
        echo "4. Verify MySQL user/password in .env\n";
    }

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>