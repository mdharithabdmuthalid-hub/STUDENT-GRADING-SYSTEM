<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (
    is_string($username)
    && is_string($password)
    && hash_equals('admin', $username)
    && hash_equals('admin123', $password)
) {
    session_regenerate_id(true);
    $_SESSION = [];
    $_SESSION['authenticated'] = true;
    $_SESSION['login_success'] = true;
    header('Location: index.php');
    exit;
}

$_SESSION['login_error'] = true;
header('Location: login.php');
exit;
