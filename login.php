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
$tujuan = trim($_GET['tujuan'] ?? '');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Inventaris</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-shell">
    <header class="app-header">
        <div class="brand">
            <div class="brand-logo">IN</div>
            <div>
                <p class="brand-text-title">Inventaris Barang</p>
                <p class="brand-text-sub">Silakan login untuk melanjutkan</p>
            </div>
        </div>
    </header>

    <main>
        <section class="card card-form">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Login</h2>
                    <p class="card-subtitle">Masukkan username dan password yang terdaftar.</p>
                </div>
            </div>

            <?php if ($pesan !== '') : ?>
                <div class="alert <?= $tipe === 'success' ? 'alert-success' : 'alert-error' ?>">
                    <?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="post" action="proses_login.php" autocomplete="off">
                <input type="hidden" name="tujuan" value="<?= htmlspecialchars($tujuan, ENT_QUOTES, 'UTF-8') ?>">

                <div class="input-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="input"
                        placeholder="Masukkan username"
                        required
                        autofocus
                    >
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="input"
                        placeholder="Masukkan password"
                        required
                    >
                </div>

                <div class="input-group-checkbox">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Ingat saya (30 hari)</label>
                </div>

                <div class="form-actions">
                    <a href="register.php" class="btn btn-outline">Daftar</a>
                    <button type="submit" class="btn btn-primary">Masuk</button>
                </div>
            </form>
        </section>
    </main>
</div>
</body>
</html>

