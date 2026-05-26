<?php
/**
 * Proteksi halaman: wajib login sebelum akses CRUD.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['username']) && !empty($_COOKIE['remember_token'])) {
    require_once ROOT_PATH . '/config/database.php';

    $token = $_COOKIE['remember_token'];
    $stmt = $pdo->prepare('SELECT id, username FROM users WHERE remember_token = :token LIMIT 1');
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['username'] = (string)$user['username'];
    }
}

if (empty($_SESSION['username'])) {
    $tujuan = $_SERVER['REQUEST_URI'] ?? base_url('index.php');
    header('Location: ' . base_url('login.php') . '?pesan=' . urlencode('Silakan login terlebih dahulu.') . '&tujuan=' . urlencode($tujuan));
    exit;
}
