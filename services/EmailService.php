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

        return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
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
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
        }
        .otp-container {
            background-color: #f8f9fa;
            border: 2px dashed #dc2626;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
        }
        .otp-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #dc2626;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
        }
        .otp-validity {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning-box p {
            margin: 5px 0;
            font-size: 14px;
            color: #856404;
        }
        .info-text {
            font-size: 14px;
            color: #666;
            line-height: 1.8;
            margin: 15px 0;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
        .email-footer p {
            margin: 5px 0;
        }
        .security-tips {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .security-tips h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #1976D2;
        }
        .security-tips ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .security-tips li {
            font-size: 13px;
            color: #0d47a1;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🔐 Reset Password</h1>
        </div>
        
        <div class="email-body">
            <p class="greeting">Halo <strong>{$name}</strong>,</p>
            
            <p class="info-text">
                Kami menerima permintaan untuk mereset password akun Anda di <strong>{$app_name}</strong>.
                Gunakan kode OTP berikut untuk melanjutkan proses reset password:
            </p>
            
            <div class="otp-container">
                <div class="otp-label">Kode OTP Anda</div>
                <div class="otp-code">{$otp}</div>
                <div class="otp-validity">⏰ Kode berlaku selama {$expire_minutes} menit</div>
            </div>
            
            <div class="warning-box">
                <p><strong>⚠️ Perhatian:</strong></p>
                <p>Jika Anda <strong>tidak</strong> meminta reset password, abaikan email ini dan segera ubah password Anda untuk keamanan akun.</p>
            </div>
            
            <div class="security-tips">
                <h3>🛡️ Tips Keamanan:</h3>
                <ul>
                    <li>Jangan bagikan kode OTP ini kepada siapapun, termasuk staff {$app_name}</li>
                    <li>Pastikan Anda mengakses website resmi kami</li>
                    <li>Gunakan password yang kuat dan unik</li>
                    <li>Aktifkan verifikasi dua langkah jika tersedia</li>
                </ul>
            </div>
            
            <p class="info-text">
                Jika Anda mengalami kesulitan atau memiliki pertanyaan, silakan hubungi tim support kami.
            </p>
            
            <p class="info-text">
                Terima kasih,<br>
                <strong>Tim {$app_name}</strong>
            </p>
        </div>
        
        <div class="email-footer">
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            <p>&copy; 2025 {$app_name}. All rights reserved.</p>
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