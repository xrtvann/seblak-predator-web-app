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
                                            <img src="dist/assets/images/menu/spice-mild.png"
                                                alt="Seblak Level 2" class="img-thumbnail"
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
                                            <img src="dist/assets/images/menu/spice-spicy.png"
                                                alt="Seblak Level 3" class="img-thumbnail"
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
                                            <img src="dist/assets/images/menu/spice-hot.png"
                                                alt="Seblak Level 4" class="img-thumbnail"
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
                                            <img src="dist/assets/images/menu/spice-extra.png"
                                                alt="Seblak Level 5" class="img-thumbnail"
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
                                        <img src="https://images.unsplash.com/photo-1586190848861-99aa4a171e90?w=400&h=250&fit=crop&crop=center"
                                            class="card-img-top" alt="Seblak Level 2"
                                            style="height: 200px; object-fit: cover;">
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
                                        <img src="https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=400&h=250&fit=crop&crop=center"
                                            class="card-img-top" alt="Seblak Level 3"
                                            style="height: 200px; object-fit: cover;">
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
                                        <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=400&h=250&fit=crop&crop=center"
                                            class="card-img-top" alt="Seblak Level 4"
                                            style="height: 200px; object-fit: cover;">
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
                                        <img src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=250&fit=crop&crop=center"
                                            class="card-img-top" alt="Seblak Level 5"
                                            style="height: 200px; object-fit: cover;">
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
                <style>
                    .menu-card {
                        transition: transform 0.3s ease-in-out;
                        border: 1px solid rgba(0, 0, 0, .125);
                    }

                    .menu-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 10px 25px rgba(0, 0, 0, .15);
                    }

                    .nav-pills .nav-link {
                        border-radius: 5px;
                        margin-right: 5px;
                    }

                    .nav-pills .nav-link.active {
                        background-color: #4680ff;
                        color: white;
                    }

                    .nav-pills .nav-link:not(.active) {
                        background-color: #f8f9fa;
                        color: #6c757d;
                        border: 1px solid #dee2e6;
                    }

                    .nav-pills .nav-link:not(.active):hover {
                        background-color: #e9ecef;
                        color: #495057;
                    }

                    /* Equal height cards using Bootstrap utility classes */
                    .row.g-3 {
                        --bs-gutter-x: 1rem;
                        --bs-gutter-y: 1rem;
                    }
                </style>
            </div>
        </div>
    </div>
    <!-- [ Data Menu ] end -->
</div>
<!-- [ Main Content ] end -->