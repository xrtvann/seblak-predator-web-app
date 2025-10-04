# 🚀 Quick Installation Guide

## Langkah 1: Install PHPMailer

### Opsi A: Menggunakan Composer (Recommended)

```bash
cd d:\laragon\www\seblak-predator
composer install
```

Jika belum punya Composer:
1. Download dari: https://getcomposer.org/download/
2. Install dan restart terminal
3. Jalankan `composer install` di folder project

### Opsi B: Manual Install

1. Download PHPMailer: https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.8.0.zip
2. Extract ke folder `vendor/phpmailer/phpmailer/`
3. Pastikan struktur: `vendor/phpmailer/phpmailer/src/PHPMailer.php` ada

## Langkah 2: Setup Database

1. Buka phpMyAdmin: http://localhost/phpmyadmin
2. Pilih database `seblak_app`
3. Import file: `sql/forgot_password_schema.sql`

Atau via command line:
```bash
mysql -u root seblak_app < sql/forgot_password_schema.sql
```

## Langkah 3: Konfigurasi Email

Edit file `config/config.php`, baris 18-24:

```php
// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com'); // ← GANTI INI
define('SMTP_PASSWORD', 'your-app-password');     // ← GANTI INI
define('SMTP_FROM_EMAIL', 'noreply@seblakpredator.com');
define('SMTP_FROM_NAME', 'Seblak Predator');
```

### Cara Mendapatkan Gmail App Password:

1. Login Gmail Anda
2. Buka: https://myaccount.google.com/apppasswords
3. Pilih "Mail" → "Other (Custom name)"  
4. Ketik "Seblak Predator"
5. Copy password yang muncul (16 karakter)
6. Paste ke `SMTP_PASSWORD` di config.php

## Langkah 4: Create Logs Folder

```bash
mkdir logs
```

Atau manual: Buat folder bernama `logs` di root project.

## Langkah 5: Update forgot-password.php

File `pages/auth/forgot-password.php` sudah di-update dengan CSRF token.

**PENTING**: Ganti script di bagian bawah forgot-password.php dari:
```html
<!-- Authentication Module -->
<script src="../../src/assets/js/auth.js"></script>
```

Menjadi:
```html
<!-- Forgot Password Backend Integration -->
<script src="../../src/assets/js/forgot-password-backend.js"></script>

<!-- Hidden CSRF Token -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inject CSRF token into page for JavaScript access
    if (!document.getElementById('csrf_token')) {
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.id = 'csrf_token';
        tokenInput.value = '<?php echo $csrf_token; ?>';
        document.body.appendChild(tokenInput);
    }
});
</script>
```

## Langkah 6: Test Installation

### Test 1: Check PHPMailer
Buat file `test_phpmailer.php` di root:
```php
<?php
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
    echo "✅ PHPMailer installed!";
} else {
    echo "❌ PHPMailer NOT installed. Run: composer install";
}
?>
```

Akses: http://localhost/seblak-predator/test_phpmailer.php

### Test 2: Check Database
Buka phpMyAdmin dan cek apakah tabel-tabel ini ada:
- `password_reset_tokens`
- `rate_limiting`
- `security_logs`

### Test 3: Test Email Sending
Buat file `test_email.php`:
```php
<?php
require_once 'config/config.php';
require_once 'config/EmailService.php';

$emailService = new EmailService();
$result = $emailService->sendPasswordResetOTP(
    'your-test-email@gmail.com',  // GANTI dengan email Anda
    'Test User',
    '123456'
);

echo $result ? '✅ Email sent!' : '❌ Failed to send email';
?>
```

Akses: http://localhost/seblak-predator/test_email.php

### Test 4: Test Forgot Password
1. Buka: http://localhost/seblak-predator/pages/auth/forgot-password.php
2. Masukkan email yang terdaftar
3. Cek email untuk kode OTP
4. Input kode OTP
5. Reset password

## ⚠️ Troubleshooting

### "PHPMailer not installed"
```bash
composer require phpmailer/phpmailer
```

### "Table doesn't exist"
Import ulang: `sql/forgot_password_schema.sql`

### Email tidak terkirim
1. Pastikan SMTP credentials benar
2. Cek koneksi internet
3. Untuk Gmail, gunakan App Password, bukan password biasa
4. Enable "Less secure app access" jika perlu

### CSRF Token Error
1. Clear browser cache
2. Clear PHP session: Hapus folder `tmp/`
3. Refresh halaman forgot password

### Rate Limit Error
Hapus session atau tunggu 15 menit

## 📱 Production Deployment

### Enable HTTPS
Edit `config/config.php`:
```php
define('DEVELOPMENT_MODE', false); // Enable HTTPS enforcement
define('APP_URL', 'https://yourdomain.com');
```

### Setup SSL Certificate
Gunakan Let's Encrypt (gratis):
```bash
certbot certonly --standalone -d yourdomain.com
```

## 📚 Documentation

Dokumentasi lengkap: `docs/FORGOT-PASSWORD-COMPLETE-SETUP.md`

## ✅ Checklist

- [ ] PHPMailer installed
- [ ] Database tables created
- [ ] Email configured with App Password
- [ ] Logs folder created
- [ ] forgot-password.php updated with CSRF
- [ ] JavaScript updated to backend version
- [ ] Tested email sending
- [ ] Tested forgot password flow

## 🎉 Done!

Sekarang fitur forgot password sudah siap digunakan dengan keamanan penuh!

For detailed documentation, see: `docs/FORGOT-PASSWORD-COMPLETE-SETUP.md`
