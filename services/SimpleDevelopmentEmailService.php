<?php
/**
 * Simple Development Email Service
 * Shows OTP directly in browser - no email configuration needed!
 */

class SimpleDevelopmentEmailService
{
    private $logFile;

    public function __construct()
    {
        $this->logFile = __DIR__ . '/../logs/development_emails.log';

        // Create logs directory if it doesn't exist
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    /**
     * Send OTP email for password reset (development version)
     * Shows OTP in browser and logs it
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

        // Log to file for debugging
        $logContent = "[{$timestamp}] OTP for {$email} ({$name}): {$otp} (expires: {$expire_time})\n";
        file_put_contents($this->logFile, $logContent, FILE_APPEND | LOCK_EX);

        // Store in session for browser display
        if (!isset($_SESSION['development_emails'])) {
            $_SESSION['development_emails'] = [];
        }

        $_SESSION['development_emails'][] = [
            'timestamp' => $timestamp,
            'to' => $email,
            'name' => $name,
            'otp' => $otp,
            'expires_at' => $expire_time,
            'subject' => 'Kode OTP Reset Password - ' . (defined('APP_NAME') ? APP_NAME : 'Seblak Predator')
        ];

        // Keep only last 10 emails in session
        if (count($_SESSION['development_emails']) > 10) {
            $_SESSION['development_emails'] = array_slice($_SESSION['development_emails'], -10);
        }

        // Log security event
        if (function_exists('logSecurityEvent')) {
            logSecurityEvent('PASSWORD_RESET_EMAIL_SENT_DEV', [
                'email' => $email,
                'name' => $name,
                'method' => 'development_display',
                'otp_shown' => true
            ]);
        }

        return true; // Always successful in development
    }

    /**
     * Get the latest OTP for display
     */
    public static function getLatestOTP()
    {
        if (isset($_SESSION['development_emails']) && !empty($_SESSION['development_emails'])) {
            return end($_SESSION['development_emails']);
        }
        return null;
    }

    /**
     * Get all development emails
     */
    public static function getAllEmails()
    {
        return $_SESSION['development_emails'] ?? [];
    }

    /**
     * Clear development emails
     */
    public static function clearEmails()
    {
        unset($_SESSION['development_emails']);
    }
}
?>