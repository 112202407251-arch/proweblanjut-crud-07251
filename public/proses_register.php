<?php
require_once dirname(__DIR__) . '/config/bootstrap.php';

if (!empty($_SESSION['username'])) {
    header('Location: ' . base_url('index.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . base_url('register.php'));
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = (string)($_POST['password'] ?? '');
$passwordConfirm = (string)($_POST['password_confirm'] ?? '');

$redirectError = function (string $message) use ($username): void {
    header('Location: ' . base_url('register.php') . '?pesan=' . urlencode($message) . '&username=' . urlencode($username));
    exit;
};

if ($username === '' || $password === '' || $passwordConfirm === '') {
    $redirectError('Semua field wajib diisi.');
}

if (mb_strlen($username) < 4) {
    $redirectError('Username minimal 4 karakter.');
}

if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    $redirectError('Username hanya boleh berisi huruf, angka, dan underscore.');
}

if (mb_strlen($password) < 6) {
    $redirectError('Password minimal 6 karakter.');
}

if (!hash_equals($password, $passwordConfirm)) {
    $redirectError('Konfirmasi password tidak cocok.');
}

try {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);
    $exists = $stmt->fetchColumn();
} catch (PDOException $e) {
    $redirectError('Tabel users belum siap. Jalankan SQL pembuatan tabel users terlebih dahulu.');
}

if ($exists) {
    $redirectError('Username sudah terdaftar. Silakan gunakan username lain.');
}

$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $insert = $pdo->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
    $insert->execute(['username' => $username, 'password' => $hash]);
} catch (PDOException $e) {
    $redirectError('Gagal membuat akun. Silakan coba lagi.');
}

header('Location: ' . base_url('login.php') . '?tipe=success&pesan=' . urlencode('Registrasi berhasil. Silakan login.'));
exit;
