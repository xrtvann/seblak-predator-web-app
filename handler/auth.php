<?php
session_start();
require_once '../config/koneksi.php';

function Register($koneksi)
{
    $name = htmlspecialchars($_POST['field_name']);
    $username = htmlspecialchars($_POST['field_username']);
    $email = htmlspecialchars($_POST['field_email']);
    $password = htmlspecialchars($_POST['field_password']);

    $sql = "SELECT id FROM users WHERE username = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        return false;
    }
    mysqli_stmt_close($stmt);


    $role_id = null;
    $sql = "SELECT id FROM roles WHERE name = 'user' LIMIT 1";
    $result = mysqli_query($koneksi, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        $role_id = $row['id'];
    } else {
        $sql_insert_role = "INSERT INTO roles (name) VALUES ('user')";
        mysqli_query($koneksi, $sql_insert_role);
        $role_id = mysqli_insert_id($koneksi);
    }
    mysqli_free_result($result);


    $id = uniqid();
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (id, name, email, username, password_hash, role_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "ssssss", $id, $name, $email, $username, $hashed_password, $role_id);


    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header('Location: ../pages/auth/login.php?success=1');
        exit();
    } else {
        mysqli_stmt_close($stmt);
        return false;
    }
}

function Login($koneksi)
{
    $username = htmlspecialchars($_POST['field_username']);
    $password = htmlspecialchars($_POST['field_password']);
    $remember_me = isset($_POST['remember_me']) ? true : false;

    // Validasi input
    if (empty($username) || empty($password)) {
        header('Location: ../pages/auth/login.php?error=empty_fields');
        exit();
    }

    $checkUserSql = "SELECT id, name, username, password_hash, role_id FROM users WHERE username = ? LIMIT 1";
    $stmt = mysqli_prepare($koneksi, $checkUserSql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    $verifiedPassword = password_verify($password, $user['password_hash']);

    if ($user['username'] && $verifiedPassword) {
        header('Location: ../index.php');
    } else {
        header('Location: ../pages/auth/login.php?error=invalid_credentials');
        exit();
    }

}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    if ($action === 'register') {
        Register($koneksi);
    } elseif ($action === 'login') {
        Login($koneksi);
    } else {
        return false;
    }
}