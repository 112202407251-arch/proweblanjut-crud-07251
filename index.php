<?php
require __DIR__ . '/koneksi.php';
require __DIR__ . '/tampilan.php';

// Pencarian sederhana berdasarkan nama atau kode barang
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($keyword !== '') {
    $stmt = $pdo->prepare(
        "SELECT * FROM barang 
         WHERE nama_barang LIKE :kw OR kode_barang LIKE :kw 
         ORDER BY created_at DESC"
    );
    $stmt->execute(['kw' => '%' . $keyword . '%']);
} else {
    $stmt = $pdo->query("SELECT * FROM barang ORDER BY created_at DESC");
}

$barang = $stmt->fetchAll();

render_header('Daftar Barang | Inventaris');
?>

<section class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">Data Barang</h2>
            <p class="card-subtitle">
                Manajemen inventaris barang sederhana dengan operasi Create, Read, Update, dan Delete.
            </p>
        </div>
        <div>
            <a href="tambah.php" class="btn btn-primary">
                + Tambah Barang
            </a>
        </div>
    </div>

    <div class="table-toolbar">
        <div class="pill">
            <span class="pill-dot"></span>
            <span><?= count($barang) ?> barang terdaftar</span>
        </div>
        <form method="get" class="search-input">
            <input
                type="text"
                name="q"
                class="input"
                placeholder="Cari nama / kode barang..."
                value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>"
            >
        </form>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Harga Satuan</th>
                <th>Terakhir Diubah</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($barang)) : ?>
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            Belum ada data barang atau hasil pencarian tidak ditemukan.
                        </div>
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ($barang as $index => $row) : ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><span class="badge"><?= htmlspecialchars($row['kode_barang']) ?></span></td>
                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td><?= htmlspecialchars($row['kategori']) ?></td>
                        <td><?= (int)$row['stok'] ?></td>
                        <td>Rp <?= number_format((float)$row['harga'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($row['updated_at'] ?? $row['created_at']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= (int)$row['id'] ?>" class="btn btn-outline" style="padding-inline:12px;font-size:12px;">
                                Edit
                            </a>
                            <a href="hapus.php?id=<?= (int)$row['id'] ?>"
                               class="btn btn-danger"
                               onclick="return confirm('Yakin ingin menghapus data ini?');">
                                Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php
render_footer();

