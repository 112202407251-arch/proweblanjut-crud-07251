<?php
require_once dirname(__DIR__) . '/config/bootstrap.php';

if (!empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('UPDATE users SET remember_token = NULL WHERE id = :id');
    $stmt->execute(['id' => $_SESSION['user_id']]);
}

if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

session_destroy();

header('Location: ' . base_url('login.php') . '?pesan=' . urlencode('Anda sudah logout.'));
exit;
