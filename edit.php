<?php
require __DIR__ . '/koneksi.php';
require __DIR__ . '/tampilan.php';

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM barang WHERE id = :id");
$stmt->execute(['id' => $id]);
$barang = $stmt->fetch();

if (!$barang) {
    header('Location: index.php');
    exit;
}

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
                "UPDATE barang
                 SET kode_barang = :kode,
                     nama_barang = :nama,
                     kategori    = :kategori,
                     stok        = :stok,
                     harga       = :harga,
                     deskripsi   = :deskripsi
                 WHERE id = :id"
            );

            $stmt->execute([
                'kode'      => strtoupper($kode),
                'nama'      => $nama,
                'kategori'  => $kategori,
                'stok'      => (int)$stok,
                'harga'     => (float)$harga,
                'deskripsi' => $deskripsi,
                'id'        => $id,
            ]);

            header('Location: index.php?status=updated');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = "Gagal: Kode Barang '$kode' sudah digunakan oleh barang lain.";
            } else {
                $errors[] = "Sistem Error: " . $e->getMessage();
            }
        }
    }

    $barang = array_merge($barang, [
        'kode_barang' => $kode,
        'nama_barang' => $nama,
        'kategori'    => $kategori,
        'stok'        => $stok,
        'harga'       => $harga,
        'deskripsi'   => $deskripsi,
    ]);
}

render_header('Edit Barang | Inventaris');
?>

<section class="card card-form">
    <div class="card-header">
        <div>
            <h2 class="card-title">Edit Barang</h2>
            <p class="card-subtitle">Perbarui informasi barang yang dipilih.</p>
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
                value="<?= htmlspecialchars($barang['kode_barang'] ?? '') ?>"
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
                value="<?= htmlspecialchars($barang['nama_barang'] ?? '') ?>"
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
                value="<?= htmlspecialchars($barang['kategori'] ?? '') ?>"
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
                    value="<?= htmlspecialchars((string)$barang['stok'], ENT_QUOTES, 'UTF-8') ?>"
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
                    value="<?= htmlspecialchars((string)$barang['harga'], ENT_QUOTES, 'UTF-8') ?>"
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
            ><?= htmlspecialchars($barang['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="form-actions">
            <a href="index.php" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</section>

<?php
render_footer();

