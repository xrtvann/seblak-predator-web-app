<?php
// Initialize secure session and authentication
require_once '../config/session.php';
require_once '../config/koneksi.php';
require_once '../services/WebAuthService.php';

// Initialize authentication service
$auth_service = new WebAuthService($koneksi);

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get action first to determine redirect location
    $action = isset($_POST['action']) ? $_POST['action'] : 'login';

    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid security token. Please try again.');

        // Redirect based on action
        if ($action === 'register') {
            header('Location: ../pages/auth/register.php');
        } else {
            header('Location: ../pages/auth/login.php');
        }
        exit();
    }

    // Handle different actions
    if ($action === 'register') {
        Register($koneksi);
    } elseif ($action === 'login') {
        // Get form data
        $username = trim($_POST['field_username'] ?? '');
        $password = $_POST['field_password'] ?? '';
        $remember_me = isset($_POST['remember_me']) && $_POST['remember_me'];

        // Validate input
        if (empty($username) || empty($password)) {
            setFlashMessage('error', 'Username and password are required.');
            header('Location: ../pages/auth/login.php');
            exit();
        }

        // Attempt login
        $result = $auth_service->login($username, $password, $remember_me);

        if ($result['success']) {
            setFlashMessage('success', 'Login successful! Welcome back.');

            // Check for redirect URL
            $redirect_url = $_SESSION['redirect_after_login'] ?? '../index.php?page=dashboard';
            unset($_SESSION['redirect_after_login']);

            header('Location: ' . $redirect_url);
            exit();
        } else {
            // Handle specific error cases
            switch ($result['code'] ?? '') {
                case 'RATE_LIMITED':
                    setFlashMessage('error', $result['message']);
                    // Add rate limiting parameters to URL for immediate display
                    $url_params = http_build_query([
                        'rate_limited' => '1',
                        'username' => $username ?? '', // Add username for sessionStorage
                        'remaining_seconds' => $result['remaining_seconds'] ?? 0,
                        'remaining_time' => $result['remaining_time_text'] ?? '0:00',
                        'lockout_until_timestamp' => $result['lockout_until_timestamp'] ?? 0 // ✅ FIX: Add timestamp!
                    ]);
                    header('Location: ../pages/auth/login.php?' . $url_params);
                    exit();
                case 'INVALID_CREDENTIALS':
                    setFlashMessage('error', 'Invalid username or password.');
                    break;
                case 'ACCOUNT_DISABLED':
                    setFlashMessage('error', 'Your account has been disabled. Please contact support.');
                    break;
                default:
                    setFlashMessage('error', 'Login failed. Please try again.');
                    break;
            }

            header('Location: ../pages/auth/login.php');
            exit();
        }
    } else {
        // Unknown action
        setFlashMessage('error', 'Invalid action.');
        header('Location: ../pages/auth/login.php');
        exit();
    }
}

// If not POST request, redirect to login
header('Location: ../pages/auth/login.php');
exit();

function Register($koneksi)
{
    $name = htmlspecialchars($_POST['field_name']);
    $username = htmlspecialchars($_POST['field_username']);
    $email = htmlspecialchars($_POST['field_email']);
    $password = htmlspecialchars($_POST['field_password']);

    // Validate input
    if (empty($name) || empty($username) || empty($email) || empty($password)) {
        setFlashMessage('error', 'All fields are required.');
        header('Location: ../pages/auth/register.php');
        exit();
    }

    // Check if username already exists
    $sql = "SELECT id FROM users WHERE username = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        setFlashMessage('error', 'Username already exists. Please choose a different username.');
        header('Location: ../pages/auth/register.php');
        exit();
    }
    mysqli_stmt_close($stmt);

    // Check if email already exists
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        setFlashMessage('error', 'Email already exists. Please use a different email address.');
        header('Location: ../pages/auth/register.php');
        exit();
    }
    mysqli_stmt_close($stmt);

    // Get or create 'Customer' role (default role for new users)
    $role_id = null;
    $sql = "SELECT id FROM roles WHERE name = 'Customer' LIMIT 1";
    $result = mysqli_query($koneksi, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        $role_id = $row['id'];
    } else {
        // Use existing role_customer ID since we know it exists from database check
        $role_id = 'role_customer';
    }
    mysqli_free_result($result);

    // Generate unique user ID
    $id = 'user_' . uniqid();
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (id, name, email, username, password_hash, role_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "ssssss", $id, $name, $email, $username, $hashed_password, $role_id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        setFlashMessage('success', 'Account created successfully! You can now log in.');
        header('Location: ../pages/auth/login.php');
        exit();
    } else {
        mysqli_stmt_close($stmt);
        setFlashMessage('error', 'Registration failed. Please try again.');
        header('Location: ../pages/auth/register.php');
        exit();
    }
}