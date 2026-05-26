<?php
require_once dirname(__DIR__) . '/config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . base_url('login.php'));
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = (string)($_POST['password'] ?? '');
$remember = isset($_POST['remember']);
$tujuan   = trim($_POST['tujuan'] ?? '');

if ($username === '' || $password === '') {
    header('Location: ' . base_url('login.php') . '?pesan=' . urlencode('Username dan password wajib diisi.'));
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    header('Location: ' . base_url('login.php') . '?pesan=' . urlencode('Tabel users belum siap. Jalankan SQL pembuatan tabel users terlebih dahulu.'));
    exit;
}

$valid = false;
if ($user) {
    $hash = (string)($user['password'] ?? '');
    if ($hash !== '' && password_verify($password, $hash)) {
        $valid = true;
    } elseif (hash_equals($hash, $password)) {
        $valid = true;
    }
}

if (!$valid) {
    header('Location: ' . base_url('login.php') . '?pesan=' . urlencode('Username atau password salah.'));
    exit;
}

session_regenerate_id(true);
$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['username'] = (string)$user['username'];

if ($remember) {
    $token = bin2hex(random_bytes(32));
    $stmt = $pdo->prepare('UPDATE users SET remember_token = :token WHERE id = :id');
    $stmt->execute(['token' => $token, 'id' => $user['id']]);
    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
}

if ($tujuan !== '' && isset($tujuan[0]) && $tujuan[0] === '/') {
    header('Location: ' . $tujuan);
    exit;
}

header('Location: ' . base_url('index.php'));
exit;
