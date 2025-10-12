<?php
/**
 * Simple Email Service using PHP mail() function
 * No SMTP configuration required - uses server's built-in mail
 */

class SimpleEmailService
{
    /**
     * Send OTP email using PHP's built-in mail() function
     * No SMTP configuration needed
     */
    public function sendPasswordResetOTP($email, $name, $otp)
    {
        $subject = "Kode OTP Reset Password - " . (defined('APP_NAME') ? APP_NAME : 'Seblak Predator');

        $message = "
Hi $name,

Kode OTP untuk reset password Anda adalah:

$otp

Kode ini akan expired dalam 15 menit.

Jangan berikan kode ini kepada siapapun.

Jika Anda tidak meminta reset password, abaikan email ini.

Best regards,
" . (defined('APP_NAME') ? APP_NAME : 'Seblak Predator') . " Team
        ";

        // Set headers
        $headers = "From: noreply@seblakpredator.com\r\n";
        $headers .= "Reply-To: noreply@seblakpredator.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        // Send email using PHP's built-in mail function
        $sent = mail($email, $subject, $message, $headers);

        if ($sent) {
            error_log("OTP email sent to: $email using PHP mail()");
            return true;
        } else {
            error_log("Failed to send OTP email to: $email using PHP mail()");
            return false;
        }
    }

    /**
     * Send password reset success notification
     */
    public function sendPasswordResetSuccess($email, $name)
    {
        $subject = "Password Berhasil Direset - " . (defined('APP_NAME') ? APP_NAME : 'Seblak Predator');

        $message = "
Hi $name,

Password Anda telah berhasil direset.

Jika ini bukan Anda, segera hubungi administrator.

Best regards,
" . (defined('APP_NAME') ? APP_NAME : 'Seblak Predator') . " Team
        ";

        $headers = "From: noreply@seblakpredator.com\r\n";
        $headers .= "Reply-To: noreply@seblakpredator.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        return mail($email, $subject, $message, $headers);
    }
}
?>