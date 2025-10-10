<?php
// Initialize secure session and authentication
require_once '../config/session.php';
require_once '../services/WebAuthService.php';

// Initialize authentication service
$auth_service = new WebAuthService($koneksi);

// Handle logout request
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Validate CSRF token for POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('error', 'Invalid security token.');
            header('Location: ../index.php');
            exit();
        }
    }

    // Perform logout
    $result = $auth_service->logout();

    if ($result['success']) {
        setFlashMessage('success', 'You have been logged out successfully.');
    } else {
        setFlashMessage('error', 'An error occurred during logout.');
    }

    // Redirect to login page
    header('Location: ../pages/auth/login.php');
    exit();
}

// If not a valid request method, redirect to dashboard
header('Location: ../index.php');
exit();
?>