<?php

function require_login(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['authenticated'])) {
        header('Location: login.php');
        exit;
    }
}
