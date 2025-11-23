<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-header-title">
                    <h5 class="m-b-10">Laporan Keuangan</h5>
                </div>
            </div>
            <div class="col-auto">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page">Laporan Keuangan</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <!-- Filter Period -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="ti ti-filter me-2"></i>Filter Laporan</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Periode</label>
                        <select class="form-select" id="periodFilter" onchange="loadFinancialData()">
                            <option value="today">Hari Ini</option>
                            <option value="week">Minggu Ini</option>
                            <option value="month" selected>Bulan Ini</option>
                            <option value="year">Tahun Ini</option>
                            <option value="all">Semua Waktu</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="startDateGroup" style="display: none;">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="startDate" onchange="loadFinancialData()">
                    </div>
                    <div class="col-md-3" id="endDateGroup" style="display: none;">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="endDate" onchange="loadFinancialData()">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" onclick="exportReport()">
                            <i class="ti ti-download me-2"></i>Export PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card bg-success-dark dashnum-card text-white overflow-hidden">
            <span class="round small"></span>
            <span class="round big"></span>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avtar avtar-lg">
                        <i class="text-white ti ti-cash"></i>
                    </div>
                    <div class="ms-2">
                        <h4 class="text-white mb-1" id="totalRevenue">Rp 0</h4>
                        <p class="mb-0 opacity-75 text-sm">Total Pendapatan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card bg-danger dashnum-card text-white overflow-hidden">
            <span class="round small"></span>
            <span class="round big"></span>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avtar avtar-lg">
                        <i class="text-white ti ti-trending-down"></i>
                    </div>
                    <div class="ms-2">
                        <h4 class="text-white mb-1" id="totalExpenses">Rp 0</h4>
                        <p class="mb-0 opacity-75 text-sm">Total Pengeluaran</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary-dark dashnum-card text-white overflow-hidden">
            <span class="round small"></span>
            <span class="round big"></span>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avtar avtar-lg">
                        <i class="text-white ti ti-chart-line"></i>
                    </div>
                    <div class="ms-2">
                        <h4 class="text-white mb-1" id="netProfit">Rp 0</h4>
                        <p class="mb-0 opacity-75 text-sm">Keuntungan Bersih</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card bg-warning-dark dashnum-card text-white overflow-hidden">
            <span class="round small"></span>
            <span class="round big"></span>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avtar avtar-lg">
                        <i class="text-white ti ti-percentage"></i>
                    </div>
                    <div class="ms-2">
                        <h4 class="text-white mb-1" id="profitMargin">0%</h4>
                        <p class="mb-0 opacity-75 text-sm">Margin Keuntungan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Tren Pendapatan & Pengeluaran</h5>
            </div>
            <div class="card-body">
                <div id="revenueExpenseChart" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Kategori Pengeluaran</h5>
            </div>
            <div class="card-body">
                <div id="expenseCategoryChart" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    <!-- Detail Tables -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">Rincian Pendapatan</h5>
                        <p class="text-muted mb-0 small">Daftar transaksi pendapatan terbaru</p>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-outline-primary" onclick="showAllRevenue()">
                            <i class="ti ti-eye me-1"></i>Lihat Semua
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Table Container -->
                <div class="table-container">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light table-header-sticky">
                                <tr>
                                    <th style="min-width: 120px;">Tanggal</th>
                                    <th style="min-width: 100px;">Order ID</th>
                                    <th style="min-width: 120px;">Metode</th>
                                    <th class="text-end" style="min-width: 150px;">Total</th>
                                </tr>
                            </thead>
                            <tbody id="revenueTableBody">
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="text-muted mt-2 mb-0">Memuat data...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">Rincian Pengeluaran</h5>
                        <p class="text-muted mb-0 small">Daftar pengeluaran terbaru dengan aksi edit/hapus</p>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-primary" onclick="showExpenseModal()">
                            <i class="ti ti-plus me-1"></i>Tambah Pengeluaran
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Table Container -->
                <div class="table-container">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light table-header-sticky">
                                <tr>
                                    <th style="min-width: 120px;">Tanggal</th>
                                    <th style="min-width: 150px;">Kategori</th>
                                    <th style="min-width: 200px;">Keterangan</th>
                                    <th class="text-end" style="min-width: 150px;">Total</th>
                                    <th class="text-center" style="min-width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="expenseTableBody">
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="text-muted mt-2 mb-0">Memuat data...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Categories Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">Kategori Pengeluaran</h5>
                        <p class="text-muted mb-0 small">Kelola kategori pengeluaran aktif dan terhapus</p>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-primary me-2" onclick="showCategoryModal()">
                            <i class="ti ti-plus me-1"></i>Tambah Kategori
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="loadAllCategories()">
                            <i class="ti ti-refresh me-1"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Table Controls Section -->
                <div class="table-controls-section p-3">
                    <!-- Filter Tabs -->
                    <ul class="nav nav-pills mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="btn-active-categories"
                                onclick="toggleCategoryView('active')" type="button">
                                <i class="ti ti-check-circle me-1"></i>Aktif
                                <span class="badge bg-success ms-1" id="badge-active">0</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="btn-deleted-categories" onclick="toggleCategoryView('deleted')"
                                type="button">
                                <i class="ti ti-trash me-1"></i>Terhapus
                                <span class="badge bg-danger ms-1" id="badge-deleted">0</span>
                            </button>
                        </li>
                    </ul>

                    <!-- Info Alert for Deleted View -->
                    <div class="alert alert-warning alert-dismissible fade show d-none mb-0" id="alert-deleted-info"
                        role="alert">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Info:</strong> Kategori yang dihapus tidak akan muncul di dropdown pengeluaran. Anda
                        dapat memulihkannya kapan saja.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="table-container">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light table-header-sticky">
                                <tr id="table-header-active">
                                    <th style="min-width: 250px;">Kategori</th>
                                    <th class="text-center" style="min-width: 120px;">Jumlah Transaksi</th>
                                    <th class="text-end" style="min-width: 150px;">Total Pengeluaran</th>
                                    <th class="text-end" style="min-width: 120px;">Persentase</th>
                                    <th class="text-center" style="min-width: 80px;">Trend</th>
                                    <th class="text-center" style="min-width: 150px;">Aksi</th>
                                </tr>
                                <tr id="table-header-deleted" class="d-none">
                                    <th style="min-width: 350px;">Informasi Kategori</th>
                                    <th class="text-center" style="min-width: 120px;">Status</th>
                                    <th class="text-center" colspan="4" style="min-width: 180px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="expenseCategoryTableBody">
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="text-muted mt-2 mb-0">Memuat data kategori...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <!-- <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Produk Terlaris</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Produk</th>
                                <th class="text-center">Qty Terjual</th>
                                <th class="text-end">Total Pendapatan</th>
                                <th class="text-end">Kontribusi</th>
                            </tr>
                        </thead>
                        <tbody id="topProductsTableBody">
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> -->
</div>
<!-- [ Main Content ] end -->

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addExpenseModalLabel">Tambah Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="expenseForm">
                    <div class="mb-3">
                        <label for="expenseTitle" class="form-label">Judul Pengeluaran <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="expenseTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="expenseCategory" class="form-label">Kategori <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="expenseCategory" required>
                            <option value="">Pilih Kategori</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="expenseAmount" class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="expenseAmount" required min="0">
                    </div>
                    <div class="mb-3">
                        <label for="expenseDate" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="expenseDate" required>
                    </div>
                    <div class="mb-3">
                        <label for="expenseDescription" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="expenseDescription" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveExpense()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <input type="hidden" id="categoryId">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Nama Kategori <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoryName" required
                            placeholder="Contoh: Bahan Baku, Gaji Karyawan">
                    </div>
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="categoryDescription" rows="3"
                            placeholder="Deskripsi singkat kategori..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveCategory()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Table container */
    .table-container {
        position: relative;
        border: 1px solid #dee2e6;
        border-radius: 0 0 0.375rem 0.375rem;
        border-top: none;
        overflow: hidden;
    }

    /* Scrollable table container */
    .table-responsive {
        border: none;
        border-radius: 0;
        position: relative;
        background: white;
    }

    /* Sticky table header */
    .table-header-sticky {
        position: sticky;
        top: 0;
        z-index: 1020;
        background-color: var(--bs-gray-100) !important;
    }

    .table-header-sticky th {
        background-color: var(--bs-gray-100) !important;
        border-bottom: 2px solid #dee2e6;
        border-top: 1px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        padding: 1rem 0.75rem;
        position: sticky;
        top: 0;
        z-index: 1020;
        border-left: none;
        border-right: none;
        background-clip: padding-box;
    }

    .table-header-sticky th:last-child {
        border-right: none;
    }

    /* Ensure table borders don't scroll with content */
    .table-responsive table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-responsive .table thead th {
        border-left: none;
        border-right: none;
    }

    .table-responsive .table thead th:first-child {
        border-left: none;
    }

    /* External controls section styling */
    .table-controls-section {
        background-color: var(--bs-gray-100) !important;
        border: 1px solid #dee2e6 !important;
        border-bottom: none !important;
        border-radius: 0.375rem 0.375rem 0 0 !important;
    }

    /* Custom scrollbar styling */
    .table-responsive::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Table hover effects */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
        cursor: pointer;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .table-responsive {
            max-height: 400px;
            font-size: 0.875rem;
        }

        .table td,
        .table th {
            padding: 8px 6px;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    let revenueExpenseChart, expenseCategoryChart;
    let currentCategoryView = 'active'; // Track current view: 'active' or 'deleted'
    let financialData = {
        revenue: 0,
        expenses: 0,
        profit: 0,
        margin: 0
    };

    // Initialize page
    document.addEventListener('DOMContentLoaded', function () {
        loadExpenseCategories();
        updateCategoryBadges();
        loadFinancialData();

        // Set default date to today
        document.getElementById('expenseDate').value = new Date().toISOString().split('T')[0];

        // Period filter handler
        document.getElementById('periodFilter').addEventListener('change', function () {
            const customGroups = ['startDateGroup', 'endDateGroup'];
            if (this.value === 'custom') {
                customGroups.forEach(id => document.getElementById(id).style.display = 'block');
            } else {
                customGroups.forEach(id => document.getElementById(id).style.display = 'none');
            }
        });
    });

    // Load financial data
    async function loadFinancialData() {
        const period = document.getElementById('periodFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        let url = `../../../api/financial-report.php?period=${period}`;
        if (period === 'custom' && startDate && endDate) {
            url += `&start_date=${startDate}&end_date=${endDate}`;
        }

        try {
            const response = await fetch(url);

            // Check if response is ok
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (result.success) {
                financialData = result.data;
                updateSummaryCards(result.data);
                updateCharts(result.data);
                updateTables(result.data);
            } else {
                console.error('API Error:', result.message);
                showToast(result.message || 'Gagal memuat data keuangan', 'error');
            }
        } catch (error) {
            console.error('Error loading financial data:', error);
            showToast('Gagal memuat data keuangan: ' + error.message, 'error');
        }
    }

    // Update summary cards
    function updateSummaryCards(data) {
        document.getElementById('totalRevenue').textContent = formatRupiah(data.total_revenue || 0);
        document.getElementById('totalExpenses').textContent = formatRupiah(data.total_expenses || 0);
        document.getElementById('netProfit').textContent = formatRupiah(data.net_profit || 0);
        document.getElementById('profitMargin').textContent = (data.profit_margin || 0).toFixed(1) + '%';
    }

    // Update charts
    function updateCharts(data) {
        try {
            // Revenue vs Expense Trend Chart
            if (revenueExpenseChart) {
                revenueExpenseChart.destroy();
            }

            const revenueExpenseOptions = {
                series: [{
                    name: 'Pendapatan',
                    data: data.revenue_trend || []
                }, {
                    name: 'Pengeluaran',
                    data: data.expense_trend || []
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: {
                        show: true,
                        offsetY: -35,
                        tools: {
                            download: true,
                            selection: false,
                            zoom: false,
                            zoomin: false,
                            zoomout: false,
                            pan: false,
                            reset: false
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                colors: ['#4680ff', '#dc2626'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                    }
                },
                xaxis: {
                    categories: data.trend_labels || [],
                    labels: {
                        style: {
                            colors: '#a1a5b7'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return 'Rp ' + (value / 1000) + 'k';
                        },
                        style: {
                            colors: '#a1a5b7'
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'left',
                    offsetY: 0,
                    markers: {
                        width: 12,
                        height: 12,
                        radius: 2
                    },
                    itemMargin: {
                        horizontal: 10,
                        vertical: 0
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return formatRupiah(value);
                        }
                    }
                }
            };

            revenueExpenseChart = new ApexCharts(document.querySelector("#revenueExpenseChart"), revenueExpenseOptions);
            revenueExpenseChart.render();

            // Expense Category Pie Chart
            if (expenseCategoryChart) {
                expenseCategoryChart.destroy();
            }

            const expenseCategoryOptions = {
                series: data.expense_by_category_values || [],
                chart: {
                    type: 'donut',
                    height: 350
                },
                labels: data.expense_by_category_labels || [],
                colors: ['#dc2626', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6'],
                legend: {
                    position: 'bottom'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    formatter: function (w) {
                                        return formatRupiah(w.globals.seriesTotals.reduce((a, b) => a + b, 0));
                                    }
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return formatRupiah(value);
                        }
                    }
                }
            };

            expenseCategoryChart = new ApexCharts(document.querySelector("#expenseCategoryChart"), expenseCategoryOptions);
            expenseCategoryChart.render();
        } catch (error) {
            console.error('Error rendering charts:', error);
            // Show error message in chart containers
            document.querySelector("#revenueExpenseChart").innerHTML =
                '<div class="text-center text-danger p-4">Error loading chart: ' + error.message + '</div>';
            document.querySelector("#expenseCategoryChart").innerHTML =
                '<div class="text-center text-danger p-4">Error loading chart: ' + error.message + '</div>';
        }
    }

    // Update tables
    function updateTables(data) {
        // Revenue table
        const revenueTable = document.getElementById('revenueTableBody');
        if (data.recent_revenues && data.recent_revenues.length > 0) {
            revenueTable.innerHTML = data.recent_revenues.map(item => `
            <tr>
                <td>${formatDate(item.created_at)}</td>
                <td><span class="badge bg-light-primary">#${item.id}</span></td>
                <td>${item.payment_method}</td>
                <td class="text-end text-success fw-bold">${formatRupiah(item.total_amount)}</td>
            </tr>
        `).join('');
        } else {
            revenueTable.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>';
        }

        // Expense table
        const expenseTable = document.getElementById('expenseTableBody');
        if (data.recent_expenses && data.recent_expenses.length > 0) {
            expenseTable.innerHTML = data.recent_expenses.map(item => `
            <tr>
                <td>${formatDate(item.expense_date)}</td>
                <td><span class="badge bg-light-warning">${item.category_name}</span></td>
                <td>${item.title}</td>
                <td class="text-end text-danger fw-bold">${formatRupiah(item.amount)}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-icon btn-link-secondary" onclick="editExpense('${item.id}')" title="Edit">
                        <i class="ti ti-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-icon btn-link-danger" onclick="deleteExpense('${item.id}')" title="Hapus">
                        <i class="ti ti-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
        } else {
            expenseTable.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>';
        }

        // Expense categories table
        updateExpenseCategories(data);

        // Top products table
        // const topProductsTable = document.getElementById('topProductsTableBody');
        // if (data.top_products && data.top_products.length > 0) {
        //     topProductsTable.innerHTML = data.top_products.map((item, index) => `
        //     <tr>
        //         <td>
        //             <span class="badge ${index === 0 ? 'bg-warning' : index === 1 ? 'bg-light-warning' : 'bg-light-secondary'}">
        //                 #${index + 1}
        //             </span>
        //         </td>
        //         <td><strong>${item.product_name}</strong></td>
        //         <td class="text-center">${item.total_quantity}</td>
        //         <td class="text-end text-success fw-bold">${formatRupiah(item.total_revenue)}</td>
        //         <td class="text-end">
        //             <div class="progress" style="height: 6px;">
        //                 <div class="progress-bar bg-success" role="progressbar" 
        //                      style="width: ${item.contribution}%" 
        //                      aria-valuenow="${item.contribution}" 
        //                      aria-valuemin="0" 
        //                      aria-valuemax="100"></div>
        //             </div>
        //             <small class="text-muted">${item.contribution}%</small>
        //         </td>
        //     </tr>
        // `).join('');
        // } else {
        //     topProductsTable.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>';
        // }
    }

    // Update expense categories table
    function updateExpenseCategories(data) {
        const categoryTable = document.getElementById('expenseCategoryTableBody');

        if (data.expense_by_category && data.expense_by_category.length > 0) {
            const totalExpenses = data.total_expenses || 0;

            categoryTable.innerHTML = data.expense_by_category.map(item => {
                const percentage = totalExpenses > 0 ? ((item.total / totalExpenses) * 100).toFixed(1) : 0;
                const trendIcon = item.trend === 'up' ?
                    '<i class="ti ti-trending-up text-danger"></i>' :
                    item.trend === 'down' ?
                        '<i class="ti ti-trending-down text-success"></i>' :
                        '<i class="ti ti-minus text-muted"></i>';

                return `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar avtar-xs bg-light-warning">
                                    <i class="ti ti-tag"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0">${item.category_name}</h6>
                                ${item.description ? `<small class="text-muted">${item.description}</small>` : ''}
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light-secondary">${item.transaction_count}</span>
                    </td>
                    <td class="text-end">
                        <span class="text-danger fw-bold">${formatRupiah(item.total)}</span>
                    </td>
                    <td class="text-end">
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="progress me-2" style="height: 6px; width: 60px;">
                                <div class="progress-bar bg-warning" role="progressbar" 
                                     style="width: ${percentage}%" 
                                     aria-valuenow="${percentage}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <span class="text-muted small">${percentage}%</span>
                        </div>
                    </td>
                    <td class="text-center">
                        ${trendIcon}
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-icon btn-link-secondary" onclick="editCategory(${item.category_id})" title="Edit">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-icon btn-link-danger" onclick="deleteCategory(${item.category_id})" title="Hapus">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
                `;
            }).join('');
        } else {
            categoryTable.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data kategori</td></tr>';
        }
    }

    // Safe close modal to prevent aria-hidden focus issue
    function safeCloseModal(modalId) {
        const modalElement = document.getElementById(modalId);
        const modalInstance = bootstrap.Modal.getInstance(modalElement);

        if (modalInstance) {
            // Remove focus from any element within the modal
            const activeElement = document.activeElement;
            if (modalElement.contains(activeElement)) {
                activeElement.blur();
            }

            modalInstance.hide();
        }
    }

    // Safe show modal to prevent aria-hidden focus issue
    function safeShowModal(modalId) {
        const modalElement = document.getElementById(modalId);

        // Remove focus from close button and other elements before showing
        const closeButtons = modalElement.querySelectorAll('.btn-close, [data-bs-dismiss="modal"]');
        closeButtons.forEach(btn => {
            btn.addEventListener('focus', function (e) {
                e.preventDefault();
                this.blur();
            }, { once: true });
        });

        const modalInstance = new bootstrap.Modal(modalElement);
        modalInstance.show();
    }

    // Show expense modal
    function showExpenseModal() {
        document.getElementById('expenseForm').reset();
        document.getElementById('expenseDate').value = new Date().toISOString().split('T')[0];
        document.getElementById('addExpenseModalLabel').textContent = 'Tambah Pengeluaran';
        delete document.getElementById('expenseForm').dataset.expenseId;
        delete document.getElementById('expenseForm').dataset.mode;
        safeShowModal('addExpenseModal');
    }

    // Save expense
    async function saveExpense() {
        const form = document.getElementById('expenseForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const isEdit = form.dataset.mode === 'edit';
        const expenseId = form.dataset.expenseId;

        const data = {
            title: document.getElementById('expenseTitle').value.trim(),
            category_id: document.getElementById('expenseCategory').value,
            amount: parseFloat(document.getElementById('expenseAmount').value),
            expense_date: document.getElementById('expenseDate').value,
            description: document.getElementById('expenseDescription').value.trim()
        };

        // Validasi tambahan
        if (!data.title) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Judul pengeluaran harus diisi',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        if (!data.category_id) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Kategori pengeluaran harus dipilih',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        if (data.amount <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Jumlah pengeluaran harus lebih dari 0',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        try {
            const url = isEdit
                ? `../../../api/expenses.php?id=${expenseId}`
                : '../../../api/expenses.php';

            const response = await fetch(url, {
                method: isEdit ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                // Tutup modal terlebih dahulu
                safeCloseModal('addExpenseModal');

                // Tunggu modal benar-benar tertutup
                await new Promise(resolve => setTimeout(resolve, 300));

                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: isEdit ? 'Pengeluaran berhasil diupdate' : 'Pengeluaran berhasil ditambahkan',
                    timer: 1500,
                    showConfirmButton: false
                });

                // Reload data setelah alert dan modal ditutup
                await loadFinancialData();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: result.message || 'Gagal menyimpan pengeluaran',
                    confirmButtonColor: '#dc2626'
                });
            }
        } catch (error) {
            console.error('Error saving expense:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat menyimpan data',
                confirmButtonColor: '#dc2626'
            });
        }
    }

    // Edit expense
    async function editExpense(expenseId) {
        try {
            const response = await fetch(`../../../api/expenses.php?id=${expenseId}`);
            const result = await response.json();

            if (result.success) {
                const expense = result.data;

                // Populate form
                document.getElementById('expenseTitle').value = expense.title;
                document.getElementById('expenseCategory').value = expense.category_id;
                document.getElementById('expenseAmount').value = expense.amount;
                document.getElementById('expenseDate').value = expense.expense_date;
                document.getElementById('expenseDescription').value = expense.description || '';

                // Change modal title and save button
                document.getElementById('addExpenseModalLabel').textContent = 'Edit Pengeluaran';
                document.getElementById('expenseForm').dataset.expenseId = expenseId;
                document.getElementById('expenseForm').dataset.mode = 'edit';

                safeShowModal('addExpenseModal');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: result.message || 'Gagal memuat data pengeluaran',
                    confirmButtonColor: '#dc2626'
                });
            }
        } catch (error) {
            console.error('Error loading expense:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat memuat data',
                confirmButtonColor: '#dc2626'
            });
        }
    }

    // Delete expense
    async function deleteExpense(expenseId) {
        const confirmation = await Swal.fire({
            title: 'Hapus Pengeluaran?',
            text: 'Data akan dihapus dari laporan',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        });

        if (!confirmation.isConfirmed) return;

        try {
            const response = await fetch(`../../../api/expenses.php?id=${expenseId}`, {
                method: 'DELETE'
            });

            const result = await response.json();

            if (result.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Pengeluaran berhasil dihapus',
                    timer: 1500,
                    showConfirmButton: false
                });

                await loadFinancialData();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: result.message || 'Gagal menghapus pengeluaran',
                    confirmButtonColor: '#dc2626'
                });
            }
        } catch (error) {
            console.error('Error deleting expense:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat menghapus data',
                confirmButtonColor: '#dc2626'
            });
        }
    }

    // Export report
    function exportReport() {
        const period = document.getElementById('periodFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        let url = `../../../api/export-financial-report.php?period=${period}`;
        if (period === 'custom' && startDate && endDate) {
            url += `&start_date=${startDate}&end_date=${endDate}`;
        }

        window.open(url, '_blank');
    }

    // Utility functions
    function formatRupiah(amount) {
        return 'Rp ' + parseFloat(amount).toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }

    function showToast(message, type = 'success') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    function showAllRevenue() {
        // Navigate to full revenue report
        window.location.href = 'index.php?page=transaksi';
    }

    // ========== CATEGORY MANAGEMENT ==========

    // Show category modal
    function showCategoryModal(categoryId = null) {
        // Reset form
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryId').value = '';

        if (categoryId) {
            // Edit mode - load category data
            loadCategoryData(categoryId);
            document.getElementById('categoryModalLabel').textContent = 'Edit Kategori';
        } else {
            // Add mode
            document.getElementById('categoryModalLabel').textContent = 'Tambah Kategori';
        }

        safeShowModal('categoryModal');
    }

    // Load category data for editing
    async function loadCategoryData(categoryId) {
        try {
            const response = await fetch(`../../../api/expense-categories.php?id=${categoryId}`);
            const result = await response.json();

            if (result.success && result.data) {
                const category = result.data;
                document.getElementById('categoryId').value = category.id;
                document.getElementById('categoryName').value = category.name;
                document.getElementById('categoryDescription').value = category.description || '';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: result.message || 'Gagal memuat data kategori'
                });
            }
        } catch (error) {
            console.error('Error loading category:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat memuat data'
            });
        }
    }

    // Save category (create or update)
    async function saveCategory() {
        const categoryId = document.getElementById('categoryId').value;
        const name = document.getElementById('categoryName').value.trim();
        const description = document.getElementById('categoryDescription').value.trim();

        // Validation
        if (!name) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Nama kategori harus diisi',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        const categoryData = {
            name: name,
            description: description
        };

        try {
            let url = '../../../api/expense-categories.php';
            let method = 'POST';

            if (categoryId) {
                // Update existing category
                url += `?id=${categoryId}`;
                method = 'PUT';
            }

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(categoryData)
            });

            const result = await response.json();

            if (result.success) {
                // Tutup modal terlebih dahulu
                safeCloseModal('categoryModal');

                // Tunggu modal benar-benar tertutup
                await new Promise(resolve => setTimeout(resolve, 300));

                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: categoryId ? 'Kategori berhasil diupdate' : 'Kategori berhasil ditambahkan',
                    timer: 1500,
                    showConfirmButton: false
                });

                // Reload data setelah alert dan modal ditutup
                await loadExpenseCategories();
                await updateCategoryBadges();
                loadFinancialData(); // Refresh to update expense category dropdown
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: result.message || 'Gagal menyimpan kategori'
                });
            }
        } catch (error) {
            console.error('Error saving category:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat menyimpan data'
            });
        }
    }

    // Load and update expense categories list
    async function loadExpenseCategories() {
        try {
            const response = await fetch('../../../api/expense-categories.php?is_active=true');
            const result = await response.json();

            if (result.success && result.data) {
                // Update category dropdown in expense form
                const categorySelect = document.getElementById('expenseCategory');
                categorySelect.innerHTML = '<option value="">Pilih Kategori</option>';
                // Update badge counter
                document.getElementById('badge-active').textContent = result.data.length;

                result.data.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    categorySelect.appendChild(option);
                });

                // Update categories table with all categories
                await updateCategoriesTable(result.data);
            }
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }

    // Update categories table with all available categories
    async function updateCategoriesTable(categories) {
        const categoryTable = document.getElementById('expenseCategoryTableBody');

        if (!categories || categories.length === 0) {
            categoryTable.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Belum ada kategori. Klik "Tambah Kategori" untuk membuat kategori baru.</td></tr>';
            return;
        }

        // Get current period for expense data
        const period = document.getElementById('periodFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        let url = `../../../api/financial-report.php?period=${period}`;
        if (period === 'custom' && startDate && endDate) {
            url += `&start_date=${startDate}&end_date=${endDate}`;
        }

        try {
            const response = await fetch(url);
            const result = await response.json();

            // Create a map of expense data by category_id
            const expenseDataMap = {};
            let totalExpenses = 0;

            if (result.success && result.data.expense_by_category) {
                result.data.expense_by_category.forEach(item => {
                    expenseDataMap[item.category_id] = item;
                    totalExpenses += parseFloat(item.total || 0);
                });
            }

            // Render all categories
            categoryTable.innerHTML = categories.map(category => {
                const expenseData = expenseDataMap[category.id];
                const transactionCount = expenseData ? expenseData.transaction_count : 0;
                const total = expenseData ? parseFloat(expenseData.total) : 0;
                const percentage = totalExpenses > 0 ? ((total / totalExpenses) * 100).toFixed(1) : 0;
                const trend = expenseData ? expenseData.trend : 'stable';

                const trendIcon = trend === 'up' ?
                    '<i class="ti ti-trending-up text-danger"></i>' :
                    trend === 'down' ?
                        '<i class="ti ti-trending-down text-success"></i>' :
                        '<i class="ti ti-minus text-muted"></i>';

                return `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar avtar-xs ${total > 0 ? 'bg-light-warning' : 'bg-light-secondary'}">
                                    <i class="ti ti-tag"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0">${category.name}</h6>
                                ${category.description ? `<small class="text-muted">${category.description}</small>` : '<small class="text-muted">Tidak ada deskripsi</small>'}
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        ${transactionCount > 0
                        ? `<span class="badge bg-light-primary text-primary">${transactionCount}</span>`
                        : `<span class="badge bg-light-muted text-muted">0</span>`
                    }
                    </td>
                    <td class="text-end">
                        ${total > 0
                        ? `<span class="text-danger fw-bold">${formatRupiah(total)}</span>`
                        : `<span class="text-muted">Rp 0</span>`
                    }
                    </td>
                    <td class="text-end">
                        ${total > 0
                        ? `<div class="d-flex align-items-center justify-content-end">
                                <div class="progress me-2" style="height: 6px; width: 60px;">
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                         style="width: ${percentage}%" 
                                         aria-valuenow="${percentage}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <span class="text-muted small">${percentage}%</span>
                              </div>`
                        : `<span class="text-muted small">0%</span>`
                    }
                    </td>
                    <td class="text-center">
                        ${total > 0 ? trendIcon : '<i class="ti ti-minus text-muted"></i>'}
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-icon btn-link-secondary" onclick="editCategory(${category.id})" title="Edit">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-icon btn-link-danger" onclick="deleteCategory(${category.id})" title="Hapus">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
                `;
            }).join('');
        } catch (error) {
            console.error('Error updating categories table:', error);
            categoryTable.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4"><i class="ti ti-alert-circle me-2"></i>Error memuat data kategori</td></tr>';
        }
    }

    // Edit category
    function editCategory(categoryId) {
        showCategoryModal(categoryId);
    }

    // Delete category
    async function deleteCategory(categoryId) {
        const confirmation = await Swal.fire({
            title: 'Hapus Kategori?',
            html: `<div class="text-start">
                    <p class="mb-3">Kategori ini akan dihapus dan dipindahkan ke tab "Terhapus".</p>
                    <div class="alert alert-warning mb-0">
                        <i class="ti ti-alert-triangle me-2"></i>
                        <strong>Perhatian:</strong> Pastikan tidak ada pengeluaran aktif yang menggunakan kategori ini.
                    </div>
                   </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="ti ti-trash me-1"></i>Ya, Hapus!',
            cancelButtonText: '<i class="ti ti-x me-1"></i>Batal',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        });

        if (!confirmation.isConfirmed) return;

        try {
            const response = await fetch(`../../../api/expense-categories.php?id=${categoryId}`, {
                method: 'DELETE'
            });

            const result = await response.json();

            if (result.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Kategori berhasil dihapus',
                    timer: 1500,
                    showConfirmButton: false
                });

                // Reload current view and update badges setelah alert selesai
                if (currentCategoryView === 'deleted') {
                    await loadDeletedCategories();
                } else {
                    await loadExpenseCategories();
                }
                await updateCategoryBadges();
                loadFinancialData();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: result.message || 'Gagal menghapus kategori'
                });
            }
        } catch (error) {
            console.error('Error deleting category:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat menghapus data'
            });
        }
    }

    // ========== CATEGORY VIEW MANAGEMENT ==========

    // Reload all categories based on current view
    function loadAllCategories() {
        if (currentCategoryView === 'deleted') {
            loadDeletedCategories();
        } else {
            loadExpenseCategories();
        }
        updateCategoryBadges();
    }

    // Toggle between active and deleted category views
    function toggleCategoryView(view) {
        currentCategoryView = view;

        // Update tab buttons
        const activeBtn = document.getElementById('btn-active-categories');
        const deletedBtn = document.getElementById('btn-deleted-categories');

        // Get table headers
        const headerActive = document.getElementById('table-header-active');
        const headerDeleted = document.getElementById('table-header-deleted');

        if (view === 'active') {
            // Switch to active view
            activeBtn.classList.add('active');
            deletedBtn.classList.remove('active');
            document.getElementById('alert-deleted-info').classList.add('d-none');

            // Show active header, hide deleted header
            headerActive.classList.remove('d-none');
            headerDeleted.classList.add('d-none');
        } else {
            // Switch to deleted view
            deletedBtn.classList.add('active');
            activeBtn.classList.remove('active');
            document.getElementById('alert-deleted-info').classList.remove('d-none');

            // Show deleted header, hide active header
            headerActive.classList.add('d-none');
            headerDeleted.classList.remove('d-none');
        }

        // Reload categories based on view
        if (view === 'deleted') {
            loadDeletedCategories();
        } else {
            loadExpenseCategories();
        }
    }

    // Update category badge counters
    async function updateCategoryBadges() {
        try {
            // Update active badge
            const activeResponse = await fetch('../../../api/expense-categories.php?is_active=true');
            const activeResult = await activeResponse.json();
            if (activeResult.success) {
                document.getElementById('badge-active').textContent = activeResult.data.length;
            }

            // Update deleted badge
            const deletedResponse = await fetch('../../../api/expense-categories.php?is_active=false');
            const deletedResult = await deletedResponse.json();
            if (deletedResult.success) {
                document.getElementById('badge-deleted').textContent = deletedResult.data.length;
            }
        } catch (error) {
            console.error('Error updating badges:', error);
        }
    }

    // Load deleted categories
    async function loadDeletedCategories() {
        const tableBody = document.getElementById('expenseCategoryTableBody');
        const badgeDeleted = document.getElementById('badge-deleted');

        try {
            const response = await fetch('../../../api/expense-categories.php?is_active=false');
            const result = await response.json();

            if (result.success && result.data) {
                // Update badge counter
                badgeDeleted.textContent = result.data.length;

                if (result.data.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="mb-3">
                                    <i class="ti ti-check-circle text-success" style="font-size: 3rem;"></i>
                                </div>
                                <h6 class="text-success mb-2">Tidak Ada Kategori Terhapus</h6>
                                <p class="text-muted mb-0">Semua kategori dalam kondisi aktif</p>
                            </td>
                        </tr>
                    `;
                } else {
                    tableBody.innerHTML = result.data.map(category => {
                        const deletedDate = new Date(category.updated_at);
                        const formattedDate = deletedDate.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });

                        return `
                        <tr class="align-middle">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avtar avtar-s bg-light-danger">
                                            <i class="ti ti-tag"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">${category.name}</h6>
                                        ${category.description ? `<p class="text-muted mb-1 small">${category.description}</p>` : '<p class="text-muted mb-1 small">Tidak ada deskripsi</p>'}
                                        <small class="text-muted">
                                            <i class="ti ti-clock me-1"></i>Dihapus: ${formattedDate}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger"><i class="ti ti-trash me-1"></i>Terhapus</span>
                            </td>
                            <td class="text-center" colspan="2">
                                <button class="btn btn-sm btn-outline-success" onclick="restoreCategory(${category.id})" title="Pulihkan kategori ini">
                                    <i class="ti ti-refresh"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger ms-1" onclick="permanentDeleteCategory(${category.id}, '${category.name}')" title="Hapus permanen">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        `;
                    }).join('');
                }
            }
        } catch (error) {
            console.error('Error loading deleted categories:', error);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <div class="text-danger">
                            <i class="ti ti-alert-circle" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Error memuat data kategori terhapus</p>
                            <small class="text-muted">Silakan coba refresh halaman</small>
                        </div>
                    </td>
                </tr>
            `;
        }
    }

    // Restore deleted category
    async function restoreCategory(categoryId) {
        const confirmation = await Swal.fire({
            title: 'Pulihkan Kategori?',
            html: `<div class="text-start">
                    <p class="mb-3">Kategori akan aktif kembali dan dapat digunakan untuk pengeluaran.</p>
                    <div class="alert alert-info mb-0">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Info:</strong> Kategori akan muncul kembali di dropdown pengeluaran.
                    </div>
                   </div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="ti ti-check me-1"></i>Ya, Pulihkan!',
            cancelButtonText: '<i class="ti ti-"></i>Batal',
        });

        if (!confirmation.isConfirmed) return;

        // Show loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang memulihkan kategori',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const response = await fetch(`../../../api/expense-categories.php?id=${categoryId}&action=restore`, {
                method: 'PATCH'
            });

            const result = await response.json();

            if (result.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Dipulihkan!',
                    text: 'Kategori berhasil dipulihkan dan dapat digunakan kembali',
                    timer: 1500,
                    showConfirmButton: false
                });

                // Reload data setelah alert selesai
                await loadDeletedCategories();
                await updateCategoryBadges();
                loadFinancialData(); // Update dropdown di form expense
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memulihkan!',
                    text: result.message || 'Gagal memulihkan kategori',
                    confirmButtonColor: '#dc2626'
                });
            }
        } catch (error) {
            console.error('Error restoring category:', error);
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan!',
                text: 'Tidak dapat memulihkan kategori. Silakan coba lagi.',
                confirmButtonColor: '#dc2626'
            });
        }
    }

    // Permanent delete category
    async function permanentDeleteCategory(categoryId, categoryName) {
        // First confirmation
        const firstConfirmation = await Swal.fire({
            title: 'Peringatan!',
            html: `<div class="text-start">
                    <p class="mb-2">Anda akan menghapus kategori:</p>
                    <div class="alert alert-danger mb-3">
                        <strong><i class="ti ti-tag me-1"></i>${categoryName}</strong>
                    </div>
                    <p class="text-danger mb-2"><strong>Data akan dihapus permanen dan TIDAK dapat dipulihkan!</strong></p>
                    <p class="text-muted small mb-0">Pastikan kategori ini tidak digunakan oleh pengeluaran manapun.</p>
                   </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Lanjutkan',
            cancelButtonText: 'Batal'
        });

        if (!firstConfirmation.isConfirmed) return;

        // Second confirmation (double check)
        const secondConfirmation = await Swal.fire({
            title: 'Konfirmasi Terakhir',
            html: `<div class="text-start">
                    <p class="mb-3">Apakah Anda benar-benar yakin ingin menghapus permanen kategori ini?</p>
                    <div class="alert alert-warning mb-0">
                        <i class="ti ti-alert-triangle me-2"></i>
                        <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
                    </div>
                   </div>`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Hapus Permanen!',
            cancelButtonText: 'Batal',
        });

        if (!secondConfirmation.isConfirmed) return;

        // Show loading
        Swal.fire({
            title: 'Menghapus...',
            text: 'Sedang menghapus kategori secara permanen',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const response = await fetch(`../../../api/expense-categories.php?id=${categoryId}&permanent=true`, {
                method: 'DELETE'
            });

            const result = await response.json();

            if (result.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Terhapus Permanen!',
                    text: 'Kategori berhasil dihapus secara permanen',
                    timer: 1500,
                    showConfirmButton: false
                });

                // Reload data setelah alert selesai
                await loadDeletedCategories();
                await updateCategoryBadges();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menghapus!',
                    text: result.message || 'Gagal menghapus kategori secara permanen',
                    confirmButtonColor: '#dc2626'
                });
            }
        } catch (error) {
            console.error('Error permanent deleting category:', error);
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan!',
                text: 'Tidak dapat menghapus kategori. Silakan coba lagi.',
                confirmButtonColor: '#dc2626'
            });
        }
    }

</script>