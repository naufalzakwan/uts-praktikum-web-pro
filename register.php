<?php
require 'functions.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn() && isActive()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

// Proses form registrasi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $nama_lengkap = sanitize($_POST['nama_lengkap']);
    
    // Validasi input
    if (empty($email) || empty($password) || empty($confirm_password) || empty($nama_lengkap)) {
        $error = 'Semua field harus diisi!';
    } elseif (!validateEmail($email)) {
        $error = 'Format email tidak valid!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan Konfirmasi Password tidak sama!';
    } else {
        // Cek apakah email sudah terdaftar
        $check_query = "SELECT id FROM users WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Email sudah terdaftar! Silakan gunakan email lain.';
        } else {
            // Generate activation token
            $activation_token = generateToken();
            $hashed_password = hashPassword($password);
            
            // Insert user ke database
            $insert_query = "INSERT INTO users (email, password, nama_lengkap, status, activation_token) 
                            VALUES ('$email', '$hashed_password', '$nama_lengkap', 'PENDING', '$activation_token')";
            
            if (mysqli_query($conn, $insert_query)) {
                // Kirim email aktivasi
                $activation_link = BASE_URL . "activate.php?token=" . $activation_token;
                
                $email_subject = "Aktivasi Akun - User Management System";
                $email_body = "
                    <html>
                    <body>
                        <h2>Selamat Datang, $nama_lengkap!</h2>
                        <p>Terima kasih telah mendaftar sebagai Admin Gudang.</p>
                        <p>Silakan klik link di bawah ini untuk mengaktifkan akun Anda:</p>
                        <p><a href='$activation_link' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Aktivasi Akun</a></p>
                        <p>Atau copy link berikut ke browser Anda:</p>
                        <p>$activation_link</p>
                        <br>
                        <p>Link aktivasi ini berlaku selama 24 jam.</p>
                        <p>Jika Anda tidak merasa mendaftar, abaikan email ini.</p>
                        <br>
                        <p>Salam,<br>Tim User Management System</p>
                    </body>
                    </html>
                ";
                
                if (sendEmail($email, $email_subject, $email_body)) {
                    $success = 'Registrasi berhasil! Silakan cek email Anda untuk aktivasi akun.';
                    // Reset form
                    $email = $nama_lengkap = '';
                } else {
                    $error = 'Registrasi berhasil, tapi gagal mengirim email aktivasi. Silakan hubungi admin.';
                }
            } else {
                $error = 'Terjadi kesalahan: ' . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - User Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Registrasi Admin Gudang</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" 
                           value="<?php echo isset($nama_lengkap) ? $nama_lengkap : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email (Username)</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo isset($email) ? $email : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <small>Minimal 6 karakter</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Daftar</button>
            </form>
            
            <p class="text-center mt-20">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </p>
        </div>
    </div>
</body>
</html>