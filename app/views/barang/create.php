<?php
$title = 'Tambah Barang | Inventaris';
$barang = $old;
require APP_PATH . '/views/layout/header.php';
?>

<section class="card card-form">
    <div class="card-header">
        <div>
            <h2 class="card-title">Tambah Barang Baru</h2>
            <p class="card-subtitle">Isi data berikut untuk menambahkan barang ke inventaris.</p>
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

    <form method="post" action="<?= base_url('index.php?action=create') ?>" enctype="multipart/form-data" autocomplete="off">
        <div class="input-group">
            <label for="kode_barang">Kode Barang</label>
            <input type="text" id="kode_barang" name="kode_barang" class="input"
                   value="<?= htmlspecialchars($barang['kode_barang'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   placeholder="Contoh: BRG-001" required>
        </div>

        <div class="input-group">
            <label for="nama_barang">Nama Barang</label>
            <input type="text" id="nama_barang" name="nama_barang" class="input"
                   value="<?= htmlspecialchars($barang['nama_barang'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   placeholder="Nama barang" required>
        </div>

        <div class="input-group">
            <label for="kategori">Kategori</label>
            <input type="text" id="kategori" name="kategori" class="input"
                   value="<?= htmlspecialchars($barang['kategori'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   placeholder="Contoh: Elektronik, ATK, dll.">
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
            <textarea id="deskripsi" name="deskripsi" class="textarea"
                      placeholder="Catatan tambahan tentang barang (opsional)"><?= htmlspecialchars($barang['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="input-group">
            <label for="gambar">Gambar Barang (JPG/PNG, max 2MB)</label>
            <input type="file" id="gambar" name="gambar" class="input" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
        </div>

        <div class="form-actions">
            <a href="<?= base_url('index.php') ?>" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Barang</button>
        </div>
    </form>
</section>

<?php require APP_PATH . '/views/layout/footer.php'; ?>
