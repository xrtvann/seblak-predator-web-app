<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-header-title">
                    <h5 class="m-b-10" id="pageTitleText">Menu</h5>
                </div>
            </div>
            <div class="col-auto">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0)">Produk</a></li>
                    <li class="breadcrumb-item" aria-current="page" id="breadcrumbText">Menu</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <!-- [ Data Menu ] start -->
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 id="cardTitleText">Data Menu</h5>
                        <p class="text-muted mb-0 d-none" id="cardSubtitleText">Isi form di bawah untuk
                            menambahkan menu baru</p>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex align-items-center" id="headerActions">
                            <!-- View Toggle Tabs -->
                            <ul class="nav nav-pills me-3" id="viewToggleTabs">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pills-table-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-table" type="button" role="tab"
                                        aria-controls="pills-table" aria-selected="true">
                                        <i class="ti ti-table"></i> Table View
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-card-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-card" type="button" role="tab" aria-controls="pills-card"
                                        aria-selected="false">
                                        <i class="ti ti-layout-grid"></i> Card View
                                    </button>
                                </li>
                            </ul>

                            <button class="btn btn-primary" id="btnTambahMenu" onclick="showFormTambah()">
                                <i class="ti ti-plus"></i>
                                Tambah Menu
                            </button>

                            <button class="btn btn-outline-secondary d-none" id="btnKembali" onclick="showDataMenu()">
                                <i class="ti ti-arrow-left me-2"></i>
                                Kembali
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body" id="mainContentArea">
                <!-- Content will be loaded dynamically by JavaScript -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Data Menu ] end -->
</div>
<!-- [ Main Content ] end -->

