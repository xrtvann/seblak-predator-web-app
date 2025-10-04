# Forgot Password Feature - Complete Setup Guide

## 📋 Overview

Fitur forgot password yang aman dengan:
- ✅ PHPMailer untuk pengiriman email
- ✅ CSRF Protection
- ✅ Rate Limiting
- ✅ HTTPS Enforcement
- ✅ OTP dengan hashing
- ✅ Security Logging
- ✅ Prepared Statements

---

## 🚀 Installation Steps

### 1. Install PHPMailer via Composer

```bash
cd d:\laragon\www\seblak-predator
composer require phpmailer/phpmailer
```

Jika belum ada composer, download dari: https://getcomposer.org/download/

Atau install manual:
1. Download PHPMailer: https://github.com/PHPMailer/PHPMailer/releases
2. Extract ke folder `vendor/phpmailer/phpmailer`

### 2. Create Required Database Tables

Jalankan SQL script berikut di database `seblak_app`:

```sql
-- File: sql/forgot_password_schema.sql
```

Execute via phpMyAdmin atau command line:
```bash
mysql -u root -p seblak_app < sql/forgot_password_schema.sql
```

### 3. Configure Email Settings

Edit file `config/config.php`:

```php
// Email Configuration (PHPMailer)
define('SMTP_HOST', 'smtp.gmail.com'); // SMTP server anda
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com'); // Email anda
define('SMTP_PASSWORD', 'your-app-password'); // App password
define('SMTP_FROM_EMAIL', 'noreply@seblakpredator.com');
define('SMTP_FROM_NAME', APP_NAME);
```

#### Cara Mendapatkan Gmail App Password:
1. Login ke Google Account
2. Buka: https://myaccount.google.com/apppasswords
3. Pilih "Mail" dan "Other (Custom name)"
4. Generate password
5. Copy password tersebut ke `SMTP_PASSWORD`

### 4. Update Application URL

Edit `config/config.php`:

```php
// Development
define('APP_URL', 'http://localhost/seblak-predator');

// Production (after SSL setup)
define('APP_URL', 'https://yourdomain.com');
define('DEVELOPMENT_MODE', false); // Set to false for HTTPS enforcement
```

### 5. Update forgot-password.php

File sudah dibuat di: `pages/auth/forgot-password.php`

Pastikan meng-include CSRF token. Tambahkan di form:

```html
<!-- Add hidden CSRF token field -->
<input type="hidden" name="csrf_token" id="csrfToken" value="<?php echo generateCSRFToken(); ?>">
```

### 6. Create Logs Directory

```bash
mkdir logs
chmod 755 logs
```

Atau manual: Buat folder `logs` di root project.

---

## 🔧 Configuration Options

### Rate Limiting Settings

Edit di `config/config.php`:

```php
// Rate Limiting Configuration
define('RATE_LIMIT_REQUESTS', 3);      // Max 3 requests
define('RATE_LIMIT_PERIOD', 900);      // Within 15 minutes
```

### OTP Settings

```php
// OTP Configuration
define('OTP_LENGTH', 6);                // 6 digit OTP
define('OTP_EXPIRE_MINUTES', 15);       // 15 minutes expiry
define('OTP_RESEND_COOLDOWN', 60);      // 60 seconds cooldown
```

### Password Settings

```php
// Password Configuration
define('PASSWORD_MIN_LENGTH', 8);       // Minimum 8 characters
```

---

## 🔐 HTTPS Setup (Production)

### Using Let's Encrypt (Recommended)

1. Install Certbot:
```bash
# Windows (via Chocolatey)
choco install certbot

# Linux (Ubuntu/Debian)
sudo apt-get install certbot
```

2. Generate SSL Certificate:
```bash
certbot certonly --standalone -d yourdomain.com -d www.yourdomain.com
```

3. Configure Apache/Nginx with SSL

**Apache (httpd.conf or .htaccess):**
```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot "D:/laragon/www/seblak-predator"
    
    SSLEngine on
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/privkey.pem
    SSLCertificateChainFile /path/to/chain.pem
</VirtualHost>

# Redirect HTTP to HTTPS
<VirtualHost *:80>
    ServerName yourdomain.com
    Redirect permanent / https://yourdomain.com/
</VirtualHost>
```

**Nginx:**
```nginx
server {
    listen 443 ssl;
    server_name yourdomain.com;
    
    ssl_certificate /path/to/fullchain.pem;
    ssl_certificate_key /path/to/privkey.pem;
    
    root /var/www/seblak-predator;
    index index.php;
}

server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}
```

4. Update config:
```php
define('DEVELOPMENT_MODE', false); // Enable HTTPS enforcement
define('APP_URL', 'https://yourdomain.com');
```

---

## 📝 File Structure

```
seblak-predator/
├── config/
│   ├── config.php           # Main configuration (NEW)
│   ├── koneksi.php          # Database connection (UPDATED)
│   └── EmailService.php     # Email service class (NEW)
├── handler/
│   └── forgot_password.php  # Forgot password handler (NEW)
├── pages/auth/
│   └── forgot-password.php  # Frontend page (EXISTS)
├── src/assets/js/
│   └── auth.js              # JavaScript handler (UPDATE NEEDED)
├── sql/
│   └── forgot_password_schema.sql  # Database schema (NEW)
├── logs/
│   └── security.log         # Security logs (AUTO-CREATED)
└── vendor/
    └── phpmailer/           # PHPMailer library (INSTALL VIA COMPOSER)
```

