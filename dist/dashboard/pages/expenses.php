<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-header-title">
                    <h5 class="m-b-10">Pengeluaran</h5>
                </div>
            </div>
            <div class="col-auto">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page">Pengeluaran</li>
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
        <div class="card bg-danger-dark dashnum-card text-white overflow-hidden">
            <span class="round small"></span>
            <span class="round big"></span>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avtar avtar-lg">
                        <i class="text-white ti ti-trending-down"></i>
                    </div>
                    <div class="ms-2">
                        <h4 class="text-white mb-1" id="todayExpenses">0</h4>
                        <p class="mb-0 opacity-75 text-sm">Pengeluaran Hari Ini</p>
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
                        <i class="text-white ti ti-calendar"></i>
                    </div>
                    <div class="ms-2">
                        <h4 class="text-white mb-1" id="monthExpenses">0</h4>
                        <p class="mb-0 opacity-75 text-sm">Pengeluaran Bulan Ini</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-info-dark dashnum-card text-white overflow-hidden">
            <span class="round small"></span>
            <span class="round big"></span>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avtar avtar-lg">
                        <i class="text-white ti ti-calculator"></i>
                    </div>
                    <div class="ms-2">
                        <h4 class="text-white mb-1" id="avgExpense">0</h4>
                        <p class="mb-0 opacity-75 text-sm">Rata-rata Pengeluaran</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-secondary-dark dashnum-card text-white overflow-hidden">
            <span class="round small"></span>
            <span class="round big"></span>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avtar avtar-lg">
                        <i class="text-white ti ti-package"></i>
                    </div>
                    <div class="ms-2">
                        <h4 class="text-white mb-1" id="totalCategories">0</h4>
                        <p class="mb-0 opacity-75 text-sm">Kategori Pengeluaran</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Statistics Cards ] end -->

    <!-- [ Action Buttons ] start -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Manajemen Pengeluaran</h6>
                    <div>
                        <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                            data-bs-target="#addExpenseModal">
                            <i class="ti ti-plus me-1"></i>Tambah Pengeluaran
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#addCategoryModal">
                            <i class="ti ti-tag me-1"></i>Kelola Kategori
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Action Buttons ] end -->

    <!-- [ Expenses Table ] start -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Daftar Pengeluaran</h5>
                <div class="card-header-right">
                    <div class="row g-2">
                        <div class="col-auto">
                            <input type="text" class="form-control" id="searchInput" placeholder="Cari pengeluaran...">
                        </div>
                        <div class="col-auto">
                            <select class="form-select" id="categoryFilter">
                                <option value="">Semua Kategori</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <input type="date" class="form-control" id="startDateFilter">
                        </div>
                        <div class="col-auto">
                            <input type="date" class="form-control" id="endDateFilter">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="expensesTable">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Metode Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="expensesTableBody">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div id="tableInfo">Menampilkan 0 dari 0 data</div>
                    <nav aria-label="Table pagination">
                        <ul class="pagination pagination-sm mb-0" id="pagination">
                            <!-- Pagination will be generated here -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Expenses Table ] end -->
</div>
<!-- [ Main Content ] end -->

<!-- [ Add Expense Modal ] start -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addExpenseModalLabel">Tambah Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addExpenseForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expenseTitle" class="form-label">Judul Pengeluaran <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="expenseTitle" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expenseCategory" class="form-label">Kategori <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="expenseCategory" name="category_id" required>
                                    <option value="">Pilih Kategori</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expenseAmount" class="form-label">Jumlah (Rp) <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="expenseAmount" name="amount" min="0"
                                    step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expenseDate" class="form-label">Tanggal Pengeluaran <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="expenseDate" name="expense_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="paymentMethod" class="form-label">Metode Pembayaran</label>
                                <select class="form-select" id="paymentMethod" name="payment_method">
                                    <option value="cash">Tunai</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="debit">Kartu Debit</option>
                                    <option value="credit">Kartu Kredit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="receiptImage" class="form-label">Gambar Kwitansi</label>
                                <input type="file" class="form-control" id="receiptImage" name="receipt_image"
                                    accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="expenseDescription" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="expenseDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- [ Add Expense Modal ] end -->

