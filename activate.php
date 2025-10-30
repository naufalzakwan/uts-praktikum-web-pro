<?php
require 'functions.php';

$message = '';
$status = 'error';

// Cek apakah ada token di URL
if (isset($_GET['token'])) {
    $token = sanitize($_GET['token']);
    
    // Cari user dengan token ini
    $query = "SELECT id, email, nama_lengkap, status FROM users 
              WHERE activation_token = '$token' 
              AND status = 'PENDING'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Update status menjadi AKTIF dan hapus token
        $update_query = "UPDATE users 
                        SET status = 'AKTIF', activation_token = NULL 
                        WHERE id = " . $user['id'];
        
        if (mysqli_query($conn, $update_query)) {
            $status = 'success';
            $message = 'Selamat! Akun Anda berhasil diaktifkan. Silakan login untuk melanjutkan.';
            
            // Kirim email konfirmasi aktivasi
            $email_subject = "Akun Berhasil Diaktifkan";
            $email_body = "
                <html>
                <body>
                    <h2>Halo, " . $user['nama_lengkap'] . "!</h2>
                    <p>Akun Anda telah berhasil diaktifkan.</p>
                    <p>Anda sekarang dapat login ke sistem dengan email: <strong>" . $user['email'] . "</strong></p>
                    <p><a href='" . BASE_URL . "login.php'>Login Sekarang</a></p>
                    <br>
                    <p>Salam,<br>Tim User Management System</p>
                </body>
                </html>
            ";
            sendEmail($user['email'], $email_subject, $email_body);
        } else {
            $message = 'Terjadi kesalahan saat aktivasi. Silakan coba lagi.';
        }
    } else {
        $message = 'Token aktivasi tidak valid atau akun sudah aktif.';
    }
} else {
    $message = 'Token aktivasi tidak ditemukan.';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun - User Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Aktivasi Akun</h2>
            
            <div class="alert alert-<?php echo $status; ?>">
                <?php echo $message; ?>
            </div>
            
            <?php if ($status == 'success'): ?>
                <a href="login.php" class="btn btn-primary">Login Sekarang</a>
            <?php else: ?>
                <a href="register.php" class="btn btn-secondary">Kembali ke Registrasi</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>