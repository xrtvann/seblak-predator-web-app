<?php
/**
 * Test Database Connection
 * Quick test to verify database connection and timezone setting
 */

require_once 'config/env.php';
require_once 'config/database.php';

echo "🔧 Database Connection Test\n";
echo "===========================\n\n";

try {
    // Load environment
    EnvLoader::load();

    echo "1. Environment Status:\n";
    echo "   ✅ Environment loaded successfully\n";
    echo "   📍 Database: " . EnvLoader::get('DB_NAME') . "\n";
    echo "   🖥️  Host: " . EnvLoader::get('DB_HOST') . "\n\n";

    echo "2. Database Connection Test:\n";

    // Test database connection
    $db = DatabaseConnection::getInstance();
    echo "   ✅ Database connection successful\n";

    // Test timezone setting
    $result = DatabaseConnection::executeQuery("SELECT @@session.time_zone AS mysql_timezone");
    if ($result && $row = $result->fetch_assoc()) {
        echo "   ✅ MySQL timezone: " . $row['mysql_timezone'] . "\n";
    }

    $result = DatabaseConnection::executeQuery("SELECT NOW() AS current_datetime");
    if ($result && $row = $result->fetch_assoc()) {
        echo "   🕐 Current MySQL time: " . $row['current_datetime'] . "\n";
    }

    // Test PHP timezone
    echo "   🕐 PHP timezone: " . date_default_timezone_get() . "\n";
    echo "   🕐 PHP time: " . date('Y-m-d H:i:s') . "\n";
    echo "   🕐 PHP offset: " . date('P') . "\n\n";

    echo "3. Database Query Test:\n";

    // Test a simple query to make sure everything works
    $result = DatabaseConnection::executeQuery("SELECT 1 as test_value, 'Hello Database!' as test_message");
    if ($result && $row = $result->fetch_assoc()) {
        echo "   ✅ Query test successful\n";
        echo "   📄 Test value: " . $row['test_value'] . "\n";
        echo "   💬 Test message: " . $row['test_message'] . "\n";
    } else {
        echo "   ❌ Query test failed\n";
    }

    echo "\n==================================================\n";
    echo "🎉 DATABASE CONNECTION: SUCCESSFUL\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";

    echo "\n🔧 TROUBLESHOOTING:\n";
    echo "1. Check if Laragon MySQL is running\n";
    echo "2. Verify database exists: " . EnvLoader::get('DB_NAME') . "\n";
    echo "3. Check database credentials in .env file\n";
    echo "4. Ensure MySQL user has proper permissions\n";
}
?>