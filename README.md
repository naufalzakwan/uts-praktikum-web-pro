# 🏢 User Management System - Admin Gudang

Sistem Manajemen Pengguna dengan fitur CRUD Produk, Aktivasi Email, dan Reset Password.

---

## 📋 Fitur Utama

### 🔐 Authentication & Authorization
- ✅ **Registrasi Pengguna** - Form pendaftaran Admin Gudang
- ✅ **Aktivasi Email** - Link aktivasi dikirim ke email setelah registrasi
- ✅ **Login/Logout** - Sistem login dengan validasi status akun
- ✅ **Lupa Password** - Reset password via email dengan token
- ✅ **Ubah Password** - Fitur ganti password dari dashboard

### 📦 CRUD Produk
- ✅ **Create** - Tambah produk baru (kode, nama, kategori, harga, stok, deskripsi)
- ✅ **Read** - List semua produk dengan filter by user
- ✅ **Update** - Edit data produk existing
- ✅ **Delete** - Hapus produk dengan konfirmasi

### 👤 Manajemen Profil
- ✅ **Edit Profil** - Update nama lengkap dan email
- ✅ **Dashboard** - Statistik dan overview produk
- ✅ **Data Isolation** - User hanya lihat/edit produk sendiri

---

## 🛠️ Teknologi

- **Backend:** PHP 7.4+ (Pure PHP, no framework)
- **Database:** MySQL 5.7+
- **Email:** PHPMailer 6.x (SMTP Gmail)
- **Frontend:** HTML5, CSS3
- **Server:** Apache (XAMPP/WAMP/LAMP)

---

## 📂 Struktur Folder

```
user_management/
├── config.php                  # Konfigurasi database & SMTP
├── functions.php               # Helper functions
├── database.sql                # Schema database
├── index.php                   # Homepage redirect
│
├── Authentication/
│   ├── register.php            # Form registrasi
│   ├── activate.php            # Aktivasi akun
│   ├── login.php               # Form login
│   ├── logout.php              # Logout
│   ├── forgot-password.php     # Lupa password
│   └── reset-password.php      # Reset password
│
├── Dashboard/
│   ├── dashboard.php           # Dashboard utama
│   ├── profile.php             # Edit profil
│   └── change-password.php     # Ubah password
│
├── Products/
│   ├── products.php            # List produk
│   ├── product-create.php      # Tambah produk
│   ├── product-edit.php        # Edit produk
│   └── product-delete.php      # Hapus produk
│
├── style.css                   # Styling
└── vendor/                     # PHPMailer (Composer)
```

---

## 🚀 Instalasi

### 1. Persiapan

**Pastikan sudah terinstall:**
- XAMPP/WAMP/LAMP (Apache + MySQL + PHP)
- Composer (untuk PHPMailer)
- Browser modern

### 2. Clone/Download Project

```bash
cd c:\xampp\htdocs\
git clone <repository-url> user_management
# atau extract ZIP ke folder user_management
```

### 3. Install Dependencies

```bash
cd user_management
composer require phpmailer/phpmailer
```

### 4. Import Database

**Opsi A: Via phpMyAdmin**
1. Buka http://localhost/phpmyadmin
2. Klik "Import"
3. Pilih file `database.sql`
4. Klik "Go"

**Opsi B: Via Command Line**
```bash
mysql -u root -p < database.sql
```

### 5. Konfigurasi Email

Edit file `config.php`:

```php
// Konfigurasi PHPMailer (SMTP Gmail)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');      // ⬅️ GANTI INI
define('SMTP_PASSWORD', 'your-app-password');         // ⬅️ GANTI INI
define('SMTP_FROM_EMAIL', 'your-email@gmail.com');    // ⬅️ GANTI INI
define('SMTP_FROM_NAME', 'User Management System');
```

**Cara Mendapatkan App Password Gmail:**
1. Buka https://myaccount.google.com/security
2. Aktifkan **2-Step Verification**
3. Buka **App passwords**: https://myaccount.google.com/apppasswords
4. Pilih **Mail** → **Windows Computer**
5. Copy 16 karakter password yang di-generate

### 6. Konfigurasi Base URL

Edit `config.php` jika perlu:

```php
define('BASE_URL', 'http://localhost/user_management/');
```

### 7. Test Instalasi

Buka browser: http://localhost/user_management/

---

## 📖 Panduan Penggunaan

### Flow Registrasi → Login

```
1. Registrasi
   → http://localhost/user_management/register.php
   → Isi form: Nama, Email, Password
   → Klik "Daftar"

2. Cek Email
   → Buka inbox email yang didaftarkan
   → Klik link aktivasi

3. Aktivasi
   → Akun berstatus AKTIF
   → Redirect ke halaman login

4. Login
   → http://localhost/user_management/login.php
   → Masukkan email & password
   → Klik "Login"

5. Dashboard
   → Kelola produk (CRUD)
   → Edit profil
   → Ubah password
```

### Flow Lupa Password