<!-- JavaScript untuk Toggle Konten Menu -->
<script>
    // Initialize page - show data menu by default
    document.addEventListener('DOMContentLoaded', function () {
        showDataMenu();
    });

    // Function to show data menu (list view)
    function showDataMenu() {
        const mainContent = document.getElementById('mainContentArea');

        // Update header
        document.getElementById('pageTitleText').textContent = 'Menu';
        document.getElementById('breadcrumbText').textContent = 'Menu';
        document.getElementById('cardTitleText').textContent = 'Data Menu';
        document.getElementById('cardSubtitleText').classList.add('d-none');

        // Toggle buttons using Bootstrap classes
        document.getElementById('btnTambahMenu').classList.remove('d-none');
        document.getElementById('btnKembali').classList.add('d-none');
        document.getElementById('viewToggleTabs').classList.remove('d-none');

        // Set content
        mainContent.innerHTML = getDataMenuHTML();
    }

    // Function to show form tambah menu
    function showFormTambah() {
        const mainContent = document.getElementById('mainContentArea');

        // Update header
        document.getElementById('pageTitleText').textContent = 'Tambah Menu';
        document.getElementById('breadcrumbText').textContent = 'Tambah Menu';
        document.getElementById('cardTitleText').textContent = 'Form Tambah Menu';
        document.getElementById('cardSubtitleText').classList.remove('d-none');

        // Toggle buttons using Bootstrap classes
        document.getElementById('btnTambahMenu').classList.add('d-none');
        document.getElementById('btnKembali').classList.remove('d-none');
        document.getElementById('viewToggleTabs').classList.add('d-none');

        // Set content
        mainContent.innerHTML = getFormTambahHTML();

        // Initialize form functionality
        initFormTambah();
    }

    // Get Data Menu HTML
    function getDataMenuHTML() {
        return `
                <!-- Tab Content -->
                <div class="tab-content" id="pills-tabContent">
                    <!-- Table View -->
                    <div class="tab-pane fade show active" id="pills-table" role="tabpanel"
                        aria-labelledby="pills-table-tab" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Gambar</th>
                                        <th>Nama Menu</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=60&h=60&fit=crop&crop=center"
                                                alt="Seblak Level 1" class="img-thumbnail"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td><strong>Seblak Level 1 (Original)</strong></td>
                                        <td><span class="badge bg-primary">Makanan</span></td>
                                        <td><strong>Rp 10.000</strong></td>
                                        <td><span class="badge bg-success">Aktif</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning"><i class="ti ti-edit"></i></button>
                                            <button class="btn btn-sm btn-danger"><i class="ti ti-trash"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>
                                            <img src="dist/assets/images/menu/spice-mild.png" alt="Seblak Level 2"
                                                class="img-thumbnail"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td><strong>Seblak Level 2 (Sedang)</strong></td>
                                        <td><span class="badge bg-primary">Makanan</span></td>
                                        <td><strong>Rp 12.000</strong></td>
                                        <td><span class="badge bg-success">Aktif</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning"><i class="ti ti-edit"></i></button>
                                            <button class="btn btn-sm btn-danger"><i class="ti ti-trash"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>
                                            <img src="dist/assets/images/menu/spice-spicy.png" alt="Seblak Level 3"
                                                class="img-thumbnail"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td><strong>Seblak Level 3 (Pedas)</strong></td>
                                        <td><span class="badge bg-primary">Makanan</span></td>
                                        <td><strong>Rp 14.000</strong></td>
                                        <td><span class="badge bg-success">Aktif</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning"><i class="ti ti-edit"></i></button>
                                            <button class="btn btn-sm btn-danger"><i class="ti ti-trash"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>
                                            <img src="dist/assets/images/menu/spice-hot.png" alt="Seblak Level 4"
                                                class="img-thumbnail"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td><strong>Seblak Level 4 (Extra Pedas)</strong></td>
                                        <td><span class="badge bg-primary">Makanan</span></td>
                                        <td><strong>Rp 16.000</strong></td>
                                        <td><span class="badge bg-success">Aktif</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning"><i class="ti ti-edit"></i></button>
                                            <button class="btn btn-sm btn-danger"><i class="ti ti-trash"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>
                                            <img src="dist/assets/images/menu/spice-extra.png" alt="Seblak Level 5"
                                                class="img-thumbnail"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td><strong>Seblak Level 5 (Predator)</strong></td>
                                        <td><span class="badge bg-primary">Makanan</span></td>
                                        <td><strong>Rp 18.000</strong></td>
                                        <td><span class="badge bg-success">Aktif</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning"><i class="ti ti-edit"></i></button>
                                            <button class="btn btn-sm btn-danger"><i class="ti ti-trash"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <td>
                                            <img src="https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=60&h=60&fit=crop&crop=center"
                                                alt="Es Teh Manis" class="img-thumbnail"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td><strong>Es Teh Manis</strong></td>
                                        <td><span class="badge bg-info">Minuman</span></td>
                                        <td><strong>Rp 5.000</strong></td>
                                        <td><span class="badge bg-success">Aktif</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning"><i class="ti ti-edit"></i></button>
                                            <button class="btn btn-sm btn-danger"><i class="ti ti-trash"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>7</td>
                                        <td>
                                            <img src="https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=60&h=60&fit=crop&crop=center"
                                                alt="Es Jeruk" class="img-thumbnail"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td><strong>Es Jeruk</strong></td>
                                        <td><span class="badge bg-info">Minuman</span></td>
                                        <td><strong>Rp 6.000</strong></td>
                                        <td><span class="badge bg-success">Aktif</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning"><i class="ti ti-edit"></i></button>
                                            <button class="btn btn-sm btn-danger"><i class="ti ti-trash"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>8</td>
                                        <td>
                                            <img src="https://images.unsplash.com/photo-1497534547324-0ebb3f54ca68?w=60&h=60&fit=crop&crop=center"
                                                alt="Teh Hangat" class="img-thumbnail"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td><strong>Teh Hangat</strong></td>
                                        <td><span class="badge bg-info">Minuman</span></td>
                                        <td><strong>Rp 4.000</strong></td>
                                        <td><span class="badge bg-success">Aktif</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning"><i class="ti ti-edit"></i></button>
                                            <button class="btn btn-sm btn-danger"><i class="ti ti-trash"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>9</td>
                                        <td>
                                            <img src="https://images.unsplash.com/photo-1553830591-d8632a99e6ff?w=60&h=60&fit=crop&crop=center"
                                                alt="Es Lemon" class="img-thumbnail"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td><strong>Es Lemon</strong></td>
                                        <td><span class="badge bg-info">Minuman</span></td>
                                        <td><strong>Rp 7.000</strong></td>
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

                    <!-- Card View -->
                    <div class="tab-pane fade" id="pills-card" role="tabpanel" aria-labelledby="pills-card-tab"
                        tabindex="0">
                        <div class="row g-3">
                            <!-- Menu Card 1 -->
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="card menu-card h-100">
                                    <div class="position-relative">
                                        <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=250&fit=crop&crop=center"
                                            class="card-img-top" alt="Seblak Level 1"
                                            style="height: 200px; object-fit: cover;">
                                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Aktif</span>
                                        <span class="badge bg-primary position-absolute top-0 start-0 m-2">Makanan</span>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">Seblak Level 1 (Original)</h5>
                                        <p class="card-text text-muted small">Seblak dengan rasa original, cocok untuk pemula</p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <h4 class="text-primary mb-0">Rp 10.000</h4>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Add more cards as needed (same structure) -->
                        </div>
                    </div>
                </div>
        `;
    }

    // Get Form Tambah HTML
    function getFormTambahHTML() {
        return `
                <form id="formTambahMenu" method="POST" action="handler/menu_handler.php" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="action" value="tambah">
                    
                    <div class="row">
                        <!-- Kolom Kiri - Form Input -->
                        <div class="col-lg-8">
                            <div class="row">
                                <!-- Nama Menu -->
                                <div class="col-12 mb-3">
                                    <label for="namaMenu" class="form-label">
                                        <i class="ti ti-soup text-primary"></i>
                                        Nama Menu <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="namaMenu" name="namaMenu" required
                                        placeholder="Contoh: Seblak Pedas, Es Teh Manis, dll">
                                    <div class="invalid-feedback">Nama menu harus diisi</div>
                                    <div class="form-text">Masukkan nama menu yang jelas dan menarik</div>
                                </div>

                                <!-- Kategori & Harga -->
                                <div class="col-md-6 mb-3">
                                    <label for="kategori" class="form-label">
                                        <i class="ti ti-category text-info"></i>
                                        Kategori <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="kategori" name="kategori" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="makanan">🍜 Makanan</option>
                                        <option value="minuman">🥤 Minuman</option>
                                        <option value="dessert">🍮 Dessert</option>
                                        <option value="snack">🍿 Snack</option>
                                    </select>
                                    <div class="invalid-feedback">Kategori harus dipilih</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="harga" class="form-label">
                                        <i class="ti ti-currency-dollar text-success"></i>
                                        Harga <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="harga" name="harga" required
                                            placeholder="15000" min="1000" step="500">
                                        <div class="invalid-feedback">Harga harus diisi dengan minimal Rp 1.000</div>
                                    </div>
                                    <div class="form-text">Masukkan harga tanpa titik atau koma</div>
                                </div>

                                <!-- Deskripsi -->
                                <div class="col-12 mb-3">
                                    <label for="deskripsi" class="form-label">
                                        <i class="ti ti-file-description text-secondary"></i>
                                        Deskripsi Menu
                                    </label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"
                                        placeholder="Deskripsi singkat tentang menu ini (opsional)"></textarea>
                                    <div class="form-text">Jelaskan keunikan atau bahan utama menu ini</div>
                                </div>

                                <!-- Upload Gambar -->
                                <div class="col-12 mb-3">
                                    <label for="gambarMenu" class="form-label">
                                        <i class="ti ti-camera text-warning"></i>
                                        Gambar Menu
                                    </label>
                                    <input type="file" class="form-control" id="gambarMenu" name="gambarMenu" accept="image/*">
                                    <div class="form-text">
                                        <small>Format yang didukung: JPG, PNG, GIF (Maksimal 2MB)</small>
                                    </div>
                                    <!-- Preview gambar -->
                                    <div id="imagePreview" class="mt-3" style="display: none;">
                                        <img id="previewImg" src="" alt="Preview" class="img-thumbnail"
                                            style="max-width: 300px; max-height: 200px;">
                                        <button type="button" class="btn btn-sm btn-danger ms-2 mt-2" onclick="removeImagePreview()">
                                            <i class="ti ti-x"></i> Hapus
                                        </button>
                                    </div>
                                </div>

                                <!-- Status & Stok -->
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">
                                        <i class="ti ti-toggle-right text-success"></i>
                                        Status Menu
                                    </label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="aktif" selected>✅ Aktif</option>
                                        <option value="nonaktif">❌ Nonaktif</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="stok" class="form-label">
                                        <i class="ti ti-package text-info"></i>
                                        Stok Tersedia
                                    </label>
                                    <input type="number" class="form-control" id="stok" name="stok" placeholder="100" min="0" value="999">
                                    <div class="form-text">Kosongkan jika stok tidak terbatas</div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan - Preview Card -->
                        <div class="col-lg-4">
                            <div class="card border bg-light sticky-top" style="top: 20px;">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="ti ti-eye"></i>
                                        Preview Menu
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="card menu-card h-100 shadow-sm">
                                        <div class="position-relative">
                                            <img id="previewCardImg"
                                                src="https://via.placeholder.com/400x250/e9ecef/6c757d?text=No+Image"
                                                class="card-img-top" alt="Preview"
                                                style="height: 200px; object-fit: cover;">
                                            <span id="previewStatus"
                                                class="badge bg-success position-absolute top-0 end-0 m-2">Aktif</span>
                                            <span id="previewKategori"
                                                class="badge bg-primary position-absolute top-0 start-0 m-2">Kategori</span>
                                        </div>
                                        <div class="card-body">
                                            <h6 id="previewNama" class="card-title">Nama Menu</h6>
                                            <p id="previewDeskripsi" class="card-text text-muted small">Deskripsi menu...</p>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <h5 id="previewHarga" class="text-success mb-0">Rp 0</h5>
                                                <span id="previewStok" class="badge bg-info">Stok: 999</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-light">
                                    <small class="text-muted">
                                        <i class="ti ti-info-circle"></i>
                                        Preview akan diupdate secara otomatis
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-danger" onclick="showDataMenu()">
                                    <i class="ti ti-x me-1"></i>
                                    Batal
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="ti ti-device-floppy me-1"></i>
                                    Simpan Menu
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
        `;
    }

    // Initialize Form Tambah functionality
    function initFormTambah() {
        // Form elements
        const form = document.getElementById('formTambahMenu');
        const namaMenu = document.getElementById('namaMenu');
        const kategori = document.getElementById('kategori');
        const harga = document.getElementById('harga');
        const deskripsi = document.getElementById('deskripsi');
        const status = document.getElementById('status');
        const stok = document.getElementById('stok');
        const gambarInput = document.getElementById('gambarMenu');

        // Preview elements
        const previewNama = document.getElementById('previewNama');
        const previewKategori = document.getElementById('previewKategori');
        const previewHarga = document.getElementById('previewHarga');
        const previewDeskripsi = document.getElementById('previewDeskripsi');
        const previewStatus = document.getElementById('previewStatus');
        const previewStok = document.getElementById('previewStok');
        const previewCardImg = document.getElementById('previewCardImg');

        // Update preview in real-time
        function updatePreview() {
            // Nama menu
            previewNama.textContent = namaMenu.value || 'Nama Menu';

            // Kategori
            const katText = kategori.options[kategori.selectedIndex].text;
            previewKategori.textContent = katText !== 'Pilih Kategori' ? katText : 'Kategori';

            // Dynamic badge color based on kategori
            const badgeColors = {
                'makanan': 'bg-primary',
                'minuman': 'bg-info',
                'dessert': 'bg-warning',
                'snack': 'bg-secondary'
            };
            const badgeColor = badgeColors[kategori.value] || 'bg-secondary';
            previewKategori.className = 'badge ' + badgeColor + ' position-absolute top-0 start-0 m-2';

            // Harga
            const hargaVal = parseInt(harga.value) || 0;
            previewHarga.textContent = 'Rp ' + hargaVal.toLocaleString('id-ID');

            // Deskripsi
            previewDeskripsi.textContent = deskripsi.value || 'Deskripsi menu...';

            // Status
            previewStatus.textContent = status.value === 'aktif' ? 'Aktif' : 'Nonaktif';
            previewStatus.className = 'badge position-absolute top-0 end-0 m-2 ' + (status.value === 'aktif' ? 'bg-success' : 'bg-secondary');

            // Stok
            const stokVal = parseInt(stok.value) || 0;
            previewStok.textContent = 'Stok: ' + stokVal;
        }

        // Event listeners for real-time preview
        [namaMenu, kategori, harga, deskripsi, status, stok].forEach(element => {
            element.addEventListener('input', updatePreview);
            element.addEventListener('change', updatePreview);
        });

        // Image preview
        gambarInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    gambarInput.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    // Show preview in form
                    document.getElementById('imagePreview').style.display = 'block';
                    document.getElementById('previewImg').src = e.target.result;

                    // Update card preview
                    previewCardImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Form submission
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Validate required fields
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="ti ti-loader rotate"></i> Menyimpan...';
            submitBtn.disabled = true;

            // Submit form (replace with actual backend call)
            // form.submit();

            // For demo: show success and return to list
            setTimeout(() => {
                alert('Menu berhasil ditambahkan!');
                showDataMenu();
            }, 1500);
        });

        // Initialize preview
        updatePreview();
    }

    // Helper function to remove image preview
    function removeImagePreview() {
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('gambarMenu').value = '';
        document.getElementById('previewCardImg').src = 'https://via.placeholder.com/400x250/e9ecef/6c757d?text=No+Image';
    }

    // CSS for rotating loader
    const style = document.createElement('style');
    style.textContent = `
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .rotate {
        animation: rotate 1s linear infinite;
    }
    .sticky-top {
        position: -webkit-sticky;
        position: sticky;
    }
`;
    document.head.appendChild(style);
</script>