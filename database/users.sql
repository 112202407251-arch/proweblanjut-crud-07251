-- Buat tabel users untuk sistem login
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(255) DEFAULT NULL
);

-- Contoh user untuk pengujian
-- Username: admin
-- Password: admin123
INSERT INTO users (username, password)
VALUES ('admin', '$2y$10$B/PBslugPmF67u6q1Ml0XOv6jJ3Ly/xeV1wH3F2PmYpu472Kgy7RS');