```
1. Klik "Lupa Password?" di halaman login
2. Masukkan email terdaftar
3. Cek email → Klik link reset password
4. Masukkan password baru
5. Login dengan password baru
```

---

## 🗄️ Database Schema

### Tabel: users

| Field | Type | Keterangan |
|-------|------|------------|
| id | INT | Primary Key |
| email | VARCHAR(255) | Username (UNIQUE) |
| password | VARCHAR(255) | Password hashed |
| nama_lengkap | VARCHAR(255) | Nama lengkap |
| status | ENUM | PENDING/AKTIF/NONAKTIF |
| activation_token | VARCHAR(255) | Token aktivasi |
| reset_token | VARCHAR(255) | Token reset password |
| reset_token_expiry | DATETIME | Expired reset token |
| created_at | TIMESTAMP | Waktu registrasi |
| updated_at | TIMESTAMP | Waktu update terakhir |

### Tabel: products

| Field | Type | Keterangan |
|-------|------|------------|
| id | INT | Primary Key |
| user_id | INT | Foreign Key → users(id) |
| nama_produk | VARCHAR(255) | Nama produk |
| kode_produk | VARCHAR(100) | Kode produk (UNIQUE) |
| kategori | VARCHAR(100) | Kategori |
| harga | DECIMAL(15,2) | Harga |
| stok | INT | Stok |
| deskripsi | TEXT | Deskripsi |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu update terakhir |

**Relasi:** 
- One to Many: 1 user → N products
- ON DELETE CASCADE

---

## 🔒 Keamanan

✅ **Implementasi Keamanan:**
- Password di-hash dengan `password_hash()` (bcrypt)
- Token generated dengan `random_bytes()` (cryptographically secure)
- Input sanitization dengan `mysqli_real_escape_string()`
- Email validation dengan `filter_var()`
- Session management dengan status check
- Access control (middleware `requireLogin()`)
- Ownership validation (user hanya bisa edit produk sendiri)
- Token expiry (reset password berlaku 1 jam)

---

## 📧 Email Notifications

Sistem mengirim email pada event berikut:

1. **Registrasi** → Email aktivasi akun
2. **Aktivasi Berhasil** → Konfirmasi aktivasi
3. **Lupa Password** → Link reset password
4. **Reset Password Berhasil** → Konfirmasi reset
5. **Ubah Password** → Notifikasi perubahan password

---

## 🎨 Screenshot

### Halaman Registrasi
![Register](screenshots/register.png)

### Dashboard Admin
![Dashboard](screenshots/dashboard.png)

### Kelola Produk
![Products](screenshots/products.png)

---

## 🐛 Troubleshooting

### Email Tidak Terkirim

**Problem:** Email aktivasi/reset password tidak masuk

**Solusi:**
1. Cek konfigurasi SMTP di `config.php`
2. Pastikan App Password Gmail sudah benar (bukan password biasa)
3. Cek folder Spam/Junk email
4. Cek error log: `error_log()` di `functions.php`
5. Test dengan Mailtrap untuk development

### Database Connection Error

**Problem:** `mysqli_connect()` error

**Solusi:**
1. Pastikan MySQL sudah running
2. Cek kredensial database di `config.php`
3. Pastikan database `user_management` sudah dibuat

### Page Not Found (404)

**Problem:** File not found error

**Solusi:**
1. Cek nama file (case-sensitive)
2. Pastikan file ada di folder `user_management`
3. Cek BASE_URL di `config.php`

---

## 📝 Developer Notes

### Custom Functions (`functions.php`)

```php
// Authentication
isLoggedIn()          // Cek apakah user sudah login
isActive()            // Cek apakah user aktif
requireLogin()        // Middleware proteksi halaman

// Validation
validateEmail($email) // Validasi format email
sanitize($data)       // Sanitasi input

// Security
hashPassword($pwd)    // Hash password
verifyPassword()      // Verifikasi password
generateToken()       // Generate random token

// Email
sendEmail()           // Kirim email via PHPMailer
```

---

## 🔄 Update Log

### Version 1.0.0 (2025-10-30)
- ✅ Initial release
- ✅ Complete CRUD Products
- ✅ Email activation system
- ✅ Password reset functionality
- ✅ Profile management
- ✅ Responsive design

---

## 👨‍💻 Author

**Naufal Zakwan**
- GitHub: [@naufalzakwan](https://github.com/naufalzakwan)
- Repo: [uts-praktikum-web-pro](https://github.com/naufalzakwan/uts-praktikum-web-pro)

---

## 📄 License

MIT License - Free to use for educational purposes

---

## 🙏 Credits

- PHPMailer - https://github.com/PHPMailer/PHPMailer
- PHP - https://www.php.net/
- MySQL - https://www.mysql.com/

---

## 📞 Support

Jika ada pertanyaan atau issue:
1. Buka GitHub Issues
2. Email: naufal1103@gmail.com

---

**Happy Coding! 🚀**
