<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-header-title">
                    <h5 class="m-b-10">Dashboard</h5>
                </div>
            </div>
            <div class="col-auto">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page">Dashboard</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <!-- [ Statistics Cards ] start -->
    <div class="col-xl-3 col-md-6">
        <div class="card bg-secondary-dark dashnum-card text-white overflow-hidden">
            <span class="round small"></span>
            <span class="round big"></span>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avtar avtar-lg">
                        <i class="text-white ti ti-soup"></i>
                    </div>
                    <div class="ms-2">
                        <h4 class="text-white mb-1" id="totalTopping"><i class="ti ti-loader"></i></h4>
                        <p class="mb-0 opacity-75 text-sm">Total Topping</p>
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
                        <i class="text-white ti ti-receipt"></i>
                    </div>
                    <div class="ms-2">
                        <h4 class="text-white mb-1" id="transaksiHariIni"><i class="ti ti-loader"></i></h4>
                        <p class="mb-0 opacity-75 text-sm">Transaksi Hari Ini</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-success-dark dashnum-card text-white overflow-hidden">
            <span class="round small"></span>
            <span class="round big"></span>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avtar avtar-lg">
                        <i class="text-white ti ti-wallet"></i>
                    </div>
                    <div class="ms-2">
                        <h4 class="text-white mb-1" id="pendapatanHariIni"><i class="ti ti-loader"></i></h4>
                        <p class="mb-0 opacity-75 text-sm">Pendapatan Hari Ini</p>
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
                        <i class="text-white ti ti-users"></i>
                    </div>
                    <div class="ms-2">
                        <h4 class="text-white mb-1" id="totalPelanggan"><i class="ti ti-loader"></i></h4>
                        <p class="mb-0 opacity-75 text-sm">Total Pelanggan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Statistics Cards ] end -->

    <!-- [ Sales Chart ] start -->
    <div class="col-sm-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5>Penjualan 7 Hari Terakhir</h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="80"></canvas>
            </div>
        </div>
    </div>
    <!-- [ Sales Chart ] end -->
    <!-- [ Statistics Cards ] end -->

    <!-- [ Welcome Card ] start -->
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Selamat Datang di Seblak Predator!</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p>Sistem manajemen untuk usaha seblak anda. Kelola transaksi, dan laporan dengan mudah.
                        </p>

                        <h6>Fitur Utama:</h6>
                        <ul class="list-unstyled">
                            <li><i class="ti ti-check text-success me-2"></i>Manajemen Topping & Kategori</li>
                            <li><i class="ti ti-check text-success me-2"></i>Pencatatan Transaksi</li>
                            <li><i class="ti ti-check text-success me-2"></i>Laporan Keuangan</li>
                            <li><i class="ti ti-check text-success me-2"></i>Manajemen User</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Quick Actions:</h6>
                        <a href="index.php?page=dasar-seblak" class="btn btn-primary me-2 mb-2">
                            <i class="ti ti-soup me-1"></i>Kelola Dasar Seblak
                        </a>
                        <a href="index.php?page=transaksi" class="btn btn-success me-2 mb-2">
                            <i class="ti ti-receipt me-1"></i>Transaksi Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Welcome Card ] end -->
</div>
<!-- [ Main Content ] end -->

<!-- Dashboard Stats Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Format Rupiah
    function formatRupiah(angka) {
        if (angka >= 1000000) {
            return 'Rp ' + (angka / 1000000).toFixed(1) + 'M';
        } else if (angka >= 1000) {
            return 'Rp ' + (angka / 1000).toFixed(1) + 'K';
        }
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }

    // Format Number dengan K
    function formatNumber(angka) {
        if (angka >= 1000) {
            return (angka / 1000).toFixed(1) + 'K';
        }
        return angka;
    }

    let salesChart = null;

    // Render Sales Chart
    function renderSalesChart(salesData) {
        const ctx = document.getElementById('salesChart');

        // Destroy existing chart if exists
        if (salesChart) {
            salesChart.destroy();
        }

        const labels = salesData.map(item => item.date);
        const data = salesData.map(item => item.total);

        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: data,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return formatRupiah(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return formatRupiah(value);
                            }
                        }
                    }
                }
            }
        });
    }

    // Load dashboard statistics
    function loadDashboardStats() {
        fetch('api/dashboard-stats.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update existing cards
                    document.getElementById('totalTopping').textContent = data.data.totalTopping;
                    document.getElementById('transaksiHariIni').textContent = data.data.transaksiHariIni;
                    document.getElementById('pendapatanHariIni').textContent = formatRupiah(data.data.pendapatanHariIni);
                    document.getElementById('totalPelanggan').textContent = formatNumber(data.data.totalPelanggan);

                    // Render chart and top products
                    renderSalesChart(data.data.penjualan7Hari);
                } else {
                    // Show zeros if API fails
                    showDefaultValues();
                }
            })
            .catch(error => {
                console.error('Error loading dashboard stats:', error);
                // Show zeros if fetch fails
                showDefaultValues();
            });
    }

    // Show default values (0) if API fails
    function showDefaultValues() {
        document.getElementById('totalTopping').textContent = '0';
        document.getElementById('transaksiHariIni').textContent = '0';
        document.getElementById('pendapatanHariIni').textContent = 'Rp 0';
        document.getElementById('totalPelanggan').textContent = '0';

        renderSalesChart([]);
    }

    // Load stats when page loads
    document.addEventListener('DOMContentLoaded', function () {
        loadDashboardStats();

        // Refresh stats every 30 seconds
        setInterval(loadDashboardStats, 30000);
    });
</script>