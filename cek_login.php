<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika session kosong, cek apakah ada cookie remember_token
if (empty($_SESSION['username']) && !empty($_COOKIE['remember_token'])) {
    require_once __DIR__ . '/koneksi.php';
    
    $token = $_COOKIE['remember_token'];
    
    // Cari user berdasarkan token
    $stmt = $pdo->prepare('SELECT id, username FROM users WHERE remember_token = :token LIMIT 1');
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Auto-login: set session
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['username'] = (string)$user['username'];
    }
}

if (empty($_SESSION['username'])) {
    $tujuan = $_SERVER['REQUEST_URI'] ?? 'index.php';
    header('Location: login.php?pesan=Silakan%20login%20terlebih%20dahulu.&tujuan=' . urlencode($tujuan));
    exit;
}

