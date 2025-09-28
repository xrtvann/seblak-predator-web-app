<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-header-title">
                    <h5 class="m-b-10">Transaksi</h5>
                </div>
            </div>
            <div class="col-auto">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page">Transaksi</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <!-- [ Statistics Cards ] start -->
    <div class="col-md-6 col-xxl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-primary">
                            <i class="ti ti-shopping-cart f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Transaksi</h6>
                        <b class="text-primary">1,234</b>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xxl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-success">
                            <i class="ti ti-check f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Transaksi Selesai</h6>
                        <b class="text-success">1,156</b>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xxl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-warning">
                            <i class="ti ti-clock f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Transaksi Pending</h6>
                        <b class="text-warning">45</b>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xxl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-info">
                            <i class="ti ti-cash f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Pendapatan</h6>
                        <b class="text-info">Rp 25,540,000</b>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Statistics Cards ] end -->

    <!-- [ Main Content ] start -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Data Transaksi</h5>
                <div class="card-header-right">
                    <button class="btn btn-primary">
                        <i class="ti ti-plus"></i>
                        Transaksi Baru
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-search"></i></span>
                            <input type="text" class="form-control" placeholder="Cari transaksi...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select">
                            <option value="">Semua Status</option>
                            <option value="selesai">Selesai</option>
                            <option value="pending">Pending</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>No. Transaksi</th>
                                <th>Tanggal & Waktu</th>
                                <th>Customer</th>
                                <th>Menu</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td><strong>#TRX001</strong></td>
                                <td>
                                    <div>28 Sep 2025</div>
                                    <small class="text-muted">14:30 WIB</small>
                                </td>
                                <td>
                                    <div>Andi Wijaya</div>
                                    <small class="text-muted">Meja #5</small>
                                </td>
                                <td>
                                    <div>2x Seblak Level 3</div>
                                    <small class="text-muted">1x Es Teh Manis</small>
                                </td>
                                <td><strong class="text-success">Rp 33.000</strong></td>
                                <td><span class="badge bg-success">Selesai</span></td>
                                <td>
                                    <button class="btn btn-sm btn-info" title="Lihat Detail"><i
                                            class="ti ti-eye"></i></button>
                                    <button class="btn btn-sm btn-secondary" title="Print"><i
                                            class="ti ti-printer"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><strong>#TRX002</strong></td>
                                <td>
                                    <div>28 Sep 2025</div>
                                    <small class="text-muted">14:15 WIB</small>
                                </td>
                                <td>
                                    <div>Sari Dewi</div>
                                    <small class="text-muted">Meja #3</small>
                                </td>
                                <td>
                                    <div>1x Seblak Level 5</div>
                                    <small class="text-muted">2x Es Jeruk</small>
                                </td>
                                <td><strong class="text-warning">Rp 30.000</strong></td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>
                                    <button class="btn btn-sm btn-info" title="Lihat Detail"><i
                                            class="ti ti-eye"></i></button>
                                    <button class="btn btn-sm btn-success" title="Selesaikan"><i
                                            class="ti ti-check"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td><strong>#TRX003</strong></td>
                                <td>
                                    <div>28 Sep 2025</div>
                                    <small class="text-muted">13:45 WIB</small>
                                </td>
                                <td>
                                    <div>Budi Santoso</div>
                                    <small class="text-muted">Meja #1</small>
                                </td>
                                <td>
                                    <div>3x Seblak Level 2</div>
                                    <small class="text-muted">1x Teh Hangat</small>
                                </td>
                                <td><strong class="text-success">Rp 40.000</strong></td>
                                <td><span class="badge bg-success">Selesai</span></td>
                                <td>
                                    <button class="btn btn-sm btn-info" title="Lihat Detail"><i
                                            class="ti ti-eye"></i></button>
                                    <button class="btn btn-sm btn-secondary" title="Print"><i
                                            class="ti ti-printer"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td><strong>#TRX004</strong></td>
                                <td>
                                    <div>28 Sep 2025</div>
                                    <small class="text-muted">13:20 WIB</small>
                                </td>
                                <td>
                                    <div>Maya Putri</div>
                                    <small class="text-muted">Meja #7</small>
                                </td>
                                <td>
                                    <div>1x Seblak Level 1</div>
                                    <small class="text-muted">1x Es Lemon</small>
                                </td>
                                <td><strong class="text-success">Rp 17.000</strong></td>
                                <td><span class="badge bg-success">Selesai</span></td>
                                <td>
                                    <button class="btn btn-sm btn-info" title="Lihat Detail"><i
                                            class="ti ti-eye"></i></button>
                                    <button class="btn btn-sm btn-secondary" title="Print"><i
                                            class="ti ti-printer"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td><strong>#TRX005</strong></td>
                                <td>
                                    <div>28 Sep 2025</div>
                                    <small class="text-muted">12:55 WIB</small>
                                </td>
                                <td>
                                    <div>Roni Pratama</div>
                                    <small class="text-muted">Meja #4</small>
                                </td>
                                <td>
                                    <div>2x Seblak Level 4</div>
                                    <small class="text-muted">2x Es Teh Manis</small>
                                </td>
                                <td><strong class="text-danger">Rp 42.000</strong></td>
                                <td><span class="badge bg-danger">Dibatalkan</span></td>
                                <td>
                                    <button class="btn btn-sm btn-info" title="Lihat Detail"><i
                                            class="ti ti-eye"></i></button>
                                    <button class="btn btn-sm btn-warning" title="Restore"><i
                                            class="ti ti-refresh"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="row align-items-center mt-3">
                    <div class="col-md-6">
                        <small class="text-muted">Menampilkan 1-5 dari 1,234 transaksi</small>
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm justify-content-end mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
<!-- [ Main Content ] end -->