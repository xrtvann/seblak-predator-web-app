<?php
require_once 'config/koneksi.php';

header('Content-Type: application/json');

try {
    if ($koneksi) {
        echo json_encode(['success' => true, 'message' => 'Database connected']);

        // Check if categories table exists and has data
        $query = "SELECT COUNT(*) as count FROM categories";
        $result = mysqli_query($koneksi, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            echo json_encode(['success' => true, 'message' => 'Categories table exists', 'count' => $row['count']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Query error: ' . mysqli_error($koneksi)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>