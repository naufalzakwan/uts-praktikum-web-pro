<?php
require 'functions.php';

$error = '';
$success = '';
$valid_token = false;
$user_data = null;

// Validasi token dari URL
if (isset($_GET['token'])) {
    $token = sanitize($_GET['token']);
    
    // Cari user dengan token yang valid dan belum expired
    $query = "SELECT id, email, nama_lengkap FROM users 
              WHERE reset_token = '$token' 
              AND reset_token_expiry > NOW() 
              AND status = 'AKTIF'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $valid_token = true;
        $user_data = mysqli_fetch_assoc($result);
    } else {
        $error = 'Link reset password tidak valid atau sudah kadaluarsa.';
    }
} else {
    $error = 'Token tidak ditemukan.';
}

// Proses reset password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $valid_token) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($password) || empty($confirm_password)) {
        $error = 'Semua field harus diisi!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan Konfirmasi Password tidak sama!';
    } else {
        $hashed_password = hashPassword($password);
        
        // Update password dan hapus reset token
        $update_query = "UPDATE users 
                        SET password = '$hashed_password', 
                            reset_token = NULL, 
                            reset_token_expiry = NULL 
                        WHERE id = " . $user_data['id'];
        
        if (mysqli_query($conn, $update_query)) {
            $success = 'Password berhasil direset! Silakan login dengan password baru Anda.';
            $valid_token = false; // Prevent form from showing again
            
            // Kirim email konfirmasi
            $email_subject = "Password Berhasil Direset";
            $email_body = "
                <html>
                <body>
                    <h2>Halo, " . $user_data['nama_lengkap'] . "!</h2>
                    <p>Password akun Anda telah berhasil direset.</p>
                    <p>Jika Anda tidak melakukan perubahan ini, segera hubungi administrator.</p>
                    <p><a href='" . BASE_URL . "login.php'>Login Sekarang</a></p>
                    <br>
                    <p>Salam,<br>Tim User Management System</p>
                </body>
                </html>
            ";
            sendEmail($user_data['email'], $email_subject, $email_body);
        } else {
            $error = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - User Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Reset Password</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
                <a href="login.php" class="btn btn-primary">Login Sekarang</a>
            <?php endif; ?>
            
            <?php if ($valid_token && !$success): ?>
                <p class="text-center" style="margin-bottom: 20px; color: #666;">
                    Halo, <strong><?php echo $user_data['nama_lengkap']; ?></strong>. <br>
                    Masukkan password baru Anda.
                </p>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <input type="password" id="password" name="password" required>
                        <small>Minimal 6 karakter</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password Baru</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </form>
            <?php endif; ?>
            
            <?php if (!$valid_token && !$success): ?>
                <a href="forgot-password.php" class="btn btn-secondary">Request Link Baru</a>
            <?php endif; ?>
            
            <p class="text-center mt-20">
                <a href="login.php">← Kembali ke Login</a>
            </p>
        </div>
    </div>
</body>
</html>