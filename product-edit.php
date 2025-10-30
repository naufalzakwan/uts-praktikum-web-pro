<?php
require 'functions.php';
requireLogin();

$error = '';
$user_id = $_SESSION['user_id'];

// Validasi ID produk
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: products.php');
    exit;
}

$product_id = (int)$_GET['id'];

// Ambil data produk
$query = "SELECT * FROM products WHERE id = $product_id AND user_id = $user_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header('Location: products.php');
    exit;
}

$product = mysqli_fetch_assoc($result);

// Proses update produk
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
        // Cek apakah kode produk sudah digunakan produk lain
        $check_query = "SELECT id FROM products WHERE kode_produk = '$kode_produk' AND id != $product_id";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Kode produk sudah digunakan produk lain!';
        } else {
            $update_query = "UPDATE products SET 
                            kode_produk = '$kode_produk',
                            nama_produk = '$nama_produk',
                            kategori = '$kategori',
                            harga = $harga,
                            stok = $stok,
                            deskripsi = '$deskripsi'
                            WHERE id = $product_id AND user_id = $user_id";
            
            if (mysqli_query($conn, $update_query)) {
                header('Location: products.php?success=update');
                exit;
            } else {
                $error = 'Gagal mengupdate produk: ' . mysqli_error($conn);
            }
        }
    }
    
    // Update data product untuk ditampilkan jika ada error
    $product['kode_produk'] = $kode_produk;
    $product['nama_produk'] = $nama_produk;
    $product['kategori'] = $kategori;
    $product['harga'] = $harga;
    $product['stok'] = $stok;
    $product['deskripsi'] = $deskripsi;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - User Management System</title>
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
                <h2>Edit Produk</h2>
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
                                   value="<?php echo $product['kode_produk']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="nama_produk">Nama Produk *</label>
                            <input type="text" id="nama_produk" name="nama_produk" 
                                   value="<?php echo $product['nama_produk']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="kategori">Kategori *</label>
                            <input type="text" id="kategori" name="kategori" 
                                   value="<?php echo $product['kategori']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="harga">Harga (Rp) *</label>
                            <input type="number" id="harga" name="harga" 
                                   value="<?php echo $product['harga']; ?>" required min="0">
                        </div>

                        <div class="form-group">
                            <label for="stok">Stok *</label>
                            <input type="number" id="stok" name="stok" 
                                   value="<?php echo $product['stok']; ?>" required min="0">
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" rows="4"><?php echo $product['deskripsi']; ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Produk</button>
                        <a href="products.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>