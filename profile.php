<?php
require 'functions.php';
requireLogin();

$error = '';
$success = '';
$user_id = $_SESSION['user_id'];

// Ambil data user
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = sanitize($_POST['nama_lengkap']);
    $email = sanitize($_POST['email']);
    
    if (empty($nama_lengkap) || empty($email)) {
        $error = 'Nama lengkap dan email harus diisi!';
    } elseif (!validateEmail($email)) {
        $error = 'Format email tidak valid!';
    } else {
        // Cek apakah email sudah digunakan user lain
        $check_query = "SELECT id FROM users WHERE email = '$email' AND id != $user_id";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Email sudah digunakan oleh user lain!';
        } else {
            $update_query = "UPDATE users SET nama_lengkap = '$nama_lengkap', email = '$email' WHERE id = $user_id";
            
            if (mysqli_query($conn, $update_query)) {
                $_SESSION['nama_lengkap'] = $nama_lengkap;
                $_SESSION['email'] = $email;
                $success = 'Profil berhasil diupdate!';
                $user['nama_lengkap'] = $nama_lengkap;
                $user['email'] = $email;
            } else {
                $error = 'Gagal update profil: ' . mysqli_error($conn);
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
    <title>Profil Saya - User Management System</title>
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
                <a href="profile.php" class="active">
                    <span>👤</span> Profil Saya
                </a>
                <a href="change-password.php">
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
                <h2>Profil Saya</h2>
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
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" 
                                   value="<?php echo $user['nama_lengkap']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo $user['email']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Status Akun</label>
                            <input type="text" value="<?php echo $user['status']; ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label>Terdaftar Sejak</label>
                            <input type="text" value="<?php echo date('d F Y', strtotime($user['created_at'])); ?>" disabled>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>