<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-header-title">
                    <h5 class="m-b-10">Menu</h5>
                </div>
            </div>
            <div class="col-auto">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0)">Produk</a></li>
                    <li class="breadcrumb-item" aria-current="page">Menu</li>
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
                        <h5>Data Menu</h5>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex align-items-center">
                            <!-- View Toggle Tabs -->
                            <ul class="nav nav-pills me-3" id="pills-tab" role="tablist">
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

                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahMenu">
                                <i class="ti ti-plus"></i>
                                Tambah Menu
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
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
                            <!-- Menu Card 1 - Seblak Level 1 -->
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="card menu-card h-100">
                                    <div class="position-relative">
                                        <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=250&fit=crop&crop=center"
                                            class="card-img-top" alt="Seblak Level 1"
                                            style="height: 200px; object-fit: cover;">
                                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Aktif</span>
                                        <span
                                            class="badge bg-primary position-absolute top-0 start-0 m-2">Makanan</span>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">Seblak Level 1 (Original)</h5>
                                        <p class="card-text text-muted small">Seblak dengan rasa original, cocok untuk
                                            pemula</p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <h4 class="text-primary mb-0">Rp 10.000</h4>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                    title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    title="Hapus">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Card 2 - Seblak Level 2 -->
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="card menu-card h-100">
                                    <div class="position-relative">
                                        <img src="dist/assets/images/menu/spice-mild.png" class="card-img-top"
                                            alt="Seblak Level 2" style="height: 200px; object-fit: cover;">
                                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Aktif</span>
                                        <span
                                            class="badge bg-primary position-absolute top-0 start-0 m-2">Makanan</span>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">Seblak Level 2 (Sedang)</h5>
                                        <p class="card-text text-muted small">Seblak dengan tingkat pedas sedang</p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <h4 class="text-primary mb-0">Rp 12.000</h4>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                    title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    title="Hapus">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Card 3 - Seblak Level 3 -->
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="card menu-card h-100">
                                    <div class="position-relative">
                                        <img src="dist/assets/images/menu/spice-spicy.png" class="card-img-top"
                                            alt="Seblak Level 3" style="height: 200px; object-fit: cover;">
                                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Aktif</span>
                                        <span
                                            class="badge bg-primary position-absolute top-0 start-0 m-2">Makanan</span>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">Seblak Level 3 (Pedas)</h5>
                                        <p class="card-text text-muted small">Seblak pedas untuk pecinta rasa pedas</p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <h4 class="text-primary mb-0">Rp 14.000</h4>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                    title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    title="Hapus">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Card 4 - Seblak Level 4 -->
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="card menu-card h-100">
                                    <div class="position-relative">
                                        <img src="dist/assets/images/menu/spice-hot.png" class="card-img-top"
                                            alt="Seblak Level 4" style="height: 200px; object-fit: cover;">
                                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Aktif</span>
                                        <span
                                            class="badge bg-primary position-absolute top-0 start-0 m-2">Makanan</span>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">Seblak Level 4 (Extra Pedas)</h5>
                                        <p class="card-text text-muted small">Seblak extra pedas untuk yang berani
                                            tantangan</p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <h4 class="text-primary mb-0">Rp 16.000</h4>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                    title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    title="Hapus">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Card 5 - Seblak Level 5 -->
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="card menu-card h-100">
                                    <div class="position-relative">
                                        <img src="dist/assets/images/menu/spice-extra.png" class="card-img-top"
                                            alt="Seblak Level 5" style="height: 200px; object-fit: cover;">
                                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Aktif</span>
                                        <span
                                            class="badge bg-primary position-absolute top-0 start-0 m-2">Makanan</span>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">Seblak Level 5 (Predator)</h5>
                                        <p class="card-text text-muted small">🔥 Level tertinggi! Hanya untuk predator
                                            sejati</p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <h4 class="text-primary mb-0">Rp 18.000</h4>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                    title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    title="Hapus">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Card 6 - Es Teh Manis -->
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="card menu-card h-100">
                                    <div class="position-relative">
                                        <img src="https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400&h=250&fit=crop&crop=center"
                                            class="card-img-top" alt="Es Teh Manis"
                                            style="height: 200px; object-fit: cover;">
                                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Aktif</span>
                                        <span class="badge bg-info position-absolute top-0 start-0 m-2">Minuman</span>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">Es Teh Manis</h5>
                                        <p class="card-text text-muted small">Minuman segar untuk meredakan pedas</p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <h4 class="text-primary mb-0">Rp 5.000</h4>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                    title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    title="Hapus">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Card 7 - Es Jeruk -->
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="card menu-card h-100">
                                    <div class="position-relative">
                                        <img src="https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=400&h=250&fit=crop&crop=center"
                                            class="card-img-top" alt="Es Jeruk"
                                            style="height: 200px; object-fit: cover;">
                                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Aktif</span>
                                        <span class="badge bg-info position-absolute top-0 start-0 m-2">Minuman</span>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">Es Jeruk</h5>
                                        <p class="card-text text-muted small">Jeruk segar dengan rasa asam manis</p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <h4 class="text-primary mb-0">Rp 6.000</h4>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                    title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    title="Hapus">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Card 8 - Teh Hangat -->
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="card menu-card h-100">
                                    <div class="position-relative">
                                        <img src="https://images.unsplash.com/photo-1497534547324-0ebb3f54ca68?w=400&h=250&fit=crop&crop=center"
                                            class="card-img-top" alt="Teh Hangat"
                                            style="height: 200px; object-fit: cover;">
                                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Aktif</span>
                                        <span class="badge bg-info position-absolute top-0 start-0 m-2">Minuman</span>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">Teh Hangat</h5>
                                        <p class="card-text text-muted small">Teh hangat untuk menghangatkan badan</p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <h4 class="text-primary mb-0">Rp 4.000</h4>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                    title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    title="Hapus">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Card 9 - Es Lemon -->
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="card menu-card h-100">
                                    <div class="position-relative">
                                        <img src="https://images.unsplash.com/photo-1553830591-d8632a99e6ff?w=400&h=250&fit=crop&crop=center"
                                            class="card-img-top" alt="Es Lemon"
                                            style="height: 200px; object-fit: cover;">
                                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Aktif</span>
                                        <span class="badge bg-info position-absolute top-0 start-0 m-2">Minuman</span>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">Es Lemon</h5>
                                        <p class="card-text text-muted small">Lemon segar dengan rasa asam yang
                                            menyegarkan</p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <h4 class="text-primary mb-0">Rp 7.000</h4>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                    title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    title="Hapus">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom CSS for Equal Card Heights -->

            </div>
        </div>
    </div>
    <!-- [ Data Menu ] end -->
