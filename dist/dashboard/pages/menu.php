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
                        <p class="text-muted mb-0 d-none" id="cardSubtitleText">Isi form di bawah untuk menambahkan menu
                            baru</p>
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

                            <!-- Action Buttons -->
                            <button type="button" class="btn btn-primary" id="btnTambahMenu" onclick="showFormTambah()">
                                <i class="ti ti-plus"></i> Tambah Menu
                            </button>
                            <button type="button" class="btn btn-secondary d-none" id="btnKembali"
                                onclick="showDataMenu()">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Main Content Area -->
                <div id="mainContentArea">
                    <!-- Content will be loaded here dynamically -->
                </div>
            </div>
        </div>
    </div>
    <!-- [ Data Menu ] end -->
</div>
<!-- [ Main Content ] end -->

<!-- Success/Error Notification -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
    <div id="notification" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto" id="notificationTitle">Notification</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="notificationBody">
            <!-- Notification message -->
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus menu "<span id="deleteItemName"></span>"?</p>
                <p class="text-muted">Data yang sudah dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript untuk CRUD Menu -->
<script>
    let currentEditId = null;
    let categories = [];
    let allMenuData = []; // Store all menu data
    let filteredMenuData = []; // Store filtered data
    let currentPage = 1;
    const itemsPerPage = 10;
    let activeFilters = new Map(); // Modern filter storage
    let currentSort = 'created_at_desc';

    // Filter Dropdown Management
    function toggleFilterDropdown() {
        const dropdown = document.getElementById('filterDropdown');
        const button = document.getElementById('filterButton');

        if (dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
            button.classList.remove('active');
        } else {
            dropdown.classList.add('show');
            button.classList.add('active');
        }
    }

    // Handle filter checkbox changes
    function handleFilterChange(checkbox) {
        const filterType = checkbox.dataset.filter;
        const filterValue = checkbox.dataset.value;
        const filterKey = `${filterType}:${filterValue}`;

        if (checkbox.checked) {
            // Add filter
            const filterLabel = checkbox.parentElement.querySelector('.filter-label').textContent;
            activeFilters.set(filterKey, {
                type: filterType,
                value: filterValue,
                label: filterLabel,
                element: checkbox
            });
        } else {
            // Remove filter
            activeFilters.delete(filterKey);
        }

        updateFilterBadge();
        updateActiveFiltersDisplay();
    }

    // Update filter badge count
    function updateFilterBadge() {
        const badge = document.getElementById('filterBadge');
        const count = activeFilters.size;

        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    }

    // Update active filters display
    function updateActiveFiltersDisplay() {
        const container = document.getElementById('activeFiltersContainer');
        const tagsContainer = document.getElementById('activeFiltersTags');

        if (activeFilters.size === 0) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'block';
        tagsContainer.innerHTML = '';

        activeFilters.forEach((filter, key) => {
            const tag = document.createElement('div');
            tag.className = 'active-filter-tag';
            tag.innerHTML = `
                <span>${filter.label}</span>
                <button class="remove-filter" onclick="removeFilter('${key}')">
                    ×
                </button>
            `;
            tagsContainer.appendChild(tag);
        });
    }

    // Remove specific filter
    function removeFilter(filterKey) {
        const filter = activeFilters.get(filterKey);
        if (filter && filter.element) {
            filter.element.checked = false;
        }
        activeFilters.delete(filterKey);
        updateFilterBadge();
        updateActiveFiltersDisplay();
        applyFilters();
    }

    // Clear all filters
    function clearAllFilters() {
        // Clear all checkboxes
        document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });

        // Clear search
        document.getElementById('searchInput').value = '';

        // Reset sort
        document.getElementById('sortBy').value = 'created_at_desc';
        currentSort = 'created_at_desc';

        // Clear filters
        activeFilters.clear();
        updateFilterBadge();
        updateActiveFiltersDisplay();
        applyFilters();
    }

    // Apply filters and close dropdown
    function applyFiltersAndClose() {
        applyFilters();
        toggleFilterDropdown();
    }

    // Populate category filter options
    function populateCategoryFilterOptions() {
        const container = document.getElementById('categoryFilterOptions');

        if (container && categories.length > 0) {
            container.innerHTML = '';

            categories.forEach(category => {
                const option = document.createElement('label');
                option.className = 'filter-option';
                option.innerHTML = `
                    <input type="checkbox" class="filter-checkbox" data-filter="category" data-value="${category.id}" onchange="handleFilterChange(this)">
                    <span class="filter-icon">📂</span>
                    <span class="filter-label">${category.name}</span>
                `;
                container.appendChild(option);
            });
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        const dropdown = document.getElementById('filterDropdown');
        const button = document.getElementById('filterButton');

        if (dropdown && dropdown.classList.contains('show')) {
            if (!dropdown.contains(e.target) && !button.contains(e.target)) {
                toggleFilterDropdown();
            }
        }
    });

    // Show form for adding new menu
    function showFormTambah() {
        currentEditId = null;
        showForm('Tambah Menu', 'Form Tambah Menu');
    }


    async function editMenu(id) {
        try {
            const response = await fetch(`api/menu/products.php?id=${id}`);
            const result = await response.json();

            if (result.success) {
                currentEditId = id;
                showForm('Edit Menu', 'Form Edit Menu', result.data);
            } else {
                showNotification('Error loading menu data: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error connecting to server', 'error');
        }
    }

    // Reset form
    function resetForm() {
        document.getElementById('menuForm').reset();
        document.getElementById('isActive').checked = true;
        updatePreview();
    }

    // Update preview from form inputs
    function updatePreview() {
        const name = document.getElementById('menuName').value;
        const description = document.getElementById('menuDescription').value;
        const price = document.getElementById('menuPrice').value;
        const categorySelect = document.getElementById('menuCategory');
        const isTopping = document.getElementById('isTopping').checked;
        const isActive = document.getElementById('isActive').checked;

        // Update preview elements
        document.getElementById('previewName').textContent = name || 'Nama Menu';
        document.getElementById('previewDescription').textContent = description || 'Deskripsi menu akan tampil di sini...';
        document.getElementById('previewPrice').textContent = 'Rp ' + formatPrice(price || 0);

        // Update category
        const selectedCategory = categorySelect.options[categorySelect.selectedIndex];
        document.getElementById('previewCategory').textContent = selectedCategory ? selectedCategory.text : 'Kategori';

        // Update topping badge
        const toppingBadge = document.getElementById('previewTopping');
        if (isTopping) {
            toppingBadge.classList.remove('d-none');
        } else {
            toppingBadge.classList.add('d-none');
        }

        // Update status badge
        const statusBadge = document.getElementById('previewStatus');
        statusBadge.textContent = isActive ? 'Active' : 'Inactive';
        statusBadge.className = `badge bg-${isActive ? 'success' : 'danger'}`;
    }

    // Update image preview
    function updateImagePreview() {
        const imageUrl = document.getElementById('menuImage').value;
        const previewImage = document.getElementById('previewImage');

        if (imageUrl && isValidUrl(imageUrl)) {
            previewImage.src = imageUrl;
            previewImage.onerror = function () {
                this.src = 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=250&fit=crop';
            };
        } else {
            previewImage.src = 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=250&fit=crop';
        }
        updatePreview();
    }

    // Format price preview
    function formatPricePreview() {
        updatePreview();
    }

    // Check if URL is valid
    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', function () {
        showDataMenu();
        loadCategories();

        // Add event listeners for live preview updates
        document.addEventListener('input', function (e) {
            if (e.target.matches('#menuName, #menuDescription, #menuPrice, #menuCategory')) {
                updatePreview();
            }
        });

        document.addEventListener('change', function (e) {
            if (e.target.matches('#isTopping, #isActive, #menuCategory')) {
                updatePreview();
            }
        });
    });

    // Load categories from API
    async function loadCategories() {
        try {
            const response = await fetch('api/menu/categories.php');
            const result = await response.json();

            if (result.success) {
                categories = result.data;
                populateCategorySelect();
            } else {
                console.error('Failed to load categories:', result.message);
            }
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }

    // Load menu data from API
    async function loadMenuData() {
        try {
            const response = await fetch('api/menu/products.php');
            const result = await response.json();

            if (result.success) {
                displayMenuData(result.data);
            } else {
                console.error('Failed to load menu data:', result.message);
                showNotification('Error loading menu data: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error connecting to server', 'error');
        }
    }

    // Show data menu view
    function showDataMenu() {
        console.log('showDataMenu called');
        const mainContent = document.getElementById('mainContentArea');
        console.log('Main content element:', mainContent);

        // Update header
        document.getElementById('pageTitleText').textContent = 'Menu';
        document.getElementById('breadcrumbText').textContent = 'Menu';
        document.getElementById('cardTitleText').textContent = 'Data Menu';
        document.getElementById('cardSubtitleText').classList.add('d-none');

        // Toggle buttons
        document.getElementById('btnTambahMenu').classList.remove('d-none');
        document.getElementById('btnKembali').classList.add('d-none');
        document.getElementById('viewToggleTabs').classList.remove('d-none');

        // Set content
        mainContent.innerHTML = getDataMenuHTML();

        // Populate category filter options
        populateCategoryFilterOptions();

        // Reload data
        loadMenuData();
    }
    function populateFilterOptions() {
        const categoryFilter = document.getElementById('categoryFilter');
        if (categoryFilter && categories.length > 0) {
            categoryFilter.innerHTML = '<option value="">Semua Kategori</option>';
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categoryFilter.appendChild(option);
            });
        }
    }

    // Apply filters with modern system
    function applyFilters() {
        // Start with all data
        filteredMenuData = [...allMenuData];

        // Get search term
        const searchInput = document.getElementById('searchInput');
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

        // Apply active filters
        activeFilters.forEach(filter => {
            if (filter.type === 'status') {
                filteredMenuData = filteredMenuData.filter(item => {
                    return filter.value === 'active' ? item.is_active : !item.is_active;
                });
            } else if (filter.type === 'category') {
                filteredMenuData = filteredMenuData.filter(item => {
                    return item.category_id === filter.value;
                });
            }
        });

        // Apply search filter
        if (searchTerm) {
            filteredMenuData = filteredMenuData.filter(item =>
                item.name.toLowerCase().includes(searchTerm) ||
                (item.description && item.description.toLowerCase().includes(searchTerm)) ||
                item.category_name.toLowerCase().includes(searchTerm)
            );
        }

        // Apply sorting
        filteredMenuData.sort((a, b) => {
            switch (currentSort) {
                case 'name_asc':
                    return a.name.localeCompare(b.name);
                case 'name_desc':
                    return b.name.localeCompare(a.name);
                case 'price_asc':
                    return parseFloat(a.price) - parseFloat(b.price);
                case 'price_desc':
                    return parseFloat(b.price) - parseFloat(a.price);
                case 'created_at_asc':
                    return new Date(a.created_at) - new Date(b.created_at);
                case 'created_at_desc':
                default:
                    return new Date(b.created_at) - new Date(a.created_at);
            }
        });

        // Update display
        currentPage = 1;
        displayTableView();
        updatePagination();
        updateFilterInfo();
    }
    function applySorting(triggerFilters = true) {
        const sortBy = document.getElementById('sortBy');
        currentSort = sortBy ? sortBy.value : 'created_at_desc';

        if (triggerFilters) {
            applyFilters();
        }
    }



    // Update filter info
    function updateFilterInfo() {
        const filterInfo = document.getElementById('filterInfo');
        const totalFiltered = filteredMenuData.length;
        const totalAll = allMenuData.length;
    }


    // Show form tambah menu
    function showFormTambah() {
        currentEditId = null;
        showForm('Tambah Menu', 'Form Tambah Menu');
    }

    // Show form edit menu
    async function editMenu(id) {
        currentEditId = id;

        try {
            const response = await fetch(`/seblak-predator/api/menu/products.php?id=${id}`);
            const result = await response.json();

            if (result.success) {
                showForm('Edit Menu', 'Form Edit Menu', result.data);
            } else {
                showNotification('Error loading menu data: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error connecting to server', 'error');
        }
    }

    // Show form (for both add and edit)
    function showForm(pageTitle, cardTitle, data = null) {
        const mainContent = document.getElementById('mainContentArea');

        // Update header
        document.getElementById('pageTitleText').textContent = pageTitle;
        document.getElementById('breadcrumbText').textContent = pageTitle;
        document.getElementById('cardTitleText').textContent = cardTitle;
        document.getElementById('cardSubtitleText').classList.remove('d-none');

        // Toggle buttons
        document.getElementById('btnTambahMenu').classList.add('d-none');
        document.getElementById('btnKembali').classList.remove('d-none');
        document.getElementById('viewToggleTabs').classList.add('d-none');

        // Set content
        mainContent.innerHTML = getFormHTML();

        // Populate form if editing
        if (data) {
            populateForm(data);
        }

        // Initialize form
        initForm();
    }

    // Get data menu HTML
    function getDataMenuHTML() {
        return `
            <!-- Tab Content -->
            <div class="tab-content" id="pills-tabContent">
                <!-- Table View -->
                <div class="tab-pane fade show active" id="pills-table" role="tabpanel"
                    aria-labelledby="pills-table-tab" tabindex="0">
                    
                    <!-- Controls Section Outside Table -->
                    <div class="table-controls-section table-light p-3 mb-0 border rounded-top">
                        <div class="table-header-controls">
                            <!-- Left side: Search -->
                            <div class="search-section">
                                <div class="search-input-wrapper">
                                    <i class="ti ti-search search-icon"></i>
                                    <input type="text" class="form-control search-input" id="searchInput" 
                                           placeholder="Search products..." onkeyup="applyFilters()">
                                </div>
                            </div>
                            
                            <!-- Center: Filter Button -->
                            <div class="filter-section">
                                <div class="filter-dropdown position-relative">
                                    <button type="button" class="btn filter-btn" id="filterButton" onclick="toggleFilterDropdown()">
                                        <i class="ti ti-filter"></i>
                                        <span class="filter-text">Filters</span>
                                        <span class="filter-badge" id="filterBadge" style="display: none;">0</span>
                                    </button>
                                    
                                    <!-- Filter Dropdown -->
                                    <div class="filter-dropdown-menu" id="filterDropdown">
                                        <div class="filter-dropdown-header">
                                            <h6 class="mb-0">Filter Options</h6>
                                            <button type="button" class="btn-close-filter" onclick="toggleFilterDropdown()">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        </div>
                                        
                                        <div class="filter-dropdown-body">
                                            <!-- Status Filters -->
                                            <div class="filter-group">
                                                <label class="filter-group-label">Status</label>
                                                <div class="filter-options">
                                                    <label class="filter-option">
                                                        <input type="checkbox" class="filter-checkbox" data-filter="status" data-value="active" onchange="handleFilterChange(this)">
                                                        <span class="filter-icon">🟢</span>
                                                        <span class="filter-label">Active</span>
                                                    </label>
                                                    <label class="filter-option">
                                                        <input type="checkbox" class="filter-checkbox" data-filter="status" data-value="inactive" onchange="handleFilterChange(this)">
                                                        <span class="filter-icon">🔴</span>
                                                        <span class="filter-label">Inactive</span>
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <!-- Category Filters -->
                                            <div class="filter-group">
                                                <label class="filter-group-label">Categories</label>
                                                <div class="filter-options" id="categoryFilterOptions">
                                                    <!-- Category options will be populated dynamically -->
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="filter-dropdown-footer">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearAllFilters()">
                                                Clear All
                                            </button>
                                            <button type="button" class="btn btn-primary btn-sm" onclick="applyFiltersAndClose()">
                                                Apply
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right side: Sort -->
                            <div class="sort-section">
                                <div class="sort-wrapper">
                                    <i class="ti ti-sort-descending sort-icon"></i>
                                    <select class="form-select sort-select" id="sortBy" onchange="applySorting()">
                                        <option value="created_at_desc">Terbaru</option>
                                        <option value="updated_at_desc">Diperbarui</option>
                                        <option value="name_asc">A-Z</option>
                                        <option value="name_desc">Z-A</option>
                                        <option value="price_asc">Harga Terendah</option>
                                        <option value="price_desc">Harga Tertinggi</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Active Filters Row -->
                        <div class="active-filters-container" id="activeFiltersContainer" style="display: none;">
                            <div class="active-filters-wrapper">
                                <span class="active-filters-label">🏷️ Active:</span>
                                <div class="active-filters-tags" id="activeFiltersTags">
                                    <!-- Active filter tags will appear here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Table with sticky header -->  
                    <div class="table-container">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover mb-0">
                                <thead class="table-light table-header-sticky">
                                    <tr class="column-headers">
                                        <th style="min-width: 50px;">#</th>
                                        <th style="min-width: 100px;">Gambar</th>
                                        <th style="min-width: 200px;">Nama Menu</th>
                                        <th style="min-width: 120px;">Kategori</th>
                                        <th style="min-width: 120px;">Harga</th>
                                        <th style="min-width: 100px;">Status</th>
                                        <th style="min-width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="menuTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            Loading menu data...
                                        </td>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="d-flex align-items-center">
                            <small class="text-muted" id="paginationInfo">Showing 0 - 0 of 0 entries</small>
                        </div>
                        <nav aria-label="Menu pagination">
                            <ul class="pagination pagination-sm mb-0" id="paginationControls">
                                <!-- Pagination buttons will be generated here -->
                            </ul>
                        </nav>
                    </div>
                </div>

                <!-- Card View -->
                <div class="tab-pane fade" id="pills-card" role="tabpanel" aria-labelledby="pills-card-tab"
                    tabindex="0">
                    <div class="row" id="menuCardContainer">
                        <div class="col-12 text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading menu data...</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Get form HTML
    function getFormHTML() {
        return `
            <form id="menuForm" enctype="multipart/form-data">
                <div class="row">
                    <!-- Left Column - Form Fields -->
                    <div class="col-lg-8">
                        <!-- Basic Information Card -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="ti ti-info-circle me-2"></i>Informasi Dasar</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="menuName" class="form-label">Nama Menu <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="menuName" name="name" required
                                                   placeholder="Masukkan nama menu">
                                            <div class="form-text">Nama menu akan tampil di aplikasi</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="menuCategory" class="form-label">Kategori <span class="text-danger">*</span></label>
                                            <select class="form-select" id="menuCategory" name="category_id" required>
                                                <option value="">Pilih Kategori</option>
                                            </select>
                                            <div class="form-text">Pilih kategori yang sesuai</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="menuPrice" class="form-label">Harga <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ti ti-currency-rupiah"></i> Rp</span>
                                                <input type="number" class="form-control" id="menuPrice" name="price" min="0" required
                                                       placeholder="0" onkeyup="formatPricePreview()">
                                            </div>
                                            <div class="form-text">Harga dalam Rupiah</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="menuImage" class="form-label">URL Gambar</label>
                                            <input type="url" class="form-control" id="menuImage" name="image_url" 
                                                   placeholder="https://example.com/image.jpg"
                                                   onchange="updateImagePreview()" onkeyup="updateImagePreview()">
                                            <div class="form-text">URL gambar untuk preview menu</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="menuDescription" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="menuDescription" name="description" rows="4" 
                                              placeholder="Deskripsi menu yang menarik untuk pelanggan..."></textarea>
                                    <div class="form-text">Deskripsi yang menarik akan meningkatkan minat pelanggan</div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Settings Card -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="ti ti-settings me-2"></i>Pengaturan Tambahan</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="isTopping" name="is_topping">
                                            <label class="form-check-label" for="isTopping">
                                                <strong>Jadikan sebagai Topping</strong>
                                            </label>
                                            <div class="form-text">Centang jika menu ini dapat dijadikan topping untuk menu lain</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="isActive" name="is_active" checked>
                                            <label class="form-check-label" for="isActive">
                                                <strong>Menu Aktif</strong>
                                            </label>
                                            <div class="form-text">Menu aktif akan tampil di aplikasi pelanggan</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Preview -->
                    <div class="col-lg-4">
                        <div class="card sticky-top" style="top: 20px;">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="ti ti-eye me-2"></i>Preview Menu</h6>
                            </div>
                            <div class="card-body">
                                <!-- Menu Preview Card -->
                                <div class="card border" id="menuPreviewCard">
                                    <div class="position-relative">
                                        <img id="previewImage" 
                                             src="https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=250&fit=crop" 
                                             alt="Preview" class="card-img-top" 
                                             style="height: 180px; object-fit: cover;">
                                        <div class="position-absolute top-0 end-0 p-2">
                                            <span class="badge bg-success" id="previewStatus">Active</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title mb-1" id="previewName">Nama Menu</h6>
                                        <p class="card-text text-muted f-12 mb-2" id="previewDescription">Deskripsi menu akan tampil di sini...</p>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-light-primary text-primary" id="previewCategory">Kategori</span>
                                            <span class="badge bg-light-info text-info d-none" id="previewTopping">Topping</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0 text-success" id="previewPrice">Rp 0</h5>
                                            <small class="text-muted">Preview</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Tips -->
                                <div class="mt-3">
                                    <h6 class="mb-2"><i class="ti ti-bulb me-1"></i>Tips:</h6>
                                    <ul class="list-unstyled f-12 text-muted">
                                        <li><i class="ti ti-check text-success me-1"></i>Gunakan gambar berkualitas tinggi</li>
                                        <li><i class="ti ti-check text-success me-1"></i>Tulis deskripsi yang menarik</li>
                                        <li><i class="ti ti-check text-success me-1"></i>Pastikan harga sudah sesuai</li>
                                        <li><i class="ti ti-check text-success me-1"></i>Pilih kategori yang tepat</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-light" onclick="showDataMenu()">
                                        <i class="ti ti-arrow-left me-1"></i> Kembali ke Daftar
                                    </button>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                            <i class="ti ti-refresh me-1"></i> Reset Form
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="ti ti-check me-1"></i> <span id="submitText">Simpan Menu</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        `;
    }

    // Display menu data
    function displayMenuData(menuData) {
        allMenuData = menuData; // Store all data
        filteredMenuData = [...allMenuData]; // Initialize filtered data
        currentPage = 1; // Reset to first page
        applyFilters(); // Apply current filters and sorting
        displayCardView(menuData);
    }

    // Display table view with pagination
    function displayTableView() {
        const tableBody = document.getElementById('menuTableBody');
        if (!tableBody) return;

        tableBody.innerHTML = '';

        if (filteredMenuData.length === 0) {
            const message = allMenuData.length === 0 ? 'Tidak ada data menu' : 'Tidak ada data yang sesuai dengan filter';
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center">
                        <p class="mb-0">${message}</p>
                    </td>
                </tr>
            `;
            return;
        }

        // Calculate pagination using filtered data
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedData = filteredMenuData.slice(startIndex, endIndex);

        paginatedData.forEach((item, index) => {
            const actualIndex = startIndex + index + 1; // Global index
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${actualIndex}</td>
                <td>
                    <img src="${item.image_url || 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=100&h=60&fit=crop'}" 
                         alt="${item.name}" class="img-fluid" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                </td>
                <td>
                    <h6 class="mb-1">${item.name}</h6>
                    <p class="text-muted f-12 mb-0">${item.description || ''}</p>
                </td>
                <td>
                    <span class="badge bg-light-${item.category_type === 'product' ? 'primary' : 'info'} text-${item.category_type === 'product' ? 'primary' : 'info'}">
                        ${item.category_name}
                    </span>
                </td>
                <td>Rp ${formatPrice(item.price)}</td>
                <td>
                    <span class="badge bg-light-${item.is_active ? 'success' : 'danger'} text-${item.is_active ? 'success' : 'danger'}">
                        ${item.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="editMenu('${item.id}')" title="Edit">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteMenu('${item.id}', '${item.name}')" title="Delete">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    // Display card view
    function displayCardView(menuData) {
        const cardContainer = document.getElementById('menuCardContainer');
        if (!cardContainer) return;

        cardContainer.innerHTML = '';

        if (menuData.length === 0) {
            cardContainer.innerHTML = `
                <div class="col-12 text-center">
                    <p class="mb-0">Tidak ada data menu</p>
                </div>
            `;
            return;
        }

        menuData.forEach(item => {
            const cardCol = document.createElement('div');
            cardCol.className = 'col-xl-3 col-md-6 col-sm-12 mb-3';
            cardCol.innerHTML = `
                <div class="card h-100">
                    <img src="${item.image_url || 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=300&h=200&fit=crop'}" 
                         alt="${item.name}" class="card-img-top" style="height: 150px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title">${item.name}</h6>
                        <p class="card-text text-muted f-12 flex-grow-1">${item.description || 'No description'}</p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-light-${item.category_type === 'product' ? 'primary' : 'info'} text-${item.category_type === 'product' ? 'primary' : 'info'}">
                                ${item.category_name}
                            </span>
                            <span class="badge bg-light-${item.is_active ? 'success' : 'danger'} text-${item.is_active ? 'success' : 'danger'}">
                                ${item.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-success">Rp ${formatPrice(item.price)}</h5>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-info" onclick="editMenu('${item.id}')" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteMenu('${item.id}', '${item.name}')" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            cardContainer.appendChild(cardCol);
        });
    }

    // Populate category select
    function populateCategorySelect() {
        const select = document.getElementById('menuCategory');
        if (!select) return;

        // Clear existing options except first
        select.innerHTML = '<option value="">Pilih Kategori</option>';

        categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            select.appendChild(option);
        });
    }

    // Populate form for editing
    function populateForm(data) {
        document.getElementById('menuName').value = data.name || '';
        document.getElementById('menuCategory').value = data.category_id || '';
        document.getElementById('menuPrice').value = data.price || '';
        document.getElementById('menuImage').value = data.image_url || '';
        document.getElementById('menuDescription').value = data.description || '';
        document.getElementById('isTopping').checked = data.is_topping || false;
        document.getElementById('submitText').textContent = 'Update';
    }

    // Initialize form
    function initForm() {
        populateCategorySelect();

        const form = document.getElementById('menuForm');
        form.addEventListener('submit', handleFormSubmit);
    }

    // Handle form submit
    async function handleFormSubmit(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;

        // Show loading
        submitBtn.innerHTML = '<i class="ti ti-loader"></i> Processing...';
        submitBtn.disabled = true;

        try {
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            // Convert checkbox to boolean
            data.is_topping = formData.has('is_topping');

            // Convert price to number
            data.price = parseFloat(data.price);

            let url = 'api/menu/products.php';
            let method = 'POST';

            if (currentEditId) {
                url += '?id=' + currentEditId;
                method = 'PUT';
            }

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showNotification(result.message, 'success');
                showDataMenu(); // Return to data view
            } else {
                showNotification('Error: ' + result.message, 'error');
            }

        } catch (error) {
            console.error('Error:', error);
            showNotification('Error connecting to server', 'error');
        } finally {
            // Restore button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }

    // Delete menu
    function deleteMenu(id, name) {
        document.getElementById('deleteItemName').textContent = name;

        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();

        document.getElementById('confirmDeleteBtn').onclick = async () => {
            try {
                const response = await fetch(`api/menu/products.php?id=${id}`, {
                    method: 'DELETE'
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    loadMenuData(); // Reload data
                } else {
                    showNotification('Error: ' + result.message, 'error');
                }

                modal.hide();

            } catch (error) {
                console.error('Error:', error);
                showNotification('Error connecting to server', 'error');
                modal.hide();
            }
        };
    }

    // Utility functions
    function formatPrice(price) {
        return new Intl.NumberFormat('id-ID').format(price);
    }

    function showNotification(message, type = 'info') {
        const notification = document.getElementById('notification');
        const title = document.getElementById('notificationTitle');
        const body = document.getElementById('notificationBody');

        title.textContent = type === 'success' ? 'Success' : type === 'error' ? 'Error' : 'Notification';
        body.textContent = message;

        // Remove existing classes
        notification.className = 'toast';

        // Add appropriate class
        if (type === 'success') {
            notification.classList.add('bg-success', 'text-white');
        } else if (type === 'error') {
            notification.classList.add('bg-danger', 'text-white');
        }

        const toast = new bootstrap.Toast(notification);
        toast.show();
    }

    // Pagination functions
    function updatePagination() {
        const totalItems = filteredMenuData.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);

        // Update pagination info
        const startItem = totalItems > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0;
        const endItem = Math.min(currentPage * itemsPerPage, totalItems);
        document.getElementById('paginationInfo').textContent =
            `Showing ${startItem} - ${endItem} of ${totalItems} entries`;

        // Generate pagination controls
        generatePaginationControls(totalPages);
    }

    function generatePaginationControls(totalPages) {
        const paginationContainer = document.getElementById('paginationControls');
        if (!paginationContainer) return;

        paginationContainer.innerHTML = '';

        if (totalPages <= 1) return;

        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1})" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        `;
        paginationContainer.appendChild(prevLi);

        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) {
            const firstLi = document.createElement('li');
            firstLi.className = 'page-item';
            firstLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(1)">1</a>`;
            paginationContainer.appendChild(firstLi);

            if (startPage > 2) {
                const dotsLi = document.createElement('li');
                dotsLi.className = 'page-item disabled';
                dotsLi.innerHTML = `<span class="page-link">...</span>`;
                paginationContainer.appendChild(dotsLi);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === currentPage ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i})">${i}</a>`;
            paginationContainer.appendChild(li);
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const dotsLi = document.createElement('li');
                dotsLi.className = 'page-item disabled';
                dotsLi.innerHTML = `<span class="page-link">...</span>`;
                paginationContainer.appendChild(dotsLi);
            }

            const lastLi = document.createElement('li');
            lastLi.className = 'page-item';
            lastLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${totalPages})">${totalPages}</a>`;
            paginationContainer.appendChild(lastLi);
        }

        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1})" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        `;
        paginationContainer.appendChild(nextLi);
    }

    function changePage(page) {
        const totalPages = Math.ceil(filteredMenuData.length / itemsPerPage);

        if (page < 1 || page > totalPages || page === currentPage) {
            return;
        }

        currentPage = page;
        displayTableView();
        updatePagination();

        // Scroll to top of table
        document.getElementById('pills-table').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
</script>

<!-- Custom CSS for Scrollable Table -->
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
        /* Remove left and right borders */
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

    /* Table header controls layout */
    .table-header-controls {
        display: flex;
        align-items: center;
        justify-content: start;
        gap: 1rem;
        flex-wrap: wrap;
    }

    /* Search section */
    .search-section {
        flex: 1;
        min-width: 200px;
        max-width: 300px;
    }

    .search-input-wrapper {
        position: relative;
    }

    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 5;
    }

    .search-input {
        padding-left: 2.5rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        transition: all 0.15s ease-in-out;
    }

    .search-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Filter section */
    .filter-section {
        flex: 0 0 auto;
    }

    .filter-btn {
        background: white;
        border: 1px solid #dee2e6;
        color: #495057;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        position: relative;
        transition: all 0.15s ease-in-out;
    }

    .filter-btn:hover {
        background: #f8f9fa;
        border-color: #667eea;
        color: #667eea;
    }

    .filter-btn.active {
        background: #667eea;
        border-color: #667eea;
        color: white;
    }

    .filter-badge {
        background: #dc3545;
        color: white;
        border-radius: 50%;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        min-width: 1.5rem;
        text-align: center;
        margin-left: 0.25rem;
    }

    /* Filter dropdown */
    .filter-dropdown-menu {
        position: absolute;
        top: calc(100% + 0.5rem);
        left: 0;
        min-width: 300px;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        z-index: 1050;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }

    .filter-dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .filter-dropdown-header {
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .btn-close-filter {
        background: none;
        border: none;
        color: #6c757d;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.25rem;
        transition: all 0.15s ease;
    }

    .btn-close-filter:hover {
        background: #f8f9fa;
        color: #495057;
    }

    .filter-dropdown-body {
        padding: 1rem;
        max-height: 300px;
        overflow-y: auto;
    }

    .filter-group {
        margin-bottom: 1.5rem;
    }

    .filter-group:last-child {
        margin-bottom: 0;
    }

    .filter-group-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.75rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-options {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .filter-option {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.15s ease;
        margin-bottom: 0;
    }

    .filter-option:hover {
        background: #f8f9fa;
    }

    .filter-checkbox {
        margin: 0;
    }

    .filter-icon {
        font-size: 1rem;
        width: 20px;
        text-align: center;
    }

    .filter-label {
        flex: 1;
        font-size: 0.875rem;
        color: #495057;
        margin: 0;
    }

    .filter-dropdown-footer {
        padding: 1rem;
        border-top: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        gap: 0.5rem;
    }

    /* Sort section */
    .sort-section {
        flex: 0 0 auto;
        min-width: 150px;
    }

    .sort-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .sort-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 5;
        pointer-events: none;
    }

    .sort-select {
        padding-left: 2.5rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        background: white;
        font-size: 0.875rem;
        transition: all 0.15s ease-in-out;
    }

    .sort-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Active filters */
    .active-filters-container {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
    }

    .active-filters-wrapper {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .active-filters-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
    }

    .active-filters-tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .active-filter-tag {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #667eea;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.8rem;
        animation: filterTagIn 0.3s ease;
    }

    @keyframes filterTagIn {
        from {
            opacity: 0;
            transform: scale(0.8);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .remove-filter {
        background: none;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0;
        width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.15s ease;
    }

    .remove-filter:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .table-header-controls {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }

        .search-section,
        .sort-section {
            min-width: unset;
            max-width: unset;
        }

        .filter-section {
            align-self: center;
        }

        .filter-dropdown-menu {
            left: 50%;
            transform: translateX(-50%) translateY(-10px);
            width: calc(100vw - 2rem);
            max-width: 300px;
        }

        .filter-dropdown-menu.show {
            transform: translateX(-50%) translateY(0);
        }

        .active-filters-wrapper {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    /* Table styling improvements */
    .table-light {
        background-color: var(--bs-gray-100) !important;
    }

    .column-headers th {
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        padding: 1rem 0.75rem;
    }

    /* Enhanced table styling */
    .table td,
    .table th {
        vertical-align: middle;
        padding: 12px 8px;
    }

    /* Row hover effects */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
        transform: translateY(-1px);
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* Image styling in table */
    .table img {
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        transition: transform 0.2s ease;
    }

    .table img:hover {
        transform: scale(1.05);
    }

    /* Button group spacing */
    .btn-group .btn {
        margin: 0 1px;
    }

    /* Mobile responsive */
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

    /* Integrated Table Header Controls */
    .table-controls-row {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        border-bottom: 2px solid #dee2e6;
    }

    .table-controls-cell {
        padding: 15px 20px !important;
        border: none !important;
    }

    .table-header-controls {
        display: flex;
        align-items: center;
        justify-content: start;
        gap: 15px;
        flex-wrap: wrap;
    }

    .search-section {
        flex: 1;
        min-width: 250px;
        max-width: 400px;
    }

    .search-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-input {
        background: white;
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        padding: 8px 12px 8px 35px;
        font-size: 14px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        width: 100%;
    }

    .search-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .search-icon {
        position: absolute;
        left: 10px;
        color: #6c757d;
        font-size: 16px;
        z-index: 1;
    }

    /* Filter Section */
    .filter-section {
        flex-shrink: 0;
    }

    .filter-dropdown {
        position: relative;
    }

    .filter-btn {
        background: white;
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        padding: 8px 16px;
        color: #495057;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        position: relative;
        min-width: 90px;
        font-size: 14px;
    }

    .filter-btn:hover {
        border-color: #667eea;
        background: #f8f9fa;
        color: #667eea;
    }

    .filter-btn.active {
        background: #667eea;
        border-color: #667eea;
        color: white;
    }

    .filter-badge {
        background: #dc3545;
        color: white;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 5px;
        border-radius: 8px;
        position: absolute;
        top: -3px;
        right: -3px;
        min-width: 16px;
        text-align: center;
        line-height: 1;
    }

    /* Filter Dropdown */
    .filter-dropdown-menu {
        position: absolute;
        top: calc(100% + 5px);
        left: 0;
        background: white;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        min-width: 280px;
        z-index: 1050;
        display: none;
        animation: dropdownSlide 0.2s ease;
    }

    .filter-dropdown-menu.show {
        display: block;
    }

    @keyframes dropdownSlide {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .filter-dropdown-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
        border-radius: 8px 8px 0 0;
    }

    .filter-dropdown-header h6 {
        color: #495057;
        font-weight: 600;
        font-size: 14px;
        margin: 0;
    }

    .btn-close-filter {
        background: none;
        border: none;
        color: #6c757d;
        font-size: 14px;
        cursor: pointer;
        padding: 2px;
        border-radius: 3px;
        transition: all 0.2s ease;
    }

    .btn-close-filter:hover {
        background: #e9ecef;
        color: #495057;
    }

    .filter-dropdown-body {
        padding: 16px;
        max-height: 250px;
        overflow-y: auto;
    }

    .filter-group {
        margin-bottom: 16px;
    }

    .filter-group:last-child {
        margin-bottom: 0;
    }

    .filter-group-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-options {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-option {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 8px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin: 0;
        font-size: 13px;
    }

    .filter-option:hover {
        background: #f8f9fa;
    }

    .filter-checkbox {
        width: 14px;
        height: 14px;
        margin: 0;
        accent-color: #667eea;
    }

    .filter-icon {
        font-size: 12px;
    }

    .filter-label {
        font-size: 13px;
        color: #495057;
        font-weight: 500;
    }

    .filter-dropdown-footer {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        padding: 12px 16px;
        border-top: 1px solid #e9ecef;
        background: #f8f9fa;
        border-radius: 0 0 8px 8px;
    }

    .filter-dropdown-footer .btn {
        font-size: 12px;
        padding: 6px 12px;
    }

    /* Sort Section */
    .sort-section {
        flex-shrink: 0;
    }

    .sort-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .sort-icon {
        position: absolute;
        left: 8px;
        color: #6c757d;
        font-size: 14px;
        z-index: 1;
    }

    .sort-select {
        background: white;
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        padding: 8px 12px 8px 30px;
        font-size: 13px;
        min-width: 120px;
        transition: all 0.3s ease;
        appearance: none;
        background-image: url('data:image/svg+xml;utf8,<svg fill="%23666" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 12px;
    }

    .sort-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    /* Active Filters */
    .active-filters-container {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #e9ecef;
    }

    .active-filters-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .active-filters-label {
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        white-space: nowrap;
    }

    .active-filters-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }

    .active-filter-tag {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 4px;
        animation: tagFadeIn 0.2s ease;
    }

    .remove-filter {
        background: rgba(255, 255, 255, 0.3);
        border: none;
        color: white;
        border-radius: 50%;
        width: 12px;
        height: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .remove-filter:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    /* Results Info */
    .results-info {
        margin-top: 8px;
        text-align: center;
    }

    .results-info small {
        font-style: italic;
        color: #6c757d !important;
    }

    /* Column Headers */
    .column-headers {
        background: #f8f9fa !important;
        border-top: 1px solid #dee2e6;
    }

    .column-headers th {
        color: #495057 !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        padding: 12px 8px !important;
        border-bottom: 2px solid #dee2e6 !important;
    }

    @keyframes tagFadeIn {
        from {
            opacity: 0;
            transform: scale(0.8);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Mobile responsive for table header */
    @media (max-width: 768px) {
        .table-header-controls {
            flex-direction: column;
            gap: 10px;
        }

        .search-section,
        .filter-section,
        .sort-section {
            width: 100%;
        }

        .filter-dropdown-menu {
            left: -20px;
            min-width: 260px;
        }

        .active-filters-wrapper {
            justify-content: center;
        }
    }

    /* Pagination styling */
    .pagination-sm .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }

    .pagination .page-link {
        color: #6c757d;
        border: 1px solid #dee2e6;
        margin: 0 2px;
        transition: all 0.2s ease;
    }

    .pagination .page-link:hover {
        color: #0d6efd;
        background-color: #f8f9fa;
        border-color: #dee2e6;
        transform: translateY(-1px);
    }

    .pagination .page-item.disabled .page-link {
        color: #adb5bd;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }

    #paginationInfo {
        font-size: 0.875rem;
        color: #6c757d;
    }

    /* Pagination container responsive */
    @media (max-width: 576px) {
        .pagination-sm .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        #paginationInfo {
            font-size: 0.75rem;
        }
    }

    /* Filter and Sorting Controls Styling */
    .form-select-sm,
    .form-control-sm {
        border-radius: 6px;
        border: 1px solid #e3e6f0;
        transition: all 0.2s ease;
    }

    .form-select-sm:focus,
    .form-control-sm:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    /* Search input icon positioning */
    .position-relative .ti-search {
        pointer-events: none;
        font-size: 0.875rem;
    }

    /* Clear filters button */
    .btn-outline-secondary {
        border-color: #e3e6f0;
        color: #6c757d;
        transition: all 0.2s ease;
    }

    .btn-outline-secondary:hover {
        background-color: #f8f9fa;
        border-color: #adb5bd;
        color: #495057;
    }

    /* Filter info text */
    #filterInfo {
        font-style: italic;
        color: #6c757d;
    }

    /* Responsive filter controls */
    @media (max-width: 768px) {
        .row.mb-3 .col-md-6 {
            margin-bottom: 1rem;
        }

        .form-label {
            font-size: 0.75rem;
        }

        .form-select-sm,
        .form-control-sm {
            font-size: 0.75rem;
        }
    }
</style>