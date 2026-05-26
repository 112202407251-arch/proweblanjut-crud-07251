<?php
require_once APP_PATH . '/models/Barang.php';

/**
 * Controller Barang – mengatur alur CRUD inventaris.
 */
class BarangController
{
    private Barang $model;

    private array $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
    ];

    private int $maxFileSize = 2 * 1024 * 1024;

    public function __construct(PDO $pdo)
    {
        $this->model = new Barang($pdo);
    }

    public function index(): void
    {
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        $barang = $this->model->getAll($keyword);
        $stats = $this->model->getStats();
        $status = $_GET['status'] ?? '';

        require APP_PATH . '/views/barang/index.php';
    }

    public function create(): void
    {
        $errors = [];
        $old = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->handleFormSubmit(null);
            $errors = $result['errors'];
            $old = $result['old'];

            if (empty($errors)) {
                header('Location: ' . base_url('index.php?status=success'));
                exit;
            }
        }

        require APP_PATH . '/views/barang/create.php';
    }

    public function edit(): void
    {
        $id = $this->resolveId();
        if ($id === null) {
            $this->redirectIndex();
        }

        $barang = $this->model->findById($id);
        if (!$barang) {
            $this->redirectIndex();
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->handleFormSubmit($barang);
            $errors = $result['errors'];
            $barang = $result['old'];

            if (empty($errors)) {
                header('Location: ' . base_url('index.php?status=updated'));
                exit;
            }
        }

        require APP_PATH . '/views/barang/edit.php';
    }

    public function delete(): void
    {
        $id = $this->resolveId();
        if ($id === null) {
            $this->redirectIndex();
        }

        $gambar = $this->model->delete($id);
        if ($gambar) {
            $this->removeImageFile($gambar);
        }

        header('Location: ' . base_url('index.php'));
        exit;
    }

    private function handleFormSubmit(?array $existing): array
    {
        $errors = [];
        $kode = trim($_POST['kode_barang'] ?? '');
        $nama = trim($_POST['nama_barang'] ?? '');
        $kategori = trim($_POST['kategori'] ?? '');
        $stok = trim($_POST['stok'] ?? '');
        $harga = trim($_POST['harga'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $gambarNama = $existing['gambar'] ?? null;
        $hapusGambarLama = false;
        $isEdit = $existing !== null;

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

        $uploadError = $this->validateUpload();
        if ($uploadError !== null) {
            $errors[] = $uploadError;
        }

        $old = [
            'id'          => $existing['id'] ?? null,
            'kode_barang' => $kode,
            'nama_barang' => $nama,
            'kategori'    => $kategori,
            'stok'        => $stok,
            'harga'       => $harga,
            'deskripsi'   => $deskripsi,
            'gambar'      => $gambarNama,
        ];

        if (!empty($errors)) {
            return ['errors' => $errors, 'old' => $old];
        }

        try {
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                $gambarNama = $this->storeUpload();
                if ($isEdit) {
                    $hapusGambarLama = true;
                }
            }

            $payload = [
                'kode_barang' => strtoupper($kode),
                'nama_barang' => $nama,
                'kategori'    => $kategori,
                'stok'        => $stok,
                'harga'       => $harga,
                'deskripsi'   => $deskripsi,
                'gambar'      => $gambarNama,
            ];

            if ($isEdit) {
                $this->model->update((int)$existing['id'], $payload);
                if ($hapusGambarLama && !empty($existing['gambar'])) {
                    $this->removeImageFile($existing['gambar']);
                }
            } else {
                $this->model->create($payload);
            }

            return ['errors' => [], 'old' => $old];
        } catch (Throwable $e) {
            if (!empty($gambarNama) && $hapusGambarLama) {
                $this->removeImageFile($gambarNama);
            } elseif (!empty($gambarNama) && !$isEdit) {
                $this->removeImageFile($gambarNama);
            }

            if ($e->getCode() == 23000) {
                $msg = $isEdit
                    ? "Gagal: Kode Barang '$kode' sudah digunakan oleh barang lain."
                    : "Kode Barang '$kode' sudah terdaftar!";
                $errors[] = $msg;
            } else {
                $errors[] = ($isEdit ? 'Sistem Error: ' : 'Gagal simpan: ') . $e->getMessage();
            }

            $old['gambar'] = $existing['gambar'] ?? null;
            return ['errors' => $errors, 'old' => $old];
        }
    }

    private function validateUpload(): ?string
    {
        if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
            return 'Upload gambar gagal. Silakan coba lagi.';
        }

        $tmpPath = $_FILES['gambar']['tmp_name'];
        $fileSize = (int)$_FILES['gambar']['size'];
        $mimeType = $this->detectMimeType($tmpPath);

        if (!isset($this->allowedMimeTypes[$mimeType])) {
            return 'Format gambar harus JPG atau PNG.';
        }
        if ($fileSize <= 0 || $fileSize > $this->maxFileSize) {
            return 'Ukuran gambar maksimal 2 MB.';
        }

        return null;
    }

    private function storeUpload(): string
    {
        if (!is_dir(UPLOAD_PATH) && !mkdir(UPLOAD_PATH, 0755, true) && !is_dir(UPLOAD_PATH)) {
            throw new RuntimeException('Gagal membuat direktori upload.');
        }

        $tmpPath = $_FILES['gambar']['tmp_name'];
        $originalName = (string)($_FILES['gambar']['name'] ?? 'gambar');
        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        $mimeType = $this->detectMimeType($tmpPath);
        $extension = $this->allowedMimeTypes[$mimeType] ?? 'jpg';

        $gambarNama = uniqid('img_', true) . '_' . $safeName;
        $gambarNama = preg_replace('/\.+/', '.', $gambarNama);
        $ekstensiAkhir = '.' . $extension;
        if (strtolower(substr($gambarNama, -strlen($ekstensiAkhir))) !== $ekstensiAkhir) {
            $gambarNama .= $ekstensiAkhir;
        }

        $targetPath = UPLOAD_PATH . '/' . $gambarNama;
        if (!move_uploaded_file($tmpPath, $targetPath)) {
            throw new RuntimeException('Gagal menyimpan file gambar.');
        }

        return $gambarNama;
    }

    private function detectMimeType(string $tmpPath): string
    {
        $fileInfo = @finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = $fileInfo ? (string)finfo_file($fileInfo, $tmpPath) : '';
        if ($fileInfo) {
            finfo_close($fileInfo);
        }
        return $mimeType;
    }

    private function removeImageFile(string $filename): void
    {
        $path = UPLOAD_PATH . '/' . $filename;
        if (is_file($path)) {
            @unlink($path);
        }
    }

    private function resolveId(): ?int
    {
        if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) {
            return null;
        }
        return (int)$_GET['id'];
    }

    private function redirectIndex(): void
    {
        header('Location: ' . base_url('index.php'));
        exit;
    }
}
