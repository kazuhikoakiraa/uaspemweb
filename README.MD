# Sistem Manajemen Anime

Aplikasi web untuk mengelola daftar anime dengan fitur CRUD dan autentikasi pengguna.

## Teknologi yang Digunakan
- PHP 7.4+
- MySQL/MariaDB
- JavaScript
- HTML5
- CSS3
- Bootstrap 5
- SweetAlert2
- Font Awesome

## Fitur Utama
- Autentikasi (Login/Register)
- Manajemen data anime (CRUD)
- Upload gambar
- Notifikasi real-time
- Desain responsif
- Remember me

## Struktur Database
```sql
-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    dob DATE NOT NULL,
    address TEXT NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Anime Table
CREATE TABLE anime (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    genre VARCHAR(50) NOT NULL,
    release_year YEAR NOT NULL,
    episodes INT NOT NULL,
    type ENUM('series', 'movie') NOT NULL,
    image_url TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Instalasi
1. Clone repositori ini
2. Import `database.sql` ke MySQL/MariaDB
3. Sesuaikan konfigurasi database di `koneksi.php`
4. Pastikan folder `uploads/` memiliki permission write
5. Akses melalui web browser

## Struktur File
```
├── cms.php           # Halaman utama admin
├── database.sql      # Schema database
├── koneksi.php      # Konfigurasi database
├── login.php        # Halaman login
├── logout.php       # Proses logout
├── proses.php       # Handler backend
├── register.php     # Halaman registrasi
├── styles.css       # Stylesheet
└── uploads/         # Folder penyimpanan gambar
```

## Keamanan
- Password di-hash menggunakan `password_hash()`
- Validasi input
- Perlindungan terhadap SQL Injection menggunakan PDO
- XSS Prevention dengan `htmlspecialchars()`
- Session management
- Upload file validation

## Penggunaan
1. Register akun baru
2. Login dengan kredensial
3. Kelola data anime (tambah/edit/hapus)
4. Upload gambar untuk setiap anime
5. Logout setelah selesai

## Konfigurasi Development

### Persyaratan Sistem
- Web Server (Apache/Nginx)
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Ekstensi PHP yang dibutuhkan:
  - PDO
  - PDO_MYSQL
  - GD
  - FileInfo

### Setup Development Environment
1. Install XAMPP/WAMP/MAMP
2. Aktifkan mod_rewrite untuk Apache
3. Set `display_errors = On` di php.ini untuk development
4. Atur `upload_max_filesize` dan `post_max_size` sesuai kebutuhan

### Panduan Coding
1. Gunakan PSR-4 untuk autoloading
2. Ikuti PHP PSR-12 coding standard
3. Dokumentasikan fungsi dan class dengan PHPDoc
4. Gunakan prepared statements untuk query database
5. Validasi semua input user
6. Handle error dengan try-catch
7. Gunakan konstanta untuk nilai yang sering digunakan

### Debug Mode
```php
// Tambahkan di koneksi.php
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
```

## API Endpoints

### Authentication
```
POST /proses.php
  action=login
  action=register
  action=logout
```

### Anime Management
```
GET /proses.php?action=get_anime&id={id}
POST /proses.php
  action=add_anime
  action=edit_anime
  action=delete_anime
```

## Troubleshooting

### Common Issues
1. Upload folder permission denied
   ```bash
   chmod 755 uploads/
   ```

2. Database connection failed
   - Periksa kredensial di koneksi.php
   - Pastikan service MySQL berjalan
   - Periksa firewall

3. Session tidak berfungsi
   - Periksa php.ini session settings
   - Pastikan session_start() dipanggil
   - Periksa permission session directory

### Error Logging
```php
// Implementasi di proses.php
function logError($message, $type = 'ERROR') {
    $logFile = 'logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $type: $message\n";
    error_log($logMessage, 3, $logFile);
}
```

## Deploy ke Production
1. Set DEBUG_MODE = false
2. Optimalkan database (index, caching)
3. Minify CSS/JS
4. Aktifkan HTTPS
5. Set header security
6. Backup database reguler
7. Monitoring error logs

## Lisensi
MIT License

## Kontribusi
1. Fork repositori
2. Buat branch fitur (`git checkout -b fitur-baru`)
3. Commit perubahan (`git commit -m 'Tambah fitur baru'`)
4. Push ke branch (`git push origin fitur-baru`)
5. Buat Pull Request