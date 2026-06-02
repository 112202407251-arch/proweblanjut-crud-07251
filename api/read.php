<?php
declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    method_not_allowed(['GET']);
}

try {
    $id = isset($_GET['id']) ? trim((string)$_GET['id']) : '';
    $keyword = isset($_GET['q']) ? trim((string)$_GET['q']) : '';

    if ($id !== '') {
        if (!ctype_digit($id)) {
            send_json(422, [
                'success' => false,
                'message' => 'Parameter id harus angka positif.',
            ]);
        }

        $stmt = $pdo->prepare('SELECT * FROM barang WHERE id = :id');
        $stmt->execute(['id' => (int)$id]);
        $barang = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$barang) {
            send_json(404, [
                'success' => false,
                'message' => 'Data barang tidak ditemukan.',
            ]);
        }

        send_json(200, [
            'success' => true,
            'message' => 'Detail barang berhasil diambil.',
            'data' => $barang,
        ]);
    }

    if ($keyword !== '') {
        $stmt = $pdo->prepare(
            'SELECT * FROM barang
             WHERE nama_barang LIKE :kw OR kode_barang LIKE :kw
             ORDER BY id DESC'
        );
        $stmt->execute(['kw' => '%' . $keyword . '%']);
    } else {
        $stmt = $pdo->query('SELECT * FROM barang ORDER BY id DESC');
    }

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    send_json(200, [
        'success' => true,
        'message' => 'Data barang berhasil diambil.',
        'total' => count($data),
        'data' => $data,
    ]);
} catch (Throwable $e) {
    send_json(500, [
        'success' => false,
        'message' => 'Terjadi kesalahan server.',
        'error' => $e->getMessage(),
    ]);
}

