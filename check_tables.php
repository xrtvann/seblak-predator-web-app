<?php
/**
 * Check Database Tables
 * Verify if required tables exist for registration
 */

require_once 'config/env.php';
require_once 'config/database.php';

echo "📊 Database Tables Check\n";
echo "========================\n\n";

try {
    EnvLoader::load();
    $conn = DatabaseConnection::getInstance();

    echo "1. Checking available tables...\n";
    $result = DatabaseConnection::executeQuery('SHOW TABLES');

    $tables = [];
    while ($row = $result->fetch_assoc()) {
        $table_name = $row['Tables_in_seblak_app'];
        $tables[] = $table_name;
        echo "   ✅ Table: $table_name\n";
    }

    echo "\n2. Checking required tables for registration...\n";

    // Check if users table exists
    if (in_array('users', $tables)) {
        echo "   ✅ users table: EXISTS\n";

        // Check users table structure
        $result = DatabaseConnection::executeQuery('DESCRIBE users');
        echo "   📋 Users table columns:\n";
        while ($row = $result->fetch_assoc()) {
            echo "      - {$row['Field']} ({$row['Type']})\n";
        }
    } else {
        echo "   ❌ users table: MISSING\n";
    }

    // Check if roles table exists
    if (in_array('roles', $tables)) {
        echo "   ✅ roles table: EXISTS\n";

        // Check roles table structure
        $result = DatabaseConnection::executeQuery('DESCRIBE roles');
        echo "   📋 Roles table columns:\n";
        while ($row = $result->fetch_assoc()) {
            echo "      - {$row['Field']} ({$row['Type']})\n";
        }

        // Check if default roles exist
        $result = DatabaseConnection::executeQuery('SELECT * FROM roles');
        echo "   📋 Existing roles:\n";
        while ($row = $result->fetch_assoc()) {
            echo "      - {$row['name']} (ID: {$row['id']})\n";
        }

    } else {
        echo "   ❌ roles table: MISSING\n";
    }

    echo "\n==================================================\n";
    echo "🎉 DATABASE CHECK COMPLETE\n";
    echo "==================================================\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
}
?>