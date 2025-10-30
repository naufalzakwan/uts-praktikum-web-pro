<?php
require 'functions.php';
requireLogin();

$error = '';
$success = '';
$user_id = $_SESSION['user_id'];

// Proses ubah password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = 'Semua field harus diisi!';
    } elseif (strlen($new_password) < 6) {
        $error = 'Password baru minimal 6 karakter!';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Password baru dan konfirmasi tidak sama!';
    } else {
        // Ambil password lama dari database
        $query = "SELECT password FROM users WHERE id = $user_id";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);
        
        // Verifikasi password lama
        if (!verifyPassword($current_password, $user['password'])) {
            $error = 'Password lama salah!';
        } else {
            // Update password baru
            $hashed_password = hashPassword($new_password);
            $update_query = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
            
            if (mysqli_query($conn, $update_query)) {
                $success = 'Password berhasil diubah!';
                
                // Kirim email notifikasi
                $email_subject = "Password Berhasil Diubah";
                $email_body = "
                    <html>
                    <body>
                        <h2>Halo, " . $_SESSION['nama_lengkap'] . "!</h2>
                        <p>Password akun Anda telah berhasil diubah.</p>
                        <p>Jika Anda tidak melakukan perubahan ini, segera hubungi administrator.</p>
                        <br>
                        <p>Salam,<br>Tim User Management System</p>
                    </body>
                    </html>
                ";
                sendEmail($_SESSION['email'], $email_subject, $email_body);
            } else {
                $error = 'Gagal mengubah password: ' . mysqli_error($conn);
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
    <title>Ubah Password - User Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>Admin Gudang</h3>
            </div>
            <nav class="sidebar-menu">
                <a href="dashboard.php">
                    <span>📊</span> Dashboard
                </a>
                <a href="products.php">
                    <span>📦</span> Kelola Produk
                </a>
                <a href="profile.php">
                    <span>👤</span> Profil Saya
                </a>
                <a href="change-password.php" class="active">
                    <span>🔒</span> Ubah Password
                </a>
                <a href="logout.php" class="logout">
                    <span>🚪</span> Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="topbar">
                <h2>Ubah Password</h2>
                <div class="user-info">
                    <span><?php echo $_SESSION['nama_lengkap']; ?></span>
                </div>
            </div>

            <div class="content">
                <div class="form-card">
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="current_password">Password Lama</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password">Password Baru</label>
                            <input type="password" id="new_password" name="new_password" required>
                            <small>Minimal 6 karakter</small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Konfirmasi Password Baru</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Ubah Password</button>
                        <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>