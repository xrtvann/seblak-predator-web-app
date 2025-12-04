<?php
/**
 * Mobile API: Update User Profile
 */


header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

require_once __DIR__ . '/middleware.php';

try {
    $user = JWTMiddleware::authenticate(); // semua user mobile boleh update
    if (!$user) exit;

    require_once __DIR__ . '/../../config/koneksi.php';

    // Terima JSON
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON input'
        ]);
        exit;
    }

    $name = $input['name'] ?? null;
    $username = $input['username'] ?? null;
    $email = $input['email'] ?? null;
    $phone = $input['phone_number'] ?? null;

    // Update database
    $stmt = mysqli_prepare($koneksi, "
        UPDATE users 
        SET name = ?, username = ?, email = ?, phone_number = ?
        WHERE id = ?
    ");

    mysqli_stmt_bind_param($stmt, "sssss", 
        $name, $username, $email, $phone, $user['user_id']
    );

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update profile'
        ]);
    }

    mysqli_stmt_close($stmt);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ]);
}
