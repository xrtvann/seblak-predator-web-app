<!-- [ breadcrumb ] start -->
<div class="page-header mb-4">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-header-title">
                    <h5 class="m-b-10" id="pageTitleText">Topping</h5>
                </div>
            </div>
            <div class="col-auto">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0)">Produk</a></li>
                    <li class="breadcrumb-item" aria-current="page" id="breadcrumbText">Topping</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<style>
    #viewToggleTabs.force-hide {
        display: none !important;
    }
</style>

<!-- [ Main Content ] start -->
<div class="row">
    <!-- [ Data Menu ] start -->
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 id="cardTitleText">Data Topping</h5>
                        <p class="text-muted mb-0 d-none" id="cardSubtitleText">Isi form di bawah untuk menambahkan
                            topping
                            baru</p>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex align-items-center" id="headerActions">
                            <!-- View Toggle Tabs -->
                            <ul class="nav nav-pills me-3" id="viewToggleTabs" style="display: none;">
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
                            <button type="button" class="btn btn-warning d-flex" id="btnTambahMenu"
                                onclick="showFormTambah()">
                                <i class="ti ti-plus me-2"></i> Tambah Topping
                            </button>
                            <button type="button" class="btn btn-secondary d-none" id="btnKembali"
                                onclick="showDataMenu()">
                                <i class="ti ti-arrow-left me-2"></i> Kembali
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

<!-- Enhanced Image Display Styles -->
<style>
    /* Table View Image Enhancements */
    .menu-image-container {
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .menu-image-container:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .menu-image {
        transition: opacity 0.3s ease;
    }

    .menu-image:hover {
        opacity: 0.9;
    }

    /* Card View Image Enhancements */
    .menu-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #e9ecef;
    }

    .menu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .menu-card-image {
        transition: transform 0.3s ease;
    }

    .menu-card:hover .menu-card-image {
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
        .menu-image-container {
            width: 50px !important;
            height: 35px !important;
        }

        .menu-image {
            width: 50px !important;
            height: 35px !important;
        }

        .menu-card-image {
            height: 120px !important;
        }
    }

    /* Image error state */
    .menu-image[src*="unsplash"] {
        filter: sepia(20%) saturate(120%) hue-rotate(15deg);
    }

    /* Topping badge styling */
    .badge.bg-light-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
        border: 1px solid rgba(255, 193, 7, 0.2);
    }

    /* Cards Grid Container */
    .cards-grid-container {
        min-height: 400px;
        background: white;
    }

    /* Card View Wrapper */
    .card-view-wrapper {
        position: relative;
        border: 1px solid #dee2e6;
        border-radius: 0 0 0.375rem 0.375rem;
        border-top: none;
        overflow: hidden;
        background: white;
    }
