<?php 
require 'functions.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn() && isActive()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi!';
    } else {
        // Cari user berdasarkan email
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            // Verifikasi password
            if (verifyPassword($password, $user['password'])) {
                // Cek status akun
                if ($user['status'] == 'PENDING') {
                    $error = 'Akun Anda belum diaktivasi. Silakan cek email untuk aktivasi.';
                } elseif ($user['status'] == 'NONAKTIF') {
                    $error = 'Akun Anda telah dinonaktifkan. Hubungi administrator.';
                } else {
                    // Login berhasil
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                    $_SESSION['status'] = $user['status'];
                    
                    header('Location: dashboard.php');
                    exit;
                }
            } else {
                $error = 'Password salah!';
            }
        } else {
            $error = 'Email tidak terdaftar!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - User Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Login Admin Gudang</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['activated'])): ?>
                <div class="alert alert-success">Akun berhasil diaktifkan! Silakan login.</div>
            <?php endif; ?>
            
            <?php if (isset($_GET['reset'])): ?>
                <div class="alert alert-success">Password berhasil direset! Silakan login dengan password baru.</div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo isset($email) ? $email : ''; ?>" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            
            <p class="text-center mt-20">
                <a href="forgot-password.php">Lupa Password?</a>
            </p>
            
            <p class="text-center mt-20">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </p>
        </div>
    </div>
</body>
</html>