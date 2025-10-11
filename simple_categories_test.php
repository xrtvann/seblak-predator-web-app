<?php
// Simple test to get categories from database
require_once 'config/koneksi.php';

header('Content-Type: application/json');

try {
    $query = "SELECT id, name, type, is_active, created_at, updated_at 
              FROM categories 
              ORDER BY type, name";

    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        throw new Exception('Database query failed: ' . mysqli_error($koneksi));
    }

    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $categories,
        'total' => count($categories),
        'message' => 'Categories retrieved successfully'
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>