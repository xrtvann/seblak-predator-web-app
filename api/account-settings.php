<?php
/**
 * Account Settings API
 * Handle profile update and password change
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/koneksi.php';

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized. Please login first.'
    ]);
    exit;
}

$current_user = getCurrentSessionUser();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'PUT':
            // Update profile information
            updateProfile($koneksi, $current_user);
            break;

        case 'PATCH':
            // Change password
            changePassword($koneksi, $current_user);
            break;

        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

/**
 * Update user profile
 */
function updateProfile($koneksi, $current_user)
{
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['name']) || !isset($input['email'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Name and email are required'
        ]);
        return;
    }

    $name = trim($input['name']);
    $email = trim($input['email']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email format'
        ]);
        return;
    }

    // Check if email already exists for another user
    $stmt = mysqli_prepare($koneksi, "SELECT id FROM users WHERE email = ? AND id != ?");
    mysqli_stmt_bind_param($stmt, "ss", $email, $current_user['id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Email sudah digunakan oleh user lain'
        ]);
        return;
    }

    // Update user profile
    $stmt = mysqli_prepare($koneksi, "UPDATE users SET name = ?, email = ?, updated_at = NOW() WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $current_user['id']);

    if (mysqli_stmt_execute($stmt)) {
        // Update session data
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => [
                'name' => $name,
                'email' => $email
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Gagal memperbarui profil'
        ]);
    }
}

/**
 * Change user password
 */
function changePassword($koneksi, $current_user)
{
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['current_password']) || !isset($input['new_password'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Current password and new password are required'
        ]);
        return;
    }

    $currentPassword = $input['current_password'];
    $newPassword = $input['new_password'];

    // Validate new password length
    if (strlen($newPassword) < 6) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Password baru minimal 6 karakter'
        ]);
        return;
    }

    // Get current password hash from database
    $stmt = mysqli_prepare($koneksi, "SELECT password_hash FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "s", $current_user['id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'User tidak ditemukan'
        ]);
        return;
    }

    // Verify current password
    if (!password_verify($currentPassword, $user['password_hash'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Password lama tidak sesuai'
        ]);
        return;
    }

    // Hash new password
    $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update password
    $stmt = mysqli_prepare($koneksi, "UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ss", $newPasswordHash, $current_user['id']);

    if (mysqli_stmt_execute($stmt)) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Password berhasil diubah'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Gagal mengubah password'
        ]);
    }
}
