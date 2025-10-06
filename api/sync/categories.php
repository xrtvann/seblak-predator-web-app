<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/koneksi.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $since = $_GET['since'] ?? '1970-01-01 00:00:00';

    // Validate timestamp format
    $timestamp = DateTime::createFromFormat('Y-m-d H:i:s', $since);
    if (!$timestamp) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid timestamp format. Use YYYY-MM-DD HH:MM:SS']);
        exit;
    }

    $query = "SELECT id, name, type, is_active, created_at, updated_at 
              FROM categories 
              WHERE updated_at > ? 
              ORDER BY updated_at";

    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $since);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        throw new Exception('Database query failed: ' . mysqli_error($koneksi));
    }

    $categories = [];
    $lastSync = $since;

    while ($row = mysqli_fetch_assoc($result)) {
        $row['is_active'] = (bool) $row['is_active'];
        $categories[] = $row;
        $lastSync = max($lastSync, $row['updated_at']);
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $categories,
        'last_sync' => $lastSync,
        'sync_timestamp' => date('Y-m-d H:i:s'),
        'total' => count($categories),
        'message' => 'Categories synchronized successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error: ' . $e->getMessage()
    ]);
}
?>