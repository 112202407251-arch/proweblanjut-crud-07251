<?php
declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if (!in_array($method, ['POST', 'DELETE'], true)) {
    method_not_allowed(['POST', 'DELETE']);
}

try {
    $input = get_input_data();
    $id = trim((string)($input['id'] ?? ''));

    if ($id === '' || !ctype_digit($id)) {
        send_json(422, ['success' => false, 'message' => 'Field id wajib dan harus angka.']);
    }

    $checkStmt = $pdo->prepare('SELECT id, gambar FROM barang WHERE id = :id');
    $checkStmt->execute(['id' => (int)$id]);
    $barang = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$barang) {
        send_json(404, ['success' => false, 'message' => 'Data barang tidak ditemukan.']);
    }

    $stmt = $pdo->prepare('DELETE FROM barang WHERE id = :id');
    $stmt->execute(['id' => (int)$id]);

    send_json(200, [
        'success' => true,
        'message' => 'Data barang berhasil dihapus.',
    ]);
} catch (Throwable $e) {
    send_json(500, [
        'success' => false,
        'message' => 'Terjadi kesalahan server.',
        'error' => $e->getMessage(),
    ]);
}

