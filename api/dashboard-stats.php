<?php
/**
 * Dashboard Statistics API
 * Returns real-time statistics for dashboard cards
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/koneksi.php';

// Set timeout for queries
mysqli_query($koneksi, "SET SESSION MAX_EXECUTION_TIME=2000");

$stats = [
    'success' => true,
    'data' => [
        'totalTopping' => 0,
        'transaksiHariIni' => 0,
        'pendapatanHariIni' => 0,
        'totalPelanggan' => 0
    ]
];

// 1. Total Topping
try {
    $resultTopping = @mysqli_query($koneksi, "SELECT COUNT(*) as total FROM toppings");
    if ($resultTopping) {
        $row = mysqli_fetch_assoc($resultTopping);
        $stats['data']['totalTopping'] = (int) ($row['total'] ?? 0);
    }
} catch (Exception $e) {
    // Keep default value
}

// 2. Transaksi Hari Ini
try {
    $today = date('Y-m-d');
    $resultTransaksi = @mysqli_query($koneksi, "SELECT COUNT(*) as total FROM orders WHERE created_at >= '$today 00:00:00' AND created_at <= '$today 23:59:59'");
    if ($resultTransaksi) {
        $row = mysqli_fetch_assoc($resultTransaksi);
        $stats['data']['transaksiHariIni'] = (int) ($row['total'] ?? 0);
    }
} catch (Exception $e) {
    // Keep default value
}

// 3. Pendapatan Hari Ini
try {
    $today = date('Y-m-d');
    // Hitung hanya transaksi yang sudah dibayar berdasarkan payment_status
    $query = "SELECT SUM(total_amount) as total FROM orders WHERE created_at >= '$today 00:00:00' AND created_at <= '$today 23:59:59' AND payment_status IN ('paid', 'settlement')";
    $resultPendapatan = @mysqli_query($koneksi, $query);
    if ($resultPendapatan) {
        $row = mysqli_fetch_assoc($resultPendapatan);
        $stats['data']['pendapatanHariIni'] = (float) ($row['total'] ?? 0);
    }
} catch (Exception $e) {
    // Keep default value
}

// 4. Total Pelanggan
try {
    $resultPelanggan = @mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE role_id = 'role_customer'");
    if ($resultPelanggan) {
        $row = mysqli_fetch_assoc($resultPelanggan);
        $stats['data']['totalPelanggan'] = (int) ($row['total'] ?? 0);
    }
} catch (Exception $e) {
    // Keep default value
}

echo json_encode($stats);
