<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

function require_admin(): void {
    if (empty($_SESSION['admin_id'])) redirect('login.php');
}
