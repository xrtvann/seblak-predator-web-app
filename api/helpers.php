<?php
/**
 * API Helper Functions for Seblak Predator Restaurant Management System
 * Contains common functions for validation, authentication, and response formatting
 */

/**
 * Validate JSON input and return decoded data
 * @param string $input Raw JSON input
 * @return array|false Decoded data or false on error
 */
function validateJsonInput($input = null)
{
    if ($input === null) {
        $input = file_get_contents('php://input');
    }

    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return false;
    }

    return $data;
}

/**
 * Send standardized JSON response
 * @param bool $success Success status
 * @param string $message Response message
 * @param mixed $data Response data
 * @param array $meta Additional metadata
 * @param int $httpCode HTTP status code
 */
function sendJsonResponse($success, $message, $data = null, $meta = null, $httpCode = 200)
{
    http_response_code($httpCode);

    $response = [
        'success' => $success,
        'message' => $message
    ];

    if ($data !== null) {
        $response['data'] = $data;
    }

    if ($meta !== null) {
        $response['meta'] = $meta;
    }

    echo json_encode($response);
    exit;
}

/**
 * Validate required fields in input data
 * @param array $input Input data
 * @param array $required Required field names
 * @return string|null Error message or null if valid
 */
function validateRequiredFields($input, $required)
{
    foreach ($required as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            return ucfirst($field) . ' is required';
        }
    }
    return null;
}

/**
 * Sanitize string input
 * @param string $input Input string
 * @param mixed $connection MySQL connection
 * @return string Sanitized string
 */
function sanitizeString($input, $connection)
{
    return mysqli_real_escape_string($connection, trim($input));
}

/**
 * Generate unique ID with prefix
 * @param string $prefix ID prefix
 * @return string Generated ID
 */
function generateId($prefix = '')
{
    return $prefix . uniqid();
}

/**
 * Validate price value
 * @param mixed $price Price value
 * @return array ['valid' => bool, 'value' => float, 'error' => string]
 */
function validatePrice($price)
{
    $value = floatval($price);

    if ($value < 0) {
        return [
            'valid' => false,
            'value' => 0,
            'error' => 'Price must be non-negative'
        ];
    }

    return [
        'valid' => true,
        'value' => $value,
        'error' => null
    ];
}

/**
 * Check if entity exists in database
 * @param mixed $connection MySQL connection
 * @param string $table Table name
 * @param string $id Entity ID
 * @param string $idColumn ID column name (default: 'id')
 * @param array $additionalConditions Additional WHERE conditions
 * @return bool Entity exists
 */
function entityExists($connection, $table, $id, $idColumn = 'id', $additionalConditions = [])
{
    $conditions = [$idColumn . ' = ?'];
    $params = [$id];
    $types = 's';

    foreach ($additionalConditions as $condition => $value) {
        $conditions[] = $condition;
        $params[] = $value;
        $types .= 's';
    }

    $query = "SELECT 1 FROM {$table} WHERE " . implode(' AND ', $conditions) . " LIMIT 1";
    $stmt = mysqli_prepare($connection, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    }

    return false;
}

/**
 * Build dynamic UPDATE query
 * @param array $fields Fields to update
 * @param array $input Input data
 * @param mixed $connection MySQL connection
 * @return array ['fields' => array, 'types' => string, 'values' => array]
 */
function buildUpdateQuery($fields, $input, $connection)
{
    $updateFields = [];
    $types = '';
    $values = [];

    foreach ($fields as $field => $config) {
        if (isset($input[$field])) {
            $value = $input[$field];

            // Apply field-specific validation/transformation
            switch ($config['type']) {
                case 'string':
                    $value = sanitizeString($value, $connection);
                    $updateFields[] = "{$field} = ?";
                    $types .= 's';
                    $values[] = $value;
                    break;

                case 'float':
                    $value = floatval($value);
                    if (isset($config['min']) && $value < $config['min']) {
                        throw new Exception($config['error'] ?? "{$field} value is invalid");
                    }
                    $updateFields[] = "{$field} = ?";
                    $types .= 'd';
                    $values[] = $value;
                    break;

                case 'boolean':
                    $value = (bool) $value ? 1 : 0;
                    $updateFields[] = "{$field} = ?";
                    $types .= 'i';
                    $values[] = $value;
                    break;

                case 'enum':
                    if (!in_array($value, $config['values'])) {
                        throw new Exception($config['error'] ?? "Invalid {$field} value");
                    }
                    $updateFields[] = "{$field} = ?";
                    $types .= 's';
                    $values[] = $value;
                    break;
            }
        }
    }

    return [
        'fields' => $updateFields,
        'types' => $types,
        'values' => $values
    ];
}

/**
 * Log API request for debugging
 * @param string $endpoint Endpoint name
 * @param string $method HTTP method
 * @param array $input Request data
 * @param bool $success Request success status
 */
function logApiRequest($endpoint, $method, $input = [], $success = true)
{
    $log = [
        'timestamp' => date('Y-m-d H:i:s'),
        'endpoint' => $endpoint,
        'method' => $method,
        'input' => $input,
        'success' => $success,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];

    // Log to file or database as needed
    error_log("API Request: " . json_encode($log));
}

/**
 * Setup CORS headers for API endpoints
 */
function setupCorsHeaders()
{
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    // Handle preflight OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit(0);
    }
}

/**
 * Simple JWT token validation (for future implementation)
 * @param string $token JWT token
 * @return bool Token is valid
 */
function validateToken($token)
{
    // TODO: Implement JWT validation
    // For now, return true for development
    return true;
}
?>