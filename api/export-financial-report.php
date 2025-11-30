<?php
/**
 * API untuk ekspor laporan keuangan ke PDF
 * Menggunakan dompdf untuk membuat file PDF
 */

require_once '../config/koneksi.php';
require_once '../vendor/autoload.php'; // Load dompdf

use Dompdf\Dompdf;
use Dompdf\Options;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    exportFinancialReportPDF();
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

function exportFinancialReportPDF()
{
    global $koneksi;

    $period = $_GET['period'] ?? 'month';
    $start_date = null;
    $end_date = null;
    $preview = isset($_GET['preview']); // Check if preview parameter is set

    // Determine date range based on period
    switch ($period) {
        case 'today':
            $start_date = date('Y-m-d 00:00:00');
            $end_date = date('Y-m-d 23:59:59');
            break;
        case 'week':
            $start_date = date('Y-m-d 00:00:00', strtotime('monday this week'));
            $end_date = date('Y-m-d 23:59:59', strtotime('sunday this week'));
            break;
        case 'month':
            $start_date = date('Y-m-01 00:00:00');
            $end_date = date('Y-m-t 23:59:59');
            break;
        case 'year':
            $start_date = date('Y-01-01 00:00:00');
            $end_date = date('Y-12-31 23:59:59');
            break;
        case 'all':
            // Get all data from earliest to latest
            $start_date = '1970-01-01 00:00:00';
            $end_date = '2099-12-31 23:59:59';
            break;
        case 'custom':
            if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
                $start_date = $_GET['start_date'] . ' 00:00:00';
                $end_date = $_GET['end_date'] . ' 23:59:59';
            } else {
                // Default to this month if custom dates not provided
                $start_date = date('Y-m-01 00:00:00');
                $end_date = date('Y-m-t 23:59:59');
            }
            break;
        default:
            $start_date = date('Y-m-01 00:00:00');
            $end_date = date('Y-m-t 23:59:59');
    }

    try {
        // Check database connection
        if (!$koneksi) {
            throw new Exception('Database connection failed');
        }

        // Calculate total revenue from orders
        $revenue_query = "SELECT
            COALESCE(SUM(total_amount), 0) as total_revenue,
            COUNT(*) as total_orders
            FROM orders
            WHERE created_at BETWEEN ? AND ?
            AND status != 'cancelled'
            AND is_deleted = 0";

        $stmt = $koneksi->prepare($revenue_query);
        if (!$stmt) {
            throw new Exception('Failed to prepare revenue query: ' . $koneksi->error);
        }

        $stmt->bind_param('ss', $start_date, $end_date);
        $stmt->execute();
        $revenue_result = $stmt->get_result()->fetch_assoc();

        // Calculate total expenses
        $expense_query = "SELECT
            COALESCE(SUM(amount), 0) as total_expenses,
            COUNT(*) as total_expense_count
            FROM expenses
            WHERE expense_date BETWEEN ? AND ?
            AND is_deleted = 0";

        $stmt = $koneksi->prepare($expense_query);
        if (!$stmt) {
            throw new Exception('Failed to prepare expense query: ' . $koneksi->error);
        }

        $stmt->bind_param('ss', $start_date, $end_date);
        $stmt->execute();
        $expense_result = $stmt->get_result()->fetch_assoc();

        $total_revenue = floatval($revenue_result['total_revenue']);
        $total_expenses = floatval($expense_result['total_expenses']);
        $net_profit = $total_revenue - $total_expenses;
        $profit_margin = $total_revenue > 0 ? ($net_profit / $total_revenue) * 100 : 0;

        // Get expenses by category
        $expense_by_category = getExpensesByCategory($start_date, $end_date);

        // Get recent revenues (last 10)
        $recent_revenues = getRecentRevenues($start_date, $end_date, 10);

        // Get recent expenses (last 10)
        $recent_expenses = getRecentExpenses($start_date, $end_date, 10);

        // Create HTML content
        $html = generatePDFContent($total_revenue, $total_expenses, $net_profit, $profit_margin, $expense_by_category, $recent_revenues, $recent_expenses, $period, $start_date, $end_date);

        // Check if preview mode is requested
        if ($preview) {
            // Output as HTML preview
            header('Content-Type: text/html; charset=utf-8');
            echo $html;
        } else {
            // Setup dompdf options for actual PDF generation
            $options = new Options();
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Output the generated PDF
            $dompdf->stream("laporan-keuangan-" . date('Y-m-d') . ".pdf", ["Attachment" => true]);
        }

    } catch (Exception $e) {
        if ($preview) {
            // If in preview mode, show error as HTML
            $error_html = '
            <!DOCTYPE html>
            <html>
            <head>
                <title>Error - Laporan Keuangan Preview</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                    .error { color: #d9534f; }
                </style>
            </head>
            <body>
                <h1 class="error">Error Generating Report Preview</h1>
                <p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>
                <button onclick="window.history.back()">Kembali</button>
            </body>
            </html>';

            echo $error_html;
        } else {
            // For PDF mode, show error in same format as before
            $error_html = '
            <!DOCTYPE html>
            <html>
            <head>
                <title>Error - Laporan Keuangan</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                    .error { color: #d9534f; }
                </style>
            </head>
            <body>
                <h1 class="error">Error Generating PDF Report</h1>
                <p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>
                <button onclick="window.history.back()">Kembali</button>
            </body>
            </html>';

            echo $error_html;
        }
    }
}

function getExpensesByCategory($start_date, $end_date)
{
    global $koneksi;

    $query = "SELECT
        ec.name as category_name,
        ec.description,
        COUNT(e.id) as transaction_count,
        COALESCE(SUM(e.amount), 0) as total
        FROM expenses e
        LEFT JOIN expense_categories ec ON e.category_id = ec.id
        WHERE e.expense_date BETWEEN ? AND ?
        AND e.is_deleted = 0
        AND ec.is_deleted = 0
        GROUP BY e.category_id, ec.name, ec.description
        ORDER BY total DESC";

    $stmt = $koneksi->prepare($query);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $expenses = [];
    while ($row = $result->fetch_assoc()) {
        $expenses[] = [
            'category_name' => $row['category_name'] ?? 'Tanpa Kategori',
            'description' => $row['description'],
            'transaction_count' => intval($row['transaction_count']),
            'total' => floatval($row['total'])
        ];
    }

    return $expenses;
}

function getRecentRevenues($start_date, $end_date, $limit = 10)
{
    global $koneksi;

    $query = "SELECT
        id,
        order_number,
        total_amount,
        payment_method,
        created_at
        FROM orders
        WHERE created_at BETWEEN ? AND ?
        AND status != 'cancelled'
        AND is_deleted = 0
        AND payment_status = 'paid'
        ORDER BY created_at DESC
        LIMIT ?";

    $stmt = $koneksi->prepare($query);
    $stmt->bind_param('ssi', $start_date, $end_date, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $revenues = [];
    while ($row = $result->fetch_assoc()) {
        $revenues[] = [
            'id' => $row['id'],
            'order_number' => $row['order_number'],
            'total_amount' => floatval($row['total_amount']),
            'payment_method' => ucfirst($row['payment_method']),
            'created_at' => $row['created_at']
        ];
    }

    return $revenues;
}

function getRecentExpenses($start_date, $end_date, $limit = 10)
{
    global $koneksi;

    $query = "SELECT
        e.id,
        e.title,
        e.amount,
        e.expense_date,
        ec.name as category_name
        FROM expenses e
        LEFT JOIN expense_categories ec ON e.category_id = ec.id
        WHERE e.expense_date BETWEEN ? AND ?
        AND e.is_deleted = 0
        ORDER BY e.expense_date DESC
        LIMIT ?";

    $stmt = $koneksi->prepare($query);
    $stmt->bind_param('ssi', $start_date, $end_date, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $expenses = [];
    while ($row = $result->fetch_assoc()) {
        $expenses[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'amount' => floatval($row['amount']),
            'expense_date' => $row['expense_date'],
            'category_name' => $row['category_name'] ?? 'Tanpa Kategori'
        ];
    }

    return $expenses;
}

function generatePDFContent($total_revenue, $total_expenses, $net_profit, $profit_margin, $expense_by_category, $recent_revenues, $recent_expenses, $period, $start_date, $end_date) {
    // Format dates for display
    $period_label = getPeriodLabel($period);
    $start_date_display = date('d M Y', strtotime($start_date));
    $end_date_display = date('d M Y', strtotime($end_date));

    // Format currency
    $format_rupiah = function($amount) {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    };

    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Laporan Keuangan</title>
        <style>
            @page {
                size: A4 portrait;
                margin: 20px;
            }
            body {
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                margin: 20px auto;
                max-width: 210mm; /* A4 width */
                min-height: 297mm; /* A4 height */
                padding: 15px;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
                background-color: white;
            }
            .header {
                text-align: center;
                border-bottom: 2px solid #000;
                padding-bottom: 15px;
                margin-bottom: 20px;
            }
            .header h1 {
                margin: 0;
                font-size: 24px;
                color: #333;
            }
            .header p {
                margin: 5px 0 0 0;
                color: #666;
                font-size: 14px;
            }
            .period-info {
                text-align: center;
                margin-bottom: 20px;
                font-size: 14px;
                color: #555;
            }
            .summary-cards {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
                margin-bottom: 25px;
            }
            .summary-card {
                flex: 1;
                min-width: 140px;
                padding: 15px;
                border-radius: 8px;
                color: white;
                text-align: center;
                box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            }
            /* Make sure PDF export also uses horizontal layout */
            @media print {
                .summary-cards {
                    display: -webkit-box !important;
                    display: -ms-flexbox !important;
                    display: flex !important;
                    -webkit-flex-wrap: nowrap !important;
                    -ms-flex-wrap: nowrap !important;
                    flex-wrap: nowrap !important;
                    gap: 15px !important;
                    width: 100% !important;
                    overflow-x: visible !important;
                }
                .summary-card {
                    -webkit-flex: 1 !important;
                    -ms-flex: 1 !important;
                    flex: 1 !important;
                    min-width: 140px !important;
                    max-width: 25% !important;
                    margin-bottom: 0 !important;
                    -webkit-flex-shrink: 0 !important;
                    -ms-flex-negative: 0 !important;
                    flex-shrink: 0 !important;
                }
            }
            .summary-card h3 {
                margin: 0 0 8px 0;
                font-size: 14px;
                font-weight: 600;
            }
            .summary-card p {
                margin: 0;
                font-size: 18px;
                font-weight: bold;
            }
            .summary-card::before {
                display: none;
            }
            .revenue-card {
                background-color: #28a745;
            }
            .expense-card {
                background-color: #dc3545;
            }
            .profit-card {
                background-color: #007bff;
            }
            .margin-card {
                background-color: #ffc107;
                color: #000;
            }
            .summary-card h3 {
                margin: 0 0 10px 0;
                font-size: 16px;
                font-weight: 600;
            }
            .summary-card p {
                margin: 0;
                font-size: 22px;
                font-weight: bold;
            }
            .section {
                margin-bottom: 25px;
            }
            .section-title {
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 10px;
                padding-bottom: 5px;
                border-bottom: 1px solid #ccc;
                color: #333;
            }
            /* Excel-like table styling */
            .excel-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 15px;
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                font-size: 12px;
            }
            .excel-table th, .excel-table td {
                border: 1px solid #d0d0d0;
                padding: 8px 10px;
                text-align: left;
            }
            .excel-table th {
                background-color: #f0f0f0;
                font-weight: 600;
                color: #333;
                text-align: center;
            }
            .excel-table tbody tr:nth-child(odd) {
                background-color: #f9f9f9;
            }
            .excel-table tbody tr:nth-child(even) {
                background-color: #ffffff;
            }
            .excel-table .text-right {
                text-align: right !important;
            }
            .excel-table .text-center {
                text-align: center !important;
            }
            .text-success {
                color: #28a745;
            }
            .text-danger {
                color: #dc3545;
            }
            .fw-bold {
                font-weight: bold;
            }
            .mb-0 {
                margin-bottom: 0 !important;
            }
            .badge {
                display: inline-block;
                padding: 2px 6px;
                border-radius: 4px;
                font-size: 10px;
                background-color: #6c757d;
                color: white;
            }
            .badge-primary {
                background-color: #007bff;
                color: white;
            }
            .badge-warning {
                background-color: #ffc107;
                color: #000;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>LAPORAN KEUANGAN</h1>
            <p>Seblak Predator Restaurant Management System</p>
        </div>

        <div class="period-info">
            Periode: <strong>' . $period_label . '</strong> (' . $start_date_display . ' - ' . $end_date_display . ')
        </div>

        <div class="summary-cards">
            <div class="summary-card revenue-card">
                <h3>Total Pendapatan</h3>
                <p>' . $format_rupiah($total_revenue) . '</p>
            </div>

            <div class="summary-card expense-card">
                <h3>Total Pengeluaran</h3>
                <p>' . $format_rupiah($total_expenses) . '</p>
            </div>

            <div class="summary-card profit-card">
                <h3>Keuntungan Bersih</h3>
                <p>' . $format_rupiah($net_profit) . '</p>
            </div>

            <div class="summary-card margin-card">
                <h3>Margin Keuntungan</h3>
                <p>' . round($profit_margin, 1) . '%</p>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Rincian Pengeluaran Berdasarkan Kategori</div>
            <table class="excel-table">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Jumlah Transaksi</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    ' . (count($expense_by_category) > 0
                        ? implode('', array_map(function($item) use ($format_rupiah) {
                            return '
                            <tr>
                                <td>' . htmlspecialchars($item['category_name']) . '</td>
                                <td class="text-center">' . $item['transaction_count'] . '</td>
                                <td class="text-right text-danger fw-bold">' . $format_rupiah($item['total']) . '</td>
                            </tr>';
                        }, $expense_by_category))
                        : '<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>') . '
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Rincian Pendapatan Terbaru</div>
            <table class="excel-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Order ID</th>
                        <th>Metode</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    ' . (count($recent_revenues) > 0
                        ? implode('', array_map(function($item) use ($format_rupiah) {
                            return '
                            <tr>
                                <td>' . date('d M Y', strtotime($item['created_at'])) . '</td>
                                <td><span class="badge badge-primary">#' . $item['id'] . '</span></td>
                                <td>' . htmlspecialchars($item['payment_method']) . '</td>
                                <td class="text-right text-success fw-bold">' . $format_rupiah($item['total_amount']) . '</td>
                            </tr>';
                        }, $recent_revenues))
                        : '<tr><td colspan="4" class="text-center">Tidak ada data</td></tr>') . '
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Rincian Pengeluaran Terbaru</div>
            <table class="excel-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Keterangan</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    ' . (count($recent_expenses) > 0
                        ? implode('', array_map(function($item) use ($format_rupiah) {
                            return '
                            <tr>
                                <td>' . date('d M Y', strtotime($item['expense_date'])) . '</td>
                                <td><span class="badge badge-warning">' . htmlspecialchars($item['category_name']) . '</span></td>
                                <td>' . htmlspecialchars($item['title']) . '</td>
                                <td class="text-right text-danger fw-bold">' . $format_rupiah($item['amount']) . '</td>
                            </tr>';
                        }, $recent_expenses))
                        : '<tr><td colspan="4" class="text-center">Tidak ada data</td></tr>') . '
                </tbody>
            </table>
        </div>

        <div class="section">
            <p class="mb-0"><small>Dicetak pada: ' . date('d M Y H:i:s') . '</small></p>
        </div>
    </body>
    </html>';
}

function getPeriodLabel($period) {
    switch ($period) {
        case 'today':
            return 'Hari Ini';
        case 'week':
            return 'Minggu Ini';
        case 'month':
            return 'Bulan Ini';
        case 'year':
            return 'Tahun Ini';
        case 'all':
            return 'Semua Waktu';
        case 'custom':
            return 'Custom Range';
        default:
            return 'Bulan Ini';
    }
}