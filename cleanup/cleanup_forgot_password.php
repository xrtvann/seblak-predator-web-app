<?php
/**
 * Cleanup Script for Forgot Password Tokens
 * Run this script daily via cron job to clean up expired tokens
 * 
 * Cron example (run daily at 2 AM):
 * 0 2 * * * php /path/to/cleanup_forgot_password.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/koneksi.php';

echo "Starting cleanup process...\n";

// Begin transaction
beginTransaction($koneksi);

try {
    // 1. Delete expired tokens
    $delete_expired = "DELETE FROM password_reset_tokens WHERE expires_at < NOW()";
    $result1 = executeUpdate($koneksi, $delete_expired);
    $expired_count = getAffectedRows($koneksi);
    echo "Deleted {$expired_count} expired tokens\n";

    // 2. Delete old used tokens (older than 7 days)
    $delete_old_used = "DELETE FROM password_reset_tokens 
                        WHERE used = 1 AND used_at < DATE_SUB(NOW(), INTERVAL 7 DAY)";
    $result2 = executeUpdate($koneksi, $delete_old_used);
    $used_count = getAffectedRows($koneksi);
    echo "Deleted {$used_count} old used tokens\n";

    // 3. Clean up old rate limiting entries (older than 24 hours)
    $delete_rate_limit = "DELETE FROM rate_limiting 
                          WHERE last_request < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $result3 = executeUpdate($koneksi, $delete_rate_limit);
    $rate_count = getAffectedRows($koneksi);
    echo "Deleted {$rate_count} old rate limiting entries\n";

    // 4. Archive old security logs (older than 30 days)
    $archive_logs = "DELETE FROM security_logs 
                     WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $result4 = executeUpdate($koneksi, $archive_logs);
    $log_count = getAffectedRows($koneksi);
    echo "Archived {$log_count} old security logs\n";

    // Commit transaction
    commitTransaction($koneksi);

    $timestamp = date('Y-m-d H:i:s');
    echo "\n✅ Cleanup completed successfully at {$timestamp}\n";
    echo "Total items processed: " . ($expired_count + $used_count + $rate_count + $log_count) . "\n";

    // Log cleanup event
    logSecurityEvent('CLEANUP_COMPLETED', [
        'expired_tokens' => $expired_count,
        'used_tokens' => $used_count,
        'rate_limits' => $rate_count,
        'security_logs' => $log_count,
        'timestamp' => $timestamp
    ]);

} catch (Exception $e) {
    // Rollback on error
    rollbackTransaction($koneksi);

    echo "❌ Error during cleanup: " . $e->getMessage() . "\n";

    logSecurityEvent('CLEANUP_FAILED', [
        'error' => $e->getMessage()
    ]);
}

// Close connection
mysqli_close($koneksi);
?>