---

## 🧪 Testing

### 1. Test Email Configuration

Create `test_email.php`:

```php
<?php
require_once 'config/config.php';
require_once 'config/EmailService.php';

$emailService = new EmailService();
$result = $emailService->sendPasswordResetOTP(
    'test@example.com',
    'Test User',
    '123456'
);

echo $result ? 'Email sent successfully!' : 'Failed to send email';
?>
```

### 2. Test Database Connection

```php
<?php
require_once 'config/koneksi.php';
echo mysqli_ping($koneksi) ? 'Database connected!' : 'Connection failed';
?>
```

### 3. Test Rate Limiting

Try submitting forgot password form multiple times rapidly.

### 4. Test CSRF Protection

Try submitting form without valid CSRF token (should fail).

---

## 🛡️ Security Features

### 1. CSRF Protection
- Token generated per session
- Verified on every POST request
- Auto-expires after 1 hour

### 2. Rate Limiting
- IP-based tracking
- Separate limits for different actions
- Automatic cooldown period

### 3. OTP Security
- Hashed using bcrypt before storage
- Time-limited (15 minutes default)
- Single-use tokens
- Old tokens auto-deleted

### 4. HTTPS Enforcement
- Automatic redirect to HTTPS in production
- Configurable for development

### 5. SQL Injection Prevention
- All queries use prepared statements
- Parameter binding for all user inputs

### 6. XSS Prevention
- Input sanitization
- HTML entity encoding

### 7. Security Logging
- All events logged with IP and timestamp
- Failed attempts tracked
- User agent recording

---

## 📊 Database Schema Details

### password_reset_tokens
- Stores hashed OTPs
- Tracks usage and expiry
- Records IP and user agent

### rate_limiting
- Tracks request frequency
- IP-based identification
- Per-action limits

### security_logs
- Comprehensive event logging
- Searchable by various criteria
- Audit trail for security events

---

## 🐛 Troubleshooting

### Email Not Sending

1. **Check SMTP credentials:**
```php
var_dump(SMTP_HOST, SMTP_USERNAME, SMTP_PASSWORD);
```

2. **Enable debug mode:**
```php
$this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
```

3. **Check firewall:** Ensure port 587 is open

4. **Gmail issues:** Enable "Less secure app access" or use App Password

### Rate Limit Issues

Clear session:
```php
session_destroy();
```

Or reset in database:
```sql
DELETE FROM rate_limiting WHERE identifier = 'YOUR_IP';
```

### CSRF Token Errors

1. Check session is started
2. Verify token in form
3. Clear browser cache
4. Check session timeout

### Database Errors

1. Check connection credentials
2. Verify tables exist
3. Check user permissions
4. Enable error reporting:
```php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
```

---

## 📚 API Endpoints

### POST /handler/forgot_password.php

#### 1. Send OTP
```javascript
{
    "action": "send_otp",
    "email": "user@example.com",
    "csrf_token": "..."
}
```

Response:
```javascript
{
    "success": true,
    "message": "Kode OTP telah dikirim ke email Anda.",
    "data": {
        "email": "user@example.com",
        "expires_in_minutes": 15
    }
}
```

#### 2. Verify OTP
```javascript
{
    "action": "verify_otp",
    "otp": "123456",
    "csrf_token": "..."
}
```

#### 3. Reset Password
```javascript
{
    "action": "reset_password",
    "new_password": "newpassword123",
    "confirm_password": "newpassword123",
    "csrf_token": "..."
}
```

#### 4. Resend OTP
```javascript
{
    "action": "resend_otp",
    "csrf_token": "..."
}
```

---

## 🔄 Maintenance

### Clean Expired Tokens (Cron Job)

Create `cleanup_expired_tokens.php`:

```php
<?php
require_once 'config/koneksi.php';

// Delete expired tokens
$query = "DELETE FROM password_reset_tokens WHERE expires_at < NOW() OR (used = 1 AND used_at < DATE_SUB(NOW(), INTERVAL 7 DAY))";
executeUpdate($koneksi, $query);

echo "Cleanup completed\n";
?>
```

Run daily via cron:
```bash
0 2 * * * php /path/to/cleanup_expired_tokens.php
```

### Monitor Security Logs

```bash
tail -f logs/security.log
```

Or create monitoring script:
```php
<?php
$log = file_get_contents('logs/security.log');
$failed_attempts = substr_count($log, 'RATE_LIMIT_EXCEEDED');
echo "Failed attempts today: $failed_attempts\n";
?>
```

---

## 📞 Support

For issues or questions:
1. Check troubleshooting section
2. Review security logs
3. Test with provided testing scripts
4. Verify all configuration settings

---

## 📄 License

Part of Seblak Predator Restaurant Management System
© 2025 All Rights Reserved