</div>
<!-- [ Main Content ] end -->

<!-- Modal Tambah Menu -->
<div class="modal fade" id="modalTambahMenu" tabindex="-1" aria-labelledby="modalTambahMenuLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahMenuLabel">
                    <i class="ti ti-plus text-primary"></i>
                    Tambah Menu Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahMenu" novalidate>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row">
                        <!-- Nama Menu -->
                        <div class="col-12 mb-3">
                            <label for="namaMenu" class="form-label">
                                <i class="ti ti-soup text-primary"></i>
                                Nama Menu <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="namaMenu" name="namaMenu" required
                                placeholder="Contoh: Seblak Pedas, Es Teh Manis, dll">
                            <div class="invalid-feedback">
                                Nama menu harus diisi
                            </div>
                            <div class="form-text">Masukkan nama menu yang jelas dan menarik</div>
                        </div>

                        <!-- Kategori -->
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
                            <div class="invalid-feedback">
                                Kategori harus dipilih
                            </div>
                        </div>

                        <!-- Harga -->
                        <div class="col-md-6 mb-3">
                            <label for="harga" class="form-label">
                                <i class="ti ti-currency-rupiah text-success"></i>
                                Harga <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="harga" name="harga" required
                                    placeholder="15000" min="1000" step="500">
                                <div class="invalid-feedback">
                                    Harga harus diisi dengan minimal Rp 1.000
                                </div>
                            </div>
                            <div class="form-text">Masukkan harga tanpa titik atau koma</div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="col-12 mb-3">
                            <label for="deskripsi" class="form-label">
                                <i class="ti ti-file-description text-secondary"></i>
                                Deskripsi Menu
                            </label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"
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
                                    style="max-width: 200px; max-height: 150px;">
                                <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeImagePreview()">
                                    <i class="ti ti-x"></i> Hapus
                                </button>
                            </div>
                        </div>

                        <!-- Status -->
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

                        <!-- Stok (opsional) -->
                        <div class="col-md-6 mb-3">
                            <label for="stok" class="form-label">
                                <i class="ti ti-package text-info"></i>
                                Stok Tersedia
                            </label>
                            <input type="number" class="form-control" id="stok" name="stok" placeholder="100" min="0"
                                value="999">
                            <div class="form-text">Kosongkan jika stok tidak terbatas</div>
                        </div>
                    </div>

                    <!-- Preview Card -->
                    <div class="col-12 mt-4">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-primary mb-3">
                                <i class="ti ti-eye"></i>
                                Preview Menu
                            </h6>
                            <div id="menuPreview" class="row">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="position-relative">
                                            <img id="previewCardImg"
                                                src="https://via.placeholder.com/400x250/e9ecef/6c757d?text=No+Image"
                                                class="card-img-top" alt="Preview"
                                                style="height: 150px; object-fit: cover;">
                                            <span id="previewStatus"
                                                class="badge bg-success position-absolute top-0 end-0 m-2">Aktif</span>
                                            <span id="previewKategori"
                                                class="badge bg-primary position-absolute top-0 start-0 m-2">Kategori</span>
                                        </div>
                                        <div class="card-body">
                                            <h6 id="previewNama" class="card-title">Nama Menu</h6>
                                            <p id="previewDeskripsi" class="card-text text-muted small">Deskripsi
                                                menu...</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 id="previewHarga" class="text-success mb-0">Rp 0</h5>
                                                <span id="previewLevel" class="badge bg-danger"
                                                    style="display: none;">Level 1</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer pe-3 py-3">
                    <button type="button" class="btn btn-danger d-flex align-items-center" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn btn-success d-flex align-items-center">
                        <i class="ti ti-device-floppy me-1"></i>
                        Simpan Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript untuk Modal Tambah Menu -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Form elements
        const form = document.getElementById('formTambahMenu');
        const namaMenu = document.getElementById('namaMenu');
        const kategori = document.getElementById('kategori');
        const harga = document.getElementById('harga');
        const deskripsi = document.getElementById('deskripsi');
        const status = document.getElementById('status');
        const gambarInput = document.getElementById('gambarMenu');

        // Preview elements
        const previewNama = document.getElementById('previewNama');
        const previewKategori = document.getElementById('previewKategori');
        const previewHarga = document.getElementById('previewHarga');
        const previewDeskripsi = document.getElementById('previewDeskripsi');
        const previewLevel = document.getElementById('previewLevel');
        const previewStatus = document.getElementById('previewStatus');
        const previewCardImg = document.getElementById('previewCardImg');

        // Update preview in real-time
        function updatePreview() {
            // Nama menu
            previewNama.textContent = namaMenu.value || 'Nama Menu';

            // Kategori
            const katText = kategori.options[kategori.selectedIndex].text;
            previewKategori.textContent = katText !== 'Pilih Kategori' ? katText : 'Kategori';
            previewKategori.className = `badge position-absolute top-0 start-0 m-2 ${kategori.value === 'makanan' ? 'bg-primary' : kategori.value === 'minuman' ? 'bg-info' : 'bg-secondary'}`;

            // Harga
            const hargaVal = parseInt(harga.value) || 0;
            previewHarga.textContent = 'Rp ' + hargaVal.toLocaleString('id-ID');

            // Deskripsi
            previewDeskripsi.textContent = deskripsi.value || 'Deskripsi menu...';

            // Hide level badge since we removed level pedas
            previewLevel.style.display = 'none';

            // Status
            previewStatus.textContent = status.value === 'aktif' ? 'Aktif' : 'Nonaktif';
            previewStatus.className = `badge position-absolute top-0 end-0 m-2 ${status.value === 'aktif' ? 'bg-success' : 'bg-secondary'}`;
        }

        // Event listeners for real-time preview
        [namaMenu, kategori, harga, deskripsi, status].forEach(element => {
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

            // Simulate save process (replace with actual AJAX call)
            setTimeout(function () {
                // Reset form
                form.reset();
                form.classList.remove('was-validated');
                updatePreview();
                removeImagePreview();

                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('modalTambahMenu')).hide();

                // Show success message
                showSuccessMessage('Menu berhasil ditambahkan!');

                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        });

        // Initialize preview on modal show
        document.getElementById('modalTambahMenu').addEventListener('shown.bs.modal', function () {
            updatePreview();
        });
    });

    // Helper functions
    function removeImagePreview() {
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('gambarMenu').value = '';
        document.getElementById('previewCardImg').src = 'https://via.placeholder.com/400x250/e9ecef/6c757d?text=No+Image';
    }

    function showSuccessMessage(message) {
        // Create and show success alert
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
        <i class="ti ti-check-circle"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
        document.body.appendChild(alertDiv);

        // Auto remove after 3 seconds
        setTimeout(function () {
            alertDiv.remove();
        }, 3000);
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
`;
    document.head.appendChild(style);
</script>