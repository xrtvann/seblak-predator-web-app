<?php
session_start();

// Destroy all session data
session_destroy();

// Clear remember me cookie if exists
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

// Redirect to login page
header('Location: ../pages/auth/login.php?logout=1');
exit();
?>