<!-- [ Edit Expense Modal ] start -->
<div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editExpenseModalLabel">Edit Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editExpenseForm">
                <input type="hidden" id="editExpenseId" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editExpenseTitle" class="form-label">Judul Pengeluaran <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editExpenseTitle" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editExpenseCategory" class="form-label">Kategori <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="editExpenseCategory" name="category_id" required>
                                    <option value="">Pilih Kategori</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editExpenseAmount" class="form-label">Jumlah (Rp) <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="editExpenseAmount" name="amount" min="0"
                                    step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editExpenseDate" class="form-label">Tanggal Pengeluaran <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="editExpenseDate" name="expense_date"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editPaymentMethod" class="form-label">Metode Pembayaran</label>
                                <select class="form-select" id="editPaymentMethod" name="payment_method">
                                    <option value="cash">Tunai</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="debit">Kartu Debit</option>
                                    <option value="credit">Kartu Kredit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editReceiptImage" class="form-label">Gambar Kwitansi</label>
                                <input type="file" class="form-control" id="editReceiptImage" name="receipt_image"
                                    accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editExpenseDescription" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="editExpenseDescription" name="description"
                            rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- [ Edit Expense Modal ] end -->

<!-- [ Add Category Modal ] start -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Kelola Kategori Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <button type="button" class="btn btn-success btn-sm mb-3" id="addNewCategoryBtn">
                        <i class="ti ti-plus me-1"></i>Tambah Kategori Baru
                    </button>
                </div>
                <div id="categoriesList">
                    <!-- Categories will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Add Category Modal ] end -->

<!-- [ Add New Category Form ] start -->
<div class="modal fade" id="newCategoryModal" tabindex="-1" aria-labelledby="newCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newCategoryModalLabel">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="newCategoryForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Nama Kategori <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoryName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="categoryDescription" name="description" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoryColor" class="form-label">Warna</label>
                                <input type="color" class="form-control" id="categoryColor" name="color"
                                    value="#A8A8A8">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoryIcon" class="form-label">Ikon</label>
                                <select class="form-select" id="categoryIcon" name="icon">
                                    <option value="ti ti-package">Package</option>
                                    <option value="ti ti-bolt">Bolt</option>
                                    <option value="ti ti-users">Users</option>
                                    <option value="ti ti-building">Building</option>
                                    <option value="ti ti-tools">Tools</option>
                                    <option value="ti ti-speakerphone">Speakerphone</option>
                                    <option value="ti ti-dots">Dots</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- [ Add New Category Form ] end -->

