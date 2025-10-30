-- =====================================================
-- USER MANAGEMENT SYSTEM - DATABASE SCHEMA
-- =====================================================

-- Buat database
CREATE DATABASE IF NOT EXISTS user_management;
USE user_management;

-- =====================================================
-- TABEL USERS
-- =====================================================
-- Tabel untuk menyimpan data pengguna Admin Gudang
-- Username menggunakan EMAIL (unique)
-- Status: PENDING (belum aktivasi), AKTIF (sudah aktivasi), NONAKTIF (dinonaktifkan)

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE COMMENT 'Email sebagai username (unique)',
    password VARCHAR(255) NOT NULL COMMENT 'Password yang sudah di-hash',
    nama_lengkap VARCHAR(255) NOT NULL COMMENT 'Nama lengkap user',
    status ENUM('PENDING', 'AKTIF', 'NONAKTIF') DEFAULT 'PENDING' COMMENT 'Status akun user',
    activation_token VARCHAR(255) DEFAULT NULL COMMENT 'Token untuk aktivasi akun via email',
    reset_token VARCHAR(255) DEFAULT NULL COMMENT 'Token untuk reset password',
    reset_token_expiry DATETIME DEFAULT NULL COMMENT 'Waktu expired reset token',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu pendaftaran',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Waktu update terakhir',
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_activation_token (activation_token),
    INDEX idx_reset_token (reset_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL PRODUCTS
-- =====================================================
-- Tabel untuk menyimpan data produk gudang
-- Setiap produk terkait dengan user yang membuatnya

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL COMMENT 'ID user yang membuat produk',
    nama_produk VARCHAR(255) NOT NULL COMMENT 'Nama produk',
    kode_produk VARCHAR(100) NOT NULL UNIQUE COMMENT 'Kode produk (unique)',
    kategori VARCHAR(100) NOT NULL COMMENT 'Kategori produk',
    harga DECIMAL(15,2) NOT NULL COMMENT 'Harga produk',
    stok INT DEFAULT 0 COMMENT 'Jumlah stok produk',
    deskripsi TEXT COMMENT 'Deskripsi produk',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu pembuatan',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Waktu update terakhir',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_kode_produk (kode_produk),
    INDEX idx_kategori (kategori)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATA SAMPLE (OPTIONAL - Hapus jika tidak diperlukan)
-- =====================================================

-- Sample user (password: admin123)
-- INSERT INTO users (email, password, nama_lengkap, status) 
-- VALUES ('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Test', 'AKTIF');

-- Sample products
-- INSERT INTO products (user_id, nama_produk, kode_produk, kategori, harga, stok, deskripsi) 
-- VALUES 
-- (1, 'Laptop Asus ROG', 'LPT-001', 'Elektronik', 15000000, 10, 'Laptop gaming dengan spesifikasi tinggi'),
-- (1, 'Mouse Logitech', 'MSE-001', 'Elektronik', 250000, 50, 'Mouse wireless dengan baterai tahan lama'),
-- (1, 'Keyboard Mechanical', 'KBD-001', 'Elektronik', 750000, 30, 'Keyboard mechanical RGB');

-- =====================================================
-- QUERIES UNTUK CEK DATA
-- =====================================================

-- Cek semua users
-- SELECT * FROM users;

-- Cek semua products
-- SELECT * FROM products;

-- Cek products dengan detail user
-- SELECT p.*, u.nama_lengkap, u.email 
-- FROM products p 
-- JOIN users u ON p.user_id = u.id;

-- =====================================================
-- END OF FILE
-- =====================================================
