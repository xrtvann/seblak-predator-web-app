<?php
/**
 * Test Midtrans Webhook Notification
 * Simulasi notifikasi dari Midtrans untuk testing
 */

// Ambil order terakhir dengan payment midtrans
require_once 'config/koneksi.php';

$query = "SELECT id, order_number, total_amount FROM orders WHERE payment_method = 'midtrans' ORDER BY created_at DESC LIMIT 1";
$result = mysqli_query($koneksi, $query);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    die("Tidak ada order dengan payment midtrans. Buat order dulu!");
}

echo "Testing webhook untuk order: {$order['order_number']}\n";
echo "Total: Rp " . number_format($order['total_amount'], 0, ',', '.') . "\n\n";

// Simulasi notifikasi settlement (pembayaran berhasil)
$notification = [
    'transaction_status' => 'settlement',
    'order_id' => $order['order_number'] . '-' . time(),
    'gross_amount' => $order['total_amount'],
    'payment_type' => 'bank_transfer',
    'transaction_time' => date('Y-m-d H:i:s'),
    'fraud_status' => 'accept',
    'signature_key' => 'test_signature'
];

echo "Sending notification to webhook...\n";
echo "Data: " . json_encode($notification, JSON_PRETTY_PRINT) . "\n\n";

// Send POST request ke webhook
$ch = curl_init('http://localhost/seblak-predator/api/midtrans/notification.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notification));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Response (HTTP $http_code):\n";
echo $response . "\n\n";

// Cek status order setelah webhook
$query = "SELECT payment_status, order_status, updated_at FROM orders WHERE order_number = '{$order['order_number']}'";
$result = mysqli_query($koneksi, $query);
$updated_order = mysqli_fetch_assoc($result);

echo "Status Order Setelah Webhook:\n";
echo "Payment Status: {$updated_order['payment_status']}\n";
echo "Order Status: {$updated_order['order_status']}\n";
echo "Updated At: {$updated_order['updated_at']}\n";

// Cek payment_notifications
$query = "SELECT * FROM payment_notifications ORDER BY created_at DESC LIMIT 1";
$result = mysqli_query($koneksi, $query);
$notification_log = mysqli_fetch_assoc($result);

if ($notification_log) {
    echo "\nNotifikasi tersimpan di database:\n";
    echo "Order ID: {$notification_log['order_id']}\n";
    echo "Transaction Status: {$notification_log['transaction_status']}\n";
    echo "Payment Type: {$notification_log['payment_type']}\n";
    echo "Gross Amount: " . number_format($notification_log['gross_amount'], 0, ',', '.') . "\n";
}

echo "\n✅ Test selesai!\n";
?>