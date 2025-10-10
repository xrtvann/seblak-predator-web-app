<?php
/**
 * Database Connection with Environment Variables
 * Secure database connection using .env configuration
 */

require_once __DIR__ . '/database.php';

// Use the new secure database connection
$koneksi = DatabaseConnection::getInstance();

// Verify connection is working
if (!DatabaseConnection::testConnection()) {
    if (!EnvLoader::isProduction()) {
        die("Database connection test failed. Please check your .env configuration.");
    } else {
        die("Database connection failed.");
    }
}

/**
 * Execute prepared statement with parameters
 * 
 * @param mysqli $connection Database connection
 * @param string $query SQL query with placeholders
 * @param string $types Parameter types (s=string, i=integer, d=double, b=blob)
 * @param array $params Array of parameters
 * @return mysqli_result|bool Result or false on failure
 */
function executePreparedStatement($connection, $query, $types = '', $params = [])
{
    try {
        $stmt = mysqli_prepare($connection, $query);

        if (!$stmt) {
            logSecurityEvent('PREPARED_STATEMENT_FAILED', [
                'error' => mysqli_error($connection),
                'query' => $query
            ]);
            return false;
        }

        if (!empty($types) && !empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    } catch (Exception $e) {
        logSecurityEvent('PREPARED_STATEMENT_EXCEPTION', [
            'error' => $e->getMessage(),
            'query' => $query
        ]);
        return false;
    }
}

/**
 * Execute INSERT/UPDATE/DELETE prepared statement
 * 
 * @param mysqli $connection Database connection
 * @param string $query SQL query with placeholders
 * @param string $types Parameter types
 * @param array $params Array of parameters
 * @return bool Success status
 */
function executeUpdate($connection, $query, $types = '', $params = [])
{
    try {
        $stmt = mysqli_prepare($connection, $query);

        if (!$stmt) {
            logSecurityEvent('UPDATE_STATEMENT_FAILED', [
                'error' => mysqli_error($connection),
                'query' => $query
            ]);
            return false;
        }

        if (!empty($types) && !empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    } catch (Exception $e) {
        logSecurityEvent('UPDATE_STATEMENT_EXCEPTION', [
            'error' => $e->getMessage(),
            'query' => $query
        ]);
        return false;
    }
}

/**
 * Get last insert ID
 */
function getLastInsertId($connection)
{
    return mysqli_insert_id($connection);
}

/**
 * Get affected rows
 */
function getAffectedRows($connection)
{
    return mysqli_affected_rows($connection);
}

/**
 * Begin transaction
 */
function beginTransaction($connection)
{
    return mysqli_begin_transaction($connection);
}

/**
 * Commit transaction
 */
function commitTransaction($connection)
{
    return mysqli_commit($connection);
}

/**
 * Rollback transaction
 */
function rollbackTransaction($connection)
{
    return mysqli_rollback($connection);
}
?>