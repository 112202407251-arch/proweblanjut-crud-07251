<?php
// Fungsi helper sederhana untuk tampilan agar kode tidak berulang

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function render_header(string $title = 'Manajemen Inventaris Barang'): void
{
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <div class="app-shell">
        <header class="app-header">
            <div class="brand">
                <div class="brand-logo">IN</div>
                <div>
                    <p class="brand-text-title">Inventaris Barang</p>
                    <p class="brand-text-sub">Aplikasi CRUD sederhana (PHP + PDO)</p>
                </div>
            </div>
            <div>
                <?php if (!empty($_SESSION['username'])) : ?>
                    <span class="pill" title="Pengguna login" style="margin-right:8px;">
                        <span class="pill-dot"></span>
                        <span>Selamat datang, <?= htmlspecialchars((string)$_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></span>
                    </span>
                    <a href="logout.php" class="btn btn-danger" title="Keluar" style="margin-right:8px;">
                        Logout
                    </a>
                <?php endif; ?>
                <a href="index.php" class="btn btn-icon" title="Kembali ke daftar">
                    ⟳
                </a>
            </div>
        </header>

        <main>
    <?php
}

function render_footer(): void
{
    ?>
        </main>
    </div>
    </body>
    </html>
    <?php
}