<script>
    // Global variables
    let currentPage = 1;
    let currentFilters = {};

    // Initialize page
    document.addEventListener('DOMContentLoaded', function () {
        loadStatistics();
        loadCategories();
        loadExpenses();
        setupEventListeners();
        setDefaultDates();
    });

    // Set default dates for filters
    function setDefaultDates() {
        const today = new Date();
        const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

        document.getElementById('startDateFilter').value = firstDayOfMonth.toISOString().split('T')[0];
        document.getElementById('endDateFilter').value = today.toISOString().split('T')[0];
    }

    // Setup event listeners
    function setupEventListeners() {
        // Search and filter events
        document.getElementById('searchInput').addEventListener('input', debounce(loadExpenses, 500));
        document.getElementById('categoryFilter').addEventListener('change', () => loadExpenses());
        document.getElementById('startDateFilter').addEventListener('change', () => loadExpenses());
        document.getElementById('endDateFilter').addEventListener('change', () => loadExpenses());

        // Form submissions
        document.getElementById('addExpenseForm').addEventListener('submit', handleAddExpense);
        document.getElementById('editExpenseForm').addEventListener('submit', handleEditExpense);
        document.getElementById('newCategoryForm').addEventListener('submit', handleAddCategory);

        // Modal events
        document.getElementById('addNewCategoryBtn').addEventListener('click', () => {
            const modal = new bootstrap.Modal(document.getElementById('newCategoryModal'));
            modal.show();
        });
    }

    // Load statistics
    async function loadStatistics() {
        try {
            const response = await fetch('api/expenses.php');
            const data = await response.json();

            if (data.success) {
                document.getElementById('todayExpenses').textContent = formatCurrency(data.statistics.today_expenses);
                document.getElementById('monthExpenses').textContent = formatCurrency(data.statistics.month_expenses);
                document.getElementById('avgExpense').textContent = formatCurrency(data.statistics.avg_expense);
            }
        } catch (error) {
            console.error('Error loading statistics:', error);
        }

        // Load category count
        try {
            const response = await fetch('api/expense-categories.php');
            const data = await response.json();

            if (data.success) {
                document.getElementById('totalCategories').textContent = data.meta.total;
            }
        } catch (error) {
            console.error('Error loading category count:', error);
        }
    }

    // Load categories for dropdowns
    async function loadCategories() {
        try {
            const response = await fetch('api/expense-categories.php');
            const data = await response.json();

            if (data.success) {
                const categorySelects = ['expenseCategory', 'editExpenseCategory', 'categoryFilter'];

                categorySelects.forEach(selectId => {
                    const select = document.getElementById(selectId);
                    select.innerHTML = selectId === 'categoryFilter' ? '<option value="">Semua Kategori</option>' : '<option value="">Pilih Kategori</option>';

                    data.data.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        select.appendChild(option);
                    });
                });

                // Load categories list for management modal
                loadCategoriesList(data.data);
            }
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }

    // Load categories list for management
    function loadCategoriesList(categories) {
        const container = document.getElementById('categoriesList');
        container.innerHTML = '';

        categories.forEach(category => {
            const categoryDiv = document.createElement('div');
            categoryDiv.className = 'd-flex justify-content-between align-items-center border rounded p-2 mb-2';
            categoryDiv.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="me-2" style="color: ${category.color}; font-size: 1.2em;">
                    <i class="${category.icon}"></i>
                </div>
                <div>
                    <strong>${category.name}</strong>
                    ${category.description ? `<br><small class="text-muted">${category.description}</small>` : ''}
                </div>
            </div>
            <div>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory('${category.id}')">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
        `;
            container.appendChild(categoryDiv);
        });
    }

    // Load expenses
    async function loadExpenses(page = 1) {
        currentPage = page;

        const search = document.getElementById('searchInput').value;
        const categoryId = document.getElementById('categoryFilter').value;
        const startDate = document.getElementById('startDateFilter').value;
        const endDate = document.getElementById('endDateFilter').value;

        currentFilters = { search, category_id: categoryId, start_date: startDate, end_date: endDate };

        const params = new URLSearchParams({
            page,
            per_page: 20,
            ...currentFilters
        });

        try {
            const response = await fetch(`api/expenses.php?${params}`);
            const data = await response.json();

            if (data.success) {
                renderExpensesTable(data.data);
                renderPagination(data.meta);
                updateTableInfo(data.meta);
            }
        } catch (error) {
            console.error('Error loading expenses:', error);
        }
    }

    // Render expenses table
    function renderExpensesTable(expenses) {
        const tbody = document.getElementById('expensesTableBody');
        tbody.innerHTML = '';

        if (expenses.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">Tidak ada data pengeluaran</td></tr>';
            return;
        }

        expenses.forEach(expense => {
            const row = document.createElement('tr');
            row.innerHTML = `
            <td>${formatDate(expense.expense_date)}</td>
            <td>
                <strong>${expense.title}</strong>
                ${expense.description ? `<br><small class="text-muted">${expense.description}</small>` : ''}
            </td>
            <td>
                <span class="badge" style="background-color: ${expense.category_color}; color: white;">
                    <i class="${expense.category_icon} me-1"></i>${expense.category_name}
                </span>
            </td>
            <td><strong>${formatCurrency(expense.amount)}</strong></td>
            <td>
                <span class="badge bg-secondary">${formatPaymentMethod(expense.payment_method)}</span>
            </td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="editExpense('${expense.id}')">
                        <i class="ti ti-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteExpense('${expense.id}')">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
            </td>
        `;
            tbody.appendChild(row);
        });
    }

    // Render pagination
    function renderPagination(meta) {
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        if (meta.last_page <= 1) return;

        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${meta.page <= 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" onclick="loadExpenses(${meta.page - 1})">Previous</a>`;
        pagination.appendChild(prevLi);

        // Page numbers
        const startPage = Math.max(1, meta.page - 2);
        const endPage = Math.min(meta.last_page, meta.page + 2);

        for (let i = startPage; i <= endPage; i++) {
            const pageLi = document.createElement('li');
            pageLi.className = `page-item ${i === meta.page ? 'active' : ''}`;
            pageLi.innerHTML = `<a class="page-link" href="#" onclick="loadExpenses(${i})">${i}</a>`;
            pagination.appendChild(pageLi);
        }

        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${meta.page >= meta.last_page ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#" onclick="loadExpenses(${meta.page + 1})">Next</a>`;
        pagination.appendChild(nextLi);
    }

    // Update table info
    function updateTableInfo(meta) {
        const start = (meta.page - 1) * meta.per_page + 1;
        const end = Math.min(meta.page * meta.per_page, meta.total);
        document.getElementById('tableInfo').textContent = `Menampilkan ${start} sampai ${end} dari ${meta.total} data`;
    }

    // Handle add expense
    async function handleAddExpense(e) {
        e.preventDefault();

        const formData = new FormData(e.target);

        try {
            const response = await fetch('api/expenses.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showToast('Pengeluaran berhasil ditambahkan', 'success');
                bootstrap.Modal.getInstance(document.getElementById('addExpenseModal')).hide();
                e.target.reset();
                loadExpenses();
                loadStatistics();
            } else {
                showToast(data.message || 'Gagal menambah pengeluaran', 'error');
            }
        } catch (error) {
            console.error('Error adding expense:', error);
            showToast('Terjadi kesalahan saat menambah pengeluaran', 'error');
        }
    }

    // Handle edit expense
    async function handleEditExpense(e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        const expenseId = document.getElementById('editExpenseId').value;

        try {
            const response = await fetch(`api/expenses.php?id=${expenseId}`, {
                method: 'PUT',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showToast('Pengeluaran berhasil diupdate', 'success');
                bootstrap.Modal.getInstance(document.getElementById('editExpenseModal')).hide();
                loadExpenses();
                loadStatistics();
            } else {
                showToast(data.message || 'Gagal mengupdate pengeluaran', 'error');
            }
        } catch (error) {
            console.error('Error updating expense:', error);
            showToast('Terjadi kesalahan saat mengupdate pengeluaran', 'error');
        }
    }

    // Handle add category
    async function handleAddCategory(e) {
        e.preventDefault();

        const formData = new FormData(e.target);

        try {
            const response = await fetch('api/expense-categories.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showToast('Kategori berhasil ditambahkan', 'success');
                bootstrap.Modal.getInstance(document.getElementById('newCategoryModal')).hide();
                e.target.reset();
                loadCategories();
                loadStatistics();
            } else {
                showToast(data.message || 'Gagal menambah kategori', 'error');
            }
        } catch (error) {
            console.error('Error adding category:', error);
            showToast('Terjadi kesalahan saat menambah kategori', 'error');
        }
    }

    // Edit expense
    async function editExpense(expenseId) {
        try {
            const response = await fetch(`api/expenses.php?id=${expenseId}`);
            const data = await response.json();

            if (data.success) {
                const expense = data.data;

                document.getElementById('editExpenseId').value = expense.id;
                document.getElementById('editExpenseTitle').value = expense.title;
                document.getElementById('editExpenseCategory').value = expense.category_id;
                document.getElementById('editExpenseAmount').value = expense.amount;
                document.getElementById('editExpenseDate').value = expense.expense_date;
                document.getElementById('editPaymentMethod').value = expense.payment_method;
                document.getElementById('editExpenseDescription').value = expense.description || '';

                const modal = new bootstrap.Modal(document.getElementById('editExpenseModal'));
                modal.show();
            }
        } catch (error) {
            console.error('Error loading expense for edit:', error);
            showToast('Terjadi kesalahan saat memuat data pengeluaran', 'error');
        }
    }

    // Delete expense
    async function deleteExpense(expenseId) {
        if (!confirm('Apakah Anda yakin ingin menghapus pengeluaran ini?')) return;

        try {
            const response = await fetch(`api/expenses.php?id=${expenseId}`, {
                method: 'DELETE'
            });

            const data = await response.json();

            if (data.success) {
                showToast('Pengeluaran berhasil dihapus', 'success');
                loadExpenses();
                loadStatistics();
            } else {
                showToast(data.message || 'Gagal menghapus pengeluaran', 'error');
            }
        } catch (error) {
            console.error('Error deleting expense:', error);
            showToast('Terjadi kesalahan saat menghapus pengeluaran', 'error');
        }
    }

    // Delete category
    async function deleteCategory(categoryId) {
        if (!confirm('Apakah Anda yakin ingin menghapus kategori ini?')) return;

        try {
            const response = await fetch(`api/expense-categories.php?id=${categoryId}`, {
                method: 'DELETE'
            });

            const data = await response.json();

            if (data.success) {
                showToast('Kategori berhasil dihapus', 'success');
                loadCategories();
                loadStatistics();
            } else {
                showToast(data.message || 'Gagal menghapus kategori', 'error');
            }
        } catch (error) {
            console.error('Error deleting category:', error);
            showToast('Terjadi kesalahan saat menghapus kategori', 'error');
        }
    }

    // Utility functions
    function formatCurrency(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    function formatPaymentMethod(method) {
        const methods = {
            'cash': 'Tunai',
            'transfer': 'Transfer',
            'debit': 'Kartu Debit',
            'credit': 'Kartu Kredit'
        };
        return methods[method] || method;
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function showToast(message, type = 'info') {
        // Simple toast implementation - you can replace with a proper toast library
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
</script>