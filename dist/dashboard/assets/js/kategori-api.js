// ========================================
// KATEGORI API - MAIN SCRIPT
// ========================================

// Global variables
let allCategoriesData = [];
let filteredCategoriesData = [];
let currentPage = 1;
const itemsPerPage = 10;
let currentEditId = null;
let currentSort = 'created_at_desc';
let currentViewMode = 'active';
let activeFilters = new Map();

// ========================================
// INITIALIZATION
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('Kategori API loaded');
    showDataKategori();
});

// ========================================
// VIEW MANAGEMENT
// ========================================

// Show data kategori view
function showDataKategori() {
    console.log('showDataKategori called');
    const mainContent = document.getElementById('mainContentArea');

    // Update header
    document.getElementById('pageTitleText').textContent = 'Kategori';
    document.getElementById('breadcrumbText').textContent = 'Kategori';
    document.getElementById('cardTitleText').textContent = 'Data Kategori';
    document.getElementById('cardSubtitleText').classList.add('d-none');

    // Toggle buttons
    document.getElementById('btnTambahKategori').classList.remove('d-none');
    document.getElementById('btnKembali').classList.add('d-none');

    // Set content
    mainContent.innerHTML = getDataKategoriHTML();

    // Load data
    loadCategoryData();
}

// Show form for add/edit
function showForm(pageTitle, cardTitle, data = null) {
    const mainContent = document.getElementById('mainContentArea');

    // Update header
    document.getElementById('pageTitleText').textContent = pageTitle;
    document.getElementById('breadcrumbText').textContent = pageTitle;
    document.getElementById('cardTitleText').textContent = cardTitle;
    document.getElementById('cardSubtitleText').classList.remove('d-none');

    // Toggle buttons
    document.getElementById('btnTambahKategori').classList.add('d-none');
    document.getElementById('btnKembali').classList.remove('d-none');

    // Set content
    mainContent.innerHTML = getFormHTML();

    // Populate form if editing
    if (data) {
        document.getElementById('categoryName').value = data.name || '';
        document.getElementById('categoryType').value = data.type || 'product';
    }

    // Setup form submission
    const form = document.getElementById('categoryForm');
    form.addEventListener('submit', handleFormSubmit);
}

function showFormTambah() {
    currentEditId = null;
    showForm('Tambah Kategori', 'Form Tambah Kategori');
}

// ========================================
// DATA LOADING
// ========================================

