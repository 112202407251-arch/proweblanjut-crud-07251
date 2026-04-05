<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hapus token di database jika sedang login
if (!empty($_SESSION['user_id'])) {
    require_once __DIR__ . '/koneksi.php';
    $stmt = $pdo->prepare('UPDATE users SET remember_token = NULL WHERE id = :id');
    $stmt->execute(['id' => $_SESSION['user_id']]);
}

// Hapus cookie remember_token
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

header('Location: login.php?pesan=' . urlencode('Anda sudah logout.'));
exit;

