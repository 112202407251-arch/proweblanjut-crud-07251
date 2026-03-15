<?php
require __DIR__ . '/koneksi.php';

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("DELETE FROM barang WHERE id = :id");
$stmt->execute(['id' => $id]);

header('Location: index.php');
exit;

