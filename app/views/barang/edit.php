<?php
$title = 'Edit Barang | Inventaris';
require APP_PATH . '/views/layout/header.php';
?>

<section class="card card-form">
    <div class="card-header">
        <div>
            <h2 class="card-title">Edit Barang</h2>
            <p class="card-subtitle">Perbarui informasi barang yang dipilih.</p>
        </div>
        <div>
            <a href="<?= base_url('index.php') ?>" class="btn btn-outline">Kembali</a>
        </div>
    </div>

    <?php if (!empty($errors)) : ?>
        <div class="alert alert-error">
            <span>Terjadi kesalahan:</span>
            <ul style="margin:0 0 0 16px;padding:0;font-size:12px;">
                <?php foreach ($errors as $error) : ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('index.php?action=edit&id=' . (int)$barang['id']) ?>" enctype="multipart/form-data" autocomplete="off">
        <div class="input-group">
            <label for="kode_barang">Kode Barang</label>
            <input type="text" id="kode_barang" name="kode_barang" class="input"
                   value="<?= htmlspecialchars($barang['kode_barang'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="input-group">
            <label for="nama_barang">Nama Barang</label>
            <input type="text" id="nama_barang" name="nama_barang" class="input"
                   value="<?= htmlspecialchars($barang['nama_barang'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="input-group">
            <label for="kategori">Kategori</label>
            <input type="text" id="kategori" name="kategori" class="input"
                   value="<?= htmlspecialchars($barang['kategori'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="input-row">
            <div class="input-group">
                <label for="stok">Stok</label>
                <input type="number" id="stok" name="stok" class="input" min="0"
                       value="<?= htmlspecialchars((string)($barang['stok'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="input-group">
                <label for="harga">Harga Satuan (Rp)</label>
                <input type="number" id="harga" name="harga" class="input" min="0" step="0.01"
                       value="<?= htmlspecialchars((string)($barang['harga'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
        </div>

        <div class="input-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" class="textarea"><?= htmlspecialchars($barang['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="input-group">
            <label for="gambar">Gambar Barang (JPG/PNG, max 2MB)</label>
            <?php if (!empty($barang['gambar'])) : ?>
                <div style="margin-bottom:8px;">
                    <img src="<?= upload_url($barang['gambar']) ?>" alt="Gambar barang" style="max-width:120px;border-radius:8px;">
                </div>
            <?php endif; ?>
            <input type="file" id="gambar" name="gambar" class="input" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
        </div>

        <div class="form-actions">
            <a href="<?= base_url('index.php') ?>" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</section>

<?php require APP_PATH . '/views/layout/footer.php'; ?>
