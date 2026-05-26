<?php
$title = 'Daftar Barang | Inventaris';
require APP_PATH . '/views/layout/header.php';
?>

<?php if ($status === 'success') : ?>
    <div class="alert alert-success">Barang berhasil ditambahkan.</div>
<?php elseif ($status === 'updated') : ?>
    <div class="alert alert-success">Data barang berhasil diperbarui.</div>
<?php endif; ?>

<section class="stats-grid" id="dashboard">
    <article class="stat-card">
        <p class="stat-label">Total Jenis Barang</p>
        <p class="stat-value"><?= (int)$stats['total_jenis'] ?></p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Total Stok</p>
        <p class="stat-value"><?= (int)$stats['total_stok'] ?></p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Total Nilai Inventaris</p>
        <p class="stat-value">Rp <?= number_format((float)$stats['total_nilai'], 0, ',', '.') ?></p>
    </article>
</section>

<section class="card" id="data-barang">
    <div class="card-header">
        <div>
            <h2 class="card-title">Data Barang</h2>
            <p class="card-subtitle">
                Manajemen inventaris dengan pola MVC (Model, View, Controller).
            </p>
        </div>
        <div>
            <a href="<?= base_url('index.php?action=create') ?>" class="btn btn-primary">
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
            <input type="hidden" name="action" value="index">
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
                <th>Gambar</th>
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
                    <td colspan="9">
                        <div class="empty-state">
                            Belum ada data barang atau hasil pencarian tidak ditemukan.
                        </div>
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ($barang as $index => $row) : ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <?php if (!empty($row['gambar'])) : ?>
                                <img
                                    src="<?= upload_url($row['gambar']) ?>"
                                    alt="Gambar <?= htmlspecialchars($row['nama_barang'] ?? 'barang', ENT_QUOTES, 'UTF-8') ?>"
                                    style="width:56px;height:56px;object-fit:cover;border-radius:8px;"
                                >
                            <?php else : ?>
                                <span class="badge">-</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge"><?= htmlspecialchars($row['kode_barang'] ?? '-') ?></span></td>
                        <td><?= htmlspecialchars($row['nama_barang'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['kategori'] ?? '-') ?></td>
                        <td><?= (int)($row['stok'] ?? 0) ?></td>
                        <td>Rp <?= number_format((float)($row['harga'] ?? 0), 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($row['updated_at'] ?? $row['created_at'] ?? '-') ?></td>
                        <td class="action-cell">
                            <a href="<?= base_url('index.php?action=edit&id=' . (int)$row['id']) ?>" class="btn btn-soft" title="Lihat detail barang">
                                <span aria-hidden="true">👁</span> Detail
                            </a>
                            <a href="<?= base_url('index.php?action=edit&id=' . (int)$row['id']) ?>" class="btn btn-outline" title="Edit barang">
                                <span aria-hidden="true">✎</span> Edit
                            </a>
                            <a href="<?= base_url('index.php?action=delete&id=' . (int)$row['id']) ?>"
                               class="btn btn-danger"
                               title="Hapus barang"
                               onclick="return confirm('Yakin ingin menghapus data ini?');">
                                <span aria-hidden="true">🗑</span> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require APP_PATH . '/views/layout/footer.php'; ?>
