<?php
require_once __DIR__ . '/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = (string)($_POST['password'] ?? '');
$passwordConfirm = (string)($_POST['password_confirm'] ?? '');

if ($username === '' || $password === '' || $passwordConfirm === '') {
    header('Location: register.php?pesan=' . urlencode('Semua field wajib diisi.') . '&username=' . urlencode($username));
    exit;
}

if (mb_strlen($username) < 4) {
    header('Location: register.php?pesan=' . urlencode('Username minimal 4 karakter.') . '&username=' . urlencode($username));
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    header('Location: register.php?pesan=' . urlencode('Username hanya boleh berisi huruf, angka, dan underscore.') . '&username=' . urlencode($username));
    exit;
}

if (mb_strlen($password) < 6) {
    header('Location: register.php?pesan=' . urlencode('Password minimal 6 karakter.') . '&username=' . urlencode($username));
    exit;
}

if (!hash_equals($password, $passwordConfirm)) {
    header('Location: register.php?pesan=' . urlencode('Konfirmasi password tidak cocok.') . '&username=' . urlencode($username));
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);
    $exists = $stmt->fetchColumn();
} catch (PDOException $e) {
    header('Location: register.php?pesan=' . urlencode('Tabel users belum siap. Jalankan SQL pembuatan tabel users terlebih dahulu.'));
    exit;
}

if ($exists) {
    header('Location: register.php?pesan=' . urlencode('Username sudah terdaftar. Silakan gunakan username lain.') . '&username=' . urlencode($username));
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $insert = $pdo->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
    $insert->execute([
        'username' => $username,
        'password' => $hash,
    ]);
} catch (PDOException $e) {
    header('Location: register.php?pesan=' . urlencode('Gagal membuat akun. Silakan coba lagi.'));
    exit;
}

header('Location: login.php?tipe=success&pesan=' . urlencode('Registrasi berhasil. Silakan login.'));
exit;

