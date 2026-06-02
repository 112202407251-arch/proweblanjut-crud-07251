<?php
declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if (!in_array($method, ['POST', 'PUT'], true)) {
    method_not_allowed(['POST', 'PUT']);
}

try {
    $input = get_input_data();

    $id = trim((string)($input['id'] ?? ''));
    $kode = strtoupper(trim((string)($input['kode_barang'] ?? '')));
    $nama = trim((string)($input['nama_barang'] ?? ''));
    $kategori = trim((string)($input['kategori'] ?? ''));
    $stok = trim((string)($input['stok'] ?? ''));
    $harga = trim((string)($input['harga'] ?? ''));
    $deskripsi = trim((string)($input['deskripsi'] ?? ''));

    if ($id === '' || !ctype_digit($id)) {
        send_json(422, ['success' => false, 'message' => 'Field id wajib dan harus angka.']);
    }
    if ($kode === '' || $nama === '' || $kategori === '' || $stok === '' || $harga === '') {
        send_json(422, [
            'success' => false,
            'message' => 'Field wajib: kode_barang, nama_barang, kategori, stok, harga.',
        ]);
    }
    if (!is_numeric($stok) || (int)$stok < 0) {
        send_json(422, ['success' => false, 'message' => 'Nilai stok tidak valid.']);
    }
    if (!is_numeric($harga) || (float)$harga < 0) {
        send_json(422, ['success' => false, 'message' => 'Nilai harga tidak valid.']);
    }

    $checkStmt = $pdo->prepare('SELECT id FROM barang WHERE id = :id');
    $checkStmt->execute(['id' => (int)$id]);
    if (!$checkStmt->fetch(PDO::FETCH_ASSOC)) {
        send_json(404, ['success' => false, 'message' => 'Data barang tidak ditemukan.']);
    }

    $stmt = $pdo->prepare(
        'UPDATE barang
         SET kode_barang = :kode_barang,
             nama_barang = :nama_barang,
             kategori = :kategori,
             stok = :stok,
             harga = :harga,
             deskripsi = :deskripsi
         WHERE id = :id'
    );

    $stmt->execute([
        'id' => (int)$id,
        'kode_barang' => $kode,
        'nama_barang' => $nama,
        'kategori' => $kategori,
        'stok' => (int)$stok,
        'harga' => (float)$harga,
        'deskripsi' => $deskripsi !== '' ? $deskripsi : null,
    ]);

    send_json(200, [
        'success' => true,
        'message' => 'Data barang berhasil diperbarui.',
    ]);
} catch (PDOException $e) {
    if ((int)$e->getCode() === 23000) {
        send_json(409, [
            'success' => false,
            'message' => 'Kode barang sudah digunakan.',
        ]);
    }

    send_json(500, [
        'success' => false,
        'message' => 'Terjadi kesalahan server.',
        'error' => $e->getMessage(),
    ]);
} catch (Throwable $e) {
    send_json(500, [
        'success' => false,
        'message' => 'Terjadi kesalahan server.',
        'error' => $e->getMessage(),
    ]);
}

