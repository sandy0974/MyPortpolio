<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

function is_admin_logged_in(): bool {
    return !empty($_SESSION['admin_id']);
}

function current_user(): ?array {
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    return [
        'id' => (int) $_SESSION['user_id'],
        'username' => $_SESSION['user_username'] ?? '',
        'name' => $_SESSION['user_name'] ?? 'User',
    ];
}

function current_user_profile(): ?array {
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $stmt = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([(int) $_SESSION['user_id']]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function require_admin(): void {
    if (empty($_SESSION['admin_id'])) redirect('login.php');
}

function require_user(): void {
    if (empty($_SESSION['user_id'])) redirect('login.php');
}

function logout_current_user(): void {
    unset($_SESSION['user_id'], $_SESSION['user_username'], $_SESSION['user_name'], $_SESSION['user_email']);
    session_regenerate_id(true);
}

function logout_current_admin(): void {
    unset($_SESSION['admin_id'], $_SESSION['admin_username']);
    session_regenerate_id(true);
}
