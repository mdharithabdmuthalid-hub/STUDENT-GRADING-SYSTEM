<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/includes/csrf.php';
$csrfToken = $_POST['csrf_token'] ?? '';

if (!csrf_token_is_valid($csrfToken)) {
    http_response_code(400);
    exit('Invalid logout request. Return to the results page and try again.');
}

$_SESSION = [];
session_regenerate_id(true);
$_SESSION['login_logged_out'] = true;

header('Location: login.php');
exit;
