<?php
/**
 * Global Search API
 * Real-time search across all content
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/koneksi.php';
require_once '../config/session.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    performSearch();
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

function performSearch()
{
    global $koneksi;

    try {
        $query = $_GET['q'] ?? '';
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

        if (strlen($query) < 2) {
            echo json_encode([
                'success' => true,
                'results' => [],
                'message' => 'Query too short'
            ]);
            return;
        }

        // Check database connection
        if (!$koneksi || $koneksi->connect_error) {
            throw new Exception('Database connection failed: ' . ($koneksi->connect_error ?? 'Unknown error'));
        }

        $searchTerm = '%' . $koneksi->real_escape_string($query) . '%';
        $results = [];

        // Check if user is logged in - simplified check
        $current_user = null;
        $accessible_pages = [];

        if (function_exists('getCurrentSessionUser')) {
            try {
                $current_user = getCurrentSessionUser();
            } catch (Exception $e) {
                // Silent fail - continue without user context
            }
        }

        if ($current_user && function_exists('getAccessiblePages')) {
            try {
                $accessible_pages = getAccessiblePages();
            } catch (Exception $e) {
                // Silent fail - continue with empty accessible pages
            }
        }

        // Search in Products (Dasar Seblak)
        if (empty($accessible_pages) || in_array('dasar-seblak', $accessible_pages)) {
            try {
                $stmt = $koneksi->prepare("
                    SELECT 'product' as type, id, name, price, 
                           'Dasar Seblak' as category,
                           'ti ti-soup' as icon,
                           'index.php?page=dasar-seblak' as url
                    FROM products 
                    WHERE is_deleted = 0 
                    AND (name LIKE ? OR description LIKE ?)
                    LIMIT ?
                ");

                if ($stmt) {
                    $stmt->bind_param('ssi', $searchTerm, $searchTerm, $limit);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $row['description'] = 'Harga: Rp ' . number_format($row['price'], 0, ',', '.');
                        unset($row['price']);
                        $results[] = $row;
                    }
                    $stmt->close();
                }
            } catch (Exception $e) {
                // Log error but continue
                error_log('Product search error: ' . $e->getMessage());
            }
        }

        // Search in Toppings
        if (empty($accessible_pages) || in_array('topping', $accessible_pages)) {
            try {
                $stmt = $koneksi->prepare("
                    SELECT 'topping' as type, id, name, price,
                           'Topping' as category,
                           'ti ti-meat' as icon,
                           'index.php?page=topping' as url
                    FROM toppings 
                    WHERE is_deleted = 0 
                    AND (name LIKE ? OR description LIKE ?)
                    LIMIT ?
                ");

                if ($stmt) {
                    $stmt->bind_param('ssi', $searchTerm, $searchTerm, $limit);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $row['description'] = 'Harga: Rp ' . number_format($row['price'], 0, ',', '.');
                        unset($row['price']);
                        $results[] = $row;
                    }
                    $stmt->close();
                }
            } catch (Exception $e) {
                error_log('Topping search error: ' . $e->getMessage());
            }
        }

        // Search in Categories
        if (empty($accessible_pages) || in_array('kategori', $accessible_pages)) {
            try {
                $stmt = $koneksi->prepare("
                    SELECT 'category' as type, id, name, description,
                           'Kategori' as category,
                           'ti ti-stack' as icon,
                           'index.php?page=kategori' as url
                    FROM categories 
                    WHERE is_deleted = 0 
                    AND (name LIKE ? OR description LIKE ?)
                    LIMIT ?
                ");

                if ($stmt) {
                    $stmt->bind_param('ssi', $searchTerm, $searchTerm, $limit);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $results[] = $row;
                    }
                    $stmt->close();
                }
            } catch (Exception $e) {
                error_log('Category search error: ' . $e->getMessage());
            }
        }

        // Search in Orders
        if (empty($accessible_pages) || in_array('transaksi', $accessible_pages)) {
            try {
                $stmt = $koneksi->prepare("
                    SELECT 'order' as type, id, 
                           CONCAT('Order #', order_number) as name,
                           CONCAT('Total: Rp ', FORMAT(total_amount, 0), ' - Status: ', status) as description,
                           'Transaksi' as category,
                           'ti ti-receipt' as icon,
                           'index.php?page=transaksi' as url
                    FROM orders 
                    WHERE is_deleted = 0 
                    AND (order_number LIKE ? OR customer_name LIKE ?)
                    ORDER BY created_at DESC
                    LIMIT ?
                ");

                if ($stmt) {
                    $stmt->bind_param('ssi', $searchTerm, $searchTerm, $limit);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $results[] = $row;
                    }
                    $stmt->close();
                }
            } catch (Exception $e) {
                error_log('Order search error: ' . $e->getMessage());
            }
        }

        // Search in Users
        if (empty($accessible_pages) || in_array('user', $accessible_pages)) {
            try {
                $stmt = $koneksi->prepare("
                    SELECT 'user' as type, u.id, 
                           u.name,
                           CONCAT(u.email, ' - ', COALESCE(r.name, 'No Role')) as description,
                           'User' as category,
                           'ti ti-user' as icon,
                           'index.php?page=user' as url
                    FROM users u
                    LEFT JOIN roles r ON u.role_id = r.id
                    WHERE u.is_deleted = 0 
                    AND (u.name LIKE ? OR u.email LIKE ?)
                    LIMIT ?
                ");

                if ($stmt) {
                    $stmt->bind_param('ssi', $searchTerm, $searchTerm, $limit);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $results[] = $row;
                    }
                    $stmt->close();
                }
            } catch (Exception $e) {
                error_log('User search error: ' . $e->getMessage());
            }
        }

        // Search in Expenses
        if (empty($accessible_pages) || in_array('laporan-keuangan', $accessible_pages)) {
            try {
                $stmt = $koneksi->prepare("
                    SELECT 'expense' as type, e.id, 
                           e.title as name,
                           CONCAT('Rp ', FORMAT(e.amount, 0), ' - ', COALESCE(ec.name, 'No Category')) as description,
                           'Pengeluaran' as category,
                           'ti ti-report-money' as icon,
                           'index.php?page=laporan-keuangan' as url
                    FROM expenses e
                    LEFT JOIN expense_categories ec ON e.category_id = ec.id
                    WHERE e.is_deleted = 0 
                    AND (e.title LIKE ? OR e.description LIKE ?)
                    ORDER BY e.expense_date DESC
                    LIMIT ?
                ");

                if ($stmt) {
                    $stmt->bind_param('ssi', $searchTerm, $searchTerm, $limit);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $results[] = $row;
                    }
                    $stmt->close();
                }
            } catch (Exception $e) {
                error_log('Expense search error: ' . $e->getMessage());
            }
        }

        // Add navigation pages
        $pages = [
            [
                'type' => 'page',
                'name' => 'Dashboard',
                'description' => 'Halaman utama dengan statistik dan ringkasan',
                'category' => 'Navigasi',
                'icon' => 'ti ti-dashboard',
                'url' => 'index.php?page=dashboard',
                'keywords' => ['dashboard', 'beranda', 'home', 'statistik']
            ],
            [
                'type' => 'page',
                'name' => 'Dasar Seblak',
                'description' => 'Kelola menu dasar seblak',
                'category' => 'Navigasi',
                'icon' => 'ti ti-soup',
                'url' => 'index.php?page=dasar-seblak',
                'keywords' => ['dasar', 'seblak', 'produk', 'menu']
            ],
            [
                'type' => 'page',
                'name' => 'Topping',
                'description' => 'Kelola menu topping seblak',
                'category' => 'Navigasi',
                'icon' => 'ti ti-meat',
                'url' => 'index.php?page=topping',
                'keywords' => ['topping', 'tambahan', 'ekstra']
            ],
            [
                'type' => 'page',
                'name' => 'Kategori',
                'description' => 'Kelola kategori produk',
                'category' => 'Navigasi',
                'icon' => 'ti ti-stack',
                'url' => 'index.php?page=kategori',
                'keywords' => ['kategori', 'category', 'kelompok']
            ],
            [
                'type' => 'page',
                'name' => 'Transaksi',
                'description' => 'Kelola transaksi dan pesanan',
                'category' => 'Navigasi',
                'icon' => 'ti ti-receipt',
                'url' => 'index.php?page=transaksi',
                'keywords' => ['transaksi', 'order', 'penjualan', 'pesanan']
            ],
            [
                'type' => 'page',
                'name' => 'Laporan Keuangan',
                'description' => 'Lihat laporan keuangan dan analisis',
                'category' => 'Navigasi',
                'icon' => 'ti ti-report-money',
                'url' => 'index.php?page=laporan-keuangan',
                'keywords' => ['laporan', 'keuangan', 'financial', 'report']
            ],
            [
                'type' => 'page',
                'name' => 'User',
                'description' => 'Kelola data pengguna sistem',
                'category' => 'Navigasi',
                'icon' => 'ti ti-users',
                'url' => 'index.php?page=user',
                'keywords' => ['user', 'pengguna', 'karyawan']
            ],
            [
                'type' => 'page',
                'name' => 'Pengaturan Akun',
                'description' => 'Pengaturan akun dan profil',
                'category' => 'Navigasi',
                'icon' => 'ti ti-user-circle',
                'url' => 'index.php?page=account',
                'keywords' => ['akun', 'account', 'profile', 'pengaturan']
            ]
        ];

        $queryLower = strtolower($query);
        foreach ($pages as $page) {
            $match = false;

            // Check if accessible
            if (!empty($accessible_pages) && isset($page['url'])) {
                $pageParam = str_replace('index.php?page=', '', $page['url']);
                if (!in_array($pageParam, $accessible_pages)) {
                    continue;
                }
            }

            // Check name
            if (stripos($page['name'], $query) !== false) {
                $match = true;
            }

            // Check description
            if (stripos($page['description'], $query) !== false) {
                $match = true;
            }

            // Check keywords
            if (isset($page['keywords'])) {
                foreach ($page['keywords'] as $keyword) {
                    if (stripos($keyword, $query) !== false) {
                        $match = true;
                        break;
                    }
                }
            }

            if ($match) {
                unset($page['keywords']);
                $results[] = $page;
            }
        }

        // Limit total results
        $results = array_slice($results, 0, $limit);

        echo json_encode([
            'success' => true,
            'results' => $results,
            'count' => count($results),
            'query' => $query
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Search error: ' . $e->getMessage(),
            'results' => [],
            'error_details' => [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        ]);
        error_log('Search API Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    }
}
