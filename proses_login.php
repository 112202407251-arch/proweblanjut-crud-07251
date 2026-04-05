<?php
require_once __DIR__ . '/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = (string)($_POST['password'] ?? '');
$remember = isset($_POST['remember']);
$tujuan   = trim($_POST['tujuan'] ?? '');

if ($username === '' || $password === '') {
    header('Location: login.php?pesan=' . urlencode('Username dan password wajib diisi.'));
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    header('Location: login.php?pesan=' . urlencode('Tabel users belum siap. Jalankan SQL pembuatan tabel users terlebih dahulu.'));
    exit;
}

$valid = false;
if ($user) {
    $hash = (string)($user['password'] ?? '');

    // Mendukung password yang disimpan sebagai hash (disarankan),
    // dan fallback untuk password plaintext (jika tabel masih sederhana).
    if ($hash !== '' && password_verify($password, $hash)) {
        $valid = true;
    } elseif (hash_equals($hash, $password)) {
        $valid = true;
    }
}

if (!$valid) {
    header('Location: login.php?pesan=' . urlencode('Username atau password salah.'));
    exit;
}

session_regenerate_id(true);
$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['username'] = (string)$user['username'];

// Logic Remember Me
if ($remember) {
    // Generate token acak yang unik
    $token = bin2hex(random_bytes(32));
    
    // Simpan token ke database
    $stmt = $pdo->prepare('UPDATE users SET remember_token = :token WHERE id = :id');
    $stmt->execute([
        'token' => $token,
        'id' => $user['id']
    ]);
    
    // Simpan token di cookie selama 30 hari (30 * 24 * 60 * 60 detik)
    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
}

if ($tujuan !== '' && isset($tujuan[0]) && $tujuan[0] === '/') {
    header('Location: ' . $tujuan);
    exit;
}

header('Location: index.php');
exit;

