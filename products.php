<?php
require 'functions.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Ambil semua produk user
$query = "SELECT * FROM products WHERE user_id = $user_id ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - User Management System</title>
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
                <h2>Kelola Produk</h2>
                <div class="user-info">
                    <span><?php echo $_SESSION['nama_lengkap']; ?></span>
                </div>
            </div>

            <div class="content">
                <div class="content-header">
                    <a href="product-create.php" class="btn btn-primary">➕ Tambah Produk Baru</a>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <?php 
                        if ($_GET['success'] == 'create') echo 'Produk berhasil ditambahkan!';
                        if ($_GET['success'] == 'update') echo 'Produk berhasil diupdate!';
                        if ($_GET['success'] == 'delete') echo 'Produk berhasil dihapus!';
                        ?>
                    </div>
                <?php endif; ?>

                <div class="table-container">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Produk</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                while($product = mysqli_fetch_assoc($result)): 
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $product['kode_produk']; ?></td>
                                    <td><?php echo $product['nama_produk']; ?></td>
                                    <td><?php echo $product['kategori']; ?></td>
                                    <td>Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></td>
                                    <td><?php echo $product['stok']; ?></td>
                                    <td>
                                        <a href="product-edit.php?id=<?php echo $product['id']; ?>" class="btn-action btn-edit">✏️ Edit</a>
                                        <a href="product-delete.php?id=<?php echo $product['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus produk ini?')">🗑️ Hapus</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>📦 Belum ada produk.</p>
                            <a href="product-create.php" class="btn btn-primary">Tambah Produk Pertama</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>