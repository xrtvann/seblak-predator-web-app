<?php
/**
 * Manual Webhook Trigger untuk Testing
 * Cara pakai: php trigger-webhook.php [order_number] [status]
 * 
 * Contoh:
 * php trigger-webhook.php INV-20251120-9332 settlement
 * php trigger-webhook.php INV-20251120-9332 pending
 * php trigger-webhook.php INV-20251120-9332 expire
 */

require_once 'config/koneksi.php';

// Get parameters
$order_number = $argv[1] ?? null;
$transaction_status = $argv[2] ?? 'settlement';

if (!$order_number) {
    // Tampilkan order terakhir
    echo "╔══════════════════════════════════════════════════════════════╗\n";
    echo "║       Manual Webhook Trigger - Midtrans Notification        ║\n";
    echo "╚══════════════════════════════════════════════════════════════╝\n\n";

    echo "Usage: php trigger-webhook.php [order_number] [status]\n\n";

    echo "Available status:\n";
    echo "  - settlement  (Pembayaran berhasil)\n";
    echo "  - pending     (Menunggu pembayaran)\n";
    echo "  - expire      (Pembayaran expired)\n";
    echo "  - cancel      (Pembayaran dibatalkan)\n";
    echo "  - deny        (Pembayaran ditolak)\n\n";

    // Tampilkan 5 order terakhir dengan midtrans
    $query = "SELECT order_number, total_amount, payment_status, order_status, created_at 
              FROM orders 
              WHERE payment_method = 'midtrans' 
              ORDER BY created_at DESC 
              LIMIT 5";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        echo "Order terakhir dengan payment Midtrans:\n";
        echo "┌─────────────────────┬────────────┬────────────┬──────────────┬─────────────────────┐\n";
        echo "│ Order Number        │ Total      │ Pay Status │ Order Status │ Created At          │\n";
        echo "├─────────────────────┼────────────┼────────────┼──────────────┼─────────────────────┤\n";

        while ($row = mysqli_fetch_assoc($result)) {
            printf(
                "│ %-19s │ %10s │ %-10s │ %-12s │ %-19s │\n",
                $row['order_number'],
                number_format($row['total_amount'], 0),
                $row['payment_status'],
                $row['order_status'],
                $row['created_at']
            );
        }
        echo "└─────────────────────┴────────────┴────────────┴──────────────┴─────────────────────┘\n\n";

        echo "Contoh penggunaan:\n";
        $query = "SELECT order_number FROM orders WHERE payment_method = 'midtrans' ORDER BY created_at DESC LIMIT 1";
        $result = mysqli_query($koneksi, $query);
        $latest = mysqli_fetch_assoc($result);
        if ($latest) {
            echo "  php trigger-webhook.php {$latest['order_number']} settlement\n";
        }
    } else {
        echo "❌ Tidak ada order dengan payment midtrans!\n";
        echo "   Buat order dengan metode pembayaran Midtrans terlebih dahulu.\n";
    }
    exit;
}

// Validasi order exists
$query = "SELECT id, order_number, total_amount, payment_status, order_status FROM orders WHERE order_number = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "s", $order_number);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    echo "❌ Error: Order '{$order_number}' tidak ditemukan!\n";
    exit(1);
}

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║           Triggering Webhook for Order                      ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

echo "Order Number      : {$order['order_number']}\n";
echo "Total Amount      : Rp " . number_format($order['total_amount'], 0, ',', '.') . "\n";
echo "Current Pay Status: {$order['payment_status']}\n";
echo "Current Order Status: {$order['order_status']}\n";
echo "Transaction Status: {$transaction_status}\n\n";

// Buat notifikasi
$notification = [
    'transaction_status' => $transaction_status,
    'order_id' => $order['order_number'] . '-' . time(),
    'gross_amount' => $order['total_amount'],
    'payment_type' => 'bank_transfer',
    'transaction_time' => date('Y-m-d H:i:s'),
    'fraud_status' => 'accept',
    'signature_key' => 'manual_trigger'
];

echo "Sending notification to webhook...\n";
echo str_repeat("─", 62) . "\n";

// Send POST request ke webhook
$ch = curl_init('http://localhost/seblak-predator/api/midtrans/notification.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notification));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$response_data = json_decode($response, true);

if ($http_code == 200 && isset($response_data['success']) && $response_data['success']) {
    echo "✅ Webhook berhasil diproses!\n\n";

    // Cek status order setelah webhook
    $query = "SELECT payment_status, order_status, updated_at FROM orders WHERE order_number = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $order_number);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $updated_order = mysqli_fetch_assoc($result);

    echo "Status Order setelah webhook:\n";
    echo "  Payment Status : {$order['payment_status']} → {$updated_order['payment_status']}\n";
    echo "  Order Status   : {$order['order_status']} → {$updated_order['order_status']}\n";
    echo "  Updated At     : {$updated_order['updated_at']}\n\n";

    // Status mapping info
    $status_map = [
        'settlement' => ['payment' => 'paid', 'order' => 'completed'],
        'pending' => ['payment' => 'pending', 'order' => 'pending'],
        'expire' => ['payment' => 'failed', 'order' => 'cancelled'],
        'cancel' => ['payment' => 'failed', 'order' => 'cancelled'],
        'deny' => ['payment' => 'failed', 'order' => 'cancelled'],
    ];

    if (isset($status_map[$transaction_status])) {
        $expected = $status_map[$transaction_status];
        if (
            $updated_order['payment_status'] == $expected['payment'] &&
            $updated_order['order_status'] == $expected['order']
        ) {
            echo "✅ Status sudah sesuai dengan mapping!\n";
        } else {
            echo "⚠️  Status tidak sesuai dengan expected mapping.\n";
        }
    }
} else {
    echo "❌ Webhook gagal!\n";
    echo "HTTP Code: {$http_code}\n";
    echo "Response: {$response}\n";
}

echo "\n" . str_repeat("═", 62) . "\n";
?>