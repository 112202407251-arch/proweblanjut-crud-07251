<?php
require_once __DIR__ . '/cek_login.php';
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/tampilan.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode      = trim($_POST['kode_barang'] ?? '');
    $nama      = trim($_POST['nama_barang'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');
    $stok      = trim($_POST['stok'] ?? '');
    $harga     = trim($_POST['harga'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if ($kode === '') {
        $errors[] = 'Kode barang wajib diisi.';
    }
    if ($nama === '') {
        $errors[] = 'Nama barang wajib diisi.';
    }
    if ($stok === '' || !is_numeric($stok)) {
        $errors[] = 'Stok harus berupa angka.';
    }
    if ($harga === '' || !is_numeric($harga)) {
        $errors[] = 'Harga harus berupa angka.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO barang (kode_barang, nama_barang, kategori, stok, harga, deskripsi, created_at)
                 VALUES (:kode, :nama, :kategori, :stok, :harga, :deskripsi, NOW())"
            );

            $stmt->execute([
                'kode'      => strtoupper($kode),
                'nama'      => $nama,
                'kategori'  => $kategori,
                'stok'      => (int)$stok,
                'harga'     => (float)$harga,
                'deskripsi' => $deskripsi,
            ]);

            header('Location: index.php?status=success');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = "Kode Barang '$kode' sudah terdaftar!";
            } else {
                $errors[] = "Gagal simpan: " . $e->getMessage();
            }
        }
    }
}

render_header('Tambah Barang | Inventaris');
?>

<section class="card card-form">
    <div class="card-header">
        <div>
            <h2 class="card-title">Tambah Barang Baru</h2>
            <p class="card-subtitle">Isi data berikut untuk menambahkan barang ke inventaris.</p>
        </div>
        <div>
            <a href="index.php" class="btn btn-outline">Kembali</a>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <span>Terjadi kesalahan:</span>
            <ul style="margin:0 0 0 16px;padding:0;font-size:12px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <div class="input-group">
            <label for="kode_barang">Kode Barang</label>
            <input
                type="text"
                id="kode_barang"
                name="kode_barang"
                class="input"
                value="<?= htmlspecialchars($_POST['kode_barang'] ?? '') ?>"
                placeholder="Contoh: BRG-001"
                required
            >
        </div>

        <div class="input-group">
            <label for="nama_barang">Nama Barang</label>
            <input
                type="text"
                id="nama_barang"
                name="nama_barang"
                class="input"
                value="<?= htmlspecialchars($_POST['nama_barang'] ?? '') ?>"
                placeholder="Nama barang"
                required
            >
        </div>

        <div class="input-group">
            <label for="kategori">Kategori</label>
            <input
                type="text"
                id="kategori"
                name="kategori"
                class="input"
                value="<?= htmlspecialchars($_POST['kategori'] ?? '') ?>"
                placeholder="Contoh: Elektronik, ATK, dll."
            >
        </div>

        <div class="input-row">
            <div class="input-group">
                <label for="stok">Stok</label>
                <input
                    type="number"
                    id="stok"
                    name="stok"
                    class="input"
                    min="0"
                    value="<?= htmlspecialchars($_POST['stok'] ?? '0') ?>"
                    required
                >
            </div>
            <div class="input-group">
                <label for="harga">Harga Satuan (Rp)</label>
                <input
                    type="number"
                    id="harga"
                    name="harga"
                    class="input"
                    min="0"
                    step="0.01"
                    value="<?= htmlspecialchars($_POST['harga'] ?? '0') ?>"
                    required
                >
            </div>
        </div>

        <div class="input-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea
                id="deskripsi"
                name="deskripsi"
                class="textarea"
                placeholder="Catatan tambahan tentang barang (opsional)"
            ><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <a href="index.php" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Barang</button>
        </div>
    </form>
</section>

<?php render_footer(); ?>

