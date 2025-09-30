<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-header-title">
                    <h5 class="m-b-10">Kategori</h5>
                </div>
            </div>
            <div class="col-auto">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a href="index.php?page=kategori">Produk</a></li>
                    <li class="breadcrumb-item" aria-current="page">Kategori</li>
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
                            <i class="ti ti-category f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Kategori</h6>
                        <b class="text-primary">15</b>
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
                        <h6 class="mb-0">Kategori Aktif</h6>
                        <b class="text-success">12</b>
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
                        <h6 class="mb-0">Kategori Nonaktif</h6>
                        <b class="text-warning">3</b>
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
                            <i class="ti ti-chart-line f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Kategori Populer</h6>
                        <b class="text-info">8</b>
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
                <h5>Data Kategori</h5>
                <div class="card-header-right">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                        <i class="ti ti-plus"></i>
                        Tambah Kategori
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Makanan</td>
                                <td>Kategori untuk makanan</td>
                                <td><span class="badge bg-success">Aktif</span></td>
                                <td>
                                    <button class="btn btn-sm btn-warning"><i class="ti ti-edit"></i></button>
                                    <button class="btn btn-sm btn-danger"><i class="ti ti-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Minuman</td>
                                <td>Kategori untuk minuman</td>
                                <td><span class="badge bg-success">Aktif</span></td>
                                <td>
                                    <button class="btn btn-sm btn-warning"><i class="ti ti-edit"></i></button>
                                    <button class="btn btn-sm btn-danger"><i class="ti ti-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
<!-- [ Main Content ] end -->

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-labelledby="modalTambahKategoriLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahKategoriLabel">
                    <i class="ti ti-plus me-2"></i>
                    Tambah Kategori Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form id="formTambahKategori" novalidate>
                <div class="modal-body">
                    <div class="row">
                        <!-- Form Input -->
                        <div class="col-lg-8">
                            <div class="row">
                                <!-- Nama Kategori -->
                                <div class="col-12 mb-4">
                                    <label for="namaKategori" class="form-label fw-semibold">
                                        <i class="ti ti-category text-primary me-1"></i>
                                        Nama Kategori <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form-control-lg" id="namaKategori"
                                        name="namaKategori" required placeholder="Contoh: Makanan, Minuman, Snack, dll">
                                    <div class="invalid-feedback">
                                        Nama kategori harus diisi
                                    </div>
                                    <div class="form-text">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Masukkan nama kategori yang mudah dipahami
                                    </div>
                                </div>

                                <!-- Deskripsi -->
                                <div class="col-12 mb-4">
                                    <label for="deskripsiKategori" class="form-label fw-semibold">
                                        <i class="ti ti-file-description text-primary me-1"></i>
                                        Deskripsi Kategori <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="deskripsiKategori" name="deskripsiKategori"
                                        rows="3" required placeholder="Jelaskan kategori ini untuk apa..."></textarea>
                                    <div class="invalid-feedback">
                                        Deskripsi kategori harus diisi
                                    </div>
                                    <div class="form-text">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Berikan penjelasan singkat tentang kategori ini
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-12 mb-4">
                                    <label for="statusKategori" class="form-label fw-semibold">
                                        <i class="ti ti-toggle-left text-primary me-1"></i>
                                        Status Kategori
                                    </label>
                                    <select class="form-select form-select-lg" id="statusKategori" name="statusKategori"
                                        required>
                                        <option value="aktif" selected>✅ Aktif - Kategori dapat digunakan</option>
                                        <option value="nonaktif">❌ Nonaktif - Kategori tidak dapat digunakan</option>
                                    </select>
                                    <div class="form-text">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Pilih "Aktif" agar kategori dapat digunakan untuk menu
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Card -->
                        <div class="col-lg-4">
                            <div class="card border border-primary">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">
                                        <i class="ti ti-eye me-1"></i>
                                        Preview Kategori
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <div class="avtar avtar-lg bg-light-primary mb-2">
                                            <i class="ti ti-category f-24" id="previewIcon"></i>
                                        </div>
                                        <h5 class="mb-1" id="previewNama">Nama Kategori</h5>
                                        <span class="badge bg-success" id="previewStatus">Aktif</span>
                                    </div>
                                    <hr>
                                    <p class="text-muted mb-0" id="previewDeskripsi">
                                        Deskripsi kategori akan muncul di sini...
                                    </p>
                                </div>
                            </div>

                            <!-- Info Helper -->
                            <div class="alert alert-info mt-3">
                                <div class="d-flex">
                                    <i class="ti ti-bulb f-20 me-2"></i>
                                    <div>
                                        <strong>Tips:</strong>
                                        <ul class="mb-0 mt-1">
                                            <li>Gunakan nama yang jelas</li>
                                            <li>Deskripsi yang mudah dipahami</li>
                                            <li>Status aktif untuk penggunaan</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy"></i>
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>