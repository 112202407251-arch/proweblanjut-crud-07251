-- Migrasi kolom password agar dapat menyimpan hash bcrypt/argon2.
-- Jalankan di phpMyAdmin jika tabel users sudah ada dengan tipe kolom lebih kecil.

USE `inventaris_sederhana`;

ALTER TABLE `users`
    MODIFY COLUMN `password` VARCHAR(255) NOT NULL;
