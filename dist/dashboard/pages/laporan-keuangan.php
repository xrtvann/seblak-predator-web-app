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
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="form-label mb-0"><i class="ti ti-calendar me-2"></i>Periode:</label>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="periodFilter" onchange="loadFinancialData()">
                            <option value="today">Hari Ini</option>
                            <option value="week">Minggu Ini</option>
                            <option value="month" selected>Bulan Ini</option>
                            <option value="year">Tahun Ini</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div class="col-md-2" id="startDateGroup" style="display: none;">
                        <input type="date" class="form-control" id="startDate" onchange="loadFinancialData()">
                    </div>
                    <div class="col-md-2" id="endDateGroup" style="display: none;">
                        <input type="date" class="form-control" id="endDate" onchange="loadFinancialData()">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" onclick="exportReport()">
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
        <div class="card bg-danger-dark dashnum-card text-white overflow-hidden">
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Rincian Pendapatan</h5>
                <button class="btn btn-sm btn-outline-primary" onclick="showAllRevenue()">
                    <i class="ti ti-eye me-1"></i>Lihat Semua
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Order ID</th>
                                <th>Metode</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody id="revenueTableBody">
                            <tr>
                                <td colspan="4" class="text-center">
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
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Rincian Pengeluaran</h5>
                <button class="btn btn-sm btn-outline-primary" onclick="showExpenseModal()">
                    <i class="ti ti-plus me-1"></i>Tambah Pengeluaran
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Keterangan</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody id="expenseTableBody">
                            <tr>
                                <td colspan="4" class="text-center">
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
    </div>

    <!-- Top Products -->
    <div class="col-12">
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
    </div>
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

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    let revenueExpenseChart, expenseCategoryChart;
    let financialData = {
        revenue: 0,
        expenses: 0,
        profit: 0,
        margin: 0
    };

    // Initialize page
    document.addEventListener('DOMContentLoaded', function () {
        loadExpenseCategories();
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

    // Load expense categories
    async function loadExpenseCategories() {
        try {
            const response = await fetch('api/expense-categories.php');
            const result = await response.json();

            if (result.success) {
                const select = document.getElementById('expenseCategory');
                select.innerHTML = '<option value="">Pilih Kategori</option>';
                result.data.forEach(cat => {
                    select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
                });
            } else {
                console.error('Error loading categories:', result.message);
            }
        } catch (error) {
            console.error('Error loading categories:', error);
            // Don't show toast for this error as it's not critical
        }
    }

    // Load financial data
    async function loadFinancialData() {
        const period = document.getElementById('periodFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        let url = `api/financial-report.php?period=${period}`;
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
            </tr>
        `).join('');
        } else {
            expenseTable.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>';
        }

        // Top products table
        const topProductsTable = document.getElementById('topProductsTableBody');
        if (data.top_products && data.top_products.length > 0) {
            topProductsTable.innerHTML = data.top_products.map((item, index) => `
            <tr>
                <td>
                    <span class="badge ${index === 0 ? 'bg-warning' : index === 1 ? 'bg-light-warning' : 'bg-light-secondary'}">
                        #${index + 1}
                    </span>
                </td>
                <td><strong>${item.product_name}</strong></td>
                <td class="text-center">${item.total_quantity}</td>
                <td class="text-end text-success fw-bold">${formatRupiah(item.total_revenue)}</td>
                <td class="text-end">
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: ${item.contribution}%" 
                             aria-valuenow="${item.contribution}" 
                             aria-valuemin="0" 
                             aria-valuemax="100"></div>
                    </div>
                    <small class="text-muted">${item.contribution}%</small>
                </td>
            </tr>
        `).join('');
        } else {
            topProductsTable.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>';
        }
    }

    // Show expense modal
    function showExpenseModal() {
        document.getElementById('expenseForm').reset();
        document.getElementById('expenseDate').value = new Date().toISOString().split('T')[0];
        new bootstrap.Modal(document.getElementById('addExpenseModal')).show();
    }

    // Save expense
    async function saveExpense() {
        const form = document.getElementById('expenseForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const data = {
            title: document.getElementById('expenseTitle').value,
            category_id: document.getElementById('expenseCategory').value,
            amount: document.getElementById('expenseAmount').value,
            expense_date: document.getElementById('expenseDate').value,
            description: document.getElementById('expenseDescription').value
        };

        try {
            const response = await fetch('api/expenses.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showToast('Pengeluaran berhasil ditambahkan', 'success');
                bootstrap.Modal.getInstance(document.getElementById('addExpenseModal')).hide();
                loadFinancialData();
            } else {
                showToast(result.message || 'Gagal menambahkan pengeluaran', 'error');
            }
        } catch (error) {
            console.error('Error saving expense:', error);
            showToast('Terjadi kesalahan', 'error');
        }
    }

    // Export report
    function exportReport() {
        const period = document.getElementById('periodFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        let url = `api/export-financial-report.php?period=${period}`;
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
        // Check if SweetAlert2 is loaded
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            // Fallback to console and alert
            console.log(`[${type.toUpperCase()}] ${message}`);
            if (type === 'error') {
                alert(message);
            }
        }
    }

    function showAllRevenue() {
        // Navigate to full revenue report
        window.location.href = 'index.php?page=transaksi';
    }
</script>