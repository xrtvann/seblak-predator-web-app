<?php
/**
 * Email Service using PHPMailer
 * Handles all email sending functionality
 */

// Load Composer autoloader
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    die('PHPMailer not installed. Please run: composer require phpmailer/phpmailer');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../config/config.php';

class EmailService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    /**
     * Configure PHPMailer settings
     */
    private function configure()
    {
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = SMTP_HOST;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = SMTP_USERNAME;
            $this->mailer->Password = SMTP_PASSWORD;
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = SMTP_PORT;

            // Set default from address
            $this->mailer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);

            // Character set
            $this->mailer->CharSet = 'UTF-8';

            // Disable debug output in production
            if (DEVELOPMENT_MODE) {
                $this->mailer->SMTPDebug = SMTP::DEBUG_OFF;
            }
        } catch (Exception $e) {
            logSecurityEvent('EMAIL_CONFIGURATION_FAILED', [
                'error' => $e->getMessage()
            ]);
            throw new Exception("Email configuration failed: " . $e->getMessage());
        }
    }

    /**
     * Send OTP email for password reset
     * 
     * @param string $email Recipient email
     * @param string $name Recipient name
     * @param string $otp OTP code
     * @return bool Success status
     */
    public function sendPasswordResetOTP($email, $name, $otp)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $name);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Kode OTP Reset Password - ' . APP_NAME;

            $body = $this->getPasswordResetEmailTemplate($name, $otp);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);

            $result = $this->mailer->send();

            if ($result) {
                logSecurityEvent('PASSWORD_RESET_EMAIL_SENT', [
                    'email' => $email,
                    'name' => $name
                ]);
            }

            return $result;
        } catch (Exception $e) {
            logSecurityEvent('PASSWORD_RESET_EMAIL_FAILED', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get password reset email HTML template
     * 
     * @param string $name Recipient name
     * @param string $otp OTP code
     * @return string HTML email content
     */
    private function getPasswordResetEmailTemplate($name, $otp)
    {
        $app_name = APP_NAME;
        $expire_minutes = OTP_EXPIRE_MINUTES;
        $current_year = date('Y');

        return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - {$app_name}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 50%, #991b1b 100%);
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        .email-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="0.5" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }
        .logo-container {
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            backdrop-filter: blur(10px);
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        .email-header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        .email-body {
            padding: 50px 40px;
        }
        .greeting {
            font-size: 24px;
            margin-bottom: 10px;
            color: #1f2937;
            font-weight: 700;
            text-align: center;
        }
        .greeting strong {
            color: #dc2626;
        }
        .subtitle {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 35px;
            text-align: center;
            font-weight: 400;
        }
        .intro-text {
            font-size: 16px;
            color: #666;
            line-height: 1.8;
            margin-bottom: 35px;
            text-align: center;
        }
        .otp-container {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 3px solid #dc2626;
            border-radius: 16px;
            padding: 40px;
            margin: 40px 0;
            text-align: center;
            position: relative;
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.1);
        }
        .otp-container::before {
            content: '🔐';
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: #dc2626;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .otp-label {
            font-size: 14px;
            color: #991b1b;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }
        .otp-code {
            font-size: 48px;
            font-weight: 900;
            color: #dc2626;
            letter-spacing: 12px;
            font-family: 'Courier New', monospace;
            margin: 20px 0;
            text-shadow: 2px 2px 4px rgba(220, 38, 38, 0.2);
            background: white;
            padding: 20px;
            border-radius: 12px;
            display: inline-block;
            border: 2px dashed #dc2626;
        }
        .otp-validity {
            font-size: 14px;
            color: #991b1b;
            margin-top: 15px;
            font-weight: 500;
        }
        .validity-icon {
            color: #f59e0b;
            margin-right: 5px;
        }
        .instruction-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-left: 4px solid #3b82f6;
            padding: 25px;
            margin: 30px 0;
            border-radius: 8px;
            position: relative;
        }
        .instruction-box h3 {
            margin: 0 0 15px 0;
            font-size: 18px;
            color: #1e40af;
            font-weight: 600;
        }
        .instruction-box ol {
            margin: 0;
            padding-left: 20px;
            color: #1e3a8a;
        }
        .instruction-box li {
            margin: 8px 0;
            font-size: 14px;
        }
        .warning-box {
            background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%);
            border-left: 4px solid #f59e0b;
            padding: 25px;
            margin: 30px 0;
            border-radius: 8px;
        }
        .warning-box h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #d97706;
            font-weight: 600;
        }
        .warning-box p {
            margin: 8px 0;
            font-size: 14px;
            color: #92400e;
        }
        .security-tips {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-left: 4px solid #22c55e;
            padding: 25px;
            margin: 30px 0;
            border-radius: 8px;
        }
        .security-tips h3 {
            margin: 0 0 15px 0;
            font-size: 16px;
            color: #16a34a;
            font-weight: 600;
        }
        .security-tips ul {
            margin: 0;
            padding-left: 20px;
        }
        .security-tips li {
            font-size: 14px;
            color: #166534;
            margin: 8px 0;
        }
        .closing-text {
            font-size: 16px;
            color: #666;
            line-height: 1.8;
            margin: 30px 0 20px 0;
            text-align: center;
        }
        .signature {
            background: linear-gradient(135deg, #fefbff 0%, #f3e8ff 100%);
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            margin: 30px 0;
        }
        .signature p {
            margin: 5px 0;
            font-size: 16px;
            color: #7c3aed;
            font-weight: 600;
        }
        .email-footer {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: #d1d5db;
            padding: 30px;
            text-align: center;
            font-size: 14px;
        }
        .email-footer p {
            margin: 8px 0;
            line-height: 1.6;
        }
        .footer-links {
            margin: 20px 0 10px 0;
        }
        .footer-links a {
            color: #dc2626;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
        }
        .footer-links a:hover {
            color: #b91c1c;
        }
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
            margin: 30px 0;
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 12px;
            }
            .email-body {
                padding: 30px 20px;
            }
            .email-header {
                padding: 30px 20px;
            }
            .otp-code {
                font-size: 36px;
                letter-spacing: 8px;
                padding: 15px;
            }
            .instruction-box, .warning-box, .security-tips {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <div class="logo-container">
                🌶️
            </div>
            <h1>Hi, Welcome Back</h1>
            <p>Reset your password to continue</p>
        </div>
        
        <div class="email-body">
            <div class="greeting">
                Hello, <strong>{$name}</strong>
            </div>
            <div class="subtitle">
                Enter your credentials to continue
            </div>
            
            <p class="intro-text">
                Kami menerima permintaan untuk mereset password akun Anda. Gunakan kode OTP di bawah ini untuk melanjutkan proses reset password.
            </p>
            
            <div class="otp-container">
                <div class="otp-label">Kode Verifikasi Anda</div>
                <div class="otp-code">{$otp}</div>
                <div class="otp-validity">
                    <span class="validity-icon">⏰</span>
                    Berlaku selama {$expire_minutes} menit
                </div>
            </div>
            
            <div class="instruction-box">
                <h3>📋 Langkah Selanjutnya:</h3>
                <ol>
                    <li>Kembali ke halaman reset password</li>
                    <li>Masukkan kode verifikasi <strong>{$otp}</strong></li>
                    <li>Buat password baru yang kuat</li>
                    <li>Konfirmasi password baru Anda</li>
                    <li>Login dengan password yang baru</li>
                </ol>
            </div>
            
            <div class="warning-box">
                <h3>⚠️ Perhatian Keamanan</h3>
                <p><strong>Jika Anda tidak meminta reset password</strong>, segera abaikan email ini dan ubah password akun Anda sebagai tindakan pencegahan.</p>
                <p>Laporkan aktivitas mencurigakan ke tim support kami.</p>
            </div>
            
            <div class="security-tips">
                <h3>🛡️ Tips Keamanan</h3>
                <ul>
                    <li><strong>Jangan bagikan</strong> kode ini kepada siapapun, termasuk staff {$app_name}</li>
                    <li><strong>Pastikan URL</strong> website yang Anda akses adalah resmi</li>
                    <li><strong>Gunakan password</strong> yang unik dan mengandung huruf, angka & simbol</li>
                    <li><strong>Aktifkan notifikasi</strong> keamanan jika tersedia</li>
                    <li><strong>Logout</strong> dari perangkat yang tidak dikenal</li>
                </ul>
            </div>
            
            <div class="divider"></div>
            
            <p class="closing-text">
                Jika Anda mengalami kesulitan atau memiliki pertanyaan mengenai keamanan akun,
                jangan ragu untuk menghubungi tim support kami.
            </p>
            
            <div class="signature">
                <p>Salam hangat,</p>
                <p><strong>Tim {$app_name}</strong> 🌶️</p>
            </div>
        </div>
        
        <div class="email-footer">
            <div class="footer-links">
                <a href="#">Support Center</a>
                <a href="#">Kebijakan Privasi</a>
                <a href="#">Syarat & Ketentuan</a>
            </div>
            <p>Email ini dikirim secara otomatis dari sistem keamanan {$app_name}</p>
            <p>Mohon tidak membalas email ini karena mailbox tidak dipantau</p>
            <p>&copy; {$current_year} {$app_name}. Semua hak dilindungi undang-undang.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Send password reset success notification
     * 
     * @param string $email Recipient email
     * @param string $name Recipient name
     * @return bool Success status
     */
    public function sendPasswordResetSuccess($email, $name)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $name);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Password Berhasil Direset - ' . APP_NAME;

            $body = $this->getPasswordResetSuccessTemplate($name);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);

            return $this->mailer->send();
        } catch (Exception $e) {
            logSecurityEvent('PASSWORD_SUCCESS_EMAIL_FAILED', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get password reset success email template
     */
    private function getPasswordResetSuccessTemplate($name)
    {
        $app_name = APP_NAME;
        $timestamp = date('d M Y, H:i:s');

        return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Success</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 30px;
        }
        .success-icon {
            text-align: center;
            font-size: 60px;
            margin: 20px 0;
        }
        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>✅ Password Berhasil Direset</h1>
        </div>
        
        <div class="email-body">
            <div class="success-icon">🎉</div>
            
            <p>Halo <strong>{$name}</strong>,</p>
            
            <p>Password akun Anda di <strong>{$app_name}</strong> telah berhasil direset pada {$timestamp}.</p>
            
            <div class="info-box">
                <p><strong>ℹ️ Informasi:</strong></p>
                <p>Jika Anda tidak melakukan perubahan ini, segera hubungi tim support kami untuk keamanan akun Anda.</p>
            </div>
            
            <p>Terima kasih,<br><strong>Tim {$app_name}</strong></p>
        </div>
        
        <div class="email-footer">
            <p>&copy; 2025 {$app_name}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
?>