async function loadCategoryData(showDeleted = false) {
    try {
        let url = 'api/menu/categories.php?per_page=1000';

        console.log('Loading category data from:', url);
        const response = await fetch(url);
        const result = await response.json();

        if (result.success) {
            allCategoriesData = result.data || [];

            // Filter based on view mode
            if (showDeleted) {
                filteredCategoriesData = allCategoriesData.filter(cat => !cat.is_active);
            } else {
                filteredCategoriesData = allCategoriesData.filter(cat => cat.is_active);
            }
            
            currentPage = 1;
            currentViewMode = showDeleted ? 'deleted' : 'active';
            
            console.log('Category data loaded:', allCategoriesData.length, 'items');
            updateInformationCards();
            applyFilters();
        } else {
            throw new Error(result.message || 'Failed to load data');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Gagal memuat data kategori: ' + error.message);
    }
}

// ========================================
// CRUD OPERATIONS
// ========================================

async function handleFormSubmit(e) {
    e.preventDefault();

    const name = document.getElementById('categoryName').value.trim();
    const type = document.getElementById('categoryType').value;

    if (!name) {
        showError('Nama kategori tidak boleh kosong');
        return;
    }

    const data = { name, type };

    try {
        showLoading();

        let url, method;
        if (currentEditId) {
            // Update existing
            url = `api/menu/categories.php?id=${currentEditId}`;
            method = 'PUT';
        } else {
            // Create new
            url = 'api/menu/categories.php';
            method = 'POST';
        }

        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showSuccess(currentEditId ? 'Kategori berhasil diperbarui' : 'Kategori berhasil ditambahkan');
            setTimeout(() => {
                showDataKategori();
            }, 1500);
        } else {
            showError(result.message || 'Gagal menyimpan kategori');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Terjadi kesalahan saat menyimpan data');
    }
}

async function editCategory(id) {
    try {
        const response = await fetch(`api/menu/categories.php?id=${id}`);
        const result = await response.json();

        if (result.success) {
            currentEditId = id;
            showForm('Edit Kategori', 'Form Edit Kategori', result.data);
        } else {
            showError('Gagal memuat data kategori: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Terjadi kesalahan saat memuat data');
    }
}

async function deleteCategory(id, name) {
    const result = await showDeleteConfirmation(name);
    
    if (result.isConfirmed) {
        try {
            showLoading();

            const response = await fetch(`api/menu/categories.php?id=${id}`, {
                method: 'DELETE'
            });

            const data = await response.json();

            if (data.success) {
                showSuccess('Kategori berhasil dihapus');
                loadCategoryData(currentViewMode === 'deleted');
            } else {
                showError(data.message || 'Gagal menghapus kategori');
            }
        } catch (error) {
            console.error('Error:', error);
            showError('Terjadi kesalahan saat menghapus data');
        }
    }
}

async function restoreCategory(id, name) {
    const result = await showRestoreConfirmation(name);
    
    if (result.isConfirmed) {
        try {
            showLoading();

            const response = await fetch(`api/menu/categories.php?id=${id}&action=restore`, {
                method: 'PATCH'
            });

            const data = await response.json();

            if (data.success) {
                showSuccess('Kategori berhasil dipulihkan');
                loadCategoryData(true); // Stay in deleted view
            } else {
                showError(data.message || 'Gagal memulihkan kategori');
            }
        } catch (error) {
            console.error('Error:', error);
            showError('Terjadi kesalahan saat memulihkan data');
        }
    }
}

async function permanentDeleteCategory(id, name) {
    const result = await showPermanentDeleteConfirmation(name);
    
    if (result.isConfirmed) {
        try {
            showLoading();

            const response = await fetch(`api/menu/categories.php?id=${id}&action=permanent-delete`, {
                method: 'PATCH'
            });

            const data = await response.json();

            if (data.success) {
                showSuccess('Kategori berhasil dihapus permanen');
                loadCategoryData(true); // Stay in deleted view
            } else {
                showError(data.message || 'Gagal menghapus permanen kategori');
            }
        } catch (error) {
            console.error('Error:', error);
            showError('Terjadi kesalahan saat menghapus permanen data');
        }
    }
}

// ========================================
// FILTER & SORT
// ========================================

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

function applyFilters() {
    filteredCategoriesData = [...allCategoriesData];

    // Apply view mode first
    if (currentViewMode === 'deleted') {
        filteredCategoriesData = filteredCategoriesData.filter(cat => !cat.is_active);
    } else {
        filteredCategoriesData = filteredCategoriesData.filter(cat => cat.is_active);
    }

    // Get search term
    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

    // Apply type filters
    const typeFilters = Array.from(activeFilters.values()).filter(filter => filter.type === 'type');
    if (typeFilters.length > 0) {
        const selectedTypes = typeFilters.map(filter => filter.value);
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

    // Apply sorting
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
            case 'updated_at_desc':
                return new Date(b.updated_at) - new Date(a.updated_at);
            case 'created_at_desc':
            default:
                return new Date(b.created_at) - new Date(a.created_at);
        }
    });

    // Update display
    currentPage = 1;
    displayCategoriesTable();
    displayCategoriesCards();
    updatePagination();
}

function applySorting() {
    const sortBy = document.getElementById('sortBy');
    currentSort = sortBy ? sortBy.value : 'created_at_desc';
    applyFilters();
}

// ========================================
// DISPLAY FUNCTIONS
// ========================================

