<?php
require 'functions.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Validasi ID produk
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: products.php');
    exit;
}

$product_id = (int)$_GET['id'];

// Cek apakah produk milik user ini
$check_query = "SELECT id, nama_produk FROM products WHERE id = $product_id AND user_id = $user_id";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) == 0) {
    header('Location: products.php');
    exit;
}

$product = mysqli_fetch_assoc($check_result);

// Proses hapus produk
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $delete_query = "DELETE FROM products WHERE id = $product_id AND user_id = $user_id";
    
    if (mysqli_query($conn, $delete_query)) {
        header('Location: products.php?success=delete');
        exit;
    } else {
        $error = 'Gagal menghapus produk: ' . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Produk - User Management System</title>
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
                <a href="products.php" class="active">
                    <span>📦</span> Kelola Produk
                </a>
                <a href="profile.php">
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
                <h2>Hapus Produk</h2>
                <div class="user-info">
                    <span><?php echo $_SESSION['nama_lengkap']; ?></span>
                </div>
            </div>

            <div class="content">
                <div class="form-card">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="alert alert-error">
                        <strong>⚠️ Peringatan!</strong><br>
                        Anda yakin ingin menghapus produk <strong><?php echo $product['nama_produk']; ?></strong>?<br>
                        Data yang sudah dihapus tidak dapat dikembalikan!
                    </div>

                    <form method="POST" action="">
                        <button type="submit" class="btn btn-delete">🗑️ Ya, Hapus Produk</button>
                        <a href="products.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>