<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-header-title">
                    <h5 class="m-b-10" id="pageTitleText">Kategori</h5>
                </div>
            </div>
            <div class="col-auto">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0)">Produk</a></li>
                    <li class="breadcrumb-item" aria-current="page" id="breadcrumbText">Kategori</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <!-- [ Data Kategori ] start -->
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 id="cardTitleText">Data Kategori</h5>
                        <p class="text-muted mb-0 d-none" id="cardSubtitleText">Isi form di bawah untuk menambahkan
                            kategori
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
                            <button type="button" class="btn btn-primary d-flex" id="btnTambahKategori"
                                onclick="showFormTambah()">
                                <i class="ti ti-plus me-2"></i> Tambah Kategori
                            </button>
                            <button type="button" class="btn btn-secondary d-none" id="btnKembali"
                                onclick="showDataKategori()">
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
    <!-- [ Data Kategori ] end -->
</div>
<!-- [ Main Content ] end -->

<!-- Enhanced Image Display Styles -->
<style>
    /* Table View Image Enhancements */
    .category-image-container {
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .category-image-container:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .category-image {
        transition: opacity 0.3s ease;
    }

    .category-image:hover {
        opacity: 0.9;
    }

    /* Card View Image Enhancements */
    .category-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #e9ecef;
    }

    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .category-card-image {
        transition: transform 0.3s ease;
    }

    .category-card:hover .category-card-image {
        transform: scale(1.1);
    }

    .card-image-container {
        border-radius: 0.375rem 0.375rem 0 0;
    }

    .image-overlay {
        background: linear-gradient(45deg, transparent 70%, rgba(0, 0, 0, 0.1) 100%);
        border-radius: 0 0.375rem 0 0;
    }

    /* Loading states */
    .image-loading {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .category-image-container {
            width: 50px !important;
            height: 35px !important;
        }

        .category-image {
            width: 50px !important;
            height: 35px !important;
        }

        .category-card-image {
            height: 120px !important;
        }
    }

    /* Image error state */
    .category-image[src*="unsplash"] {
        filter: sepia(20%) saturate(120%) hue-rotate(15deg);
    }

    /* Topping badge styling */
    .badge.bg-light-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
        border: 1px solid rgba(255, 193, 7, 0.2);
    }

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

    /* Filter Dropdown Styling */
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
        display: block;
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

    /* Button group spacing */
    .btn-group .btn {
        margin: 0 1px;
    }

    /* Table light styling */
    .table-light {
        background-color: var(--bs-gray-100) !important;
    }

    .column-headers th {
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        padding: 1rem 0.75rem;
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

        .table-responsive {
            max-height: 400px;
            font-size: 0.875rem;
        }

        .table td,
        .table th {
            padding: 8px 6px;
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
</style>

<!-- JavaScript untuk CRUD Kategori -->
<script>
    let currentEditId = null;
    let allCategoriesData = []; // Store all categories data
    let filteredCategoriesData = []; // Store filtered data
    let currentPage = 1;
    const itemsPerPage = 10;
    let activeFilters = new Map(); // Modern filter storage
    let currentSort = 'created_at_desc';
    let currentViewMode = 'active'; // Track if showing active or deleted items

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
            const filterLabel = checkbox.parentElement.querySelector('.filter-label').textContent;
            activeFilters.set(filterKey, {
                type: filterType,
                value: filterValue,
                label: filterLabel,
                element: checkbox
            });
        } else {
            activeFilters.delete(filterKey);
        }

        updateFilterBadge();
        updateActiveFiltersDisplay();
        applyFilters();
    }

    // Update filter badge count
    function updateFilterBadge() {
        const badge = document.getElementById('filterBadge');
        const count = activeFilters.size;

        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'inline-block';
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
                <button type="button" class="remove-filter" onclick="removeFilter('${key}')">
                    <i class="ti ti-x"></i>
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

        if (filterKey === 'view_mode:deleted') {
            currentViewMode = 'active';
            loadCategoryData(false);
        }

        activeFilters.delete(filterKey);
        updateFilterBadge();
        updateActiveFiltersDisplay();

        if (filterKey !== 'view_mode:deleted') {
            applyFilters();
        }
    }

    // Handle view mode change (show deleted items)
    function handleViewModeChange(checkbox) {
        const isShowingDeleted = checkbox.checked;

        if (isShowingDeleted) {
            currentViewMode = 'deleted';
            loadCategoryData(true);
            activeFilters.set('view_mode:deleted', {
                type: 'view_mode',
                value: 'deleted',
                label: 'Tampilkan yang Dihapus',
                element: checkbox
            });
        } else {
            currentViewMode = 'active';
            loadCategoryData(false);
            activeFilters.delete('view_mode:deleted');
        }

        updateFilterBadge();
        updateActiveFiltersDisplay();
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        const dropdown = document.getElementById('filterDropdown');
        const button = document.getElementById('filterButton');

        if (dropdown && dropdown.classList.contains('show')) {
            if (!dropdown.contains(e.target) && !button.contains(e.target)) {
                dropdown.classList.remove('show');
                button.classList.remove('active');
            }
        }
    });

    // Show form for adding new category
    function showFormTambah() {
        currentEditId = null;
        showForm('Tambah Kategori', 'Form Tambah Kategori');
    }

    // Show data category view
    function showDataKategori() {
        console.log('showDataKategori called');
        const mainContent = document.getElementById('mainContentArea');
        console.log('Main content element:', mainContent);

        // Update header
        document.getElementById('pageTitleText').textContent = 'Kategori';
        document.getElementById('breadcrumbText').textContent = 'Kategori';
        document.getElementById('cardTitleText').textContent = 'Data Kategori';
        document.getElementById('cardSubtitleText').classList.add('d-none');

        // Toggle buttons
        document.getElementById('btnTambahKategori').classList.remove('d-none');
        document.getElementById('btnKembali').classList.add('d-none');

        // Set content
        if (mainContent) {
            mainContent.innerHTML = getDataKategoriHTML();
            console.log('Content set, loading data...');
            loadCategoryData();
        } else {
            console.error('Main content element not found');
        }
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', function () {
        showDataKategori();
    });

    // Load category data from API
    async function loadCategoryData(showDeleted = false) {
        try {
            // Build URL with is_active parameter
            let url = 'api/menu/categories.php';
            if (!showDeleted) {
                url += '?is_active=true';
            } else {
                url += '?is_active=false';
            }

            console.log('Loading category data from:', url);
            const response = await fetch(url);
            const result = await response.json();

            if (result.success) {
                allCategoriesData = result.data || [];
                filteredCategoriesData = [...allCategoriesData];
                currentPage = 1;
                currentViewMode = showDeleted ? 'deleted' : 'active';
                console.log('Category data loaded:', allCategoriesData.length, 'items');
                applyFilters();
            } else {
                throw new Error(result.message || 'Failed to load data');
            }
        } catch (error) {
            console.error('Error:', error);
            showError('Failed to load category data: ' + error.message);
        }
    }

    // Get data category HTML
    function getDataKategoriHTML() {
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
                                           placeholder="Search categories..." onkeyup="applyFilters()"
                                           onchange="applyFilters()">
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
                                            <!-- View Mode Filter -->
                                                        <div class="filter-group">
                                                <label class="filter-group-label">View Mode</label>
                                                            <div class="filter-options">
                                                                <label class="filter-option">
                                                        <input type="checkbox" class="filter-checkbox" data-filter="view_mode" data-value="deleted" onchange="handleViewModeChange(this)">
                                                        <span class="filter-icon">🗑️</span>
                                                        <span class="filter-label">Show Deleted Items</span>
                                                                </label>
                                                            </div>
                                                        </div>

                                            <!-- Type Filters -->
                                                        <div class="filter-group">
                                                <label class="filter-group-label">Category Type</label>
                                                            <div class="filter-options">
                                                                <label class="filter-option">
                                                        <input type="checkbox" class="filter-checkbox" data-filter="type" data-value="product" onchange="handleFilterChange(this)">
                                                        <span class="filter-icon">📂</span>
                                                        <span class="filter-label">Product</span>
                                                    </label>
                                                    <label class="filter-option">
                                                        <input type="checkbox" class="filter-checkbox" data-filter="type" data-value="topping" onchange="handleFilterChange(this)">
                                                        <span class="filter-icon">➕</span>
                                                        <span class="filter-label">Topping</span>
                                                                </label>
                                                            </div>
                                                        </div>
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
                                                    <option value="type_asc">Tipe A-Z</option>
                                                    <option value="type_desc">Tipe Z-A</option>
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
                                        <th style="min-width: 200px;">Nama Kategori</th>
                                        <th style="min-width: 120px;">Tipe</th>
                                        <th style="min-width: 100px;">Status</th>
                                        <th style="min-width: 120px;">Tanggal</th>
                                        <th style="min-width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            Loading category data...
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
                        <nav aria-label="Category pagination">
                            <ul class="pagination pagination-sm mb-0" id="paginationControls">
                                <!-- Pagination buttons will be generated here -->
                        </ul>
                    </nav>
                    </div>
                </div>

                <!-- Card View -->
                <div class="tab-pane fade" id="pills-card" role="tabpanel" aria-labelledby="pills-card-tab"
                    tabindex="0">
                    <div class="row" id="categoryCardContainer">
                        <div class="col-12 text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading category data...</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Show/Hide states
    function showLoadingState() {
        const loading = document.getElementById('loadingState');
        const empty = document.getElementById('emptyState');
        const dataContainer = document.getElementById('categoriesDataContainer');
        const pagination = document.getElementById('paginationContainer');

        if (loading) loading.style.display = 'block';
        if (empty) empty.style.display = 'none';
        if (dataContainer) dataContainer.style.display = 'none';
        if (pagination) pagination.style.display = 'none';
    }

    function hideLoadingState() {
        const loading = document.getElementById('loadingState');
        if (loading) loading.style.display = 'none';
    }

    function showEmptyState() {
        const empty = document.getElementById('emptyState');
        const dataContainer = document.getElementById('categoriesDataContainer');
        const pagination = document.getElementById('paginationContainer');

        if (empty) empty.style.display = 'block';
        if (dataContainer) dataContainer.style.display = 'none';
        if (pagination) pagination.style.display = 'none';
    }

    function showTableView() {
        const empty = document.getElementById('emptyState');
        const dataContainer = document.getElementById('categoriesDataContainer');

        if (empty) empty.style.display = 'none';
        if (dataContainer) dataContainer.style.display = 'block';
    }

    // Apply filters
    function applyFilters() {
        console.log('Applying filters...');
        filteredCategoriesData = [...allCategoriesData];

        // Get search term
        const searchInput = document.getElementById('searchInput');
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

        console.log('Search term:', searchTerm);

        // Apply type filters
        const typeFilters = Array.from(activeFilters.values()).filter(filter => filter.type === 'type');
        if (typeFilters.length > 0) {
            const selectedTypes = typeFilters.map(filter => filter.value);
            console.log('Type filters:', selectedTypes);
            filteredCategoriesData = filteredCategoriesData.filter(item =>
                selectedTypes.includes(item.type)
            );
        }

        // Apply search filter
        if (searchTerm) {
            filteredCategoriesData = filteredCategoriesData.filter(item =>
                item.name.toLowerCase().includes(searchTerm) ||
                item.type.toLowerCase().includes(searchTerm)
            );
        }

        console.log('Filtered data:', filteredCategoriesData.length, 'items');

        // Apply sorting
        applySortingToData();

        // Update display
        currentPage = 1;
        displayCategoriesTable();
        displayCategoriesCards();
        updatePagination();
    }

    // Apply sorting to filtered data
    function applySortingToData() {
        filteredCategoriesData.sort((a, b) => {
            switch (currentSort) {
                case 'name_asc':
                    return a.name.localeCompare(b.name);
                case 'name_desc':
                    return b.name.localeCompare(a.name);
                case 'type_asc':
                    return a.type.localeCompare(b.type);
                case 'type_desc':
                    return b.type.localeCompare(a.type);
                case 'created_at_asc':
                    return new Date(a.created_at) - new Date(b.created_at);
                case 'created_at_desc':
                default:
                    return new Date(b.created_at) - new Date(a.created_at);
            }
        });
    }

    // Handle sorting change
    function applySorting(triggerFilters = true) {
        const sortBy = document.getElementById('sortBy');
        currentSort = sortBy ? sortBy.value : 'created_at_desc';

        if (triggerFilters) {
            applyFilters();
        }
    }

    // Update results info
    function updateResultsInfo() {
        const resultsInfo = document.getElementById('resultsInfo');
        const total = filteredCategoriesData.length;

        if (total === 0) {
            resultsInfo.textContent = 'Tidak ada kategori yang ditemukan';
        } else if (total === allCategoriesData.length) {
            resultsInfo.textContent = `Menampilkan ${total} kategori`;
        } else {
            resultsInfo.textContent = `Menampilkan ${total} dari ${allCategoriesData.length} kategori`;
        }
    }

    // Display categories table with pagination
    function displayCategoriesTable() {
        const tableBody = document.getElementById('categoriesTableBody');
        if (!tableBody) return;

        tableBody.innerHTML = '';

        if (filteredCategoriesData.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td colspan="6" class="text-center py-4">
                    <div class="text-muted">
                        <i class="ti ti-search f-20 mb-2"></i>
                        <p class="mb-0">Tidak ada kategori yang ditemukan</p>
                    </div>
                </td>
            `;
            tableBody.appendChild(row);
            return;
        }

        // Calculate pagination
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedData = filteredCategoriesData.slice(startIndex, endIndex);

        paginatedData.forEach((category, index) => {
            const row = document.createElement('tr');
            const globalIndex = startIndex + index + 1;

            row.innerHTML = `
                <td class="text-center">
                    <span class="badge bg-light text-dark rounded-pill" style="min-width: 30px;">${globalIndex}</span>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <span class="fw-medium text-dark">${category.name}</span>
                        <small class="text-muted">ID: ${category.id.substring(0, 8)}...</small>
                    </div>
                </td>
                <td>
                    <span class="badge ${category.type === 'product' ? 'bg-primary' : 'bg-info'} text-white">
                        <i class="ti ${category.type === 'product' ? 'ti-category' : 'ti-plus'} me-1"></i>
                        ${category.type === 'product' ? 'Produk' : 'Topping'}
                    </span>
                </td>
                <td>
                    <span class="badge ${category.is_active ? 'bg-success' : 'bg-danger'} text-white">
                        <i class="ti ${category.is_active ? 'ti-check' : 'ti-x'} me-1"></i>
                        ${category.is_active ? 'Aktif' : 'Nonaktif'}
                    </span>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <span class="text-dark">${new Date(category.created_at).toLocaleDateString('id-ID')}</span>
                        <small class="text-muted">${new Date(category.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</small>
                    </div>
                </td>
                <td class="text-center">
                    ${renderActionButtons(category)}
                </td>
            `;

            tableBody.appendChild(row);
        });
    }

    // Render action buttons based on category status
    function renderActionButtons(category) {
        const isActive = category.is_active;

        if (isActive) {
            return `
                <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-outline-warning" onclick="editCategory('${category.id}')" 
                            title="Edit Kategori" data-bs-toggle="tooltip">
                        <i class="ti ti-edit f-16"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="deleteCategory('${category.id}', '${category.name}')" 
                            title="Hapus Kategori" data-bs-toggle="tooltip">
                        <i class="ti ti-trash f-16"></i>
                    </button>
                </div>
            `;
        } else {
            return `
                <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-outline-success" onclick="restoreCategory('${category.id}', '${category.name}')" 
                            title="Pulihkan Kategori" data-bs-toggle="tooltip">
                        <i class="ti ti-refresh f-16"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="permanentDeleteCategory('${category.id}', '${category.name}')" 
                            title="Hapus Permanen" data-bs-toggle="tooltip">
                        <i class="ti ti-trash-x f-16"></i>
                    </button>
                </div>
            `;
        }
    }

    // Pagination functions
    function updatePagination() {
        const totalItems = filteredCategoriesData.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const paginationContainer = document.getElementById('paginationControls');
        const paginationInfo = document.getElementById('paginationInfo');

        if (totalPages <= 1) {
            if (paginationContainer) paginationContainer.style.display = 'none';
            return;
        }

        if (paginationContainer) paginationContainer.style.display = 'flex';

        // Update pagination info
        const startItem = totalItems > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0;
        const endItem = Math.min(currentPage * itemsPerPage, totalItems);

        if (paginationInfo) {
            paginationInfo.textContent = `Showing ${startItem} - ${endItem} of ${totalItems} entries`;
        }

        generatePaginationControls(totalPages);
    }

    function generatePaginationControls(totalPages) {
        const paginationList = document.getElementById('paginationControls');
        if (!paginationList) return;

        paginationList.innerHTML = '';

        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;">
                <i class="ti ti-chevron-left"></i>
            </a>
        `;
        paginationList.appendChild(prevLi);

        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);

        for (let i = startPage; i <= endPage; i++) {
            const pageLi = document.createElement('li');
            pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
            pageLi.innerHTML = `
                <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
            `;
            paginationList.appendChild(pageLi);
        }

        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;">
                <i class="ti ti-chevron-right"></i>
            </a>
        `;
        paginationList.appendChild(nextLi);
    }

    function changePage(page) {
        const totalPages = Math.ceil(filteredCategoriesData.length / itemsPerPage);

        if (page < 1 || page > totalPages || page === currentPage) {
            return;
        }

        currentPage = page;
        displayCategoriesTable();
        displayCategoriesCards();
        updatePagination();
    }

    // Clear all filters
    function clearAllFilters() {
        console.log('Clearing all filters...');

        activeFilters.forEach((filter, key) => {
            if (filter.element) {
                filter.element.checked = false;
            }
        });

        activeFilters.clear();

        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.value = '';
        }

        currentViewMode = 'active';
        loadCategoryData(false);

        updateFilterBadge();
        updateActiveFiltersDisplay();
    }

    // Display categories in card view
    function displayCategoriesCards() {
        const cardContainer = document.getElementById('categoryCardContainer');
        if (!cardContainer) return;

        cardContainer.innerHTML = '';

        if (filteredCategoriesData.length === 0) {
            cardContainer.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="text-muted">
                        <i class="ti ti-search f-20 mb-2"></i>
                        <p class="mb-0">Tidak ada kategori yang ditemukan</p>
                    </div>
                </div>
            `;
            return;
        }

        // Calculate pagination
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedData = filteredCategoriesData.slice(startIndex, endIndex);

        paginatedData.forEach((category) => {
            const cardCol = document.createElement('div');
            cardCol.className = 'col-lg-4 col-md-6 mb-4';

            cardCol.innerHTML = `
                <div class="card category-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avtar avtar-s bg-light-${category.type === 'product' ? 'primary' : 'info'} me-3">
                                <i class="ti ti-${category.type === 'product' ? 'category' : 'plus'} f-16"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-semibold">${category.name}</h6>
                                <small class="text-muted">ID: ${category.id.substring(0, 8)}...</small>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-${category.type === 'product' ? 'primary' : 'info'} text-uppercase">
                                <i class="ti ti-${category.type === 'product' ? 'category' : 'plus'} me-1"></i>
                                ${category.type === 'product' ? 'Produk' : 'Topping'}
                            </span>
                            <span class="badge bg-${category.is_active ? 'success' : 'danger'}">
                                <i class="ti ti-${category.is_active ? 'check' : 'x'} me-1"></i>
                                ${category.is_active ? 'Aktif' : 'Dihapus'}
                            </span>
                        </div>
                        
                        <div class="text-muted mb-3">
                            <small class="d-block">Dibuat: ${new Date(category.created_at).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            })}</small>
                            <small class="text-muted">${new Date(category.created_at).toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            })}</small>
                        </div>
                        
                        <div class="d-flex gap-2">
                            ${renderActionButtons(category)}
                        </div>
                    </div>
                </div>
            `;

            cardContainer.appendChild(cardCol);
        });
    }

    // Show form (placeholder - to be implemented)
    function showForm(pageTitle, cardTitle, data = null) {
        alert('Form untuk ' + pageTitle + ' akan diimplementasikan');
    }

    // Delete category (placeholder)
    function deleteCategory(categoryId, categoryName) {
        if (confirm('Hapus kategori "' + categoryName + '"?')) {
            alert('Hapus kategori akan diimplementasikan');
        }
    }

    // Restore category (placeholder)
    function restoreCategory(categoryId, categoryName) {
        if (confirm('Pulihkan kategori "' + categoryName + '"?')) {
            alert('Pulihkan kategori akan diimplementasikan');
        }
    }

    // Permanent delete category (placeholder)
    function permanentDeleteCategory(categoryId, categoryName) {
        if (confirm('HAPUS PERMANEN kategori "' + categoryName + '"? Tindakan ini tidak dapat dibatalkan!')) {
            alert('Hapus permanen akan diimplementasikan');
        }
    }

    // Utility functions
    function showSuccess(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            alert(message);
        }
    }

    function showError(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message
            });
        } else {
            alert(message);
        }
    }
</script>