function updateInformationCards() {
    const totalCategories = allCategoriesData.length;
    const activeCategories = allCategoriesData.filter(cat => cat.is_active).length;
    const deletedCategories = allCategoriesData.filter(cat => !cat.is_active).length;
    const toppingCategories = allCategoriesData.filter(cat => cat.type === 'topping' && cat.is_active).length;

    const totalElement = document.getElementById('totalCategoriesCount');
    const activeElement = document.getElementById('activeCategoriesCount');
    const deletedElement = document.getElementById('deletedCategoriesCount');
    const toppingElement = document.getElementById('toppingCategoriesCount');

    if (totalElement) totalElement.textContent = totalCategories;
    if (activeElement) activeElement.textContent = activeCategories;
    if (deletedElement) deletedElement.textContent = deletedCategories;
    if (toppingElement) toppingElement.textContent = toppingCategories;
}

function displayCategoriesTable() {
    const tableBody = document.getElementById('categoriesTableBody');
    if (!tableBody) return;

    tableBody.innerHTML = '';

    if (filteredCategoriesData.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="text-muted">
                        <i class="ti ti-search f-20 mb-2"></i>
                        <p class="mb-0">Tidak ada kategori yang ditemukan</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedData = filteredCategoriesData.slice(startIndex, endIndex);

    paginatedData.forEach((category, index) => {
        const row = document.createElement('tr');
        const globalIndex = startIndex + index + 1;

        row.innerHTML = `
            <td>${globalIndex}</td>
            <td><h6 class="mb-1">${escapeHtml(category.name)}</h6></td>
            <td>
                <span class="badge bg-light-${category.type === 'product' ? 'primary' : 'info'} text-${category.type === 'product' ? 'primary' : 'info'}">
                    ${category.type === 'product' ? 'Produk' : 'Topping'}
                </span>
            </td>
            <td>
                <span class="badge bg-light-${category.is_active ? 'success' : 'danger'} text-${category.is_active ? 'success' : 'danger'}">
                    ${category.is_active ? 'Aktif' : 'Nonaktif'}
                </span>
            </td>
            <td>
                <span class="text-dark f-12">${formatDate(category.created_at)}</span>
            </td>
            <td>
                <div class="btn-group" role="group">
                    ${renderActionButtons(category)}
                </div>
            </td>
        `;

        tableBody.appendChild(row);
    });
}

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
                            <h6 class="mb-0 fw-semibold">${escapeHtml(category.name)}</h6>
                            <small class="text-muted">ID: ${category.id.substring(0, 8)}...</small>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-light-${category.type === 'product' ? 'primary' : 'info'} text-${category.type === 'product' ? 'primary' : 'info'}">
                            ${category.type === 'product' ? 'Produk' : 'Topping'}
                        </span>
                        <span class="badge bg-light-${category.is_active ? 'success' : 'danger'} text-${category.is_active ? 'success' : 'danger'}">
                            ${category.is_active ? 'Aktif' : 'Dihapus'}
                        </span>
                    </div>
                    
                    <div class="text-muted mb-3">
                        <small class="d-block">Dibuat: ${formatDate(category.created_at)}</small>
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

function renderActionButtons(category) {
    if (category.is_active) {
        return `
            <button type="button" class="btn btn-sm btn-outline-warning" onclick="editCategory('${category.id}')" title="Edit Kategori">
                <i class="ti ti-edit"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCategory('${category.id}', '${escapeHtml(category.name)}')" title="Hapus Kategori">
                <i class="ti ti-trash"></i>
            </button>
        `;
    } else {
        return `
            <button type="button" class="btn btn-sm btn-outline-success" onclick="restoreCategory('${category.id}', '${escapeHtml(category.name)}')" title="Pulihkan Kategori">
                <i class="ti ti-refresh"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="permanentDeleteCategory('${category.id}', '${escapeHtml(category.name)}')" title="Hapus Permanen">
                <i class="ti ti-trash-x"></i>
            </button>
        `;
    }
}

// ========================================
// PAGINATION
// ========================================

function updatePagination() {
    const totalItems = filteredCategoriesData.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const paginationContainer = document.getElementById('paginationControls');
    const paginationInfo = document.getElementById('paginationInfo');

    if (paginationContainer) {
        if (totalPages <= 1) {
            paginationContainer.style.display = 'none';
        } else {
            paginationContainer.style.display = 'flex';
        }
    }

    const startItem = totalItems > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0;
    const endItem = Math.min(currentPage * itemsPerPage, totalItems);

    if (paginationInfo) {
        paginationInfo.textContent = `Showing ${startItem} - ${endItem} of ${totalItems} entries`;
    }

    if (totalPages > 1) {
        generatePaginationControls(totalPages);
    }
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

// Close dropdown on outside click
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('filterDropdown');
    const button = document.getElementById('filterButton');

    if (dropdown && dropdown.classList.contains('show')) {
        if (!dropdown.contains(e.target) && !button.contains(e.target)) {
            dropdown.classList.remove('show');
            button.classList.remove('active');
        }
    }
});

