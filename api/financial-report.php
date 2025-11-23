<?php
/**
 * API untuk Laporan Keuangan
 * Menghitung pendapatan, pengeluaran, dan profit
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/koneksi.php';
// require_once '../config/session.php'; // Commented out for now

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get current user (commented out for now)
// $current_user = getCurrentSessionUser();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    getFinancialReport();
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

function getFinancialReport()
{
    global $koneksi;

    $period = $_GET['period'] ?? 'month';
    $start_date = null;
    $end_date = null;

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

        // Get revenue trend
        $revenue_trend = getRevenueTrend($start_date, $end_date, $period);

        // Get expense trend
        $expense_trend = getExpenseTrend($start_date, $end_date, $period);

        // Get expenses by category
        $expense_by_category = getExpensesByCategory($start_date, $end_date);

        // Get recent revenues (last 10)
        $recent_revenues = getRecentRevenues($start_date, $end_date, 10);

        // Get recent expenses (last 10)
        $recent_expenses = getRecentExpenses($start_date, $end_date, 10);

        // Get top products
        $top_products = getTopProducts($start_date, $end_date, 10);

        echo json_encode([
            'success' => true,
            'data' => [
                'total_revenue' => $total_revenue,
                'total_expenses' => $total_expenses,
                'net_profit' => $net_profit,
                'profit_margin' => round($profit_margin, 2),
                'total_orders' => $revenue_result['total_orders'],
                'total_expense_count' => $expense_result['total_expense_count'],
                'period' => $period,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'revenue_trend' => $revenue_trend['data'],
                'expense_trend' => $expense_trend['data'],
                'trend_labels' => $revenue_trend['labels'],
                'expense_by_category_labels' => $expense_by_category['labels'],
                'expense_by_category_values' => $expense_by_category['values'],
                'expense_by_category' => $expense_by_category['details'], // Add detailed category data
                'recent_revenues' => $recent_revenues,
                'recent_expenses' => $recent_expenses,
                'top_products' => $top_products
            ]
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

function getRevenueTrend($start_date, $end_date, $period)
{
    global $koneksi;

    // Determine grouping based on period
    $group_by = "DATE(created_at)";
    $date_format = "%d %b";

    if ($period === 'year') {
        $group_by = "DATE_FORMAT(created_at, '%Y-%m')";
        $date_format = "%b %Y";
    } elseif ($period === 'month') {
        $group_by = "DATE(created_at)";
        $date_format = "%d %b";
    }

    $query = "SELECT 
        $group_by as period,
        DATE_FORMAT(created_at, '$date_format') as label,
        COALESCE(SUM(total_amount), 0) as revenue
        FROM orders 
        WHERE created_at BETWEEN ? AND ?
        AND status != 'cancelled'
        AND is_deleted = 0
        GROUP BY period, label
        ORDER BY period ASC";

    $stmt = $koneksi->prepare($query);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $labels = [];
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['label'];
        $data[] = floatval($row['revenue']);
    }

    return ['labels' => $labels, 'data' => $data];
}

function getExpenseTrend($start_date, $end_date, $period)
{
    global $koneksi;

    // Determine grouping based on period
    $group_by = "DATE(expense_date)";
    $date_format = "%d %b";

    if ($period === 'year') {
        $group_by = "DATE_FORMAT(expense_date, '%Y-%m')";
        $date_format = "%b %Y";
    } elseif ($period === 'month') {
        $group_by = "DATE(expense_date)";
        $date_format = "%d %b";
    }

    $query = "SELECT 
        $group_by as period,
        DATE_FORMAT(expense_date, '$date_format') as label,
        COALESCE(SUM(amount), 0) as expense
        FROM expenses 
        WHERE expense_date BETWEEN ? AND ?
        AND is_deleted = 0
        GROUP BY period, label
        ORDER BY period ASC";

    $stmt = $koneksi->prepare($query);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $labels = [];
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['label'];
        $data[] = floatval($row['expense']);
    }

    return ['labels' => $labels, 'data' => $data];
}

function getExpensesByCategory($start_date, $end_date)
{
    global $koneksi;

    $query = "SELECT 
        ec.id as category_id,
        ec.name as category_name,
        ec.description,
        COUNT(e.id) as transaction_count,
        COALESCE(SUM(e.amount), 0) as total
        FROM expenses e
        LEFT JOIN expense_categories ec ON e.category_id = ec.id
        WHERE e.expense_date BETWEEN ? AND ?
        AND e.is_deleted = 0
        AND ec.is_deleted = 0
        GROUP BY e.category_id, ec.id, ec.name, ec.description
        ORDER BY total DESC";

    $stmt = $koneksi->prepare($query);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $labels = [];
    $values = [];
    $details = [];

    while ($row = $result->fetch_assoc()) {
        $categoryName = $row['category_name'] ?? 'Tanpa Kategori';
        $labels[] = $categoryName;
        $values[] = floatval($row['total']);

        // Add detailed category information
        $details[] = [
            'category_id' => $row['category_id'],
            'category_name' => $categoryName,
            'description' => $row['description'],
            'transaction_count' => intval($row['transaction_count']),
            'total' => floatval($row['total']),
            'trend' => 'stable' // You can calculate trend by comparing with previous period if needed
        ];
    }

    return [
        'labels' => $labels,
        'values' => $values,
        'details' => $details
    ];
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

function getTopProducts($start_date, $end_date, $limit = 10)
{
    global $koneksi;

    // Get total revenue for percentage calculation
    $total_query = "SELECT COALESCE(SUM(total_amount), 0) as total 
                    FROM orders 
                    WHERE created_at BETWEEN ? AND ?
                    AND status != 'cancelled'
                    AND is_deleted = 0";

    $stmt = $koneksi->prepare($total_query);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $total_revenue = floatval($stmt->get_result()->fetch_assoc()['total']);

    $query = "SELECT 
        oi.spice_level_name as product_name,
        COUNT(*) as total_quantity,
        SUM(oi.subtotal) as total_revenue
        FROM order_items oi
        INNER JOIN orders o ON oi.order_id = o.id
        WHERE o.created_at BETWEEN ? AND ?
        AND o.status != 'cancelled'
        AND o.is_deleted = 0
        GROUP BY oi.spice_level_name
        ORDER BY total_revenue DESC
        LIMIT ?";

    $stmt = $koneksi->prepare($query);
    $stmt->bind_param('ssi', $start_date, $end_date, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $revenue = floatval($row['total_revenue']);
        $contribution = $total_revenue > 0 ? ($revenue / $total_revenue) * 100 : 0;

        $products[] = [
            'product_name' => $row['product_name'],
            'total_quantity' => intval($row['total_quantity']),
            'total_revenue' => $revenue,
            'contribution' => round($contribution, 1)
        ];
    }

    return $products;
}
