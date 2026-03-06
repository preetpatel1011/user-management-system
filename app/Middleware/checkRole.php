<?php
if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'] ?? null;

    if ($role === 'admin') {
        header('Location: /admin/dashboard');
    } else {
        header('Location: /dashboard');
    }
    exit;
}