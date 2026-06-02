<?php
declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    method_not_allowed(['POST']);
}

try {
    $input = get_input_data();

    $kode = strtoupper(trim((string)($input['kode_barang'] ?? '')));
    $nama = trim((string)($input['nama_barang'] ?? ''));
    $kategori = trim((string)($input['kategori'] ?? ''));
    $stok = trim((string)($input['stok'] ?? ''));
    $harga = trim((string)($input['harga'] ?? ''));
    $deskripsi = trim((string)($input['deskripsi'] ?? ''));

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

    $stmt = $pdo->prepare(
        'INSERT INTO barang (kode_barang, nama_barang, kategori, stok, harga, deskripsi, created_at)
         VALUES (:kode_barang, :nama_barang, :kategori, :stok, :harga, :deskripsi, NOW())'
    );

    $stmt->execute([
        'kode_barang' => $kode,
        'nama_barang' => $nama,
        'kategori' => $kategori,
        'stok' => (int)$stok,
        'harga' => (float)$harga,
        'deskripsi' => $deskripsi !== '' ? $deskripsi : null,
    ]);

    send_json(201, [
        'success' => true,
        'message' => 'Data barang berhasil ditambahkan.',
        'data' => [
            'id' => (int)$pdo->lastInsertId(),
            'kode_barang' => $kode,
            'nama_barang' => $nama,
            'kategori' => $kategori,
            'stok' => (int)$stok,
            'harga' => (float)$harga,
            'deskripsi' => $deskripsi !== '' ? $deskripsi : null,
        ],
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

