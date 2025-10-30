<?php
require 'config.php';

// Import PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Jika pakai Composer

// Fungsi untuk cek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fungsi untuk cek apakah user aktif
function isActive() {
    return isset($_SESSION['status']) && $_SESSION['status'] == 'AKTIF';
}

// Fungsi redirect jika belum login
function requireLogin() {
    if (!isLoggedIn() || !isActive()) {
        header('Location: login.php');
        exit;
    }
}

// Fungsi validasi email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Fungsi generate token random
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Fungsi kirim email dengan PHPMailer
function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email error: {$mail->ErrorInfo}");
        return false;
    }
}

// Fungsi sanitasi input
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// Fungsi hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Fungsi verifikasi password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
?>