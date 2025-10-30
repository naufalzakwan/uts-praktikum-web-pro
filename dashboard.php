<?php
require 'functions.php';
requireLogin();

// Ambil jumlah produk user
$user_id = $_SESSION['user_id'];
$count_query = "SELECT COUNT(*) as total FROM products WHERE user_id = $user_id";
$count_result = mysqli_query($conn, $count_query);
$total_products = mysqli_fetch_assoc($count_result)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - User Management System</title>
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
                <a href="dashboard.php" class="active">
                    <span>📊</span> Dashboard
                </a>
                <a href="products.php">
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
                <h2>Dashboard</h2>
                <div class="user-info">
                    <span>Selamat datang, <strong><?php echo $_SESSION['nama_lengkap']; ?></strong></span>
                </div>
            </div>

            <div class="content">
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">📦</div>
                        <div class="stat-details">
                            <h3><?php echo $total_products; ?></h3>
                            <p>Total Produk</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">✅</div>
                        <div class="stat-details">
                            <h3><?php echo $_SESSION['status']; ?></h3>
                            <p>Status Akun</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">📧</div>
                        <div class="stat-details">
                            <h3><?php echo $_SESSION['email']; ?></h3>
                            <p>Email Terdaftar</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <h3>Aksi Cepat</h3>
                    <div class="action-buttons">
                        <a href="product-create.php" class="btn btn-primary">
                            ➕ Tambah Produk Baru
                        </a>
                        <a href="products.php" class="btn btn-secondary">
                            📋 Lihat Semua Produk
                        </a>
                        <a href="profile.php" class="btn btn-secondary">
                            ⚙️ Edit Profil
                        </a>
                    </div>
                </div>

                <!-- Recent Products -->
                <div class="recent-section">
                    <h3>Produk Terbaru</h3>
                    <?php
                    $recent_query = "SELECT * FROM products WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 5";
                    $recent_result = mysqli_query($conn, $recent_query);
                    
                    if (mysqli_num_rows($recent_result) > 0):
                    ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Kode Produk</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($product = mysqli_fetch_assoc($recent_result)): ?>
                                <tr>
                                    <td><?php echo $product['kode_produk']; ?></td>
                                    <td><?php echo $product['nama_produk']; ?></td>
                                    <td><?php echo $product['kategori']; ?></td>
                                    <td>Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></td>
                                    <td><?php echo $product['stok']; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-center">Belum ada produk. <a href="product-create.php">Tambah produk pertama Anda!</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>