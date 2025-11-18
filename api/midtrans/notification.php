<?php
/**
 * Midtrans Payment Notification Handler
 * Endpoint untuk menerima notifikasi dari Midtrans tentang status pembayaran
 * 
 * Webhook URL: http://yourdomain.com/api/midtrans/notification.php
 */

header('Content-Type: application/json');
require_once '../../config/koneksi.php';
require_once 'config.php';

// Log incoming notification
$request_body = file_get_contents('php://input');
error_log('=== Midtrans Notification Received ===');
error_log('Body: ' . $request_body);
error_log('Headers: ' . json_encode(getallheaders()));

try {
    // Get notification data
    $notification = json_decode($request_body, true);

    if (!$notification) {
        throw new Exception('Invalid notification data');
    }

    // Extract important data (be tolerant to nested structure)
    $order_id = $notification['order_id'] ?? ($notification['transaction_details']['order_id'] ?? null);
    $transaction_status = $notification['transaction_status'] ?? ($notification['transaction_status'] ?? null);
    $fraud_status = $notification['fraud_status'] ?? ($notification['fraud_status'] ?? 'accept');
    $payment_type = $notification['payment_type'] ?? ($notification['payment_type'] ?? null);
    $transaction_time = $notification['transaction_time'] ?? ($notification['transaction_time'] ?? null);
    $gross_amount = $notification['gross_amount'] ?? ($notification['transaction_details']['gross_amount'] ?? null);
    $signature_key = $notification['signature_key'] ?? ($notification['signature_key'] ?? null);

    if (!$order_id || !$transaction_status) {
        throw new Exception('Missing required notification fields');
    }

    // Extract original order_number from Midtrans order_id
    // Format: ORD-20251118-001-1234567890 -> ORD-20251118-001
    $order_number = $order_id;
    if (preg_match('/^(ORD-\d{8}-\d+)-\d+$/', $order_id, $matches)) {
        $order_number = $matches[1]; // Extract the original order number
        error_log('Extracted order_number: ' . $order_number . ' from order_id: ' . $order_id);
    }

    // Verify signature (security check) - Skip in development/sandbox
    $is_production = defined('MIDTRANS_IS_PRODUCTION') ? MIDTRANS_IS_PRODUCTION : false;

    if ($is_production && !empty($signature_key)) {
        $server_key = MIDTRANS_SERVER_KEY;
        $status_code = $notification['status_code'] ?? '200';
        $sig_source = $order_id . $status_code . $gross_amount . $server_key;
        $generated_signature = hash('sha512', $sig_source);

        if ($signature_key !== $generated_signature) {
            error_log('Signature mismatch! Source: ' . $sig_source);
            error_log('Expected: ' . $generated_signature . ', Got: ' . $signature_key);
            throw new Exception('Invalid signature');
        }
        error_log('✓ Signature verified');
    } else {
        error_log('⚠ Signature verification skipped (sandbox/development mode)');
    }

    // Determine payment status based on transaction status
    $payment_status = 'pending';
    $order_status = 'pending';

    switch ($transaction_status) {
        case 'capture':
            if ($fraud_status == 'accept') {
                $payment_status = 'paid';
                $order_status = 'processing';
            }
            break;

        case 'settlement':
            $payment_status = 'paid';
            $order_status = 'processing';
            break;

        case 'pending':
            $payment_status = 'pending';
            $order_status = 'pending';
            break;

        case 'deny':
        case 'cancel':
        case 'expire':
            $payment_status = 'failed';
            $order_status = 'cancelled';
            break;

        case 'refund':
        case 'partial_refund':
            $payment_status = 'refunded';
            $order_status = 'cancelled';
            break;
    }

    // Update order in database (payment_method already set as 'midtrans')
    $update_query = "UPDATE orders 
                     SET payment_status = ?,
                         order_status = ?,
                         updated_at = NOW()
                     WHERE order_number = ?";

    $stmt = mysqli_prepare($koneksi, $update_query);
    if (!$stmt) {
        throw new Exception('DB prepare failed: ' . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($stmt, 'sss', $payment_status, $order_status, $order_number);

    if (!mysqli_stmt_execute($stmt)) {
        $err = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        throw new Exception('Failed to update order: ' . $err);
    }

    $affected_rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    // Log notification details
    $log_query = "INSERT INTO payment_notifications 
                  (order_id, transaction_status, payment_type, gross_amount, notification_data, created_at) 
                  VALUES (?, ?, ?, ?, ?, NOW())";

    $log_stmt = mysqli_prepare($koneksi, $log_query);
    if ($log_stmt) {
        $notification_json = json_encode($notification);
        // types: s=order_id, s=transaction_status, s=payment_type, d=gross_amount, s=notification_json
        $gross_amount_val = (float) $gross_amount;
        mysqli_stmt_bind_param($log_stmt, 'sssds', $order_id, $transaction_status, $payment_type, $gross_amount_val, $notification_json);
        mysqli_stmt_execute($log_stmt);
        mysqli_stmt_close($log_stmt);
    } else {
        error_log('Failed to prepare payment_notifications insert: ' . mysqli_error($koneksi));
    }

    // Response
    echo json_encode([
        'success' => true,
        'message' => 'Notification processed',
        'midtrans_order_id' => $order_id,
        'order_number' => $order_number,
        'payment_status' => $payment_status,
        'order_status' => $order_status,
        'affected_rows' => $affected_rows
    ]);

    error_log('Notification processed successfully for order: ' . $order_number . ' (Midtrans ID: ' . $order_id . ')');

} catch (Exception $e) {
    error_log('Notification Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
