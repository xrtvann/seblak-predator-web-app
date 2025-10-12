<?php
/**
 * Development Email Service
 * For development/testing - logs emails to file instead of sending them
 */

class DevelopmentEmailService
{
    private $logFile;

    public function __construct()
    {
        $this->logFile = __DIR__ . '/../logs/emails.log';

        // Create logs directory if it doesn't exist
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    /**
     * Send OTP email for password reset (development version)
     * 
     * @param string $email Recipient email
     * @param string $name Recipient name
     * @param string $otp OTP code
     * @return bool Success status
     */
    public function sendPasswordResetOTP($email, $name, $otp)
    {
        $timestamp = date('Y-m-d H:i:s');
        $expire_time = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRE_MINUTES . ' minutes'));

        $emailContent = "
========================================
PASSWORD RESET EMAIL - DEVELOPMENT MODE
========================================
Date: {$timestamp}
To: {$email}
Name: {$name}
Subject: Kode OTP Reset Password - " . APP_NAME . "

Hi {$name},

Kode OTP untuk reset password Anda adalah:

**{$otp}**

Kode ini akan expire pada: {$expire_time}

Jangan berikan kode ini kepada siapapun.

Jika Anda tidak meminta reset password, abaikan email ini.

Best regards,
" . APP_NAME . " Team

========================================

";

        // Log to file
        $logSuccess = file_put_contents($this->logFile, $emailContent, FILE_APPEND | LOCK_EX) !== false;

        if ($logSuccess) {
            // Also display in browser for development
            if (DEVELOPMENT_MODE && !empty($_SESSION['show_email_debug'])) {
                $_SESSION['email_debug'][] = [
                    'to' => $email,
                    'subject' => 'Kode OTP Reset Password - ' . APP_NAME,
                    'otp' => $otp,
                    'expires' => $expire_time
                ];
            }

            logSecurityEvent('PASSWORD_RESET_EMAIL_SENT_DEV', [
                'email' => $email,
                'name' => $name,
                'method' => 'file_log'
            ]);

            return true;
        }

        logSecurityEvent('PASSWORD_RESET_EMAIL_FAILED_DEV', [
            'email' => $email,
            'error' => 'Failed to write to log file'
        ]);

        return false;
    }

    /**
     * Send password reset success email (development version)
     * 
     * @param string $email Recipient email
     * @param string $name Recipient name
     * @return bool Success status
     */
    public function sendPasswordResetSuccess($email, $name)
    {
        $timestamp = date('Y-m-d H:i:s');

        $emailContent = "
========================================
PASSWORD RESET SUCCESS EMAIL - DEVELOPMENT MODE
========================================
Date: {$timestamp}
To: {$email}
Name: {$name}
Subject: Password Reset Berhasil - " . APP_NAME . "

Hi {$name},

Password Anda telah berhasil direset.

Jika Anda tidak melakukan reset password, segera hubungi administrator.

Best regards,
" . APP_NAME . " Team

========================================

";

        // Log to file
        $logSuccess = file_put_contents($this->logFile, $emailContent, FILE_APPEND | LOCK_EX) !== false;

        if ($logSuccess) {
            logSecurityEvent('PASSWORD_RESET_SUCCESS_EMAIL_SENT_DEV', [
                'email' => $email,
                'name' => $name,
                'method' => 'file_log'
            ]);

            return true;
        }

        return false;
    }

    /**
     * Get the latest emails from log (for development debugging)
     */
    public function getRecentEmails($limit = 10)
    {
        if (!file_exists($this->logFile)) {
            return [];
        }

        $content = file_get_contents($this->logFile);
        $emails = explode('========================================', $content);

        return array_slice(array_filter($emails), -$limit);
    }

    /**
     * Clear email log
     */
    public function clearEmailLog()
    {
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
    }
}
?>