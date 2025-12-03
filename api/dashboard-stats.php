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
        'totalPelanggan' => 0,
        'pendapatanBulanIni' => 0,
        'pengeluaranHariIni' => 0,
        'pengeluaranBulanIni' => 0,
        'profitBersih' => 0,
        'penjualan7Hari' => []
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

// 5. Pendapatan Bulan Ini
try {
    $firstDayOfMonth = date('Y-m-01 00:00:00');
    $lastDayOfMonth = date('Y-m-t 23:59:59');
    $query = "SELECT SUM(total_amount) as total FROM orders WHERE created_at >= '$firstDayOfMonth' AND created_at <= '$lastDayOfMonth' AND payment_status IN ('paid', 'settlement')";
    $resultPendapatanBulan = @mysqli_query($koneksi, $query);
    if ($resultPendapatanBulan) {
        $row = mysqli_fetch_assoc($resultPendapatanBulan);
        $stats['data']['pendapatanBulanIni'] = (float) ($row['total'] ?? 0);
    }
} catch (Exception $e) {
    // Keep default value
}

// 6. Pengeluaran Hari Ini
try {
    $today = date('Y-m-d');
    $query = "SELECT SUM(amount) as total FROM expenses WHERE expense_date >= '$today 00:00:00' AND expense_date <= '$today 23:59:59'";
    $resultPengeluaranHari = @mysqli_query($koneksi, $query);
    if ($resultPengeluaranHari) {
        $row = mysqli_fetch_assoc($resultPengeluaranHari);
        $stats['data']['pengeluaranHariIni'] = (float) ($row['total'] ?? 0);
    }
} catch (Exception $e) {
    // Keep default value
}

// 7. Pengeluaran Bulan Ini
try {
    $firstDayOfMonth = date('Y-m-01 00:00:00');
    $lastDayOfMonth = date('Y-m-t 23:59:59');
    $query = "SELECT SUM(amount) as total FROM expenses WHERE expense_date >= '$firstDayOfMonth' AND expense_date <= '$lastDayOfMonth'";
    $resultPengeluaranBulan = @mysqli_query($koneksi, $query);
    if ($resultPengeluaranBulan) {
        $row = mysqli_fetch_assoc($resultPengeluaranBulan);
        $stats['data']['pengeluaranBulanIni'] = (float) ($row['total'] ?? 0);
    }
} catch (Exception $e) {
    // Keep default value
}

// 8. Profit Bersih (Pendapatan - Pengeluaran Bulan Ini)
$stats['data']['profitBersih'] = $stats['data']['pendapatanBulanIni'] - $stats['data']['pengeluaranBulanIni'];

// 9. Penjualan 7 Hari Terakhir
try {
    $penjualan7Hari = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $query = "SELECT 
            COALESCE(SUM(total_amount), 0) as total 
            FROM orders 
            WHERE DATE(created_at) = '$date' 
            AND payment_status IN ('paid', 'settlement')";
        $result = @mysqli_query($koneksi, $query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $penjualan7Hari[] = [
                'date' => date('d M', strtotime($date)),
                'total' => (float) ($row['total'] ?? 0)
            ];
        }
    }
    $stats['data']['penjualan7Hari'] = $penjualan7Hari;
} catch (Exception $e) {
    // Keep default value
}

echo json_encode($stats);