// ========================================
// UTILITY FUNCTIONS
// ========================================

function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return unsafe
        .toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
}

// ========================================
// SWEETALERT HELPERS
// ========================================

function showLoading() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Memproses...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
}

function showSuccess(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: message,
            timer: 2000,
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
            text: message,
            confirmButtonText: 'OK'
        });
    } else {
        alert(message);
    }
}

function showDeleteConfirmation(name) {
    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Apakah Anda yakin ingin menghapus kategori <strong>${escapeHtml(name)}</strong>?<br><small class="text-muted">Data dapat dipulihkan kembali nanti.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        });
    } else {
        return Promise.resolve({ isConfirmed: confirm(`Hapus kategori "${name}"?`) });
    }
}

function showRestoreConfirmation(name) {
    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            title: 'Konfirmasi Pulihkan',
            html: `Apakah Anda yakin ingin memulihkan kategori <strong>${escapeHtml(name)}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Pulihkan!',
            cancelButtonText: 'Batal'
        });
    } else {
        return Promise.resolve({ isConfirmed: confirm(`Pulihkan kategori "${name}"?`) });
    }
}

function showPermanentDeleteConfirmation(name) {
    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            title: 'PERINGATAN!',
            html: `Apakah Anda BENAR-BENAR yakin ingin menghapus PERMANEN kategori <strong>${escapeHtml(name)}</strong>?<br><br><span class="text-danger fw-bold">Tindakan ini TIDAK DAPAT dibatalkan!</span>`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus Permanen!',
            cancelButtonText: 'Batal',
            input: 'checkbox',
            inputValue: 0,
            inputPlaceholder: 'Saya memahami tindakan ini tidak dapat dibatalkan'
        }).then((result) => {
            if (result.isConfirmed && !result.value) {
                Swal.fire({
                    icon: 'info',
                    title: 'Konfirmasi diperlukan',
                    text: 'Silakan centang kotak konfirmasi untuk melanjutkan'
                });
                return { isConfirmed: false };
            }
            return result;
        });
    } else {
        return Promise.resolve({ 
            isConfirmed: confirm(`HAPUS PERMANEN kategori "${name}"?\n\nTindakan ini TIDAK DAPAT dibatalkan!`) 
        });
    }
}


// ========================================
// HTML TEMPLATES
// ========================================

function getDataKategoriHTML() {
    return `
        <div class="tab-content" id="pills-tabContent">
            <!-- Table View -->
            <div class="tab-pane fade show active" id="pills-table" role="tabpanel">
                <!-- Table Controls -->
                <div class="table-controls-section table-light p-3 mb-0 border rounded-top">
                    <div class="table-header-controls">
                        <!-- Search -->
                        <div class="search-section">
                            <div class="search-input-wrapper">
                                <i class="ti ti-search search-icon"></i>
                                <input type="text" class="form-control search-input" id="searchInput" 
                                       placeholder="Cari kategori..." onkeyup="applyFilters()">
                            </div>
                        </div>

                        <!-- Filter -->
                        <div class="filter-section">
                            <div class="filter-dropdown position-relative">
                                <button type="button" class="btn filter-btn" id="filterButton" onclick="toggleFilterDropdown()">
                                    <i class="ti ti-filter"></i>
                                    <span class="filter-text">Filters</span>
                                    <span class="filter-badge" id="filterBadge" style="display: none;">0</span>
                                </button>
                                
                                <div class="filter-dropdown-menu" id="filterDropdown">
                                    <div class="filter-dropdown-header">
                                        <h6 class="mb-0">Filter Options</h6>
                                        <button type="button" class="btn-close-filter" onclick="toggleFilterDropdown()">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="filter-dropdown-body">
                                        <!-- View Mode -->
                                        <div class="filter-group">
                                            <label class="filter-group-label">View Mode</label>
                                            <div class="filter-options">
                                                <label class="filter-option">
                                                    <input type="checkbox" class="filter-checkbox" data-filter="view_mode" data-value="deleted" onchange="handleViewModeChange(this)">
                                                    <span class="filter-icon">ÔøΩÔøΩÔ∏è</span>
                                                    <span class="filter-label">Tampilkan yang Dihapus</span>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Type Filters -->
                                        <div class="filter-group">
                                            <label class="filter-group-label">Tipe Kategori</label>
                                            <div class="filter-options">
                                                <label class="filter-option">
                                                    <input type="checkbox" class="filter-checkbox" data-filter="type" data-value="product" onchange="handleFilterChange(this)">
                                                    <span class="filter-icon">Ì≥Ç</span>
                                                    <span class="filter-label">Product</span>
                                                </label>
                                                <label class="filter-option">
                                                    <input type="checkbox" class="filter-checkbox" data-filter="type" data-value="topping" onchange="handleFilterChange(this)">
                                                    <span class="filter-icon">‚ûï</span>
                                                    <span class="filter-label">Topping</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sort -->
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

                    <!-- Active Filters -->
                    <div class="active-filters-container" id="activeFiltersContainer" style="display: none;">
                        <div class="active-filters-wrapper">
                            <span class="active-filters-label">Ìø∑Ô∏è Active:</span>
                            <div class="active-filters-tags" id="activeFiltersTags"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Table -->
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
                                        Loading...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="d-flex align-items-center">
                        <small class="text-muted" id="paginationInfo">Showing 0 - 0 of 0 entries</small>
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="paginationControls"></ul>
                    </nav>
                </div>
            </div>

            <!-- Card View -->
            <div class="tab-pane fade" id="pills-card" role="tabpanel">
                <div class="row" id="categoryCardContainer">
                    <div class="col-12 text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function getFormHTML() {
    return `
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form id="categoryForm">
                            <div class="mb-3">
                                <label for="categoryName" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="categoryName" required 
                                       placeholder="Masukkan nama kategori">
                            </div>

                            <div class="mb-3">
                                <label for="categoryType" class="form-label">Tipe <span class="text-danger">*</span></label>
                                <select class="form-select" id="categoryType" required>
                                    <option value="product">Product</option>
                                    <option value="topping">Topping</option>
                                </select>
                                <small class="text-muted">Pilih jenis kategori</small>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-1"></i> Simpan
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="showDataKategori()">
                                    <i class="ti ti-x me-1"></i> Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Informasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="ti ti-info-circle me-1"></i> Panduan</h6>
                            <ul class="mb-0 ps-3">
                                <li>Nama kategori harus unik</li>
                                <li>Pilih tipe Product untuk kategori menu</li>
                                <li>Pilih tipe Topping untuk kategori tambahan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

console.log('‚úÖ Kategori API fully loaded');
