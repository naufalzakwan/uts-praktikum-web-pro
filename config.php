<?php
session_start();

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'user_management');

// Koneksi Database
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8");

// Konfigurasi PHPMailer (SMTP Gmail)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'naufal1103@gmail.com'); // Ganti dengan email Anda
define('SMTP_PASSWORD', 'ixkpzvawktujgaza'); // Ganti dengan App Password Gmail
define('SMTP_FROM_EMAIL', 'naufal1103@gmail.com');
define('SMTP_FROM_NAME', 'User Management System');

// URL Base
define('BASE_URL', 'http://localhost/user_management/');

// Timezone
date_default_timezone_set('Asia/Jakarta');
?>