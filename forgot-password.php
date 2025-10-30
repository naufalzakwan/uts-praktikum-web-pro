<?php
require 'functions.php';

$error = '';
$success = '';

// Proses forgot password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    
    if (empty($email)) {
        $error = 'Email harus diisi!';
    } elseif (!validateEmail($email)) {
        $error = 'Format email tidak valid!';
    } else {
        // Cari user berdasarkan email
        $query = "SELECT id, email, nama_lengkap FROM users WHERE email = '$email' AND status = 'AKTIF'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            // Generate reset token
            $reset_token = generateToken();
            $reset_expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token berlaku 1 jam
            
            // Update database dengan reset token
            $update_query = "UPDATE users 
                            SET reset_token = '$reset_token', 
                                reset_token_expiry = '$reset_expiry' 
                            WHERE id = " . $user['id'];
            
            if (mysqli_query($conn, $update_query)) {
                // Kirim email reset password
                $reset_link = BASE_URL . "reset-password.php?token=" . $reset_token;
                
                $email_subject = "Reset Password - User Management System";
                $email_body = "
                    <html>
                    <body>
                        <h2>Halo, " . $user['nama_lengkap'] . "!</h2>
                        <p>Kami menerima permintaan untuk reset password akun Anda.</p>
                        <p>Silakan klik link di bawah ini untuk mereset password:</p>
                        <p><a href='$reset_link' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
                        <p>Atau copy link berikut ke browser Anda:</p>
                        <p>$reset_link</p>
                        <br>
                        <p><strong>Link ini berlaku selama 1 jam.</strong></p>
                        <p>Jika Anda tidak merasa meminta reset password, abaikan email ini.</p>
                        <br>
                        <p>Salam,<br>Tim User Management System</p>
                    </body>
                    </html>
                ";
                
                if (sendEmail($user['email'], $email_subject, $email_body)) {
                    $success = 'Link reset password telah dikirim ke email Anda. Silakan cek inbox/spam.';
                    $email = ''; // Reset input
                } else {
                    $error = 'Gagal mengirim email. Silakan coba lagi.';
                }
            } else {
                $error = 'Terjadi kesalahan. Silakan coba lagi.';
            }
        } else {
            // Untuk keamanan, tampilkan pesan yang sama meskipun email tidak ditemukan
            $success = 'Jika email terdaftar, link reset password akan dikirim ke email Anda.';
            $email = '';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - User Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Lupa Password</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <p class="text-center" style="margin-bottom: 20px; color: #666;">
                Masukkan email Anda dan kami akan mengirimkan link untuk reset password.
            </p>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo isset($email) ? $email : ''; ?>" required autofocus>
                </div>
                
                <button type="submit" class="btn btn-primary">Kirim Link Reset</button>
            </form>
            
            <p class="text-center mt-20">
                <a href="login.php">← Kembali ke Login</a>
            </p>
        </div>
    </div>
</body>
</html>