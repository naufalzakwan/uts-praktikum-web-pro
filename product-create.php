<?php
require 'functions.php';
requireLogin();

$error = '';
$user_id = $_SESSION['user_id'];

// Proses tambah produk
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_produk = sanitize($_POST['kode_produk']);
    $nama_produk = sanitize($_POST['nama_produk']);
    $kategori = sanitize($_POST['kategori']);
    $harga = sanitize($_POST['harga']);
    $stok = sanitize($_POST['stok']);
    $deskripsi = sanitize($_POST['deskripsi']);
    
    if (empty($kode_produk) || empty($nama_produk) || empty($kategori) || empty($harga) || empty($stok)) {
        $error = 'Semua field wajib diisi kecuali deskripsi!';
    } elseif (!is_numeric($harga) || $harga < 0) {
        $error = 'Harga harus berupa angka positif!';
    } elseif (!is_numeric($stok) || $stok < 0) {
        $error = 'Stok harus berupa angka positif!';
    } else {
        // Cek apakah kode produk sudah ada
        $check_query = "SELECT id FROM products WHERE kode_produk = '$kode_produk'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Kode produk sudah digunakan!';
        } else {
            $insert_query = "INSERT INTO products (user_id, kode_produk, nama_produk, kategori, harga, stok, deskripsi) 
                            VALUES ($user_id, '$kode_produk', '$nama_produk', '$kategori', $harga, $stok, '$deskripsi')";
            
            if (mysqli_query($conn, $insert_query)) {
                header('Location: products.php?success=create');
                exit;
            } else {
                $error = 'Gagal menambah produk: ' . mysqli_error($conn);
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
    <title>Tambah Produk - User Management System</title>
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
                <h2>Tambah Produk Baru</h2>
                <div class="user-info">
                    <span><?php echo $_SESSION['nama_lengkap']; ?></span>
                </div>
            </div>

            <div class="content">
                <div class="form-card">
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="kode_produk">Kode Produk *</label>
                            <input type="text" id="kode_produk" name="kode_produk" 
                                   value="<?php echo isset($kode_produk) ? $kode_produk : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="nama_produk">Nama Produk *</label>
                            <input type="text" id="nama_produk" name="nama_produk" 
                                   value="<?php echo isset($nama_produk) ? $nama_produk : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="kategori">Kategori *</label>
                            <input type="text" id="kategori" name="kategori" 
                                   value="<?php echo isset($kategori) ? $kategori : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="harga">Harga (Rp) *</label>
                            <input type="number" id="harga" name="harga" 
                                   value="<?php echo isset($harga) ? $harga : ''; ?>" required min="0">
                        </div>

                        <div class="form-group">
                            <label for="stok">Stok *</label>
                            <input type="number" id="stok" name="stok" 
                                   value="<?php echo isset($stok) ? $stok : ''; ?>" required min="0">
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" rows="4"><?php echo isset($deskripsi) ? $deskripsi : ''; ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Produk</button>
                        <a href="products.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>