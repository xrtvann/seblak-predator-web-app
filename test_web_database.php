<?php
/**
 * Web Database Connection Test
 * Test database connection in web context (like how browser accesses it)
 */

header('Content-Type: text/html; charset=utf-8');

echo "<h2>🔧 Web Database Connection Test</h2>";
echo "<hr>";

try {
    echo "<p><strong>1. Testing Environment Loading...</strong></p>";

    // This simulates what happens when the web page loads
    require_once 'config/env.php';
    require_once 'config/database.php';

    echo "<p>✅ Environment and database classes loaded</p>";

    echo "<p><strong>2. Testing Database Connection...</strong></p>";

    // Test database connection (this is what fails in koneksi.php)
    $test_result = DatabaseConnection::testConnection();

    if ($test_result) {
        echo "<p>✅ Database connection test: PASSED</p>";

        // Get the actual connection
        $koneksi = DatabaseConnection::getInstance();
        echo "<p>✅ Database instance: SUCCESSFUL</p>";

        // Test a simple query
        $result = DatabaseConnection::executeQuery("SELECT 1 as test, NOW() as current_datetime");
        if ($result && $row = $result->fetch_assoc()) {
            echo "<p>✅ Database query test: SUCCESSFUL</p>";
            echo "<p>📄 Test value: " . $row['test'] . "</p>";
            echo "<p>🕐 Current time: " . $row['current_datetime'] . "</p>";
        }

    } else {
        echo "<p>❌ Database connection test: FAILED</p>";
        echo "<p>This is the error that shows 'Database connection test failed. Please check your .env configuration.'</p>";
    }

    echo "<p><strong>3. Environment Variables Check...</strong></p>";
    echo "<p>DB_HOST: " . EnvLoader::get('DB_HOST') . "</p>";
    echo "<p>DB_NAME: " . EnvLoader::get('DB_NAME') . "</p>";
    echo "<p>DB_USER: " . EnvLoader::get('DB_USER') . "</p>";
    echo "<p>DB_PORT: " . EnvLoader::get('DB_PORT') . "</p>";

    echo "<hr>";
    echo "<h3>🎉 WEB DATABASE CONNECTION: SUCCESSFUL</h3>";

} catch (Exception $e) {
    echo "<hr>";
    echo "<h3>❌ ERROR: " . htmlspecialchars($e->getMessage()) . "</h3>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";

    echo "<h4>🔧 TROUBLESHOOTING:</h4>";
    echo "<ul>";
    echo "<li>Check if .env file exists in the root directory</li>";
    echo "<li>Verify database credentials in .env file</li>";
    echo "<li>Ensure Laragon MySQL is running</li>";
    echo "<li>Check if database 'seblak_app' exists</li>";
    echo "</ul>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    h2 {
        color: #333;
    }

    h3 {
        color: #0066cc;
    }

    p {
        margin: 5px 0;
    }

    hr {
        margin: 20px 0;
    }
</style>