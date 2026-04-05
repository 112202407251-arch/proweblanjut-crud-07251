<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$pesan = trim($_GET['pesan'] ?? '');
$tipe = trim($_GET['tipe'] ?? 'error');
$username = trim($_GET['username'] ?? '');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Inventaris</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-shell">
    <header class="app-header">
        <div class="brand">
            <div class="brand-logo">IN</div>
            <div>
                <p class="brand-text-title">Inventaris Barang</p>
                <p class="brand-text-sub">Buat akun baru untuk melanjutkan</p>
            </div>
        </div>
        <div>
            <a href="login.php" class="btn btn-outline">Kembali ke Login</a>
        </div>
    </header>

    <main>
        <section class="card card-form">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Register</h2>
                    <p class="card-subtitle">Daftarkan username dan password baru.</p>
                </div>
            </div>

            <?php if ($pesan !== '') : ?>
                <div class="alert <?= $tipe === 'success' ? 'alert-success' : 'alert-error' ?>">
                    <?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="post" action="proses_register.php" autocomplete="off">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="input"
                        placeholder="Contoh: firdaus_01"
                        value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
                        required
                        autofocus
                    >
                    <div class="hint">Minimal 4 karakter. Disarankan hanya huruf, angka, underscore.</div>
                </div>

                <div class="input-row">
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="input"
                            placeholder="Minimal 6 karakter"
                            required
                        >
                    </div>
                    <div class="input-group">
                        <label for="password_confirm">Konfirmasi Password</label>
                        <input
                            type="password"
                            id="password_confirm"
                            name="password_confirm"
                            class="input"
                            placeholder="Ulangi password"
                            required
                        >
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Daftar</button>
                </div>
            </form>
        </section>
    </main>
</div>
</body>
</html>

