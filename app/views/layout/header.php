<?php
/** @var string $title */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Manajemen Inventaris Barang', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= asset_url('style.css') ?>">
</head>
<body>
<div class="dashboard-shell">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo">IN</div>
            <div>
                <p class="brand-text-title">Inventaris</p>
                <p class="brand-text-sub">Manajemen Barang (MVC)</p>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="<?= base_url('index.php') ?>#dashboard" class="nav-item">Dashboard</a>
        </nav>
        <div class="sidebar-footer">
            <?php if (!empty($_SESSION['username'])) : ?>
                <div class="user-chip">
                    <span class="pill-dot"></span>
                    <span><?= htmlspecialchars((string)$_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <a href="<?= base_url('logout.php') ?>" class="btn btn-danger" title="Keluar">Logout</a>
            <?php endif; ?>
        </div>
    </aside>

    <main class="main-content">
        <header class="app-header">
            <div>
                <h1 class="page-title">Dashboard Inventaris</h1>
                <p class="page-subtitle">Pantau stok, nilai barang, dan aktivitas data inventaris.</p>
            </div>
            <a href="<?= base_url('index.php') ?>" class="btn btn-icon" title="Refresh halaman">⟳</a>
        </header>
