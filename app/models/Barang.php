<?php
/**
 * Model Barang – mengelola data dan query database inventaris.
 */
class Barang
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(string $keyword = ''): array
    {
        if ($keyword !== '') {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM barang
                 WHERE nama_barang LIKE :kw OR kode_barang LIKE :kw
                 ORDER BY id DESC'
            );
            $stmt->execute(['kw' => '%' . $keyword . '%']);
        } else {
            $stmt = $this->pdo->query('SELECT * FROM barang ORDER BY id DESC');
        }

        return $stmt->fetchAll();
    }

    public function getStats(): array
    {
        $stmt = $this->pdo->query(
            'SELECT
                COUNT(*) AS total_jenis,
                COALESCE(SUM(stok), 0) AS total_stok,
                COALESCE(SUM(stok * harga), 0) AS total_nilai
             FROM barang'
        );
        $stats = $stmt->fetch();

        return $stats ?: [
            'total_jenis' => 0,
            'total_stok'  => 0,
            'total_nilai' => 0,
        ];
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM barang WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function create(array $data): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO barang (kode_barang, nama_barang, kategori, stok, harga, deskripsi, gambar, created_at)
             VALUES (:kode, :nama, :kategori, :stok, :harga, :deskripsi, :gambar, NOW())'
        );

        $stmt->execute([
            'kode'      => $data['kode_barang'],
            'nama'      => $data['nama_barang'],
            'kategori'  => $data['kategori'],
            'stok'      => (int)$data['stok'],
            'harga'     => (float)$data['harga'],
            'deskripsi' => $data['deskripsi'],
            'gambar'    => $data['gambar'],
        ]);
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE barang
             SET kode_barang = :kode,
                 nama_barang = :nama,
                 kategori    = :kategori,
                 stok        = :stok,
                 harga       = :harga,
                 deskripsi   = :deskripsi,
                 gambar      = :gambar
             WHERE id = :id'
        );

        $stmt->execute([
            'kode'      => $data['kode_barang'],
            'nama'      => $data['nama_barang'],
            'kategori'  => $data['kategori'],
            'stok'      => (int)$data['stok'],
            'harga'     => (float)$data['harga'],
            'deskripsi' => $data['deskripsi'],
            'gambar'    => $data['gambar'],
            'id'        => $id,
        ]);
    }

    public function delete(int $id): ?string
    {
        $barang = $this->findById($id);
        if (!$barang) {
            return null;
        }

        $stmt = $this->pdo->prepare('DELETE FROM barang WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $barang['gambar'] ?? null;
    }
}
