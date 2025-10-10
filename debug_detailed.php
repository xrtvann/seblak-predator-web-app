<?php
/**
 * Detailed Database Connection Debug
 * Step-by-step analysis of what happens during connection
 */

echo "🔧 Detailed Database Connection Debug\n";
echo "=====================================\n\n";

try {
    echo "Step 1: Loading environment...\n";
    require_once 'config/env.php';

    echo "Step 2: Calling EnvLoader::load()...\n";
    EnvLoader::load();
    echo "   ✅ Environment loaded\n";

    echo "Step 3: Checking environment variables...\n";
    $host = EnvLoader::get('DB_HOST');
    $database = EnvLoader::get('DB_NAME');
    $user = EnvLoader::get('DB_USER');
    $debug = EnvLoader::get('APP_DEBUG');

    echo "   DB_HOST: $host\n";
    echo "   DB_NAME: $database\n";
    echo "   DB_USER: $user\n";
    echo "   APP_DEBUG: $debug\n";

    echo "\nStep 4: Loading DatabaseConnection class...\n";
    require_once 'config/database.php';
    echo "   ✅ DatabaseConnection class loaded\n";

    echo "\nStep 5: Testing DatabaseConnection::testConnection()...\n";
    $test_result = DatabaseConnection::testConnection();
    echo "   Test result: " . ($test_result ? 'TRUE' : 'FALSE') . "\n";

    if (!$test_result) {
        echo "\nStep 6: Manual connection test...\n";

        // Try direct connection like testConnection does
        try {
            $conn = DatabaseConnection::getInstance();
            echo "   ✅ getInstance() worked\n";

            $result = mysqli_query($conn, "SELECT 1 as test");
            if ($result) {
                echo "   ✅ Query executed successfully\n";
                $row = mysqli_fetch_assoc($result);
                echo "   Test value: " . $row['test'] . "\n";
                echo "   Row test === 1: " . ($row['test'] === 1 ? 'TRUE' : 'FALSE') . "\n";
                echo "   Row test == 1: " . ($row['test'] == 1 ? 'TRUE' : 'FALSE') . "\n";
            } else {
                echo "   ❌ Query failed: " . mysqli_error($conn) . "\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Exception in getInstance(): " . $e->getMessage() . "\n";
        }
    }

    echo "\n==================================================\n";
    echo "🎉 DEBUG COMPLETE\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>