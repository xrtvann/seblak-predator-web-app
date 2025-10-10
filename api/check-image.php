<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Get the image filename from query parameter
$filename = $_GET['filename'] ?? '';

if (empty($filename)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Filename parameter is required'
    ]);
    exit;
}

// Sanitize filename to prevent directory traversal
$filename = basename($filename);

// Define the upload directory
$uploadDir = '../uploads/menu-images/';
$filePath = $uploadDir . $filename;

// Check if file exists and is readable
$exists = file_exists($filePath) && is_readable($filePath);

// Get file info if exists
$fileInfo = [];
if ($exists) {
    $fileInfo = [
        'size' => filesize($filePath),
        'modified' => filemtime($filePath),
        'mime_type' => mime_content_type($filePath)
    ];
}

echo json_encode([
    'success' => true,
    'exists' => $exists,
    'filename' => $filename,
    'path' => 'uploads/menu-images/' . $filename,
    'file_info' => $fileInfo
]);
?>