</style>

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
    let currentViewMode = 'active'; // Track if showing active or deleted items
    let currentTypeFilter = 'all'; // Track product type filter: 'all', 'product', 'topping'

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

        console.log('Filter change:', filterType, filterValue, checkbox.checked);

        if (checkbox.checked) {
            // Add filter
            const filterLabel = checkbox.parentElement.querySelector('.filter-label').textContent;
            activeFilters.set(filterKey, {
                type: filterType,
                value: filterValue,
                label: filterLabel,
                element: checkbox
            });
            console.log('Added filter:', filterKey, filterLabel);
        } else {
            // Remove filter
            activeFilters.delete(filterKey);
            console.log('Removed filter:', filterKey);
        }

        console.log('Active filters:', Array.from(activeFilters.keys()));
        updateFilterBadge();
        updateActiveFiltersDisplay();
        applyFilters(); // Auto-apply filters when changed
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

        // Also update card filter badge
        updateCardFilterBadge();
    }

    // Update active filters display
    function updateActiveFiltersDisplay() {
        const container = document.getElementById('activeFiltersContainer');
        const tagsContainer = document.getElementById('activeFiltersTags');

        if (activeFilters.size === 0) {
            container.style.display = 'none';
        } else {
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

        // Also update card active filters display
        updateCardActiveFiltersDisplay();
    }

    // Remove specific filter
    function removeFilter(filterKey) {
        const filter = activeFilters.get(filterKey);
        if (filter && filter.element) {
            filter.element.checked = false;
        }

        // Special handling for view mode filter
        if (filterKey === 'view_mode:deleted') {
            // Switch back to active items view
            currentViewMode = 'active';
            loadMenuData(false); // Load active items data
        }

        activeFilters.delete(filterKey);
        updateFilterBadge();
        updateActiveFiltersDisplay();

        // Only apply filters for non-view-mode filters
        if (filterKey !== 'view_mode:deleted') {
            applyFilters();
        }
    }

    // Handle view mode change (show deleted items)
    function handleViewModeChange(checkbox) {
        const isShowingDeleted = checkbox.checked;

        if (isShowingDeleted) {
            // Switch to deleted items view
            currentViewMode = 'deleted';
            loadMenuData(true); // Load deleted items

            // Add to active filters for visual feedback
            activeFilters.set('view_mode:deleted', {
                type: 'view_mode',
                value: 'deleted',
                label: 'Show Deleted Items',
                element: checkbox
            });
        } else {
            // Switch back to active items view
            currentViewMode = 'active';
            loadMenuData(false); // Load active items

            // Remove from active filters
            activeFilters.delete('view_mode:deleted');
        }

        updateFilterBadge();
        updateActiveFiltersDisplay();
        updatePermanentDeleteButtonVisibility();
    }

    // Populate category filter options
    function populateCategoryFilterOptions() {
        const container = document.getElementById('categoryFilterOptions');

        if (!container) {
            console.log('Category filter container not found');
            return;
        }

        // Filter only active topping categories
        const toppingCategories = categories.filter(cat => cat.type === 'topping' && cat.is_active);

        console.log('Populating category filter options:', toppingCategories.length, 'categories');

        if (toppingCategories.length === 0) {
            container.innerHTML = '<p class="text-muted small">No categories available</p>';
            return;
        }

        container.innerHTML = '';

        toppingCategories.forEach(category => {
            console.log('Adding category filter:', category.name, category.id);
            const option = document.createElement('label');
            option.className = 'filter-option';
            option.innerHTML = `
                <input type="checkbox" class="filter-checkbox" data-filter="category" data-value="${category.id}" onchange="handleFilterChange(this)">
                <span class="filter-icon">📂</span>
                <span class="filter-label">${category.name}</span>
            `;
            container.appendChild(option);
        });

        // Also populate card view category filter options
        populateCardCategoryFilterOptions();
    }

    // Populate card category filter options
    function populateCardCategoryFilterOptions() {
        const container = document.getElementById('cardCategoryFilterOptions');

        if (!container) {
            console.log('Card category filter container not found');
            return;
        }

        // Filter only active topping categories
        const toppingCategories = categories.filter(cat => cat.type === 'topping' && cat.is_active);

        if (toppingCategories.length === 0) {
            container.innerHTML = '<p class="text-muted small">No categories available</p>';
            return;
        }

        container.innerHTML = '';

        toppingCategories.forEach(category => {
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

    // Toggle card filter dropdown
    function toggleCardFilterDropdown() {
        const dropdown = document.getElementById('cardFilterDropdown');
        const button = document.getElementById('cardFilterButton');

        if (dropdown && button) {
            const isOpen = dropdown.classList.contains('show');

            if (isOpen) {
                dropdown.classList.remove('show');
                button.classList.remove('active');
            } else {
                dropdown.classList.add('show');
                button.classList.add('active');
            }
        }
    }

    // Update card filter badge
    function updateCardFilterBadge() {
        const badge = document.getElementById('cardFilterBadge');
        if (badge) {
            const filterCount = activeFilters.size;
            if (filterCount > 0) {
                badge.textContent = filterCount;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    // Update card active filters display
    function updateCardActiveFiltersDisplay() {
        const container = document.getElementById('cardActiveFiltersContainer');
        const tagsContainer = document.getElementById('cardActiveFiltersTags');

        if (!container || !tagsContainer) return;

        if (activeFilters.size === 0) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'block';
        tagsContainer.innerHTML = '';

        activeFilters.forEach((filter, key) => {
            const tag = document.createElement('span');
            tag.className = 'filter-tag';
            tag.innerHTML = `
                ${filter.label}
                <button type="button" class="btn-remove-filter" onclick="removeFilter('${key}')">
                    <i class="ti ti-x"></i>
                </button>
            `;
            tagsContainer.appendChild(tag);
        });
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

        // Also handle card filter dropdown
        const cardDropdown = document.getElementById('cardFilterDropdown');
        const cardButton = document.getElementById('cardFilterButton');

        if (cardDropdown && cardDropdown.classList.contains('show')) {
            if (!cardDropdown.contains(e.target) && !cardButton.contains(e.target)) {
                toggleCardFilterDropdown();
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
            const response = await fetch(`api/menu/toppings.php?id=${id}`);
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
        updatePreview();
    }

    // Update preview from form inputs
    function updatePreview() {
        const name = document.getElementById('menuName').value;
        const description = document.getElementById('menuDescription').value;
        const price = document.getElementById('menuPrice').value;
        const categorySelect = document.getElementById('menuCategory');
        const isTopping = document.getElementById('isTopping').checked;

        // Update preview elements
        document.getElementById('previewName').textContent = name || 'Nama Menu';
        document.getElementById('previewDescription').textContent = description || '';
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

        // Update status badge - always active for new menus
        const statusBadge = document.getElementById('previewStatus');
        statusBadge.textContent = 'Active';
        statusBadge.className = 'badge bg-success';
    }

    // Update image preview
    function updateImagePreview() {
        const imageUrl = document.getElementById('menuImage').value;
        const previewImage = document.getElementById('previewImage');
        const previewPlaceholder = document.getElementById('previewPlaceholder');

        if (imageUrl && imageUrl !== '0' && imageUrl !== 'null' && imageUrl.trim() !== '') {
            // Convert database filename to proper URL
            const fullImageUrl = getImageUrl(imageUrl, 'large');

            previewImage.src = fullImageUrl;
            previewImage.style.display = 'block';
            previewPlaceholder.style.display = 'none';

            previewImage.onerror = function () {
                // If image fails to load, show placeholder
                previewImage.style.display = 'none';
                previewPlaceholder.style.display = 'block';
            };
        } else {
            // No image URL, show placeholder
            previewImage.style.display = 'none';
            previewPlaceholder.style.display = 'block';
        }
        updatePreview();
    }

    // Update image preview for edit mode (when we have an existing image URL)
    function updateImagePreviewForEdit(imageUrl) {
        console.log('updateImagePreviewForEdit called with:', imageUrl);

        const previewImage = document.getElementById('previewImage');
        const previewPlaceholder = document.getElementById('previewPlaceholder');

        console.log('Preview elements found:', {
            previewImage: !!previewImage,
            previewPlaceholder: !!previewPlaceholder
        });

        if (imageUrl && imageUrl !== '0' && imageUrl !== 'null' && imageUrl.trim() !== '') {
            // Convert database filename to proper URL
            const fullImageUrl = getImageUrl(imageUrl, 'large');
            console.log('Setting preview image to:', fullImageUrl);

            previewImage.src = fullImageUrl;
            previewImage.style.display = 'block';
            previewPlaceholder.style.display = 'none';

            previewImage.onload = function () {
                console.log('Preview image loaded successfully');
            };

            previewImage.onerror = function () {
                console.log('Preview image failed to load');
                // If image fails to load, show placeholder
                previewImage.style.display = 'none';
                previewPlaceholder.style.display = 'block';
            };
        } else {
            console.log('No valid image URL, showing placeholder');
            // No image URL, show placeholder
            previewImage.style.display = 'none';
            previewPlaceholder.style.display = 'block';
        }
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
            if (e.target.matches('#isTopping, #menuCategory')) {
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
                populateCategoryFilterOptions(); // Also populate filter options
            } else {
                console.error('Failed to load categories:', result.message);
            }
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }

    // Load menu data from API
    async function loadMenuData(showDeleted = false) {
        try {
            // Build URL for toppings API and filter by availability
            let url = 'api/menu/toppings.php';
            if (!showDeleted) {
                url += '?is_available=true';
            } else {
                url += '?is_available=false';
            }

            const response = await fetch(url);
            const result = await response.json();

            if (result.success) {
                displayMenuData(result.data, showDeleted);
                updatePermanentDeleteButtonVisibility(); // Update button visibility after loading data
            } else {
                console.error('Failed to load topping data:', result.message);
                showNotification('Error loading topping data: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error connecting to server', 'error');
        }
    }

    // Show data topping view
    function showDataMenu() {
        const mainContent = document.getElementById('mainContentArea');

        // Update header
        document.getElementById('pageTitleText').textContent = 'Topping';
        document.getElementById('breadcrumbText').textContent = 'Topping';
        document.getElementById('cardTitleText').textContent = 'Data Topping';
        document.getElementById('cardSubtitleText').classList.add('d-none');

        // Toggle buttons
        document.getElementById('btnTambahMenu').classList.remove('d-none');
        document.getElementById('btnKembali').classList.add('d-none');
        const viewToggleTabs = document.getElementById('viewToggleTabs');
        viewToggleTabs.classList.remove('d-none', 'force-hide');
        viewToggleTabs.style.display = ''; // Remove inline style to show tabs

        // Set content
        mainContent.innerHTML = getDataMenuHTML();

        // Populate category filter options (only topping categories)
        populateCategoryFilterOptions();

        // Reload data (only toppings)
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
        // Start with all data (already filtered for toppings only from API)
        filteredMenuData = [...allMenuData];

        // Get search term from both table and card view inputs
        const searchInput = document.getElementById('searchInput');
        const cardSearchInput = document.getElementById('cardSearchInput');
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() :
            (cardSearchInput ? cardSearchInput.value.toLowerCase().trim() : '');

        // Sync both search inputs
        if (searchInput && cardSearchInput) {
            if (searchInput.value !== cardSearchInput.value) {
                const activeElement = document.activeElement;
                if (activeElement === searchInput) {
                    cardSearchInput.value = searchInput.value;
                } else if (activeElement === cardSearchInput) {
                    searchInput.value = cardSearchInput.value;
                }
            }
        }

        // Apply active filters
        // Collect category filters for OR logic
        const categoryFilters = Array.from(activeFilters.values()).filter(filter => filter.type === 'category');

        console.log('Applying filters - Category filters found:', categoryFilters.length);

        if (categoryFilters.length > 0) {
            // OR logic: show items that match ANY selected category
            const selectedCategoryIds = categoryFilters.map(filter => filter.value);
            console.log('Selected category IDs:', selectedCategoryIds);
            console.log('Total items before filtering:', filteredMenuData.length);

            filteredMenuData = filteredMenuData.filter(item => {
                const matches = selectedCategoryIds.includes(item.category_id);
                if (matches) {
                    console.log('Item matches filter:', item.name, item.category_id);
                }
                return matches;
            });

            console.log('Total items after category filtering:', filteredMenuData.length);
        }

        // Apply search filter
        if (searchTerm) {
            filteredMenuData = filteredMenuData.filter(item =>
                item.name.toLowerCase().includes(searchTerm) ||
                (item.category_name && item.category_name.toLowerCase().includes(searchTerm))
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
                case 'updated_at_desc':
                    return new Date(b.updated_at) - new Date(a.updated_at);
                case 'created_at_asc':
                    return new Date(a.created_at) - new Date(b.created_at);
                case 'created_at_desc':
                default:
                    return new Date(b.created_at) - new Date(a.created_at);
            }
        });

        // Update display based on active view (table or card)
        currentPage = 1;
        updateActiveView();
        updatePagination();
    }

    // Update display based on which view tab is active
    function updateActiveView() {
        const tableTab = document.getElementById('pills-table');
        const cardTab = document.getElementById('pills-card');

        // Check which tab is currently active
        if (cardTab && cardTab.classList.contains('show') && cardTab.classList.contains('active')) {
            displayCardView();
        } else {
            displayTableView();
        }
    }

    function applySorting(triggerFilters = true) {
        const sortBy = document.getElementById('sortBy');
        const cardSortBy = document.getElementById('cardSortBy');

        // Determine which sort select triggered the change
        const activeElement = document.activeElement;
        let sortValue;

        if (activeElement === sortBy || !cardSortBy) {
            sortValue = sortBy ? sortBy.value : 'created_at_desc';
            // Sync card sort select
            if (cardSortBy) cardSortBy.value = sortValue;
        } else if (activeElement === cardSortBy || !sortBy) {
            sortValue = cardSortBy ? cardSortBy.value : 'created_at_desc';
            // Sync table sort select
            if (sortBy) sortBy.value = sortValue;
        } else {
            // Default to sortBy value
            sortValue = sortBy ? sortBy.value : 'created_at_desc';
        }

        currentSort = sortValue;

        if (triggerFilters) {
            applyFilters();
        }
    }



    // Show form tambah topping
    function showFormTambah() {
        currentEditId = null;
        showForm('Tambah Topping', 'Form Tambah Topping');
    }

    // Show form edit menu
    async function editMenu(id) {
        console.log('Editing menu with ID:', id);
        currentEditId = id;

        try {
            const apiUrl = `api/menu/toppings.php?id=${id}`;
            console.log('Fetching from URL:', apiUrl);

            const response = await fetch(apiUrl);
            console.log('Fetch response status:', response.status);
            console.log('Response headers:', [...response.headers.entries()]);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log('Edit data received:', result);

            if (result.success) {
                showForm('Edit Topping', 'Form Edit Topping', result.data);
            } else {
                showNotification('Error loading menu data: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Detailed error:', error);
            console.error('Error name:', error.name);
            console.error('Error message:', error.message);
            console.error('Error stack:', error.stack);

            if (error.message.includes('fetch')) {
                showNotification('Network error: Cannot connect to server. Please check if the server is running.', 'error');
            } else if (error.message.includes('JSON')) {
                showNotification('Server response error: Invalid data format received.', 'error');
            } else {
                showNotification('Error connecting to server: ' + error.message, 'error');
            }
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
        document.getElementById('btnKembali').classList.add('d-flex');
        const viewToggleTabs = document.getElementById('viewToggleTabs');
        viewToggleTabs.classList.add('d-none', 'force-hide');
        viewToggleTabs.style.display = 'none'; // Force hide with inline style

        // Set content
        mainContent.innerHTML = getFormHTML();

        // Update form title in the header
        const formTitle = document.getElementById('formTitle');
        if (formTitle) {
            formTitle.textContent = data ? 'Edit Topping' : 'Tambah Topping Baru';
        }

        // Initialize form first (this loads categories)
        initForm();

        // Populate form if editing (this should come after initForm)
        if (data) {
            populateForm(data);
        }
    }

    // Get data menu HTML
    function getDataMenuHTML() {
        return `
            <!-- Info Alert -->
            <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                <i class="ti ti-meat me-2"></i>
                <strong>Halaman Topping</strong> - Halaman ini khusus untuk mengelola topping saja.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

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
                                           placeholder="Cari" onkeyup="applyFilters()"
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
                                            
                                            <!-- Category Filters -->
                                            <div class="filter-group">
                                                <label class="filter-group-label">Categories</label>
                                                <div class="filter-options" id="categoryFilterOptions">
                                                    <!-- Category options will be populated dynamically -->
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
                        
                        <!-- Permanent Delete Inactive Button -->
                        <div class="permanent-delete-section mt-3" id="permanentDeleteSection" style="display: none;">
                            <button type="button" class="btn btn-danger" id="btnPermanentDeleteInactive" onclick="permanentDeleteInactiveToppings()">
                                <i class="ti ti-trash me-1"></i>Hapus Permanen Semua Topping
                            </button>
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
                                        <th style="min-width: 200px;">Nama Topping</th>
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
                    
                    <!-- Card View Controls - Same style as Table View -->
                    <div class="table-controls-section">
                        <div class="controls-row">
                            <!-- Left side: Search -->
                            <div class="search-section">
                                <div class="search-input-wrapper">
                                    <i class="ti ti-search search-icon"></i>
                                    <input type="text" class="form-control search-input" id="cardSearchInput" 
                                           placeholder="Cari" 
                                           onkeyup="applyFilters()"
                                           onchange="applyFilters()">
                                </div>
                            </div>
                            
                            <!-- Center: Filter Button -->
                            <div class="filter-section">
                                <div class="filter-dropdown position-relative">
                                    <button type="button" class="btn filter-btn" id="cardFilterButton" onclick="toggleCardFilterDropdown()">
                                        <i class="ti ti-filter"></i>
                                        <span class="filter-text">Filter</span>
                                        <span class="filter-badge" id="cardFilterBadge" style="display: none;">0</span>
                                    </button>
                                    
                                    <!-- Filter Dropdown for Card View -->
                                    <div class="filter-dropdown-menu" id="cardFilterDropdown">
                                        <div class="filter-dropdown-header">
                                            <h6 class="mb-0">Filter Options</h6>
                                            <button type="button" class="btn-close-filter" onclick="toggleCardFilterDropdown()">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        </div>
                                        
                                        <div class="filter-dropdown-body">
                                            <!-- Category Filters Only (No Deleted Items) -->
                                            <div class="filter-group">
                                                <label class="filter-group-label">Categories</label>
                                                <div class="filter-options" id="cardCategoryFilterOptions">
                                                    <!-- Category options will be populated dynamically -->
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
                                    <select class="form-select sort-select" id="cardSortBy" onchange="applySorting()">
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
                        
                        <!-- Active Filters Row for Card View -->
                        <div class="active-filters-container" id="cardActiveFiltersContainer" style="display: none;">
                            <div class="active-filters-wrapper">
                                <span class="active-filters-label">🏷️ Active:</span>
                                <div class="active-filters-tags" id="cardActiveFiltersTags">
                                    <!-- Active filter tags will appear here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card View Container - Wrapped like table -->
                    <div class="card-view-wrapper">
                        <!-- Cards Container -->
                        <div class="cards-grid-container p-3">
                            <div class="row" id="menuCardContainer">
                                <div class="col-12 text-center">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Loading menu data...</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pagination for Card View -->
                        <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light">
                            <div class="d-flex align-items-center">
                                <small class="text-muted" id="cardPaginationInfo">Showing 0 - 0 of 0 entries</small>
                            </div>
                            <nav aria-label="Card pagination">
                                <ul class="pagination pagination-sm mb-0" id="cardPaginationControls">
                                    <!-- Pagination buttons will be generated here -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Get form HTML
    function getFormHTML() {
        return `
            <!-- Form Container with Table Header Background -->
            <div class="card">
                <div class="card-body table-light p-4 rounded">
                    <form id="menuForm" enctype="multipart/form-data">
                <div class="row">
                    <!-- Left Column - Form Fields -->
                    <div class="col-lg-8">
                        <!-- Basic Information Card -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0 text-light fw-bold"><i class="ti ti-info-circle me-2"></i>Informasi Dasar</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="menuName" class="form-label">Nama Topping <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="menuName" name="name" required
                                                   placeholder="Masukkan nama topping">
                                            <div class="form-text">Nama topping akan tampil di aplikasi</div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="menuCategory" class="form-label">Kategori</label>
                                            <select class="form-select" id="menuCategory" name="category">
                                                <option value="">Pilih Kategori</option>
                                                <!-- Options will be populated dynamically from categories API -->
                                            </select>
                                            <div class="form-text">Pilih kategori topping</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
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
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Gambar Menu</label>
                                            
                                            <!-- File Upload Only -->
                                            <input type="file" class="form-control" id="menuImageFile" name="image_file" 
                                                   accept="image/*" onchange="handleFileUpload(this)">
                                            <div class="form-text">Upload gambar (JPG, PNG, GIF - Max: 2MB)</div>
                                            <div id="uploadProgress" class="progress mt-2" style="display: none; height: 4px;">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                                     role="progressbar" style="width: 0%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 d-none">
                                    <label for="menuDescription" class="form-label">Deskripsi (Opsional)</label>
                                    <textarea class="form-control" id="menuDescription" name="description" rows="4" 
                                              placeholder="Deskripsi menu yang menarik untuk pelanggan..."></textarea>
                                 
                                </div>
                            </div>
                        </div>

                        <!-- Additional Settings Card - Hidden for topping page -->
                        <input type="hidden" id="isTopping" name="is_topping" value="1">
                    </div>

                    <!-- Right Column - Preview -->
                    <div class="col-lg-4">
                        <div class="card sticky-top" style="top: 20px;">
                            <div class="card-header">
                                <h6 class="mb-0 text-light fw-bold"><i class="ti ti-eye me-2"></i>Preview Topping</h6>
                            </div>
                            <div class="card-body">
                                <!-- Menu Preview Card -->
                                <div class="card border" id="menuPreviewCard">
                                    <div class="position-relative">
                                        <!-- Image Preview -->
                                        <div id="previewImageContainer" class="position-relative" style="height: 180px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                            <!-- Default placeholder icon -->
                                            <div id="previewPlaceholder" class="text-center">
                                                <i class="ti ti-photo text-muted" style="font-size: 3rem;"></i>
                                                <p class="text-muted mt-2 mb-0 small">Belum ada gambar</p>
                                            </div>
                                            <!-- Actual image (hidden by default) -->
                                            <img id="previewImage" 
                                                 alt="Preview" class="card-img-top" 
                                                 style="height: 180px; object-fit: cover; display: none;">
                                        </div>
                                        <div class="position-absolute top-0 end-0 p-2">
                                            <span class="badge bg-success" id="previewStatus">Active</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title mb-1" id="previewName">Nama Menu</h6>
                                        <p class="card-text text-muted f-12 mb-2" id="previewDescription"></p>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-light-primary text-primary" id="previewCategory">Kategori</span>
                                            <span class="badge bg-light-info text-info d-none" id="previewTopping">Topping</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0" id="previewPrice">Rp 0</h5>
                                 
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
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-light border d-flex" onclick="showDataMenu()">
                                        <i class="ti ti-arrow-left me-1"></i> Kembali ke Daftar
                                    </button>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-secondary d-flex" onclick="resetForm()">
                                            <i class="ti ti-refresh me-2"></i> Reset Form
                                        </button>
                                        <button type="submit" class="btn btn-success d-flex" id="submitBtn">
                                            <i class="ti ti-check me-2"></i> <span id="submitText">Simpan Menu</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    </form>
                </div>
            </div>
        `;
    }

    // Display menu data
    function displayMenuData(menuData, showDeleted = false) {
        allMenuData = menuData; // Store all data (already filtered for toppings)
        filteredMenuData = [...allMenuData]; // Initialize filtered data
        currentPage = 1; // Reset to first page
        currentViewMode = showDeleted ? 'deleted' : 'active'; // Store current view mode
        applyFilters(); // Apply current filters and sorting
        displayCardView(menuData);
    }

    // Display table view with pagination
    function displayTableView() {
        const tableBody = document.getElementById('menuTableBody');
        if (!tableBody) return;

        tableBody.innerHTML = '';

        if (filteredMenuData.length === 0) {
            const message = allMenuData.length === 0 ? 'Tidak ada data topping' : 'Tidak ada data yang sesuai dengan filter';
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
                    <div class="menu-image-container position-relative" style="width: 60px; height: 40px;">
                        ${getImageHTML(item.image_url, item.name, 'small')}
                    </div>
                </td>
                <td>
                    <h6 class="mb-1">${item.name}</h6>
                   
                </td>
                <td>
                    <span class="badge bg-light-amazon">
                        ${item.category_name || 'No category'}
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
                        ${currentViewMode === 'active' ? `
                            <!-- Active Items: Edit + Delete -->
                            <button type="button" class="btn btn-sm btn-outline-warning me-2" onclick="editMenu('${item.id}')" title="Edit Menu">
                                <i class="ti ti-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteMenu('${item.id}', '${item.name.replace(/'/g, "&apos;")}')" title="Soft Delete">
                                <i class="ti ti-trash"></i>
                            </button>
                        ` : `
                            <!-- Deleted Items: Restore + Permanent Delete -->
                            <button type="button" class="btn btn-sm btn-outline-success me-2" onclick="restoreMenu('${item.id}', '${item.name.replace(/'/g, "&apos;")}')" title="Restore Item">
                                <i class="ti ti-refresh"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="permanentDeleteMenu('${item.id}', '${item.name.replace(/'/g, "&apos;")}')" title="Permanent Delete">
                                <i class="ti ti-trash"></i>
                            </button>
                        `}
                    </div>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    // Display card view
    function displayCardView() {
        const cardContainer = document.getElementById('menuCardContainer');
        if (!cardContainer) return;

        cardContainer.innerHTML = '';

        if (filteredMenuData.length === 0) {
            const message = allMenuData.length === 0 ? 'Tidak ada data topping' : 'Tidak ada data yang sesuai dengan filter';
            cardContainer.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="ti ti-file-off text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2 mb-0">${message}</p>
                </div>
            `;
            return;
        }

        // Calculate pagination using filtered data (same as table view)
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedData = filteredMenuData.slice(startIndex, endIndex);

        paginatedData.forEach(item => {
            const cardCol = document.createElement('div');
            cardCol.className = 'col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-3';

            // Get category name
            const categoryName = item.category_name || 'Tanpa Kategori';

            cardCol.innerHTML = `
                <div class="card border h-100 topping-card" style="transition: transform 0.2s, box-shadow 0.2s;">
                    <!-- Image with Status Badge Overlay -->
                    <div class="position-relative" style="height: 150px; background-color: #f8f9fa; overflow: hidden;">
                        <!-- Status Badge - Top Left -->
                        <span class="badge ${item.is_available ? 'bg-success' : 'bg-secondary'} position-absolute" 
                              style="top: 8px; left: 8px; z-index: 10;">
                            ${item.is_available ? 'Tersedia' : 'Tidak Tersedia'}
                        </span>
                        
                        ${item.image ? `
                            <img src="uploads/menu-images/${item.image}" alt="${item.name}" 
                                 style="width: 100%; height: 150px; object-fit: cover;">
                        ` : `
                            <div class="d-flex align-items-center justify-content-center h-100">
                                <i class="ti ti-photo text-muted" style="font-size: 2.5rem;"></i>
                            </div>
                        `}
                    </div>
                    
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="mb-1 fw-bold">${item.name}</h6>
                            <span class="badge bg-light-warning text-warning">${categoryName}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-primary">Rp ${formatPrice(item.price || 0)}</h5>
                        </div>
                    </div>
                </div>
            `;
            cardContainer.appendChild(cardCol);
        });

        // Add hover effect via CSS
        const style = document.createElement('style');
        style.textContent = `
            .topping-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            }
        `;
        if (!document.getElementById('topping-card-styles')) {
            style.id = 'topping-card-styles';
            document.head.appendChild(style);
        }
    }

    // Populate category select
    function populateCategorySelect() {
        const categorySelect = document.getElementById('menuCategory');

        if (!categorySelect) {
            console.log('Category select not found');
            return;
        }

        // Filter only topping categories
        const toppingCategories = categories.filter(cat => cat.type === 'topping' && cat.is_active);

        if (toppingCategories.length === 0) {
            console.log('No active topping categories found');
            return;
        }

        // Clear existing options except the first (placeholder)
        categorySelect.innerHTML = '<option value="">Pilih Kategori</option>';

        // Add category options
        toppingCategories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            categorySelect.appendChild(option);
        });

        console.log(`Loaded ${toppingCategories.length} topping categories`);
    }

    // Populate form for editing
    function populateForm(data) {
        console.log('Populating form with data:', data);

        // Basic form fields
        document.getElementById('menuName').value = data.name || '';
        document.getElementById('menuPrice').value = data.price || '';

        // Topping-specific fields
        const categorySelect = document.getElementById('menuCategory');
        if (categorySelect && data.category_id) {
            categorySelect.value = data.category_id;
        }

        document.getElementById('submitText').textContent = 'Update Topping';

        // Handle image data for edit mode - toppings table uses 'image' field
        const fileInput = document.getElementById('menuImageFile');
        if (fileInput) {
            // Clear any previous data
            fileInput.removeAttribute('data-existing-image');
            fileInput.removeAttribute('data-uploaded-url');
            fileInput.removeAttribute('data-has-file');

            if (data.image_url && data.image_url !== '0' && data.image_url !== 'null') {
                // Store the existing image URL as a data attribute
                fileInput.setAttribute('data-existing-image', data.image_url);
                console.log('Stored existing image:', data.image_url);

                // Show current image in preview
                updateImagePreviewForEdit(data.image_url);
            } else {
                // No existing image, show placeholder
                resetImagePreview();
            }
        }

        // Update preview with the loaded data
        updatePreview();
    }    // Initialize form
    function initForm() {
        populateCategorySelect();

        // Clear any upload status
        const fileInput = document.getElementById('menuImageFile');
        if (fileInput) {
            fileInput.value = '';
            fileInput.removeAttribute('data-uploaded-url');
            fileInput.removeAttribute('data-has-file');
        }

        // Reset image preview to placeholder for new forms
        resetImagePreview();

        const form = document.getElementById('menuForm');
        form.addEventListener('submit', handleFormSubmit);
    }

    // Handle form submit
    async function handleFormSubmit(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;

        // Show loading with SweetAlert
        showLoading('Menyimpan...', 'Sedang memproses data menu, tunggu sebentar...');

        try {
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            // Debug: Log what's in the form data
            console.log('Form data before processing:', data);

            // Handle image upload if file is selected
            const fileInput = document.getElementById('menuImageFile');
            const hasFile = fileInput.getAttribute('data-has-file') === 'true';
            const existingUrl = fileInput.getAttribute('data-uploaded-url');
            const existingImage = fileInput.getAttribute('data-existing-image');
            const actualFileExists = fileInput.files && fileInput.files[0];

            console.log('File detection:', {
                hasFile: hasFile,
                existingUrl: existingUrl,
                existingImage: existingImage,
                actualFileExists: actualFileExists,
                filesLength: fileInput.files ? fileInput.files.length : 0,
                isEditMode: !!currentEditId
            });

            if (actualFileExists) {
                console.log('Uploading new file:', fileInput.files[0].name);

                // Upload the file now
                const uploadFormData = new FormData();
                uploadFormData.append('image', fileInput.files[0]);

                const uploadResponse = await fetch('api/upload/image.php', {
                    method: 'POST',
                    body: uploadFormData
                });

                const uploadResult = await uploadResponse.json();
                console.log('Upload result:', uploadResult);

                if (uploadResult.success) {
                    data.image_url = uploadResult.data.relative_url;
                    console.log('Set image_url to:', data.image_url);
                } else {
                    throw new Error('Gagal mengupload gambar: ' + uploadResult.message);
                }
            } else if (existingUrl) {
                // Use existing uploaded URL (for edit mode)
                data.image_url = existingUrl;
                console.log('Using existing URL:', existingUrl);
            } else if (currentEditId && existingImage) {
                // Edit mode with existing image - keep the current image
                data.image_url = existingImage;
                console.log('Keeping existing image:', existingImage);
            } else {
                // No file uploaded and no existing image - set to empty string
                data.image_url = '';
                console.log('No image file, setting image_url to empty string');
            }

            // Remove file input data as it's not needed in JSON
            delete data.image_file;

            // Debug: Log final data being sent
            console.log('Final data being sent to API:', data);

            // Convert checkbox to boolean
            data.is_available = true; // Default to available for new toppings

            // Convert price to number
            data.price = parseFloat(data.price);

            let url = 'api/menu/toppings.php';
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

            // Hide loading
            hideAlert();

            if (result.success) {
                showSuccess('Berhasil!', result.message, () => {
                    showDataMenu(); // Return to data view after user closes success alert
                });
            } else {
                showError('Gagal!', result.message);
            }

        } catch (error) {
            console.error('Error:', error);
            hideAlert();
            showError('Kesalahan!', 'Terjadi kesalahan saat menghubungi server');
        }
    }

    // Delete menu
    function deleteMenu(id, name) {
        showDeleteConfirmation(name, async () => {
            // Show loading
            showLoading('Menghapus...', 'Sedang menghapus menu, tunggu sebentar...');

            try {
                const response = await fetch(`api/menu/toppings.php?id=${id}`, {
                    method: 'DELETE'
                });

                const result = await response.json();

                // Hide loading
                hideAlert();

                if (result.success) {
                    showSuccess('Berhasil!', result.message, () => {
                        loadMenuData(); // Reload data after user closes success alert
                    });
                } else {
                    showError('Gagal!', result.message);
                }

            } catch (error) {
                console.error('Error:', error);
                hideAlert();
                showError('Kesalahan!', 'Terjadi kesalahan saat menghubungi server');
            }
        });
    }

    // Restore menu item
    function restoreMenu(id, name) {
        showRestoreConfirmation(name, async () => {
            // Show loading
            showLoading('Memulihkan...', 'Sedang memulihkan menu, tunggu sebentar...');

            try {
                const response = await fetch(`api/menu/toppings.php?id=${id}&action=restore`, {
                    method: 'PATCH'
                });

                const result = await response.json();

                // Hide loading
                hideAlert();

                if (result.success) {
                    showSuccess('Berhasil!', result.message, () => {
                        loadMenuData(true); // Reload deleted items view after user closes success alert
                    });
                } else {
                    showError('Gagal!', result.message);
                }

            } catch (error) {
                console.error('Error:', error);
                hideAlert();
                showError('Kesalahan!', 'Terjadi kesalahan saat menghubungi server');
            }
        });
    }

    // Permanently delete menu item
    function permanentDeleteMenu(id, name) {
        showPermanentDeleteConfirmation(name, async () => {
            // Show loading
            showLoading('Menghapus Permanen...', 'Sedang menghapus menu secara permanen, tunggu sebentar...');

            try {
                const response = await fetch(`api/menu/toppings.php?id=${id}&action=permanent_delete`, {
                    method: 'PATCH'
                });

                const result = await response.json();

                // Hide loading
                hideAlert();

                if (result.success) {
                    showSuccess('Berhasil!', result.message, () => {
                        loadMenuData(true); // Reload deleted items view after user closes success alert
                    });
                } else {
                    showError('Gagal!', result.message);
                }

            } catch (error) {
                console.error('Error:', error);
                hideAlert();
                showError('Kesalahan!', 'Terjadi kesalahan saat menghubungi server');
            }
        });
    }

    // SweetAlert helper functions
    function showDeleteConfirmation(itemName, onConfirm) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus "${itemName}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    onConfirm();
                }
            });
        } else {
            // Fallback to native confirm if SweetAlert is not available
            if (confirm(`Apakah Anda yakin ingin menghapus "${itemName}"?`)) {
                onConfirm();
            }
        }
    }

    function showRestoreConfirmation(itemName, onConfirm) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Konfirmasi Pulihkan',
                text: `Apakah Anda yakin ingin memulihkan "${itemName}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Pulihkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    onConfirm();
                }
            });
        } else {
            // Fallback to native confirm if SweetAlert is not available
            if (confirm(`Apakah Anda yakin ingin memulihkan "${itemName}"?`)) {
                onConfirm();
            }
        }
    }

    function showPermanentDeleteConfirmation(itemName, onConfirm) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Peringatan!',
                html: `<div style="text-align: left;">
                    <p><strong>Anda akan menghapus PERMANEN:</strong></p>
                    <p style="color: #dc3545; font-weight: bold;">"${itemName}"</p>
                    <br>
                    <p style="color: #dc3545;"><i class="ti ti-alert-triangle"></i> <strong>PERHATIAN:</strong></p>
                    <ul style="text-align: left; color: #dc3545;">
                        <li>Data akan hilang SELAMANYA</li>
                        <li>Tidak dapat dikembalikan</li>
                        <li>Semua riwayat akan terhapus</li>
                    </ul>
                    <p style="margin-top: 15px;"><strong>Apakah Anda yakin?</strong></p>
                </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="ti ti-trash-x"></i> Ya, Hapus Permanen!',
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    onConfirm();
                }
            });
        } else {
            // Fallback to native confirm if SweetAlert is not available
            if (confirm(`PERINGATAN: Anda akan menghapus PERMANEN "${itemName}". Data tidak dapat dikembalikan. Apakah Anda yakin?`)) {
                onConfirm();
            }
        }
    }

    // Permanently delete all inactive toppings
    async function permanentDeleteInactiveToppings() {
        const result = await Swal.fire({
            title: 'PERINGATAN!',
            html: `<div style="text-align: left;">
                <p><strong>Anda akan menghapus PERMANEN SEMUA topping inaktif!</strong></p>
                <br>
                <p style="color: #dc3545;"><i class="ti ti-alert-triangle"></i> <strong>PERHATIAN:</strong></p>
                <ul style="text-align: left; color: #dc3545;">
                    <li>Semua topping inaktif akan dihapus SELAMANYA</li>
                    <li>Data tidak dapat dikembalikan</li>
                    <li>Semua riwayat topping akan terhapus</li>
                    <li>Tindakan ini tidak dapat dibatalkan</li>
                </ul>
                <p style="margin-top: 15px;"><strong>Apakah Anda yakin?</strong></p>
            </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="ti ti-trash-x"></i> Ya, Hapus Semua!',
            cancelButtonText: '<i class="ti ti-x"></i> Batal',
            reverseButtons: true,
            focusCancel: true
        });

        if (result.isConfirmed) {
            showLoading('Menghapus Permanen...', 'Sedang menghapus semua topping inaktif secara permanen...');

            try {
                const response = await fetch(`api/menu/toppings.php?action=permanent-delete-inactive`, {
                    method: 'PATCH'
                });

                const data = await response.json();
                hideAlert();

                if (data.success) {
                    showSuccess('Berhasil!', data.message, () => {
                        loadMenuData(true); // Reload deleted items view
                    });
                } else {
                    showError('Gagal!', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                hideAlert();
                showError('Kesalahan!', 'Terjadi kesalahan saat menghubungi server');
            }
        }
    }

    // Update permanent delete button visibility
    function updatePermanentDeleteButtonVisibility() {
        const permanentDeleteSection = document.getElementById('permanentDeleteSection');
        if (!permanentDeleteSection) return;

        // Show the button only when viewing inactive/deleted items
        if (currentViewMode === 'deleted') {
            permanentDeleteSection.style.display = 'block';
        } else {
            permanentDeleteSection.style.display = 'none';
        }
    }

    function showLoading(title, text) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: text,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        } else {
            console.log('Loading:', title, text);
        }
    }

    function showSuccess(title, text, callback) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: text,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                if (callback) callback();
            });
        } else {
            alert(`${title}: ${text}`);
            if (callback) callback();
        }
    }

    function showError(title, text) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: text,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } else {
            alert(`${title}: ${text}`);
        }
    }

    function showWarning(title, text) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                confirmButtonText: 'OK'
            });
        } else {
            alert(`${title}: ${text}`);
        }
    }

    function showInfo(title, text) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: text,
                icon: 'info',
                confirmButtonText: 'OK'
            });
        } else {
            alert(`${title}: ${text}`);
        }
    }

    function hideAlert() {
        if (typeof Swal !== 'undefined') {
            Swal.close();
        }
    }

    function showToast(type, message) {
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: type,
                title: message
            });
        } else {
            console.log(`Toast (${type}):`, message);
        }
    }

    // Utility functions
    function formatPrice(price) {
        return new Intl.NumberFormat('id-ID').format(price);
    }

    // Simple image URL handling - converts database filename to proper path
    function getImageUrl(imageUrl, size = 'medium') {
        // Handle invalid values: null, undefined, empty string, '0', 'null'
        if (!imageUrl || imageUrl === '0' || imageUrl === 'null' || imageUrl === 'undefined' || imageUrl.trim() === '') {
            // Return default placeholder based on size
            switch (size) {
                case 'small':
                    return 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=100&h=60&fit=crop';
                case 'large':
                    return 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=250&fit=crop';
                default:
                    return 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=300&h=200&fit=crop';
            }
        }

        // Check if it's already a full URL (external image)
        if (imageUrl.startsWith('http://') || imageUrl.startsWith('https://')) {
            return imageUrl;
        }

        // Check if it's already a full relative path (legacy support)
        if (imageUrl.startsWith('uploads/menu-images/')) {
            return imageUrl;
        }

        // If it's just a filename (current format), prepend the upload path
        return 'uploads/menu-images/' + imageUrl;
    }

    // Generate image HTML with placeholder fallback
    function getImageHTML(imageUrl, altText, size = 'medium') {
        // Check if image URL is valid
        const hasValidImage = imageUrl && imageUrl !== '0' && imageUrl !== 'null' && imageUrl !== 'undefined' && imageUrl.trim() !== '';

        if (!hasValidImage) {
            // Return placeholder HTML based on size
            return getPlaceholderHTML(size);
        }

        // Return image HTML with fallback to placeholder
        const imagePath = getImageUrl(imageUrl, size);
        const imageId = 'img_' + Math.random().toString(36).substr(2, 9);
        const placeholderId = 'placeholder_' + Math.random().toString(36).substr(2, 9);

        const { width, height, iconSize, textSize } = getImageDimensions(size);

        return `
            <img id="${imageId}" 
                 src="${imagePath}" 
                 alt="${altText}" 
                 class="img-fluid menu-image" 
                 style="width: ${width}; height: ${height}; object-fit: cover; border-radius: 6px; border: 1px solid #e9ecef;"
                 loading="lazy"
                 onerror="document.getElementById('${imageId}').style.display='none'; document.getElementById('${placeholderId}').style.display='flex';">
            <div id="${placeholderId}" 
                 class="image-placeholder text-center" 
                 style="display: none; width: ${width}; height: ${height}; background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px; align-items: center; justify-content: center; flex-direction: column;">
                <i class="ti ti-photo text-muted" style="font-size: ${iconSize};"></i>
                <p class="text-muted mt-1 mb-0" style="font-size: ${textSize};">No Image</p>
            </div>
        `;
    }

    // Get placeholder HTML for when no image URL exists
    function getPlaceholderHTML(size = 'medium') {
        const { width, height, iconSize, textSize } = getImageDimensions(size);

        return `
            <div class="image-placeholder text-center" 
                 style="width: ${width}; height: ${height}; background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                <i class="ti ti-photo text-muted" style="font-size: ${iconSize};"></i>
            </div>
        `;
    }

    // Get dimensions based on size
    function getImageDimensions(size) {
        switch (size) {
            case 'small':
                return {
                    width: '60px',
                    height: '40px',
                    iconSize: '1.2rem',
                    textSize: '0.6rem'
                };
            case 'large':
                return {
                    width: '100%',
                    height: '200px',
                    iconSize: '3rem',
                    textSize: '0.9rem'
                };
            default: // medium
                return {
                    width: '100%',
                    height: '150px',
                    iconSize: '2.5rem',
                    textSize: '0.8rem'
                };
        }
    }    // Get fallback image URL based on size
    function getFallbackImageUrl(size = 'medium') {
        switch (size) {
            case 'small':
                return 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=100&h=60&fit=crop';
            case 'large':
                return 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=250&fit=crop';
            default:
                return 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=300&h=200&fit=crop';
        }
    }

    // Verify if image exists on server (async)
    async function verifyImageExists(filename) {
        try {
            const response = await fetch(`api/check-image.php?filename=${encodeURIComponent(filename)}`);
            const result = await response.json();
            return result.success && result.exists;
        } catch (error) {
            console.error('Error checking image existence:', error);
            return false;
        }
    }

    // Smart image loading with fallback
    function loadImageWithFallback(imgElement, imageUrl, size = 'medium') {
        console.log('Loading image with fallback:', { imageUrl, size });

        // Show loading state
        const loadingDiv = imgElement.parentElement.querySelector('.image-loading');
        if (loadingDiv) {
            loadingDiv.style.display = 'flex';
        }

        // Set the primary image URL
        const primaryUrl = getImageUrl(imageUrl, size);

        // Set up the image with proper error handling
        imgElement.onload = function () {
            console.log('Image loaded successfully:', this.src);
            if (loadingDiv) {
                loadingDiv.style.display = 'none';
            }
            imgElement.style.opacity = '1';
            imgElement.classList.add('image-loaded');
        };

        imgElement.onerror = function () {
            console.log('Primary image failed, using fallback:', this.src);
            if (loadingDiv) {
                loadingDiv.style.display = 'none';
            }

            // Use fallback image
            this.src = getFallbackImageUrl(size);
            this.style.opacity = '1';
            this.classList.add('image-error');
            this.onerror = null; // Prevent infinite loop
        };

        // Set the src to start loading
        imgElement.src = primaryUrl;
    }

    // Handle file selection and preview (upload happens on form submit)
    function handleFileUpload(input) {
        const file = input.files[0];
        if (!file) {
            resetImagePreview();
            return;
        }

        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            showError('Format File Tidak Valid', 'Silakan pilih file gambar (JPG, PNG, GIF)');
            input.value = '';
            resetImagePreview();
            return;
        }

        // Validate file size (2MB)
        const maxSize = 2 * 1024 * 1024; // 2MB in bytes
        if (file.size > maxSize) {
            showError('File Terlalu Besar', 'Ukuran file maksimal 2MB');
            input.value = '';
            resetImagePreview();
            return;
        }

        // Store file for later upload
        input.setAttribute('data-has-file', 'true');

        // Show preview immediately using FileReader
        const reader = new FileReader();
        reader.onload = function (e) {
            const previewImage = document.getElementById('previewImage');
            const previewPlaceholder = document.getElementById('previewPlaceholder');

            if (previewImage && previewPlaceholder) {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
                previewPlaceholder.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);

        // Show success message for file selection
        showToast('success', 'Gambar dipilih!');
    }

    // Reset image preview to placeholder
    function resetImagePreview() {
        const previewImage = document.getElementById('previewImage');
        const previewPlaceholder = document.getElementById('previewPlaceholder');

        if (previewImage && previewPlaceholder) {
            previewImage.style.display = 'none';
            previewImage.src = '';
            previewPlaceholder.style.display = 'block';
        }
    }

    // Show upload progress animation
    function showUploadProgress() {
        const progressContainer = document.getElementById('uploadProgress');
        const progressBar = progressContainer.querySelector('.progress-bar');

        if (progressContainer && progressBar) {
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';

            // Animate progress
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 15 + 5; // 5-20% increments
                if (progress > 90) {
                    progress = 90; // Stop at 90% until upload completes
                    clearInterval(interval);
                }
                progressBar.style.width = progress + '%';
            }, 100);

            // Store interval ID for cleanup
            progressContainer.setAttribute('data-interval', interval);
        }
    }

    // Hide upload progress
    function hideUploadProgress() {
        const progressContainer = document.getElementById('uploadProgress');
        const progressBar = progressContainer.querySelector('.progress-bar');

        if (progressContainer && progressBar) {
            // Clear any running interval
            const interval = progressContainer.getAttribute('data-interval');
            if (interval) {
                clearInterval(interval);
                progressContainer.removeAttribute('data-interval');
            }

            // Complete progress to 100%
            progressBar.style.width = '100%';

            // Hide after brief delay
            setTimeout(() => {
                progressContainer.style.display = 'none';
                progressBar.style.width = '0%';
            }, 500);
        }
    }

    function showNotification(message, type = 'info') {
        // Use SweetAlert instead of Bootstrap toast
        switch (type) {
            case 'success':
                showSuccess('Berhasil!', message);
                break;
            case 'error':
                showError('Kesalahan!', message);
                break;
            case 'warning':
                showWarning('Peringatan!', message);
                break;
            default:
                showInfo('Informasi', message);
        }
    }

    // Pagination functions
    function updatePagination() {
        const totalItems = filteredMenuData.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);

        // Update pagination info for both table and card views
        const startItem = totalItems > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0;
        const endItem = Math.min(currentPage * itemsPerPage, totalItems);
        const paginationText = `Showing ${startItem} - ${endItem} of ${totalItems} entries`;

        // Update table view pagination info
        const tablePaginationInfo = document.getElementById('paginationInfo');
        if (tablePaginationInfo) {
            tablePaginationInfo.textContent = paginationText;
        }

        // Update card view pagination info
        const cardPaginationInfo = document.getElementById('cardPaginationInfo');
        if (cardPaginationInfo) {
            cardPaginationInfo.textContent = paginationText;
        }

        // Generate pagination controls for both views
        generatePaginationControls(totalPages, 'paginationControls'); // Table view
        generatePaginationControls(totalPages, 'cardPaginationControls'); // Card view
    }

    function generatePaginationControls(totalPages, containerId) {
        const paginationContainer = document.getElementById(containerId);
        if (!paginationContainer) return;

        paginationContainer.innerHTML = '';

        if (totalPages <= 1) return;

        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;" aria-label="Previous">
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
            firstLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(1); return false;">1</a>`;
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
            li.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>`;
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
            lastLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${totalPages}); return false;">${totalPages}</a>`;
            paginationContainer.appendChild(lastLi);
        }

        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;" aria-label="Next">
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

    // Image handling functions
    function handleImageLoad(img) {
        // Hide loading placeholder if it exists
        const loadingDiv = img.parentElement.querySelector('.image-loading');
        if (loadingDiv) {
            loadingDiv.style.display = 'none';
        }

        // Add loaded class for animations
        img.classList.add('image-loaded');
    }

    function handleImageError(img) {
        // Hide loading placeholder
        const loadingDiv = img.parentElement.querySelector('.image-loading');
        if (loadingDiv) {
            loadingDiv.style.display = 'none';
        }

        // Set fallback image with proper error handling
        if (!img.src.includes('unsplash.com')) {
            img.src = 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=300&h=200&fit=crop';
        }

        // Add error class for styling
        img.classList.add('image-error');
    }

    function preloadImage(src) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => resolve(img);
            img.onerror = () => reject(new Error(`Failed to load image: ${src}`));
            img.src = src;
        });
    }

    // Initialize all images in the current view
    function initializeImages() {
        console.log('Initializing images...');
        const images = document.querySelectorAll('img[data-image-url]');
        console.log('Found images to initialize:', images.length);

        images.forEach((img, index) => {
            const imageUrl = img.getAttribute('data-image-url');
            const size = img.getAttribute('data-size') || 'medium';

            console.log(`Initializing image ${index + 1}:`, { imageUrl, size });

            // Load image with smart fallback
            loadImageWithFallback(img, imageUrl, size);
        });
    }

    // Initialize image lazy loading and optimization
    function initializeImageOptimization() {
        // Add intersection observer for lazy loading if supported
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;

                        // Show loading state
                        const loadingDiv = img.parentElement.querySelector('.image-loading');
                        if (loadingDiv) {
                            loadingDiv.style.display = 'flex';
                        }

                        // Load the actual image
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }

                        observer.unobserve(img);
                    }
                });
            });

            // Observe all images with data-src attribute
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    // Call image optimization after DOM content is loaded
    document.addEventListener('DOMContentLoaded', function () {
        initializeImageOptimization();
    });
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

    /* Controls row layout - horizontal arrangement */
    .controls-row {
        display: flex;
        align-items: center;
        justify-content: start;
        gap: 1rem;
        padding: 1rem;
        flex-wrap: nowrap;
    }

    @media (max-width: 992px) {
        .controls-row {
            flex-wrap: wrap;
        }
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
        flex: 1 1 auto;
        min-width: 250px;
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
        width: 100%;
    }

    .search-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Filter section */
    .filter-section {
        flex: 0 0 auto;
        min-width: 120px;
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
        min-width: 200px;
        max-width: 220px;
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
        width: 100%;
    }

    .sort-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Active filters */
    .active-filters-container {
        padding: 0 1rem 1rem 1rem;
        border-top: 1px solid #e9ecef;
    }

    .active-filters-wrapper {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        padding-top: 1rem;
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

    #cardPaginationInfo {
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

    /* Form Container Styling */
    .table-light {
        background-color: #f8f9fa !important;
        border: 1px solid #dee2e6;
    }

    .card-header.table-light {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-bottom: 2px solid #dee2e6;
        color: white;
    }

    .card-body.table-light {
        background-color: #f8f9fa;
    }

    /* Enhanced form styling */
    .table-light .card {
        background-color: #ffffff;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease;
    }

    .table-light .card:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .table-light .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom: none;
    }

    /* Form field enhancements */
    .table-light .form-control:focus,
    .table-light .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Upload progress styling */
    .progress-bar {
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    }

    /* Preview card enhancements */
    .sticky-top {
        position: sticky !important;
        top: 20px !important;
    }

    /* Form title styling */
    #formTitle {
        color: white !important;
        font-weight: 700;
        font-size: 1.25rem;
    }

    /* Preview image placeholder styling */
    #previewImageContainer {
        border-radius: 0.375rem;
        transition: border-color 0.3s ease;
    }

    #previewImageContainer:hover {
        border-color: #adb5bd;
    }

    #previewPlaceholder {
        opacity: 0.7;
    }

    #previewPlaceholder i {
        color: #6c757d;
    }

    /* Responsive form layout */
    @media (max-width: 992px) {
        .sticky-top {
            position: relative !important;
            top: auto !important;
        }
    }
</style>