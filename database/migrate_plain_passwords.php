<?php
/**
 * Skrip migrasi: mengubah password plain-text di tabel users menjadi hash.
 * Jalankan sekali via browser atau CLI setelah tugas 6.
 *
 * Contoh CLI: php database/migrate_plain_passwords.php
 */

require_once dirname(__DIR__) . '/config/database.php';

$isCli = PHP_SAPI === 'cli';

function output(string $message, bool $isCli): void
{
    echo $isCli ? $message . PHP_EOL : '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
}

if (!$isCli) {
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Migrasi Password</title></head><body>';
    echo '<h1>Migrasi Password Plain-Text ke Hash</h1>';
}

try {
    $stmt = $pdo->query('SELECT id, username, password FROM users');
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    output('Gagal membaca tabel users: ' . $e->getMessage(), $isCli);
    if (!$isCli) {
        echo '</body></html>';
    }
    exit(1);
}

$migrated = 0;
$skipped = 0;

foreach ($users as $user) {
    $stored = (string)($user['password'] ?? '');
    $info = password_get_info($stored);

    if ($info['algo'] !== 0) {
        $skipped++;
        continue;
    }

    if ($stored === '') {
        output("Lewati user #{$user['id']} ({$user['username']}): password kosong.", $isCli);
        $skipped++;
        continue;
    }

    $hash = password_hash($stored, PASSWORD_DEFAULT);
    $update = $pdo->prepare('UPDATE users SET password = :password WHERE id = :id');
    $update->execute(['password' => $hash, 'id' => $user['id']]);

    output("User #{$user['id']} ({$user['username']}): password di-hash.", $isCli);
    $migrated++;
}

output("Selesai. Di-hash: {$migrated}, dilewati (sudah hash): {$skipped}.", $isCli);

if (!$isCli) {
    echo '<p><a href="../public/login.php">Kembali ke Login</a></p>';
    echo '</body></html>';
}
