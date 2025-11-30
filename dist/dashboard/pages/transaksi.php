<?php
// Load Midtrans configuration
if (file_exists(__DIR__ . '/../../../api/midtrans/config.php')) {
    require_once __DIR__ . '/../../../api/midtrans/config.php';
}
?>
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-header-title">
                    <h5 class="m-b-10" id="pageTitleText">Transaksi</h5>
                </div>
            </div>
            <div class="col-auto">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page" id="breadcrumbText">Transaksi</li>
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
                        <b class="text-primary" id="totalOrders">0</b>
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
                        <b class="text-success" id="completedOrders">0</b>
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
                        <b class="text-warning" id="pendingOrders">0</b>
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
                        <b class="text-info" id="totalRevenue">Rp 0</b>
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
                <div class="row align-items-center">
                    <div class="col">
                        <h5 id="cardTitleText">Data Transaksi</h5>
                        <p class="text-muted mb-0 d-none" id="cardSubtitleText">Form transaksi baru</p>
                    </div>
                    <div class="col-auto">
                        <button class="d-flex btn btn-primary" id="btnNewOrder" onclick="showNewOrderForm()">
                            <i class="ti ti-plus me-2"></i> Transaksi Baru
                        </button>
                        <button class="d-flex btn btn-secondary d-none" id="btnBackToList" onclick="showOrderList()">
                            <i class="ti ti-arrow-left me-2"></i> Kembali
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Main Content Area -->
                <div id="mainContentArea">
                    <!-- Order list will be loaded here -->
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
<!-- [ Main Content ] end -->

<!-- Topping Selection Modal - Clean & Minimal Design -->
<div class="modal fade" id="toppingModal" tabindex="-1" aria-labelledby="toppingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <!-- Minimal Header -->
            <div class="modal-header border-0 pb-2" style="background: #fff;">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <h5 class="modal-title fw-semibold mb-1" id="toppingModalLabel" style="color: #1f2937;">Pilih
                            Topping</h5>
                        <div class="d-flex align-items-center gap-3 mt-2">
                            <span class="badge bg-light text-dark border" style="font-weight: 500; padding: 6px 12px;">
                                <i class="ti ti-shopping-bag me-1" style="font-size: 14px;"></i>
                                <span id="modalSelectedCounter">0 item</span>
                            </span>
                            <span class="badge bg-light text-success border border-success"
                                style="font-weight: 500; padding: 6px 12px;">
                                <span id="modalTotalPrice">Rp 0</span>
                            </span>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 pt-3" style="background: #f9fafb;">
                <!-- Clean Search Bar -->
                <div class="mb-3">
                    <div class="position-relative">
                        <i class="ti ti-search position-absolute"
                            style="left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 18px;"></i>
                        <input type="text" id="modalToppingSearch" class="form-control border-0 shadow-sm"
                            placeholder="Cari topping..." autocomplete="off"
                            style="padding-left: 42px; border-radius: 10px; background: white; height: 44px;">
                    </div>
                </div>

                <!-- Minimal Category Filters -->
                <div class="mb-4">
                    <div class="d-flex gap-2 flex-wrap" id="modalCategoryFilters" style="margin-top: -4px;">
                        <!-- Category filters will be inserted here -->
                    </div>
                </div>

                <!-- Clean Selected Summary -->
                <div id="modalSelectedSummary" class="mb-4" style="display: none;">
                    <div class="p-3 bg-white border-0 shadow-sm" style="border-radius: 10px;">
                        <div class="d-flex align-items-center mb-2">
                            <i class="ti ti-check-circle text-success me-2" style="font-size: 18px;"></i>
                            <span class="fw-semibold" style="color: #1f2937; font-size: 14px;">Terpilih</span>
                        </div>
                        <div id="modalSelectedItems" class="d-flex flex-wrap gap-2">
                            <!-- Selected items badges will be shown here -->
                        </div>
                    </div>
                </div>

                <!-- Clean Toppings Grid -->
                <div class="row g-3" id="modalToppingsGrid"
                    style="max-height: 450px; overflow-y: auto; padding-right: 4px;">
                    <!-- Topping cards will be rendered here -->
                </div>
            </div>

            <!-- Minimal Footer -->
            <div class="modal-footer border-0 pt-3" style="background: #fff;">
                <div class="d-flex gap-2 w-100">
                    <button type="button" class="btn btn-sm text-danger border-0" id="btnClearAllToppings"
                        style="background: #fef2f2; border-radius: 8px; padding: 8px 16px;">
                        <i class="ti ti-trash" style="font-size: 16px;"></i>
                    </button>
                    <div class="flex-fill"></div>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="border-radius: 8px; padding: 8px 20px; font-weight: 500;">
                        Batal
                    </button>
                    <button type="button" class="btn btn-dark" id="btnConfirmToppings"
                        style="border-radius: 8px; padding: 8px 24px; font-weight: 500;">
                        <i class="ti ti-check me-1"></i>Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Midtrans Snap JS -->
<!-- Automatically loads Production or Sandbox based on MIDTRANS_IS_PRODUCTION config -->
<script type="text/javascript"
    src="<?php echo MIDTRANS_IS_PRODUCTION ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js'; ?>"
    data-client-key="<?php echo MIDTRANS_CLIENT_KEY; ?>"></script>

<script>
    // Debug: Check Midtrans configuration
    console.log('Midtrans Mode:', '<?php echo MIDTRANS_IS_PRODUCTION ? 'PRODUCTION' : 'SANDBOX'; ?>');
    console.log('Midtrans Client Key:', '<?php echo substr(MIDTRANS_CLIENT_KEY, 0, 20); ?>...');
    console.log('Snap.js loaded from:', '<?php echo MIDTRANS_IS_PRODUCTION ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js'; ?>');

    // Global variables
    let allOrders = [];
    let allProducts = [];
    let allToppings = [];
    let allSpiceLevels = [];
    let allCustomizationOptions = [];
    let selectedItemId = '';
    let currentOrder = {
        customer_name: '',
        table_number: '',
        notes: '',
        payment_method: 'cash',
        items: []
    };
    // Pagination variables
    let currentPage = 1;
    const itemsPerPage = 10;

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        loadProducts();
        loadToppings();
        loadSpiceLevels();
        loadCustomizationOptions();
        showOrderList();

        // Event delegation for topping quantity buttons
        document.addEventListener('click', function (e) {
            // Handle decrease topping quantity
            if (e.target.closest('.btn-decrease-topping')) {
                const btn = e.target.closest('.btn-decrease-topping');
                const orderId = btn.getAttribute('data-order-id');
                const toppingId = btn.getAttribute('data-topping-id');
                updateToppingQtyInOrder(orderId, toppingId, -1);
            }

            // Handle increase topping quantity
            if (e.target.closest('.btn-increase-topping')) {
                const btn = e.target.closest('.btn-increase-topping');
                const orderId = btn.getAttribute('data-order-id');
                const toppingId = btn.getAttribute('data-topping-id');
                updateToppingQtyInOrder(orderId, toppingId, 1);
            }
        });
    });

    // Load products
    async function loadProducts() {
        try {
            const response = await fetch('api/menu/products.php?is_active=1&is_topping=0');

            if (!response.ok) {
                console.error('Products API error:', response.status);
                return;
            }

            const result = await response.json();
            console.log('Products loaded:', result.data?.length || 0);

            if (result.success) {
                allProducts = result.data || [];
            }
        } catch (error) {
            console.error('Error loading products:', error);
        }
    }

    // Load toppings
    async function loadToppings() {
        try {
            const response = await fetch('api/menu/toppings.php?is_available=1');

            if (!response.ok) {
                console.error('Toppings API error:', response.status);
                return;
            }

            const result = await response.json();
            console.log('Toppings loaded:', result.data?.length || 0);

            if (result.success) {
                allToppings = result.data || [];
            }
        } catch (error) {
            console.error('Error loading toppings:', error);
        }
    }

    // Load spice levels
    async function loadSpiceLevels() {
        try {
            const response = await fetch('api/spice-levels.php?status=active');

            if (!response.ok) {
                console.error('Spice levels API error:', response.status);
                return;
            }

            const result = await response.json();
            console.log('Spice levels loaded:', result.data?.length || 0);

            if (result.success) {
                allSpiceLevels = result.data || [];
            }
        } catch (error) {
            console.error('Error loading spice levels:', error);
        }
    }

    // Load customization options
    async function loadCustomizationOptions() {
        try {
            const response = await fetch('api/customization-options.php?is_active=1');

            if (!response.ok) {
                console.error('Customization options API error:', response.status);
                return;
            }

            const result = await response.json();
            console.log('Customization options loaded:', result.data?.length || 0);

            if (result.success) {
                allCustomizationOptions = result.data || [];
            }
        } catch (error) {
            console.error('Error loading customization options:', error);
        }
    }

    // Load orders with filters
    async function loadOrders() {
        try {
            // Get filter values
            const filterStatus = document.getElementById('filterStatus')?.value || '';
            const filterDate = document.getElementById('filterDate')?.value || '';

            // Build query parameters
            const params = new URLSearchParams();
            if (filterStatus) params.append('status', filterStatus);
            if (filterDate) params.append('date', filterDate);

            const queryString = params.toString();
            const url = queryString ? `api/orders.php?${queryString}` : 'api/orders.php';

            const response = await fetch(url);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log('Orders API Response:', result);

            if (result.success) {
                allOrders = result.data || [];
                updateStatistics(result.statistics || {});
                displayOrders();
            } else {
                console.error('API returned error:', result.message);
                showNotification(result.message || 'Error loading orders', 'error');
            }
        } catch (error) {
            console.error('Error loading orders:', error);
            showNotification('Error: ' + error.message, 'error');
        }
    }

    // Update statistics
    function updateStatistics(stats) {
        document.getElementById('totalOrders').textContent = stats.total_orders || 0;
        document.getElementById('completedOrders').textContent = stats.completed_orders || 0;
        document.getElementById('pendingOrders').textContent = stats.pending_orders || 0;
        document.getElementById('totalRevenue').textContent = 'Rp ' + formatPrice(stats.total_revenue || 0);
    }

    // Show order list
    // Reset new order form
    function resetNewOrderForm() {
        currentOrder = {
            customer_name: '',
            order_type: 'dine_in',
            table_number: '',
            pickup_time: '',
            delivery_address: '',
            notes: '',
            payment_method: 'cash',
            items: [],
            currentStep: 1
        };

        // Clear form inputs
        const form = document.getElementById('orderForm');
        if (form) {
            form.reset();
        }

        console.log('Order form reset');
    }

    function showOrderList() {
        document.getElementById('pageTitleText').textContent = 'Transaksi';
        document.getElementById('breadcrumbText').textContent = 'Transaksi';
        document.getElementById('cardTitleText').textContent = 'Data Transaksi';
        document.getElementById('cardSubtitleText').classList.add('d-none');
        document.getElementById('btnNewOrder').classList.remove('d-none');
        document.getElementById('btnBackToList').classList.add('d-none');

        const mainContent = document.getElementById('mainContentArea');
        mainContent.innerHTML = getOrderListHTML();

        loadOrders();
    }

    // Show new order form
    function showNewOrderForm() {
        document.getElementById('pageTitleText').textContent = 'Transaksi Baru';
        document.getElementById('breadcrumbText').textContent = 'Transaksi Baru';
        document.getElementById('cardTitleText').textContent = 'Transaksi Baru - Step 1: Informasi Pelanggan';
        document.getElementById('cardSubtitleText').classList.remove('d-none');
        document.getElementById('btnNewOrder').classList.add('d-none');
        document.getElementById('btnBackToList').classList.remove('d-none');

        // Reset current order
        currentOrder = {
            customer_name: '',
            order_type: 'dine_in',
            table_number: '',
            pickup_time: '',
            delivery_address: '',
            notes: '',
            payment_method: 'cash',
            items: [],
            currentStep: 1
        };

        const mainContent = document.getElementById('mainContentArea');
        mainContent.innerHTML = getStepWizardHTML();

        // Render first step
        setTimeout(() => renderStep(1), 100);
    }

    // Get step wizard HTML
    function getStepWizardHTML() {
        return `
            <!-- Progress Steps -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center position-relative">
                        <div class="flex-grow-1 position-relative" style="height: 4px; background: #e9ecef; margin: 0 20px;">
                            <div id="progressBar" class="position-absolute h-100 bg-primary" style="width: 0%; transition: width 0.3s;"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <div class="text-center" style="width: 33.33%;">
                            <div class="step-circle" id="stepCircle1">
                                <i class="ti ti-user"></i>
                            </div>
                            <small class="d-block mt-2 fw-bold" id="stepLabel1">Informasi Pelanggan</small>
                        </div>
                        <div class="text-center" style="width: 33.33%;">
                            <div class="step-circle" id="stepCircle2">
                                <i class="ti ti-shopping-cart"></i>
                            </div>
                            <small class="d-block mt-2" id="stepLabel2">Informasi Pesanan</small>
                        </div>
                        <div class="text-center" style="width: 33.33%;">
                            <div class="step-circle" id="stepCircle3">
                                <i class="ti ti-cash"></i>
                            </div>
                            <small class="d-block mt-2" id="stepLabel3">Pembayaran</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step Content -->
            <div id="stepContent"></div>

            <!-- Navigation Buttons -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" id="btnPrevStep" onclick="previousStep()" style="display: none;">
                            <i class="ti ti-arrow-left"></i> Kembali
                        </button>
                        <button type="button" class="btn btn-primary" id="btnNextStep" onclick="nextStep()">
                            Selanjutnya <i class="ti ti-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <style>
                .step-circle {
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    background: #e9ecef;
                    color: #6c757d;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 24px;
                    transition: all 0.3s;
                }
                .step-circle.active {
                    background: #0d6efd;
                    color: white;
                    box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.2);
                }
                .step-circle.completed {
                    background: #198754;
                    color: white;
                }
            </style>
        `;
    }

    // Render current step
    function renderStep(step) {
        currentOrder.currentStep = step;
        const stepContent = document.getElementById('stepContent');
        const btnPrev = document.getElementById('btnPrevStep');
        const btnNext = document.getElementById('btnNextStep');

        // Update progress bar - now 3 steps instead of 4
        const progressBar = document.getElementById('progressBar');
        progressBar.style.width = ((step - 1) / 2 * 100) + '%';

        // Update step circles - now only 3 circles
        for (let i = 1; i <= 3; i++) {
            const circle = document.getElementById('stepCircle' + i);
            const label = document.getElementById('stepLabel' + i);
            circle.classList.remove('active', 'completed');
            label.classList.remove('fw-bold');

            if (i < step) {
                circle.classList.add('completed');
            } else if (i === step) {
                circle.classList.add('active');
                label.classList.add('fw-bold');
            }
        }

        // Update title
        const titles = {
            1: 'Transaksi Baru - Step 1: Informasi Pelanggan',
            2: 'Transaksi Baru - Step 2: Informasi Pesanan',
            3: 'Transaksi Baru - Step 3: Pembayaran'
        };
        document.getElementById('cardTitleText').textContent = titles[step];

        // Show/hide buttons
        btnPrev.style.display = step === 1 ? 'none' : 'inline-block';
        btnNext.innerHTML = step === 3 ? '<i class="ti ti-check"></i> Proses Transaksi' : 'Selanjutnya <i class="ti ti-arrow-right"></i>';

        // Render step content
        switch (step) {
            case 1:
                stepContent.innerHTML = getStep1HTML();
                populateStep1Data();
                break;
            case 2:
                stepContent.innerHTML = getStep2HTML();
                // Auto-add first seblak order if empty
                if (currentOrder.items.length === 0) {
                    addNewSeblakOrder();
                } else {
                    renderProductsAndToppings();
                }
                break;
            case 3:
                stepContent.innerHTML = getStep3HTML();
                updateStep3Summary();
                break;
        }
    }

    // Next step
    function nextStep() {
        const currentStep = currentOrder.currentStep;
        console.log('nextStep() called, current step:', currentStep);

        // Validate current step
        if (currentStep === 1) {
            console.log('Validating step 1...');
            if (!validateStep1()) return;
        } else if (currentStep === 2) {
            console.log('Validating step 2...');
            if (!validateStep2()) return;
        } else if (currentStep === 3) {
            console.log('Step 3, calling submitNewOrder()...');
            submitNewOrder();
            return;
        }

        console.log('Moving to step:', currentStep + 1);
        renderStep(currentStep + 1);
    }

    // Previous step
    function previousStep() {
        const currentStep = currentOrder.currentStep;
        if (currentStep > 1) {
            renderStep(currentStep - 1);
        }
    }

    // STEP 1: Informasi Pelanggan
    function getStep1HTML() {
        return `
            <div class="card">
                <div class="card-body">
                    <!-- Order Type Buttons -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Tipe Pesanan <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="d-flex justify-content-center btn btn-outline-danger order-type-btn active w-50" data-order-type="dine_in" onclick="changeOrderType('dine_in')">
                                <i class="fas fa-utensils me-2"></i>Dine In
                            </button>
                            <button type="button" class="d-flex justify-content-center btn btn-outline-primary order-type-btn w-50" data-order-type="take_away" onclick="changeOrderType('take_away')">
                                <i class="fas fa-shopping-bag me-2"></i>Take Away
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="step1_customerName" placeholder="Masukkan nama pelanggan">
                        </div>
                        <div class="col-md-3 mb-3 dine-in-field">
                            <label class="form-label">No. Meja <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="step1_tableNumber" placeholder="Contoh: 5">
                        </div>
                        <div class="col-md-3 mb-3 take-away-field d-none">
                            <label class="form-label">Waktu Ambil</label>
                            <input type="time" class="form-control" id="step1_pickupTime">
                        </div>
                        <div class="col-12 mb-3 take-away-field d-none">
                            <label class="form-label">Alamat Pengiriman</label>
                            <textarea class="form-control" id="step1_deliveryAddress" rows="2" placeholder="Alamat lengkap untuk pengiriman (opsional)"></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" id="step1_notes" rows="3" placeholder="Catatan tambahan (opsional)"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function populateStep1Data() {
        document.getElementById('step1_customerName').value = currentOrder.customer_name || '';
        document.getElementById('step1_tableNumber').value = currentOrder.table_number || '';
        document.getElementById('step1_notes').value = currentOrder.notes || '';

        // Set order type if exists
        if (currentOrder.order_type) {
            const radio = document.querySelector(`input[name="orderType"][value="${currentOrder.order_type}"]`);
            if (radio) {
                radio.checked = true;
                changeOrderType(currentOrder.order_type);
            }
        }

        // Populate take away fields
        document.getElementById('step1_pickupTime').value = currentOrder.pickup_time || '';
        document.getElementById('step1_deliveryAddress').value = currentOrder.delivery_address || '';
    }

    // Change order type
    function changeOrderType(orderType) {
        const dineInFields = document.querySelectorAll('.dine-in-field');
        const takeAwayFields = document.querySelectorAll('.take-away-field');
        const buttons = document.querySelectorAll('.order-type-btn');

        if (orderType === 'dine_in') {
            // Show dine in fields, hide take away fields
            dineInFields.forEach(field => field.classList.remove('d-none'));
            takeAwayFields.forEach(field => field.classList.add('d-none'));
        } else if (orderType === 'take_away') {
            // Hide dine in fields, show take away fields
            dineInFields.forEach(field => field.classList.add('d-none'));
            takeAwayFields.forEach(field => field.classList.remove('d-none'));
        }

        // Update button active states
        buttons.forEach(btn => {
            if (btn.getAttribute('data-order-type') === orderType) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // Store order type
        currentOrder.order_type = orderType;
    }

    function validateStep1() {
        const name = document.getElementById('step1_customerName').value.trim();

        if (!name) {
            showNotification('Nama pelanggan wajib diisi', 'error');
            return false;
        }

        // Validate table number for dine in
        if (currentOrder.order_type === 'dine_in') {
            const tableNumber = document.getElementById('step1_tableNumber').value.trim();
            if (!tableNumber) {
                showNotification('No. meja wajib diisi untuk dine in', 'error');
                return false;
            }
        }

        // Save to currentOrder
        currentOrder.customer_name = name;
        currentOrder.table_number = document.getElementById('step1_tableNumber').value.trim();
        currentOrder.notes = document.getElementById('step1_notes').value.trim();
        currentOrder.pickup_time = document.getElementById('step1_pickupTime').value;
        currentOrder.delivery_address = document.getElementById('step1_deliveryAddress').value.trim();

        return true;
    }

    // STEP 2: Informasi Pesanan (Multiple Seblak Orders with Levels, Customizations & Toppings)
    function getStep2HTML() {
        return `
            <style>
                .spice-card {
                    cursor: pointer;
                    transition: all 0.2s;
                    border: 2px solid #dee2e6;
                    background-color: #fff;
                }
                
                .spice-card:hover {
                    background-color: rgba(13, 110, 253, 0.05);
                    transform: translateY(-2px);
                }
                
                .spice-card.active {
                    border-color: #0d6efd;
                    background-color: rgba(13, 110, 253, 0.1);
                }
                
                .spice-card.active .card-body {
                    color: #0d6efd;
                }
                
                .option-card {
                    cursor: pointer;
                    transition: all 0.2s;
                    border: 2px solid #dee2e6;
                }
                
                .option-card:hover {
                    background-color: rgba(13, 110, 253, 0.05);
                }
                
                .option-card.active {
                    border-color: #0d6efd;
                    background-color: rgba(13, 110, 253, 0.1);
                }
                
                .topping-card {
                    position: relative;
                    overflow: hidden;
                    border: 2px solid #dee2e6;
                    cursor: pointer;
                    transition: all 0.2s;
                }
                
                .topping-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                }

                .topping-card:hover .position-absolute:last-child {
                    opacity: 1 !important;
                }
                
                .topping-card.active {
                    border-color: #dc3545;
                    background-color: rgba(220, 53, 69, 0.05);
                    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.15);
                }
                
                .topping-card img {
                    width: 100%;
                    aspect-ratio: 1;
                    object-fit: cover;
                }
                
                .topping-checkbox {
                    position: absolute;
                    top: 0.5rem;
                    right: 0.5rem;
                    width: 24px;
                    height: 24px;
                    cursor: pointer;
                }
                
                .filter-btn {
                    border-radius: 50px;
                    border: 1px solid #dee2e6;
                    background-color: transparent;
                    color: #6c757d;
                    transition: all 0.2s;
                }
                
                .filter-btn.active {
                    background-color: rgba(13, 110, 253, 0.1);
                    color: #0d6efd;
                    border-color: #0d6efd;
                }
                
                .filter-btn:hover {
                    background-color: rgba(0, 0, 0, 0.05);
                }

                .seblak-order-card {
                    border: 2px solid #dee2e6;
                    border-radius: 12px;
                    margin-bottom: 1.5rem;
                }

                .seblak-order-card.first-order {
                    border-color: #0d6efd;
                }

                .qty-control {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    background: #f8f9fa;
                    padding: 0.25rem 0.75rem;
                    border-radius: 50px;
                }
            </style>

            <div class="row">
                <!-- Left Column: Seblak Configuration -->
                <div class="col-lg-8">
                    <div id="seblakOrdersList">
                        <div class="text-center text-muted py-4">
                            <i class="ti ti-shopping-cart" style="font-size: 48px;"></i>
                            <p class="mt-2">Memuat pesanan...</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="col-lg-4">
                    <div class="card sticky-top" style="top: 20px;">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="ti ti-file-invoice me-2"></i>Ringkasan Pesanan</h6>
                        </div>
                        <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                            <div id="orderSummary">
                                <div class="text-center text-muted py-4">
                                    <i class="ti ti-file-invoice" style="font-size: 48px;"></i>
                                    <p class="mt-2">Belum ada item dipilih</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <!-- Add Seblak Button -->
                            <button type="button" class="btn btn-outline-primary w-100 mb-3" onclick="addNewSeblakOrder()">
                                <i class="ti ti-plus me-2"></i>Tambah Seblak
                            </button>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <strong id="orderSubtotal">Rp 0</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <strong>Total:</strong>
                                <h5 class="mb-0 text-success" id="orderTotalPrice">Rp 0</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Render products and manage items in Step 2
    function renderProductsAndToppings() {
        renderSeblakOrdersList();
        updateOrderSummary();
    }

    // Add new seblak order
    function addNewSeblakOrder() {
        const newOrder = {
            id: 'order_' + Date.now(),
            quantity: 1,
            spice_level: null,
            customizations: {},
            toppings: [],
            base_price: 0, // Base price 0, harga dihitung dari level pedas + topping + customization
            notes: ''
        };

        currentOrder.items.push(newOrder);
        renderSeblakOrdersList();
        updateOrderSummary();
    }

    // Render seblak orders list
    function renderSeblakOrdersList() {
        const container = document.getElementById('seblakOrdersList');
        if (!container) return;

        if (currentOrder.items.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="ti ti-shopping-cart" style="font-size: 64px; opacity: 0.3;"></i>
                    <p class="mt-3">Gunakan tombol "Tambah Seblak" di keranjang untuk menambah pesanan</p>
                </div>
            `;
            return;
        }

        container.innerHTML = currentOrder.items.map((order, index) => {
            const spiceLevel = order.spice_level ? allSpiceLevels.find(sl => sl.id === order.spice_level) : null;
            const spiceLevelPrice = spiceLevel ? parseFloat(spiceLevel.price) : 0;

            let customizationsPrice = 0;
            const customizationsByType = {};

            // Group customizations by type
            Object.entries(order.customizations || {}).forEach(([type, optionId]) => {
                const option = allCustomizationOptions.find(c => c.id === optionId);
                if (option) {
                    customizationsByType[type] = option;
                    customizationsPrice += parseFloat(option.price || 0);
                }
            });

            const toppingsPrice = order.toppings.reduce((sum, t) => sum + (t.unit_price * t.quantity), 0);
            const orderTotal = (order.base_price + spiceLevelPrice + customizationsPrice + toppingsPrice) * order.quantity;

            // Get icon for spice level
            const getSpiceIcon = (levelName) => {
                if (!levelName) return 'ti-flame';
                const name = levelName.toLowerCase();
                if (name.includes('0') || name.includes('tidak')) return 'ti-mood-smile';
                if (name.includes('1')) return 'ti-mood-happy';
                if (name.includes('2')) return 'ti-mood-neutral';
                if (name.includes('3')) return 'ti-mood-sad';
                if (name.includes('4')) return 'ti-mood-cry';
                if (name.includes('predator') || name.includes('5')) return 'ti-flame';
                return 'ti-flame';
            };

            return `
                <div class="seblak-order-card ${index === 0 ? 'first-order' : ''} p-4 mb-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="ti ti-soup me-2"></i>Seblak #${index + 1}
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <div class="qty-control">
                                <button type="button" class="btn btn-sm btn-link p-0" onclick="updateOrderQuantity('${order.id}', -1)">
                                    <i class="ti ti-minus"></i>
                                </button>
                                <span class="fw-bold px-2">${order.quantity}</span>
                                <button type="button" class="btn btn-sm btn-link p-0" onclick="updateOrderQuantity('${order.id}', 1)">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSeblakOrder('${order.id}')">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Pilih Level Pedas -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3"><i class="ti ti-flame me-2 text-danger"></i>Pilih Level Pedas <span class="text-danger">*</span></h6>
                        <div class="row g-3">
                            ${allSpiceLevels.map(level => {
                const isSelected = order.spice_level === level.id;
                const icon = getSpiceIcon(level.name);

                return `
                                    <div class="col-6 col-sm-4 col-md-3">
                                        <label class="topping-card card h-100 mb-0 ${isSelected ? 'active' : ''}" 
                                               onclick="selectSpiceLevel('${order.id}', '${level.id}')" 
                                               style="cursor: pointer;">
                                            <input type="radio" name="spiceLevel_${order.id}" value="${level.id}" class="d-none" ${isSelected ? 'checked' : ''}>
                                            
                                            <div class="position-relative" style="aspect-ratio: 1; overflow: hidden; background: linear-gradient(135deg, #fff5f5, #fed7d7);">
                                                ${level.image ? `
                                                    <img src="../../../uploads/menu-images/${level.image}" alt="${level.name}" 
                                                         style="width: 100%; height: 100%; object-fit: cover;"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="d-flex align-items-center justify-content-center h-100 position-absolute top-0 start-0 w-100" style="display: none !important;">
                                                        <i class="${icon} text-danger" style="font-size: 3rem;"></i>
                                                    </div>
                                                ` : `
                                                    <div class="d-flex align-items-center justify-content-center h-100">
                                                        <i class="${icon} text-danger" style="font-size: 3rem;"></i>
                                                    </div>
                                                `}
                                                
                                                <!-- Selection Indicator -->
                                                ${isSelected ? `
                                                    <div class="position-absolute top-0 end-0 p-2">
                                                        <div class="bg-danger rounded-circle p-1">
                                                            <i class="ti ti-check text-white"></i>
                                                        </div>
                                                    </div>
                                                ` : ''}
                                                
                                                <!-- Level Badge -->
                                                <div class="position-absolute bottom-0 start-0 p-2">
                                                    <span class="badge bg-danger bg-opacity-90 small">${level.name.replace('Pedas ', '')}</span>
                                                </div>
                                            </div>
                                            
                                            <div class="card-body p-2">
                                                <h6 class="card-title small mb-1 text-truncate fw-bold">${level.name}</h6>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-danger fw-bold small">${level.price > 0 ? `+ Rp ${formatPrice(level.price)}` : 'Gratis'}</span>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                `;
            }).join('')}
                        </div>
                    </div>

                    <!-- Customization Options (Grouped by Type) -->
                    ${renderCustomizationOptions(order)}

                    <!-- Pilih Topping Tambahan -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">
                                <i class="ti ti-cheese me-2 text-warning"></i>Pilih Topping Tambahan
                                ${order.toppings.length > 0 ? `<span class="badge bg-warning text-dark ms-2">${order.toppings.length} item</span>` : ''}
                            </h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="showToppingsModal('${order.id}')">
                                <i class="ti ti-plus me-1"></i>Tambah Topping
                            </button>
                        </div>
                        
                        ${order.toppings.length > 0 ? `
                            <div class="row g-3">
                                ${order.toppings.map(t => {
                const topping = allToppings.find(tp => tp.id === t.topping_id);
                const totalPrice = t.unit_price * t.quantity;

                return `
                                        <div class="col-6 col-sm-4 col-md-3">
                                            <div class="card topping-card h-100 mb-0">
                                                <div class="position-relative" style="aspect-ratio: 1; overflow: hidden; background: linear-gradient(135deg, #fff5f5, #fed7d7);">
                                                    ${topping && topping.image ? `
                                                        <img src="../../../uploads/menu-images/${topping.image}" alt="${t.topping_name}" 
                                                             style="width: 100%; height: 100%; object-fit: cover;"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <div class="d-flex align-items-center justify-content-center h-100 position-absolute top-0 start-0 w-100" style="display: none !important;">
                                                            <i class="ti ti-cheese text-warning" style="font-size: 3rem;"></i>
                                                        </div>
                                                    ` : `
                                                        <div class="d-flex align-items-center justify-content-center h-100">
                                                            <i class="ti ti-cheese text-warning" style="font-size: 3rem;"></i>
                                                        </div>
                                                    `}
                                                    
                                                    <!-- Remove Button -->
                                                    <div class="position-absolute top-0 end-0 p-2">
                                                        <button type="button" class="btn btn-sm btn-danger rounded-circle p-1" 
                                                                style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;"
                                                                onclick="event.stopPropagation(); removeToppingFromOrder('${order.id}', '${t.topping_id}');"
                                                                title="Hapus ${t.topping_name}">
                                                            <i class="ti ti-x" style="font-size: 1rem;"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Quantity Badge -->
                                                    <div class="position-absolute bottom-0 start-0 p-2">
                                                        <span class="badge bg-warning bg-opacity-90 small">${t.quantity}x</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="card-body p-2">
                                                    <h6 class="card-title small mb-1 text-truncate fw-bold" title="${t.topping_name}">
                                                        ${t.topping_name}
                                                    </h6>
                                                    <div class="mb-2">
                                                        <span class="text-warning fw-bold small">+ Rp ${formatPrice(totalPrice)}</span>
                                                    </div>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button type="button" class="btn btn-sm btn-decrease-topping" 
                                                                data-order-id="${order.id}" 
                                                                data-topping-id="${t.topping_id}"
                                                                style="width: 32px; height: 32px; padding: 0; background: #fff; border: 2px solid #dc3545; color: #dc3545; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: bold; transition: all 0.2s;"
                                                                onmouseover="this.style.background='#dc3545'; this.style.color='#fff';"
                                                                onmouseout="this.style.background='#fff'; this.style.color='#dc3545';"
                                                                title="Kurangi">
                                                            <i class="ti ti-minus" style="font-size: 14px;"></i>
                                                        </button>
                                                        <div class="d-flex align-items-center justify-content-center" 
                                                             style="width: 40px; height: 32px; background: #f8f9fa; border: 2px solid #dee2e6; border-radius: 6px; font-weight: bold; color: #212529; font-size: 14px;">
                                                            ${t.quantity}
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-increase-topping" 
                                                                data-order-id="${order.id}" 
                                                                data-topping-id="${t.topping_id}"
                                                                style="width: 32px; height: 32px; padding: 0; background: #fff; border: 2px solid #28a745; color: #28a745; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: bold; transition: all 0.2s;"
                                                                onmouseover="this.style.background='#28a745'; this.style.color='#fff';"
                                                                onmouseout="this.style.background='#fff'; this.style.color='#28a745';"
                                                                title="Tambah">
                                                            <i class="ti ti-plus" style="font-size: 14px;"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
            }).join('')}
                            </div>
                        ` : `
                            <div class="text-center py-4 bg-light rounded">
                                <i class="ti ti-cheese text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="text-muted mb-0 mt-2">Belum ada topping dipilih</p>
                                <small class="text-muted">Klik tombol "Tambah Topping" untuk memilih</small>
                            </div>
                        `}
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="ti ti-note me-2"></i>Catatan</label>
                        <textarea class="form-control" rows="2" 
                                  placeholder="Catatan khusus untuk pesanan ini..."
                                  onchange="updateOrderNotes('${order.id}', this.value)">${order.notes || ''}</textarea>
                    </div>

                    <!-- Price Summary -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Harga per item:</small>
                                <div class="fw-bold">Rp ${formatPrice(order.base_price + spiceLevelPrice + customizationsPrice + toppingsPrice)}</div>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted">Subtotal (${order.quantity}x):</small>
                                <div class="h5 mb-0 text-success fw-bold">Rp ${formatPrice(orderTotal)}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Helper function to render customization options grouped by type
    function renderCustomizationOptions(order) {
        // Group customization options by analyzing their names
        const optionsByType = {
            'kencur_level': [],
            'broth_flavor': [],
            'egg_type': []
        };

        allCustomizationOptions.forEach(option => {
            const name = option.name.toLowerCase();

            // Group by name patterns
            if (name.includes('kencur')) {
                optionsByType['kencur_level'].push(option);
            } else if (name.includes('kuah') || name.includes('nyemek')) {
                optionsByType['broth_flavor'].push(option);
            } else if (name.includes('orak') || name.includes('telur') || name.includes('teluh')) {
                optionsByType['egg_type'].push(option);
            }
        });

        // Remove empty groups
        Object.keys(optionsByType).forEach(key => {
            if (optionsByType[key].length === 0) {
                delete optionsByType[key];
            }
        });

        // Type labels
        const typeLabels = {
            'egg_type': 'Tipe Telur',
            'broth_flavor': 'Rasa Kuah',
            'kencur_level': 'Level Kencur'
        };

        if (Object.keys(optionsByType).length === 0) return '';

        return `
            <div class="mb-4">
                <h6 class="fw-bold mb-3"><i class="ti ti-adjustments me-2 text-info"></i>Customization Options</h6>
                <div class="row g-3">
                    ${Object.entries(optionsByType).map(([type, options]) => `
                        <div class="col-md-4">
                            <h6 class="small fw-bold text-muted mb-2">${typeLabels[type] || type}</h6>
                            <div class="d-flex flex-column gap-2">
                                ${options.map(option => {
            const isSelected = order.customizations[type] === option.id;
            return `
                                        <label class="option-card card mb-0 ${isSelected ? 'active' : ''}" onclick="selectCustomizationOption('${order.id}', '${type}', '${option.id}')">
                                            <div class="card-body d-flex justify-content-between align-items-center py-2 px-3">
                                                <span class="small">${option.name}</span>
                                                <input type="radio" name="${type}_${order.id}" class="form-check-input" ${isSelected ? 'checked' : ''} onclick="event.stopPropagation()">
                                            </div>
                                        </label>
                                    `;
        }).join('')}
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    // Select customization option (radio-based per type)
    function selectCustomizationOption(orderId, type, optionId) {
        const order = currentOrder.items.find(o => o.id === orderId);
        if (!order) return;

        if (!order.customizations) {
            order.customizations = {};
        }

        // Toggle or set the option for this type
        if (order.customizations[type] === optionId) {
            // If clicking the same option, do nothing (keep it selected)
            return;
        } else {
            order.customizations[type] = optionId;
        }

        renderSeblakOrdersList();
        updateOrderSummary();
    }

    // Update order quantity
    function updateOrderQuantity(orderId, change) {
        const order = currentOrder.items.find(o => o.id === orderId);
        if (!order) return;

        order.quantity = Math.max(1, order.quantity + change);
        renderSeblakOrdersList();
        updateOrderSummary();
    }

    // Remove seblak order
    function removeSeblakOrder(orderId) {
        const index = currentOrder.items.findIndex(o => o.id === orderId);
        if (index >= 0) {
            currentOrder.items.splice(index, 1);
            renderSeblakOrdersList();
            updateOrderSummary();
        }
    }

    // Select spice level
    function selectSpiceLevel(orderId, levelId) {
        const order = currentOrder.items.find(o => o.id === orderId);
        if (!order) return;

        order.spice_level = levelId;
        renderSeblakOrdersList();
        updateOrderSummary();
    }

    // Update order notes
    function updateOrderNotes(orderId, notes) {
        const order = currentOrder.items.find(o => o.id === orderId);
        if (!order) return;

        order.notes = notes;
    }

    // Remove topping from order
    // Update order summary in right sidebar
    function updateOrderSummary() {
        const container = document.getElementById('orderSummary');
        if (!container) return;

        if (currentOrder.items.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5" style="color: #9ca3af;">
                    <i class="ti ti-shopping-cart-off" style="font-size: 56px; opacity: 0.3;"></i>
                    <p class="mt-3 mb-0 fw-medium">Keranjang Kosong</p>
                    <small style="font-size: 12px;">Tambahkan item untuk memulai pesanan</small>
                </div>
            `;
            document.getElementById('orderSubtotal').textContent = 'Rp 0';
            document.getElementById('orderTotalPrice').textContent = 'Rp 0';
            return;
        }

        let grandTotal = 0;

        container.innerHTML = currentOrder.items.map((order, index) => {
            const spiceLevelPrice = order.spice_level ? (allSpiceLevels.find(sl => sl.id === order.spice_level)?.price || 0) : 0;

            // Calculate customizations price (support both object and array format)
            let customizationsPrice = 0;
            if (order.customizations) {
                if (Array.isArray(order.customizations)) {
                    customizationsPrice = order.customizations.reduce((sum, custId) => {
                        const cust = allCustomizationOptions.find(c => c.id === custId);
                        return sum + (cust ? parseFloat(cust.price) : 0);
                    }, 0);
                } else {
                    // Object format: {type: optionId}
                    customizationsPrice = Object.values(order.customizations).reduce((sum, custId) => {
                        const cust = allCustomizationOptions.find(c => c.id === custId);
                        return sum + (cust ? parseFloat(cust.price) : 0);
                    }, 0);
                }
            }

            const toppingsPrice = order.toppings.reduce((sum, t) => sum + (t.unit_price * t.quantity), 0);
            const itemPrice = order.base_price + spiceLevelPrice + customizationsPrice + toppingsPrice;
            const orderTotal = itemPrice * order.quantity;
            grandTotal += orderTotal;

            const spiceLevel = allSpiceLevels.find(sl => sl.id === order.spice_level);

            return `
                <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; margin-bottom: 12px;">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #f97316, #fb923c); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-soup text-white" style="font-size: 18px;"></i>
                            </div>
                            <div>
                                <div style="font-weight: 600; font-size: 14px; color: #1f2937;">Seblak #${index + 1}</div>
                                <div style="font-size: 11px; color: #6b7280;">${order.quantity} Porsi</div>
                            </div>
                        </div>
                        <div style="font-weight: 700; font-size: 16px; color: #059669;">
                            Rp ${formatPrice(orderTotal)}
                        </div>
                    </div>

                    <!-- Breakdown Harga -->
                    <div style="background: #f9fafb; border-radius: 8px; padding: 12px; margin-bottom: 12px;">
                        ${spiceLevel ? `
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-flame" style="font-size: 14px; color: #dc2626;"></i>
                                    <span style="font-size: 13px; color: #374151;">${spiceLevel.name}</span>
                                </div>
                                <span style="font-size: 13px; font-weight: 600; color: #6b7280;">
                                    ${spiceLevelPrice > 0 ? 'Rp ' + formatPrice(spiceLevelPrice) : 'Gratis'}
                                </span>
                            </div>
                        ` : `
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-alert-circle" style="font-size: 14px; color: #dc2626;"></i>
                                    <span style="font-size: 13px; color: #dc2626; font-style: italic;">Level pedas belum dipilih</span>
                                </div>
                            </div>
                        `}
                        
                        ${order.customizations && Object.keys(order.customizations).length > 0 ? `
                            <div class="mb-2">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="ti ti-adjustments" style="font-size: 14px; color: #7c3aed;"></i>
                                    <span style="font-size: 12px; font-weight: 600; color: #6b7280;">Kustomisasi</span>
                                </div>
                                ${Object.values(order.customizations).map(cId => {
                const cust = allCustomizationOptions.find(c => c.id === cId);
                return cust ? `
                                        <div class="d-flex justify-content-between align-items-center ms-4 mb-1">
                                            <span style="font-size: 12px; color: #6b7280;">• ${cust.name}</span>
                                            <span style="font-size: 12px; font-weight: 600; color: #6b7280;">
                                                ${cust.price > 0 ? 'Rp ' + formatPrice(cust.price) : 'Gratis'}
                                            </span>
                                        </div>
                                    ` : '';
            }).join('')}
                            </div>
                        ` : ''}
                        
                        ${order.toppings.length > 0 ? `
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="ti ti-cheese" style="font-size: 14px; color: #f59e0b;"></i>
                                    <span style="font-size: 12px; font-weight: 600; color: #6b7280;">Topping (${order.toppings.length})</span>
                                </div>
                                ${order.toppings.map(t => `
                                    <div class="d-flex justify-content-between align-items-center ms-4 mb-1">
                                        <span style="font-size: 12px; color: #6b7280;">• ${t.topping_name} (${t.quantity}x)</span>
                                        <span style="font-size: 12px; font-weight: 600; color: #6b7280;">
                                            Rp ${formatPrice(t.unit_price * t.quantity)}
                                        </span>
                                    </div>
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>

                    <!-- Total per Item -->
                    <div class="d-flex justify-content-between align-items-center pt-2" style="border-top: 1px dashed #d1d5db;">
                        <span style="font-size: 13px; font-weight: 600; color: #6b7280;">
                            ${order.quantity}x @ Rp ${formatPrice(itemPrice)}
                        </span>
                        <span style="font-size: 14px; font-weight: 700; color: #1f2937;">
                            Rp ${formatPrice(orderTotal)}
                        </span>
                    </div>
                </div>
            `;
        }).join('');

        document.getElementById('orderSubtotal').textContent = 'Rp ' + formatPrice(grandTotal);
        document.getElementById('orderTotalPrice').textContent = 'Rp ' + formatPrice(grandTotal);
    }


    // Helper function to get image HTML
    function getImageHTML(imageUrl, name, size) {
        if (imageUrl) {
            return `<img src="${imageUrl}" alt="${name}" class="img-fluid rounded" style="width: 100%; height: 150px; object-fit: cover;">`;
        } else {
            return `<div class="bg-light d-flex align-items-center justify-content-center rounded" style="width: 100%; height: 150px;">
                        <i class="ti ti-soup" style="font-size: 48px; color: #6c757d;"></i>
                    </div>`;
        }
    }

    function validateStep2() {
        if (currentOrder.items.length === 0) {
            showNotification('Tambahkan minimal 1 pesanan seblak', 'error');
            return false;
        }

        // Check if all orders have spice level selected
        for (let i = 0; i < currentOrder.items.length; i++) {
            const order = currentOrder.items[i];
            if (!order.spice_level) {
                showNotification(`Pilih level pedas untuk Seblak #${i + 1}`, 'error');
                return false;
            }
        }

        return true;
    }

    // Show toppings modal for specific seblak order
    let currentModalOrderId = null;

    function showToppingsModal(orderId) {
        const order = currentOrder.items.find(o => o.id === orderId);
        if (!order) return;

        // Store current order ID for modal
        currentModalOrderId = orderId;

        // Get unique topping categories
        const toppingCategories = [...new Set(allToppings.map(t => t.category_name).filter(Boolean))];

        // Build category filters - Clean & Minimal Style
        const categoryFiltersHTML = `
            <button type="button" class="filter-btn active" data-category="" 
                    style="border: none; background: #1f2937; color: white; border-radius: 20px; padding: 6px 16px; font-size: 13px; font-weight: 500; transition: all 0.2s;">
                Semua
            </button>
            ${toppingCategories.map(category => `
                <button type="button" class="filter-btn" data-category="${category}"
                        style="border: 1px solid #e5e7eb; background: white; color: #6b7280; border-radius: 20px; padding: 6px 16px; font-size: 13px; font-weight: 500; transition: all 0.2s;">
                    ${category}
                </button>
            `).join('')}
        `;

        document.getElementById('modalCategoryFilters').innerHTML = categoryFiltersHTML;

        // Add event listeners to filter buttons with clean style toggle
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.filter-btn').forEach(b => {
                    b.classList.remove('active');
                    b.style.background = 'white';
                    b.style.color = '#6b7280';
                    b.style.border = '1px solid #e5e7eb';
                });
                this.classList.add('active');
                this.style.background = '#1f2937';
                this.style.color = 'white';
                this.style.border = 'none';
                renderModalToppingsGrid();
            });
        });

        // Add event listener to search
        document.getElementById('modalToppingSearch').addEventListener('input', renderModalToppingsGrid);

        // Add event listeners to action buttons
        document.getElementById('btnClearAllToppings').onclick = () => clearAllToppingsInOrder(orderId);
        document.getElementById('btnConfirmToppings').onclick = () => {
            bootstrap.Modal.getInstance(document.getElementById('toppingModal')).hide();
        };

        // Initial render
        renderModalToppingsGrid();
        updateModalCounters();
        updateModalSelectedSummary();

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('toppingModal'));
        modal.show();

        // Update main page when modal closes
        document.getElementById('toppingModal').addEventListener('hidden.bs.modal', function () {
            currentModalOrderId = null; // Reset modal order ID
            renderSeblakOrdersList();
            updateOrderSummary();
        }, { once: true });
    }

    // Filter toppings by category - legacy for compatibility
    function filterToppingsByCategory(orderId, category) {
        // Update active button
        document.querySelectorAll('.filter-btn').forEach(btn => {
            if (btn.getAttribute('data-category') === category) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // Re-render grid with filter
        renderModalToppingsGrid();
    }

    // Render toppings grid in modal
    function renderModalToppingsGrid() {
        const orderId = currentModalOrderId;
        if (!orderId) return;

        const search = document.getElementById('modalToppingSearch')?.value.toLowerCase() || '';
        const category = document.querySelector('.filter-btn.active')?.getAttribute('data-category') || '';
        const toppingsGrid = document.getElementById('modalToppingsGrid');
        if (!toppingsGrid) return;

        const filteredToppings = allToppings.filter(topping => {
            const nameMatch = topping.name.toLowerCase().includes(search);
            const categoryMatch = category ? topping.category_name === category : true;
            return nameMatch && categoryMatch;
        });

        if (filteredToppings.length === 0) {
            toppingsGrid.innerHTML = `
                <div class="col-12 text-center text-muted py-5">
                    <i class="ti ti-search-off" style="font-size: 48px; opacity: 0.3;"></i>
                    <p class="mt-3 mb-2" style="color: #6b7280; font-weight: 500;">Tidak ada topping ditemukan</p>
                    <small style="color: #9ca3af;">Coba kata kunci yang lain atau lihat semua kategori</small>
                </div>
            `;
            return;
        }

        toppingsGrid.innerHTML = filteredToppings.map(topping => {
            const isSelected = isToppingSelectedInOrder(orderId, topping.id);
            const toppingQty = getToppingQuantityInOrder(orderId, topping.id);

            return `
                <div class="col-6 col-sm-4 col-md-3">
                    <div class="h-100 bg-white shadow-sm" 
                         style="cursor: pointer; border-radius: 12px; overflow: hidden; transition: all 0.2s; border: ${isSelected ? '2px solid #1f2937' : '1px solid #e5e7eb'};"
                         onmouseover="if (!${isSelected}) this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';"
                         onclick="toggleToppingInOrder('${orderId}', '${topping.id}')">
                        <div class="position-relative" style="aspect-ratio: 1; overflow: hidden; border-radius: 10px; background: ${isSelected ? '#f9fafb' : '#ffffff'}; border: ${isSelected ? '2px solid #1f2937' : '1px solid #e5e7eb'};">
                            ${topping.image ? `
                                <img src="../../../uploads/menu-images/${topping.image}" alt="${topping.name}" 
                                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                            ` : `
                                <div class="d-flex align-items-center justify-content-center h-100">
                                    <i class="ti ti-cheese" style="font-size: 2.5rem; color: #d1d5db;"></i>
                                </div>
                            `}
                            
                            <!-- Checkmark for Selected -->
                            ${isSelected ? `
                                <div class="position-absolute top-0 start-0 m-2">
                                    <div class="d-flex align-items-center justify-content-center" 
                                         style="width: 24px; height: 24px; background: #1f2937; border-radius: 50%; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                                        <i class="ti ti-check text-white" style="font-size: 14px;"></i>
                                    </div>
                                </div>
                            ` : ''}
                            
                            <!-- Quantity Badge -->
                            ${isSelected ? `
                                <div class="position-absolute top-0 end-0 m-2">
                                    <div class="d-flex align-items-center justify-content-center px-2 py-1" 
                                         style="background: #1f2937; border-radius: 12px; min-width: 28px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                                        <span class="text-white fw-semibold" style="font-size: 12px;">${toppingQty}x</span>
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                        
                        <div class="p-2">
                            <h6 class="mb-1 text-truncate fw-semibold" style="font-size: 13px; color: #1f2937;" title="${topping.name}">
                                ${topping.name}
                            </h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold" style="font-size: 13px; color: #059669;">Rp ${formatPrice(topping.price)}</span>
                                ${isSelected ? `
                                    <div class="d-flex align-items-center gap-1" onclick="event.stopPropagation();">
                                        <button type="button" class="btn btn-sm p-0 d-flex align-items-center justify-content-center" 
                                                onclick="event.stopPropagation(); updateToppingQtyInOrder('${orderId}', '${topping.id}', -1);"
                                                style="width: 24px; height: 24px; border: 1px solid #e5e7eb; border-radius: 6px; background: white;"
                                                title="Kurangi">
                                            <i class="ti ti-minus" style="font-size: 12px; color: #6b7280;"></i>
                                        </button>
                                        <span class="px-2 fw-semibold" style="font-size: 13px; color: #1f2937; min-width: 24px; text-align: center;">${toppingQty}</span>
                                        <button type="button" class="btn btn-sm p-0 d-flex align-items-center justify-content-center" 
                                                onclick="event.stopPropagation(); updateToppingQtyInOrder('${orderId}', '${topping.id}', 1);"
                                                style="width: 24px; height: 24px; border: 1px solid #1f2937; border-radius: 6px; background: #1f2937;"
                                                title="Tambah">
                                            <i class="ti ti-plus" style="font-size: 12px; color: white;"></i>
                                        </button>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Check if topping is selected in specific order
    function isToppingSelectedInOrder(orderId, toppingId) {
        const order = currentOrder.items.find(o => o.id === orderId);
        if (!order) return false;
        return order.toppings.some(t => t.topping_id === toppingId);
    }

    // Get topping quantity in specific order
    function getToppingQuantityInOrder(orderId, toppingId) {
        const order = currentOrder.items.find(o => o.id === orderId);
        if (!order) return 0;
        const topping = order.toppings.find(t => t.topping_id === toppingId);
        return topping ? topping.quantity : 0;
    }

    // Toggle topping in specific order
    function toggleToppingInOrder(orderId, toppingId) {
        const order = currentOrder.items.find(o => o.id === orderId);
        if (!order) return;

        const toppingIndex = order.toppings.findIndex(t => t.topping_id === toppingId);

        if (toppingIndex >= 0) {
            // Remove topping
            order.toppings.splice(toppingIndex, 1);
        } else {
            // Add topping
            const topping = allToppings.find(t => t.id === toppingId);
            if (topping) {
                order.toppings.push({
                    topping_id: topping.id,
                    topping_name: topping.name,
                    unit_price: parseFloat(topping.price),
                    quantity: 1
                });
            }
        }

        // Update modal if exists (check if modal is open)
        if (currentModalOrderId && document.getElementById('modalToppingsGrid')) {
            renderModalToppingsGrid();
            updateModalSelectedSummary();
            updateModalCounters();
        }
    }

    // Update topping quantity in specific order
    function updateToppingQtyInOrder(orderId, toppingId, change) {
        const order = currentOrder.items.find(o => o.id === orderId);
        if (!order) return;

        const topping = order.toppings.find(t => t.topping_id === toppingId);
        if (topping) {
            const newQuantity = topping.quantity + change;

            // If quantity becomes 0 or less, remove the topping
            if (newQuantity <= 0) {
                removeToppingFromOrder(orderId, toppingId);
                return;
            }

            topping.quantity = newQuantity;

            // Check if modal is actually open/visible
            const modalElement = document.getElementById('toppingModal');
            const isModalOpen = modalElement && modalElement.classList.contains('show');

            if (isModalOpen && currentModalOrderId && document.getElementById('modalToppingsGrid')) {
                renderModalToppingsGrid();
                updateModalSelectedSummary();
                updateModalCounters();
            } else {
                // Update main page
                renderSeblakOrdersList();
                updateOrderSummary();
            }
        }
    }

    // Clear all toppings in specific order
    function clearAllToppingsInOrder(orderId) {
        const order = currentOrder.items.find(o => o.id === orderId);
        if (!order) return;

        order.toppings = [];

        // Update modal if exists
        if (currentModalOrderId && document.getElementById('modalToppingsGrid')) {
            renderModalToppingsGrid();
            updateModalSelectedSummary();
            updateModalCounters();
        }
    }

    // Update selected summary in modal
    function updateModalSelectedSummary() {
        const orderId = currentModalOrderId;
        if (!orderId) return;

        const order = currentOrder.items.find(o => o.id === orderId);
        const summaryContainer = document.getElementById('modalSelectedSummary');
        const selectedItems = document.getElementById('modalSelectedItems');

        if (!order || !summaryContainer || !selectedItems) return;

        if (order.toppings.length > 0) {
            summaryContainer.style.display = 'block';
            selectedItems.innerHTML = order.toppings.map(topping => `
                <div class="d-inline-flex align-items-center gap-2 px-3 py-2 border-0" 
                     style="background: #f3f4f6; border-radius: 8px;">
                    <span class="fw-medium" style="font-size: 13px; color: #1f2937;">${topping.topping_name}</span>
                    <span class="badge bg-dark" style="font-size: 11px; padding: 3px 8px; border-radius: 6px;">${topping.quantity}x</span>
                    <button type="button" class="btn-close" 
                            style="font-size: 10px; opacity: 0.5;"
                            onclick="removeToppingFromOrder('${orderId}', '${topping.topping_id}')"
                            title="Hapus ${topping.topping_name}"></button>
                </div>
            `).join('');
        } else {
            summaryContainer.style.display = 'none';
        }
    }

    // Update modal counters
    function updateModalCounters() {
        const orderId = currentModalOrderId;
        if (!orderId) return;

        const order = currentOrder.items.find(o => o.id === orderId);
        const selectedCounter = document.getElementById('modalSelectedCounter');
        const totalPriceElement = document.getElementById('modalTotalPrice');

        if (!order || !selectedCounter || !totalPriceElement) return;

        const selectedCount = order.toppings.length;
        const totalSelectedPrice = order.toppings.reduce((sum, t) => sum + (t.unit_price * t.quantity), 0);

        selectedCounter.textContent = `${selectedCount} item`;
        totalPriceElement.textContent = `Rp ${formatPrice(totalSelectedPrice)}`;
    }

    // Remove specific topping from order
    function removeToppingFromOrder(orderId, toppingId) {
        const order = currentOrder.items.find(o => o.id === orderId);
        if (!order) return;

        order.toppings = order.toppings.filter(t => t.topping_id !== toppingId);

        // Update modal if exists (check if modal is open)
        if (currentModalOrderId && document.getElementById('modalToppingsGrid')) {
            renderModalToppingsGrid();
            updateModalSelectedSummary();
            updateModalCounters();
        } else {
            // Update main page
            renderSeblakOrdersList();
            updateOrderSummary();
        }
    }



    function renderToppingsGrid(itemId) {

        const search = document.getElementById('toppingSearch').value.toLowerCase();

        const category = document.getElementById('toppingCategoryFilter').value;

        const toppingsGrid = document.getElementById('toppingsGrid');



        const filteredToppings = allToppings.filter(topping => {

            const nameMatch = topping.name.toLowerCase().includes(search);

            const categoryMatch = category ? topping.category_name === category : true;

            return nameMatch && categoryMatch;

        });



        toppingsGrid.innerHTML = filteredToppings.map(topping => `

                    <div class="col-xl-3 col-md-6 col-sm-12 mb-3 topping-item">

                        <div class="card h-100 menu-card ${isToppingSelected(itemId, topping.id) ? 'border-primary' : ''}" style="cursor: pointer;" onclick="toggleTopping('${itemId}', '${topping.id}')">

                            <div class="card-image-container position-relative" style="height: 150px; overflow: hidden;">

                                ${getImageHTML(topping.image_url, topping.name, 'medium')}

                                <div class="position-absolute top-0 end-0 p-2">

                                    ${isToppingSelected(itemId, topping.id) ?

                '<span class="badge bg-primary">Dipilih</span>' :

                '<span class="badge bg-light text-dark">Tersedia</span>'

            }

                                </div>

                            </div>

                            <div class="card-body d-flex flex-column">

                                <h6 class="card-title mb-1">${topping.name}</h6>

                                <p class="card-text text-muted f-12 flex-grow-1">${topping.description || 'Deskripsi topping'}</p>

                                <div class="d-flex justify-content-between align-items-center mb-2">

                                    <span class="badge bg-light-info text-info">${topping.category_name || 'Topping'}</span>

                                </div>

                                <div class="d-flex justify-content-between align-items-center">

                                    <h5 class="mb-0 text-success">Rp ${formatPrice(topping.price)}</h5>

                                    <div class="btn-group" role="group">

                                        ${isToppingSelected(itemId, topping.id) ? `

                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateToppingQty('${itemId}', '${topping.id}', -1); event.stopPropagation();" title="Kurangi">

                                                <i class="ti ti-minus"></i>

                                            </button>

                                            <span class="btn btn-sm btn-primary">${getToppingQty(itemId, topping.id)}</span>

                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateToppingQty('${itemId}', '${topping.id}', 1); event.stopPropagation();" title="Tambah">

                                                <i class="ti ti-plus"></i>

                                            </button>

                                        ` : `

                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Pilih Topping">

                                                <i class="ti ti-plus"></i>

                                            </button>

                                        `}

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                `).join('');

    }































    // Check if topping is selected for an item

    function isToppingSelected(itemId, toppingId) {

        const item = currentOrder.items.find(i => i.id === itemId);

        if (!item) return false;

        return item.toppings.some(t => t.topping_id === toppingId);

    }



    // Get topping quantity for an item

    function getToppingQty(itemId, toppingId) {

        const item = currentOrder.items.find(i => i.id === itemId);

        if (!item) return 0;

        const topping = item.toppings.find(t => t.topping_id === toppingId);

        return topping ? topping.quantity : 0;

    }



    // Toggle topping selection
    function toggleTopping(itemId, toppingId) {
        const item = currentOrder.items.find(i => i.id === itemId);
        const topping = allToppings.find(t => t.id === toppingId);

        if (!item || !topping) return;

        const existingIndex = item.toppings.findIndex(t => t.topping_id === toppingId);

        if (existingIndex >= 0) {
            // Remove topping
            item.toppings.splice(existingIndex, 1);
        } else {
            // Add topping
            item.toppings.push({
                id: 'topping_' + Date.now(),
                topping_id: topping.id,
                topping_name: topping.name,
                unit_price: topping.price,
                quantity: 1
            });
        }

        // Re-render toppings grid to update selection state
        renderToppingsGrid(itemId);
        // Update the selected items list (will be called when modal closes)
    }

    // Update topping quantity
    function updateToppingQty(itemId, toppingId, change) {
        const item = currentOrder.items.find(i => i.id === itemId);
        if (!item) return;

        const topping = item.toppings.find(t => t.topping_id === toppingId);
        if (!topping) return;

        topping.quantity = Math.max(1, topping.quantity + change);
        // Re-render toppings grid to update quantity display
        renderToppingsGrid(itemId);
    }

    // Show variant (level) selection modal
    function showVariantSelectionModal(product) {
        if (!product.variants || product.variants.length === 0) {
            // No variants, add product directly
            currentOrder.items.push({
                id: 'item_' + Date.now(),
                product_id: product.id,
                product_name: product.name,
                unit_price: product.price,
                quantity: 1,
                toppings: [],
                variants: []
            });
            renderProducts();
            updateSelectedItemsList();
            return;
        }

        const selectedVariants = {};
        let totalPriceAdjustment = 0;

        Swal.fire({
            title: `Pilih Level untuk ${product.name}`,
            html: `
                <div class="container-fluid">
                    <div class="text-start mb-3">
                        <p class="text-muted mb-0">Harga Dasar: <strong class="text-success">Rp ${formatPrice(product.price)}</strong></p>
                        <p class="text-muted mb-0">Penyesuaian Harga: <strong id="variantPriceAdjustment" class="text-info">Rp 0</strong></p>
                        <hr>
                        <h5>Total: <strong id="variantTotalPrice" class="text-success">Rp ${formatPrice(product.price)}</strong></h5>
                    </div>
                    <div id="variantGroupsContainer">
                        ${renderVariantGroups(product.variants)}
                    </div>
                </div>
            `,
            width: '600px',
            showCancelButton: true,
            confirmButtonText: 'Tambahkan ke Pesanan',
            cancelButtonText: 'Batal',
            showCloseButton: true,
            preConfirm: () => {
                // Validate required variant groups
                const requiredGroups = product.variants.filter(g => g.is_required);
                for (const group of requiredGroups) {
                    if (!selectedVariants[group.id]) {
                        Swal.showValidationMessage(`Pilih ${group.name}`);
                        return false;
                    }
                }
                return selectedVariants;
            },
            didOpen: () => {
                // Add event listeners to radio buttons
                product.variants.forEach(group => {
                    group.options.forEach(option => {
                        const radioBtn = document.getElementById(`variant_${group.id}_${option.id}`);
                        if (radioBtn) {
                            radioBtn.addEventListener('change', function () {
                                if (this.checked) {
                                    // Update selected variants
                                    selectedVariants[group.id] = {
                                        group_id: group.id,
                                        group_name: group.name,
                                        option_id: option.id,
                                        option_name: option.name,
                                        price_adjustment: option.price_adjustment
                                    };

                                    // Calculate total price adjustment
                                    totalPriceAdjustment = Object.values(selectedVariants)
                                        .reduce((sum, v) => sum + parseFloat(v.price_adjustment || 0), 0);

                                    // Update price display
                                    document.getElementById('variantPriceAdjustment').textContent =
                                        'Rp ' + formatPrice(totalPriceAdjustment);
                                    document.getElementById('variantTotalPrice').textContent =
                                        'Rp ' + formatPrice(product.price + totalPriceAdjustment);
                                }
                            });
                        }
                    });
                });
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                // Add product with selected variants
                const finalPrice = product.price + totalPriceAdjustment;
                currentOrder.items.push({
                    id: 'item_' + Date.now(),
                    product_id: product.id,
                    product_name: product.name,
                    unit_price: finalPrice,
                    quantity: 1,
                    toppings: [],
                    variants: Object.values(selectedVariants)
                });
                renderProducts();
                updateSelectedItemsList();
                showNotification('Produk berhasil ditambahkan', 'success');
            }
        });
    }

    // Render variant groups for modal
    function renderVariantGroups(variantGroups) {
        return variantGroups.map(group => `
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        ${group.name}
                        ${group.is_required ? '<span class="badge bg-danger ms-2">Wajib</span>' : '<span class="badge bg-secondary ms-2">Opsional</span>'}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        ${group.options.map((option, index) => `
                            <label class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" style="cursor: pointer;">
                                <div class="d-flex align-items-center">
                                    <input 
                                        type="radio" 
                                        class="form-check-input me-3" 
                                        name="variant_group_${group.id}" 
                                        id="variant_${group.id}_${option.id}"
                                        value="${option.id}"
                                        ${index === 0 && group.is_required ? 'checked' : ''}
                                    >
                                    <div>
                                        <strong>${option.name}</strong>
                                        ${option.price_adjustment !== 0 ?
                `<small class="d-block text-muted">
                                                ${option.price_adjustment > 0 ? '+' : ''}Rp ${formatPrice(option.price_adjustment)}
                                            </small>`
                : ''
            }
                                    </div>
                                </div>
                                ${option.price_adjustment > 0 ?
                `<span class="badge bg-light-warning text-warning">+Rp ${formatPrice(option.price_adjustment)}</span>` :
                option.price_adjustment < 0 ?
                    `<span class="badge bg-light-success text-success">-Rp ${formatPrice(Math.abs(option.price_adjustment))}</span>` :
                    `<span class="badge bg-light-secondary text-secondary">Standar</span>`
            }
                            </label>
                        `).join('')}
                    </div>
                </div>
            </div>
        `).join('');
    }


    // STEP 3: Pembayaran (formerly Step 4)
    function getStep3HTML() {
        return `
            <div class="row g-4">
                <!-- Left Column - Order Summary -->
                <div class="col-lg-8">
                    <!-- Ringkasan Pesanan -->
                    <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 16px; overflow: hidden; margin-bottom: 24px;">
                        <div style="background: linear-gradient(135deg, #f97316, #fb923c); padding: 20px;">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="ti ti-shopping-cart text-white" style="font-size: 24px;"></i>
                                </div>
                                <div>
                                    <h5 class="text-white mb-0" style="font-weight: 700;">Ringkasan Pesanan</h5>
                                    <p class="text-white mb-0" style="opacity: 0.9; font-size: 13px;">Detail pesanan yang akan diproses</p>
                                </div>
                            </div>
                        </div>
                        <div style="padding: 24px;">
                            <div id="step3_orderSummary"></div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Customer Info & Payment -->
                <div class="col-lg-4">
                    <!-- Detail Pelanggan -->
                    <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 20px; margin-bottom: 20px;">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #8b5cf6, #a78bfa); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-user text-white" style="font-size: 18px;"></i>
                            </div>
                            <h6 class="mb-0" style="font-weight: 700; color: #1f2937;">Detail Pelanggan</h6>
                        </div>
                        
                        <div style="background: #f9fafb; border-radius: 12px; padding: 16px;">
                            <div class="mb-3">
                                <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; mb-1">Nama</div>
                                <div style="font-size: 14px; font-weight: 600; color: #1f2937;" id="step3_name">-</div>
                            </div>
                            <div class="mb-3">
                                <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; mb-1">Meja</div>
                                <div style="font-size: 14px; font-weight: 600; color: #1f2937;" id="step3_table">-</div>
                            </div>
                            <div>
                                <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; mb-1">Catatan</div>
                                <div style="font-size: 13px; color: #6b7280; font-style: italic;" id="step3_notes">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Pembayaran -->
                    <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 20px; margin-bottom: 20px;">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #059669, #10b981); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-calculator text-white" style="font-size: 18px;"></i>
                            </div>
                            <h6 class="mb-0" style="font-weight: 700; color: #1f2937;">Total Pembayaran</h6>
                        </div>
                        
                        <div style="background: #f9fafb; border-radius: 12px; padding: 16px;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span style="font-size: 13px; color: #6b7280;">Subtotal</span>
                                <span style="font-size: 14px; font-weight: 600; color: #1f2937;" id="step3_subtotal">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span style="font-size: 13px; color: #6b7280;">Pajak (0%)</span>
                                <span style="font-size: 14px; font-weight: 600; color: #1f2937;" id="step3_tax">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span style="font-size: 13px; color: #6b7280;">Diskon</span>
                                <span style="font-size: 14px; font-weight: 600; color: #dc2626;" id="step3_discount">Rp 0</span>
                            </div>
                            <div style="border-top: 2px dashed #d1d5db; padding-top: 12px;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span style="font-size: 15px; font-weight: 700; color: #1f2937;">TOTAL</span>
                                    <span style="font-size: 20px; font-weight: 700; color: #059669;" id="step3_total">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 20px;">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #3b82f6, #60a5fa); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-credit-card text-white" style="font-size: 18px;"></i>
                            </div>
                            <h6 class="mb-0" style="font-weight: 700; color: #1f2937;">Metode Pembayaran</h6>
                        </div>
                        
                        <select class="form-select mb-3" id="step3_paymentMethod" onchange="togglePaymentMethod()" 
                                style="border: 2px solid #e5e7eb; border-radius: 12px; padding: 12px 16px; font-weight: 600; font-size: 14px;">
                            <option value="cash">💵 Tunai (Cash)</option>
                            <option value="midtrans">💳 Midtrans (Digital Payment)</option>
                        </select>
                        
                        <!-- Cash Payment Info -->
                        <div id="cashPaymentInfo" style="background: linear-gradient(135deg, #dbeafe, #bfdbfe); border: 1px solid #93c5fd; border-radius: 12px; padding: 16px;">
                            <div class="d-flex gap-3">
                                <div style="flex-shrink: 0;">
                                    <div style="width: 40px; height: 40px; background: #3b82f6; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="ti ti-cash text-white" style="font-size: 20px;"></i>
                                    </div>
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 14px; color: #1e40af; margin-bottom: 4px;">Pembayaran Tunai</div>
                                    <div style="font-size: 13px; color: #1e40af; line-height: 1.5;">
                                        Pembayaran akan diproses di kasir setelah pesanan dikonfirmasi
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Midtrans Payment Info -->
                        <div id="midtransPaymentInfo" class="d-none" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); border: 1px solid #6ee7b7; border-radius: 12px; padding: 16px;">
                            <div class="d-flex gap-3 mb-3">
                                <div style="flex-shrink: 0;">
                                    <div style="width: 40px; height: 40px; background: #059669; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="ti ti-credit-card text-white" style="font-size: 20px;"></i>
                                    </div>
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 14px; color: #065f46; margin-bottom: 4px;">Midtrans Payment Gateway</div>
                                    <div style="font-size: 12px; color: #065f46;">Metode pembayaran digital yang tersedia:</div>
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                                <div style="background: rgba(255,255,255,0.5); border-radius: 8px; padding: 8px; text-align: center;">
                                    <div style="font-size: 11px; font-weight: 600; color: #065f46;">💳 Kartu</div>
                                    <div style="font-size: 9px; color: #047857;">Visa, Mastercard</div>
                                </div>
                                <div style="background: rgba(255,255,255,0.5); border-radius: 8px; padding: 8px; text-align: center;">
                                    <div style="font-size: 11px; font-weight: 600; color: #065f46;">📱 E-Wallet</div>
                                    <div style="font-size: 9px; color: #047857;">GoPay, DANA, OVO</div>
                                </div>
                                <div style="background: rgba(255,255,255,0.5); border-radius: 8px; padding: 8px; text-align: center;">
                                    <div style="font-size: 11px; font-weight: 600; color: #065f46;">🏦 Bank Transfer</div>
                                    <div style="font-size: 9px; color: #047857;">BCA, Mandiri, BNI</div>
                                </div>
                                <div style="background: rgba(255,255,255,0.5); border-radius: 8px; padding: 8px; text-align: center;">
                                    <div style="font-size: 11px; font-weight: 600; color: #065f46;">🏪 Retail</div>
                                    <div style="font-size: 9px; color: #047857;">Indomaret, Alfamart</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function updateStep3Summary() {
        // Customer info
        document.getElementById('step3_name').textContent = currentOrder.customer_name || '-';
        document.getElementById('step3_table').textContent = currentOrder.table_number || '-';
        document.getElementById('step3_notes').textContent = currentOrder.notes || 'Tidak ada catatan';

        // Order summary - using seblak items format
        let subtotal = 0;
        const summaryHTML = currentOrder.items.map((order, index) => {
            const spiceLevelPrice = order.spice_level ? (allSpiceLevels.find(sl => sl.id === order.spice_level)?.price || 0) : 0;

            // Calculate customizations price
            let customizationsPrice = 0;
            if (order.customizations) {
                if (Array.isArray(order.customizations)) {
                    customizationsPrice = order.customizations.reduce((sum, custId) => {
                        const cust = allCustomizationOptions.find(c => c.id === custId);
                        return sum + (cust ? parseFloat(cust.price) : 0);
                    }, 0);
                } else {
                    customizationsPrice = Object.values(order.customizations).reduce((sum, custId) => {
                        const cust = allCustomizationOptions.find(c => c.id === custId);
                        return sum + (cust ? parseFloat(cust.price) : 0);
                    }, 0);
                }
            }

            const toppingsPrice = order.toppings.reduce((sum, t) => sum + (t.unit_price * t.quantity), 0);
            const itemPrice = order.base_price + spiceLevelPrice + customizationsPrice + toppingsPrice;
            const orderTotal = itemPrice * order.quantity;
            subtotal += orderTotal;

            const spiceLevel = allSpiceLevels.find(sl => sl.id === order.spice_level);

            return `
                <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; margin-bottom: 16px;">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #f97316, #fb923c); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-soup text-white" style="font-size: 16px;"></i>
                            </div>
                            <div>
                                <div style="font-weight: 700; font-size: 15px; color: #1f2937;">Seblak #${index + 1}</div>
                                <div style="font-size: 12px; color: #6b7280;">${order.quantity} Porsi × Rp ${formatPrice(itemPrice)}</div>
                            </div>
                        </div>
                        <div style="font-weight: 700; font-size: 16px; color: #059669;">
                            Rp ${formatPrice(orderTotal)}
                        </div>
                    </div>

                    <!-- Details -->
                    <div style="background: #f9fafb; border-radius: 8px; padding: 12px;">
                        ${spiceLevel ? `
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-flame" style="font-size: 13px; color: #dc2626;"></i>
                                    <span style="font-size: 12px; color: #374151;">${spiceLevel.name}</span>
                                </div>
                                <span style="font-size: 12px; font-weight: 600; color: #6b7280;">
                                    ${spiceLevelPrice > 0 ? 'Rp ' + formatPrice(spiceLevelPrice) : 'Gratis'}
                                </span>
                            </div>
                        ` : ''}
                        
                        ${order.customizations && Object.keys(order.customizations).length > 0 ? `
                            ${Object.values(order.customizations).map(cId => {
                const cust = allCustomizationOptions.find(c => c.id === cId);
                return cust ? `
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ti ti-adjustments" style="font-size: 13px; color: #7c3aed;"></i>
                                            <span style="font-size: 12px; color: #374151;">${cust.name}</span>
                                        </div>
                                        <span style="font-size: 12px; font-weight: 600; color: #6b7280;">
                                            ${cust.price > 0 ? 'Rp ' + formatPrice(cust.price) : 'Gratis'}
                                        </span>
                                    </div>
                                ` : '';
            }).join('')}
                        ` : ''}
                        
                        ${order.toppings.length > 0 ? `
                            ${order.toppings.map(t => `
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ti ti-cheese" style="font-size: 13px; color: #f59e0b;"></i>
                                        <span style="font-size: 12px; color: #374151;">${t.topping_name} (${t.quantity}x)</span>
                                    </div>
                                    <span style="font-size: 12px; font-weight: 600; color: #6b7280;">
                                        Rp ${formatPrice(t.unit_price * t.quantity)}
                                    </span>
                                </div>
                            `).join('')}
                        ` : ''}
                    </div>
                </div>
            `;
        }).join('');

        document.getElementById('step3_orderSummary').innerHTML = summaryHTML || `
            <div class="text-center py-4" style="color: #9ca3af;">
                <i class="ti ti-shopping-cart-off" style="font-size: 48px; opacity: 0.3;"></i>
                <p class="mt-2 mb-0">Tidak ada pesanan</p>
            </div>
        `;

        const tax = 0;
        const discount = 0;
        const total = subtotal + tax - discount;

        document.getElementById('step3_subtotal').textContent = 'Rp ' + formatPrice(subtotal);
        document.getElementById('step3_tax').textContent = 'Rp ' + formatPrice(tax);
        document.getElementById('step3_discount').textContent = 'Rp ' + formatPrice(discount);
        document.getElementById('step3_total').textContent = 'Rp ' + formatPrice(total);

        document.getElementById('step3_paymentMethod').value = currentOrder.payment_method || 'cash';
    }

    // Toggle payment method info
    function togglePaymentMethod() {
        const method = document.getElementById('step3_paymentMethod').value;
        const cashInfo = document.getElementById('cashPaymentInfo');
        const midtransInfo = document.getElementById('midtransPaymentInfo');

        if (method === 'midtrans') {
            cashInfo.classList.add('d-none');
            midtransInfo.classList.remove('d-none');
        } else {
            cashInfo.classList.remove('d-none');
            midtransInfo.classList.add('d-none');
        }
    }

    // Submit transaction
    async function submitNewOrder() {
        console.log('🚀 submitNewOrder() called');
        currentOrder.payment_method = document.getElementById('step3_paymentMethod').value;
        console.log('Payment method:', currentOrder.payment_method);
        console.log('Current order object:', currentOrder);

        // Validate items exist
        if (!currentOrder.items || currentOrder.items.length === 0) {
            console.error('❌ No items in order!');
            showNotification('Tidak ada item dalam pesanan', 'error');
            return;
        }

        // Prepare order data with proper structure
        const orderData = {
            customer_name: currentOrder.customer_name,
            order_type: currentOrder.order_type || 'dine_in',
            table_number: currentOrder.table_number,
            pickup_time: currentOrder.pickup_time || null,
            delivery_address: currentOrder.delivery_address || null,
            notes: currentOrder.notes,
            payment_method: currentOrder.payment_method,
            items: currentOrder.items.map(item => ({
                quantity: item.quantity || 1,
                spice_level: item.spice_level,
                customizations: item.customizations || {},
                toppings: item.toppings ? item.toppings.map(t => ({
                    topping_id: t.topping_id,
                    topping_name: t.topping_name,
                    quantity: t.quantity,
                    unit_price: t.unit_price
                })) : []
            }))
        };

        console.log('📦 Prepared order data:');
        console.log(JSON.stringify(orderData, null, 2));

        try {
            // Semua metode pembayaran (Cash, Midtrans, dll) menggunakan endpoint yang sama
            console.log('Processing payment with method:', currentOrder.payment_method);
            await processPayment(orderData);
        } catch (error) {
            console.error('❌ Error submitting order:', error);
            // Error sudah ditangani di processPayment
            // Tidak perlu alert lagi di sini
        }
    }

    // Process payment (all payment methods)
    async function processPayment(orderData) {
        try {
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                html: 'Mohon tunggu, sedang menyimpan transaksi',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            console.log('Calling API: /api/orders/create-transaction.php');
            const response = await fetch('/api/orders/create-transaction.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(orderData)
            });

            console.log('Response status:', response.status);
            const result = await response.json();
            console.log('Response data:', result);

            Swal.close();

            if (result.success) {
                // Show success message with order details
                Swal.fire({
                    icon: 'success',
                    title: 'Transaksi Berhasil!',
                    html: `
                        <div class="text-start">
                            <p class="mb-2"><strong>No. Invoice:</strong> ${result.data.order_number}</p>
                            <p class="mb-2"><strong>Total:</strong> Rp ${formatPrice(result.data.total_amount)}</p>
                            <p class="mb-2"><strong>Metode:</strong> ${result.data.payment_method.toUpperCase()}</p>
                            <hr>
                            <small class="text-muted">Silakan lanjutkan pembayaran sesuai metode yang dipilih</small>
                        </div>
                    `,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#10b981'
                }).then(() => {
                    // Reset form and reload orders
                    resetNewOrderForm();
                    showOrderList();
                    loadOrders(); // Refresh order list
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: result.message || 'Gagal membuat transaksi',
                    confirmButtonColor: '#ef4444'
                });
            }
        } catch (error) {
            Swal.close();
            console.error('Error in processCashPayment:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan: ' + error.message,
                confirmButtonColor: '#ef4444'
            });
            throw error;
        }
    }


    // Add topping to item
    function addToppingToItem(itemId) {
        const toppingsHTML = allToppings.map(topping => `
            <div class="col-md-6 mb-2">
                <div class="card topping-card" onclick="selectTopping('${itemId}', '${topping.id}')">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between">
                            <span>${topping.name}</span>
                            <strong class="text-success">Rp ${formatPrice(topping.price)}</strong>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        Swal.fire({
            title: 'Pilih Topping',
            html: `<div class="row">${toppingsHTML}</div>`,
            width: '600px',
            showConfirmButton: false,
            showCloseButton: true
        });
    }











    // View order detail
    async function viewOrderDetail(orderId) {
        try {
            console.log('Loading order detail for ID:', orderId);

            // Show loading
            Swal.fire({
                title: 'Memuat Detail...',
                html: '<div class="spinner-border text-primary" role="status"></div>',
                showConfirmButton: false,
                allowOutsideClick: false
            });

            const url = `api/orders.php?id=${orderId}`;
            console.log('Fetching from:', url);

            const response = await fetch(url);
            console.log('Response status:', response.status);

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Response error:', errorText);
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log('Order detail result:', result);

            if (result.success && result.data) {
                const order = result.data;

                const itemsHTML = order.items && order.items.length > 0 ? order.items.map((item, idx) => `
                    <div style="padding: 20px; ${idx !== order.items.length - 1 ? 'border-bottom: 2px solid #f1f5f9;' : ''}">
                        <!-- Item Header dengan Harga -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="background: #667eea; 
                                            width: 36px; height: 36px; 
                                            border-radius: 50%; 
                                            display: flex; 
                                            align-items: center; 
                                            justify-content: center;
                                            color: white;
                                            font-weight: 700;
                                            font-size: 15px;
                                            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);">
                                    ${idx + 1}
                                </div>
                                <div>
                                    <div style="color: #0f172a; font-size: 16px; font-weight: 700;">
                                        ${item.product_name || 'Seblak'}
                                    </div>
                                    <div style="color: #64748b; font-size: 12px; margin-top: 2px;">
                                        ${item.quantity || 1} porsi
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="color: #0f172a; font-size: 20px; font-weight: 700;">
                                    Rp ${formatPrice(item.subtotal || 0)}
                                </div>
                            </div>
                        </div>

                        <!-- Detail Section dengan Color Coding -->
                        <div style="margin-left: 48px;">
                            <!-- 1. Spice Level (Red = Pedas) -->
                            ${item.spice_level_name ? `
                            <div style="background: #fef2f2; 
                                        border-left: 4px solid #ef4444; 
                                        border-radius: 8px; 
                                        padding: 12px 16px; 
                                        margin-bottom: 10px;">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <i class="ti ti-flame" style="color: #dc2626; font-size: 16px;"></i>
                                        <span style="color: #991b1b; font-weight: 600; font-size: 13px;">Tingkat Kepedasan</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="background: #dc2626; color: white; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                            ${item.spice_level_name}
                                        </span>
                                        ${item.spice_level_price && parseFloat(item.spice_level_price) > 0 ? `
                                        <span style="color: #dc2626; font-size: 12px; font-weight: 600;">+Rp ${formatPrice(item.spice_level_price)}</span>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                            ` : ''}

                            <!-- 2. Customizations (Blue = Pilihan) -->
                            ${item.customizations && item.customizations.length > 0 ? `
                            <div style="background: #eff6ff; 
                                        border-left: 4px solid #3b82f6; 
                                        border-radius: 8px; 
                                        padding: 12px 16px; 
                                        margin-bottom: 10px;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                    <i class="ti ti-adjustments" style="color: #2563eb; font-size: 16px;"></i>
                                    <span style="color: #1e40af; font-weight: 600; font-size: 13px;">Pilihan Kustomisasi</span>
                                </div>
                                <div style="display: grid; gap: 6px;">
                                    ${item.customizations.map(c => {
                    // Format nama customization type untuk display
                    const typeLabel = c.customization_type === 'kencur_level' ? 'Kencur' :
                        c.customization_type === 'broth_flavor' ? 'Kuah' :
                            c.customization_type === 'egg_type' ? 'Telur' :
                                c.customization_type;
                    return `
                                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 4px 0;">
                                            <span style="color: #1e40af; font-size: 13px;">• ${typeLabel}: <strong>${c.customization_name || '-'}</strong></span>
                                            <span style="color: #2563eb; font-size: 12px; font-weight: 600;">+Rp ${formatPrice(c.price || 0)}</span>
                                        </div>
                                    `}).join('')}
                                </div>
                            </div>
                            ` : ''}

                            <!-- 3. Toppings (Green = Extra/Tambahan) -->
                            ${item.toppings && item.toppings.length > 0 ? `
                            <div style="background: #f0fdf4; 
                                        border-left: 4px solid #10b981; 
                                        border-radius: 8px; 
                                        padding: 12px 16px; 
                                        margin-bottom: 10px;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                    <i class="ti ti-circle-plus" style="color: #059669; font-size: 16px;"></i>
                                    <span style="color: #065f46; font-weight: 600; font-size: 13px;">Topping Tambahan</span>
                                </div>
                                <div style="display: grid; gap: 6px;">
                                    ${item.toppings.map(t => `
                                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 4px 0;">
                                            <span style="color: #065f46; font-size: 13px;">• ${t.topping_name} <span style="color: #64748b;">(${t.quantity}x)</span></span>
                                            <span style="color: #059669; font-size: 12px; font-weight: 600;">Rp ${formatPrice(t.subtotal)}</span>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                            ` : ''}

                            <!-- 4. Notes (Yellow = Catatan Khusus) -->
                            ${item.notes ? `
                            <div style="background: #fefce8; 
                                        border-left: 4px solid #eab308; 
                                        border-radius: 8px; 
                                        padding: 12px 16px;">
                                <div style="display: flex; gap: 10px;">
                                    <i class="ti ti-message-circle" style="color: #ca8a04; font-size: 16px; margin-top: 2px;"></i>
                                    <div>
                                        <div style="color: #854d0e; font-weight: 600; font-size: 12px; margin-bottom: 4px;">Catatan Khusus</div>
                                        <div style="color: #713f12; font-size: 13px; line-height: 1.5; font-style: italic;">
                                            "${item.notes}"
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                `).join('') : '<p class="text-muted text-center py-3">Tidak ada item</p>';

                Swal.fire({
                    html: `
                        <div style="text-align: left; padding: 32px; background: #ffffff;">
                            <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                                <button class="btn btn-dark" id="btnPrintStruk" style="font-size: 14px;">
                                    <i class="ti ti-printer me-2"></i> Cetak Struk
                                </button>
                            </div>
                            <!-- Minimalist Header - Invoice Info -->
                            <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 24px; margin-bottom: 28px;">
                                <div style="margin-bottom: 24px;">
                                    <div style="color: #0f172a; font-size: 28px; font-weight: 700; margin-bottom: 6px;">
                                        ${order.order_number}
                                    </div>
                                    <div style="color: #64748b; font-size: 14px;">
                                        <i class="ti ti-calendar" style="font-size: 14px;"></i> ${formatDate(order.created_at)} • ${formatTime(order.created_at)}
                                    </div>
                                </div>
                                <!-- Unified Container: Info Lengkap -->
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;">
                                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0;">
                                        <div style="padding: 20px; border-right: 1px solid #e2e8f0;">
                                            <div style="color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 14px;">👤 Pelanggan</div>
                                            <div style="margin-bottom: 10px;"><div style="color: #94a3b8; font-size: 11px; margin-bottom: 3px;">Nama</div><div style="color: #0f172a; font-size: 14px; font-weight: 600;">${order.customer_name}</div></div>
                                        </div>
                                        <div style="padding: 20px; border-right: 1px solid #e2e8f0;">
                                            <div style="color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 14px;">📋 Tipe Pesanan</div>
                                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                                                ${order.order_type === 'dine_in'
                            ? `<i class="ti ti-utensils" style="font-size: 24px; color: #667eea;"></i><div><div style="color: #0f172a; font-weight: 700; font-size: 15px;">Dine In</div><div style="color: #64748b; font-size: 11px;">Makan di Tempat</div></div>`
                            : `<i class="ti ti-shopping-bag" style="font-size: 24px; color: #667eea;"></i><div><div style="color: #0f172a; font-weight: 700; font-size: 15px;">Take Away</div><div style="color: #64748b; font-size: 11px;">Bawa Pulang</div></div>`}
                                            </div>
                                            ${order.order_type === 'dine_in' && order.table_number ? `<div style="background: #fff; border: 1px dashed #e2e8f0; border-radius: 6px; padding: 8px; text-align: center;"><div style="color: #64748b; font-size: 10px; margin-bottom: 2px;">Nomor Meja</div><div style="color: #0f172a; font-size: 22px; font-weight: 700;">${order.table_number}</div></div>` : ''}
                                        </div>
                                        <div style="padding: 20px; border-right: 1px solid #e2e8f0;">
                                            <div style="color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 14px;">💳 Pembayaran</div>
                                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                                                ${order.payment_method === 'midtrans'
                            ? `<i class="ti ti-credit-card" style="font-size: 24px; color: #3b82f6;"></i><div><div style="color: #0f172a; font-weight: 700; font-size: 15px;">Midtrans</div><div style="color: #64748b; font-size: 11px;">Payment Gateway</div></div>`
                            : `<i class="ti ti-cash" style="font-size: 24px; color: #10b981;"></i><div><div style="color: #0f172a; font-weight: 700; font-size: 15px;">Cash</div><div style="color: #64748b; font-size: 11px;">Tunai</div></div>`}
                                            </div>
                                        </div>
                                        <div style="padding: 20px;">
                                            <div style="color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 14px;">📊 Status</div>
                                            <div style="margin-bottom: 10px;"><div style="color: #94a3b8; font-size: 11px; margin-bottom: 4px;">Pesanan</div>${getStatusBadge(order.order_status)}</div>
                                            <div><div style="color: #94a3b8; font-size: 11px; margin-bottom: 4px;">Pembayaran</div>${getPaymentBadge(order.payment_status)}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="max-height: 550px; overflow-y: auto; margin: 0 -8px; padding: 0 8px;">
                                <div style="margin-bottom: 28px;"><div style="color: #0f172a; font-size: 15px; font-weight: 700; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #e2e8f0;">Pesanan</div><div style="background: #fafbfc; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;">${itemsHTML}</div></div>
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;"><div style="padding: 20px 24px;"><div style="display: flex; justify-content: space-between; margin-bottom: 10px;"><span style="color: #64748b; font-size: 14px;">Subtotal</span><span style="color: #0f172a; font-size: 14px; font-weight: 600;">Rp ${formatPrice(order.subtotal)}</span></div>${order.tax > 0 ? `<div style="display: flex; justify-content: space-between; margin-bottom: 10px;"><span style="color: #64748b; font-size: 14px;">Pajak</span><span style="color: #0f172a; font-size: 14px; font-weight: 600;">Rp ${formatPrice(order.tax)}</span></div>` : ''}</div><div style="background: #0f172a; padding: 20px 24px; border-top: 1px solid #e2e8f0;"><div style="display: flex; justify-content: space-between; align-items: center;"><span style="color: #94a3b8; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Pembayaran</span><span style="color: #ffffff; font-size: 24px; font-weight: 700;">Rp ${formatPrice(order.total_amount)}</span></div></div></div>
                                ${order.notes ? `<div style="margin-top: 20px; background: #fffbeb; border: 1px solid #fcd34d; border-radius: 8px; padding: 16px;"><div style="display: flex; gap: 10px;"><i class="ti ti-message-circle" style="color: #d97706; font-size: 18px;"></i><div style="flex: 1;"><div style="color: #92400e; font-size: 11px; font-weight: 600; text-transform: uppercase; margin-bottom: 6px;">Catatan</div><div style="color: #78350f; font-size: 13px; line-height: 1.5;">${order.notes}</div></div></div></div>` : ''}
                            </div>
                        </div>
                        <!-- Template Struk untuk Print (hidden) -->
                        <div id="strukPrintArea" style="display:none;">
                            <div style="width:340px; font-family:monospace, 'Courier New', Courier, Arial; color:#222; background:#fff; padding:8px 0;">
                                <div style="text-align:center; font-size:17px; font-weight:bold; letter-spacing:1px; margin-bottom:2px;">SEBLAK PREDATOR</div>
                                <div style="text-align:center; font-size:11px; margin-bottom:8px;">No: <b>${order.order_number}</b><br>${formatDate(order.created_at)} ${formatTime(order.created_at)}</div>
                                <div style="border-bottom:1px dashed #aaa; margin-bottom:7px;"></div>
                                <div style="font-size:12px; margin-bottom:3px;"><b>Pelanggan:</b> ${order.customer_name}</div>
                                <div style="font-size:12px; margin-bottom:3px;"><b>Tipe:</b> ${order.order_type === 'dine_in' ? 'Dine In' : 'Take Away'}${order.table_number ? ' (Meja ' + order.table_number + ')' : ''}</div>
                                <div style="border-bottom:1px dashed #aaa; margin-bottom:7px;"></div>
                                <div style="font-size:12px; font-weight:bold; margin-bottom:2px;">Pesanan</div>
                                <table style="width:100%; font-size:12px; border-collapse:collapse; margin-bottom:4px;">
                                    <thead>
                                        <tr>
                                            <th style="text-align:left; border-bottom:1px solid #eee;">Menu</th>
                                            <th style="text-align:right; border-bottom:1px solid #eee;">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${(order.items || []).map(item => `
                                            <tr>
                                                <td>${item.product_name} <span style='font-size:11px;'>x${item.quantity}</span>
                                                    ${(item.toppings && item.toppings.length > 0) ? item.toppings.map(t => `<div style='font-size:11px; margin-left:8px;'>+ ${t.topping_name} (${t.quantity}x)</div>`).join('') : ''}
                                                </td>
                                                <td style="text-align:right; vertical-align:top;">Rp${formatPrice(item.subtotal)}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                                <div style="border-bottom:1px dashed #aaa; margin:7px 0;"></div>
                                <div style="display:flex; justify-content:space-between; font-size:12px;"><span>Subtotal</span><span>Rp${formatPrice(order.subtotal)}</span></div>
                                ${order.tax > 0 ? `<div style='display:flex; justify-content:space-between; font-size:12px;'><span>Pajak</span><span>Rp${formatPrice(order.tax)}</span></div>` : ''}
                                <div style="display:flex; justify-content:space-between; font-size:14px; font-weight:bold; margin-top:2px;"><span>Total</span><span>Rp${formatPrice(order.total_amount)}</span></div>
                                <div style="font-size:12px; margin-top:7px;"><b>Metode:</b> ${order.payment_method === 'midtrans' ? 'Midtrans' : 'Cash'}</div>
                                <div style="font-size:12px;"><b>Status:</b> ${order.payment_status === 'paid' ? 'Sudah Bayar' : 'Belum Bayar'}</div>
                                ${order.notes ? `<div style='font-size:12px; margin-top:7px;'><b>Catatan:</b> ${order.notes}</div>` : ''}
                                <div style="text-align:center; font-size:12px; margin-top:10px; border-top:1px dashed #aaa; padding-top:6px;">Terima kasih telah berbelanja!<br><span style='font-size:11px;'>${new Date().getFullYear()} Seblak Predator</span></div>
                            </div>
                        </div>
                    `,
                    width: '900px',
                    showCancelButton: false,
                    confirmButtonText: '<i class="ti ti-x me-2"></i>Tutup',
                    confirmButtonColor: '#0f172a',
                    buttonsStyling: true,
                    customClass: {
                        popup: 'border-0 shadow-xl',
                        confirmButton: 'px-4 py-2'
                    },
                    padding: '0',
                    didOpen: () => {
                        document.getElementById('btnPrintStruk').onclick = function () {
                            printStruk();
                        };
                    }
                });
                // Fungsi untuk print struk
                function printStruk() {
                    const printContents = document.getElementById('strukPrintArea').innerHTML;
                    const win = window.open('', '', 'width=400,height=600');
                    win.document.write('<html><head><title>Cetak Struk</title>');
                    win.document.write('<style>body{margin:0;padding:10px;}@media print{body{margin:0;}}</style>');
                    win.document.write('</head><body>');
                    win.document.write(printContents);
                    win.document.write('</body></html>');
                    win.document.close();
                    win.focus();
                    setTimeout(() => { win.print(); win.close(); }, 300);
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: result.message || 'Gagal memuat detail transaksi'
                });
            }
        } catch (error) {
            console.error('Error loading order detail:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memuat Detail',
                text: error.message || 'Terjadi kesalahan saat memuat detail transaksi',
                footer: '<small>Periksa console browser untuk detail error</small>'
            });
        }
    }

    // Reopen Midtrans Snap for existing order
    async function reopenMidtransSnap(orderNumber, orderId) {
        try {
            // Show loading
            Swal.fire({
                title: 'Memuat...',
                html: 'Sedang menyiapkan pembayaran Midtrans',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Get order details to fetch snap token
            const response = await fetch(`api/orders.php?id=${orderId}`);
            const result = await response.json();

            if (result.success && result.data) {
                const order = result.data;

                // Prepare item details for Midtrans
                const itemDetails = order.items.map(item => {
                    // Calculate item price including customizations and toppings
                    let itemPrice = parseFloat(item.base_price || 0);

                    // Add spice level price
                    if (item.spice_level_price) {
                        itemPrice += parseFloat(item.spice_level_price);
                    }

                    // Add customizations price
                    if (item.customizations && Array.isArray(item.customizations)) {
                        item.customizations.forEach(c => {
                            itemPrice += parseFloat(c.price || 0);
                        });
                    }

                    // Add toppings price
                    if (item.toppings && Array.isArray(item.toppings)) {
                        item.toppings.forEach(t => {
                            itemPrice += parseFloat(t.unit_price || 0) * parseInt(t.quantity || 1);
                        });
                    }

                    return {
                        id: item.id,
                        name: item.product_name + (item.spice_level_name ? ' - ' + item.spice_level_name : ''),
                        price: Math.round(itemPrice),
                        quantity: parseInt(item.quantity)
                    };
                });

                // Get snap token using new endpoint (no duplicate transaction)
                const snapResponse = await fetch('api/midtrans/get-snap-token.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        order_number: order.order_number,
                        customer_name: order.customer_name,
                        total_amount: order.total_amount,
                        items: itemDetails
                    })
                });

                const snapResult = await snapResponse.json();
                console.log('Snap token response:', snapResult);

                if (snapResult.success && snapResult.snap_token) {
                    Swal.close();

                    // Log if token is reused
                    if (snapResult.reused) {
                        console.log('✅ Using existing Snap token (no duplicate transaction created)');
                    } else {
                        console.log('🆕 New Snap token generated');
                    }

                    // Check if Snap.js is loaded
                    if (typeof window.snap === 'undefined') {
                        console.error('Snap.js not loaded!');
                        showNotification('Snap.js tidak dimuat! Periksa kredensial Midtrans.', 'error');
                        return;
                    }

                    console.log('Opening Snap with token:', snapResult.snap_token);

                    // Add delay to ensure Swal is completely closed and DOM is ready
                    setTimeout(() => {
                        try {
                            // Open Midtrans Snap popup
                            window.snap.pay(snapResult.snap_token, {
                                onSuccess: function (result) {
                                    console.log('Payment success:', result);
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Pembayaran Berhasil!',
                                        text: 'Status pembayaran akan diperbarui otomatis oleh sistem',
                                        timer: 3000,
                                        showConfirmButton: false
                                    });
                                    // Reload orders to get updated status from webhook
                                    setTimeout(() => loadOrders(), 3000);
                                },
                                onPending: function (result) {
                                    console.log('Payment pending:', result);
                                    Swal.fire({
                                        icon: 'info',
                                        title: 'Pembayaran Menunggu',
                                        text: 'Pembayaran sedang diproses',
                                        timer: 3000,
                                        showConfirmButton: false
                                    });
                                    setTimeout(() => loadOrders(), 3000);
                                },
                                onError: function (result) {
                                    console.log('Payment error:', result);
                                    showNotification('Pembayaran gagal! Silakan coba lagi.', 'error');
                                },
                                onClose: function () {
                                    console.log('Payment popup closed');
                                    showNotification('Pembayaran dibatalkan', 'info');
                                    loadOrders(); // Refresh to check if any status update
                                }
                            });
                        } catch (error) {
                            console.error('Error opening Snap:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Membuka Pembayaran',
                                text: error.message || 'Silakan coba lagi',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    }, 500); // Delay 500ms to ensure clean state
                } else {
                    Swal.close();
                    showNotification(snapResult.message || 'Gagal mendapatkan token pembayaran', 'error');
                }
            } else {
                Swal.close();
                showNotification('Gagal memuat detail order', 'error');
            }
        } catch (error) {
            console.error('Error reopening Midtrans Snap:', error);
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan: ' + error.message
            });
        }
    }

    // Confirm payment and complete order (for CASH payment only)
    async function confirmPaymentAndComplete(orderId) {
        const confirm = await Swal.fire({
            title: 'Konfirmasi Pembayaran & Selesaikan?',
            text: 'Tandai pembayaran CASH sudah diterima dan pesanan selesai?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="ti ti-check me-1"></i>Ya, Sudah Dibayar & Selesai',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#10b981'
        });

        if (!confirm.isConfirmed) return;

        try {
            const response = await fetch('api/orders.php', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: orderId,
                    payment_status: 'paid',
                    order_status: 'completed'
                })
            });

            console.log('Confirm payment response status:', response.status);

            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Pembayaran dikonfirmasi dan pesanan selesai',
                    timer: 2000,
                    showConfirmButton: false
                });
                loadOrders();
            } else {
                showNotification(result.message || 'Gagal memproses pesanan', 'error');
            }
        } catch (error) {
            console.error('Error processing order:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memproses',
                text: error.message || 'Terjadi kesalahan saat memproses pesanan'
            });
        }
    }

    // Cancel order
    async function cancelOrder(orderId) {
        const confirm = await Swal.fire({
            title: 'Batalkan Transaksi?',
            text: 'Transaksi yang dibatalkan tidak dapat dikembalikan',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Tidak',
            confirmButtonColor: '#dc3545'
        });

        if (!confirm.isConfirmed) return;

        try {
            const response = await fetch('api/orders.php', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: orderId,
                    order_status: 'cancelled'
                })
            });

            const result = await response.json();

            if (result.success) {
                showNotification('Transaksi berhasil dibatalkan', 'success');
                loadOrders();
            } else {
                showNotification(result.message || 'Gagal membatalkan transaksi', 'error');
            }
        } catch (error) {
            console.error('Error cancelling order:', error);
            showNotification('Terjadi kesalahan', 'error');
        }
    }

    // Get order list HTML
    function getOrderListHTML() {
        return `
            <!-- Table Controls Section -->
            <div class="table-controls-section table-light p-3 mb-0 border rounded-top">
                <div class="table-header-controls">
                    <div class="search-section">
                        <div class="search-input-wrapper">
                            <i class="ti ti-search search-icon"></i>
                            <input type="text" class="form-control search-input" id="searchOrder" 
                                   placeholder="Cari no. transaksi, customer, atau no. meja..." 
                                   onkeyup="searchOrders()" 
                                   onchange="searchOrders()">
                            <button type="button" class="btn-clear-search" id="btnClearSearch" 
                                    onclick="clearSearch()" style="display: none;">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="filter-section d-flex gap-3 align-items-center">
                        <div class="filter-select-wrapper">
                            <i class="ti ti-filter filter-icon"></i>
                            <select class="form-select filter-select" id="filterStatus" onchange="loadOrders()">
                                <option value="">Semua Status</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>
                        
                        <div class="date-input-wrapper">
                            <i class="ti ti-calendar date-icon"></i>
                            <input type="date" class="form-control filter-date" id="filterDate" 
                                   value="${new Date().toISOString().split('T')[0]}" onchange="loadOrders()">
                        </div>
                        
                        <button class="btn refresh-btn" onclick="loadOrders()">
                            <i class="ti ti-refresh"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Table Container -->
            <div class="table-container">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover mb-0">
                        <thead class="table-light table-header-sticky">
                            <tr class="column-headers">
                                <th style="min-width: 60px;">No</th>
                                <th style="min-width: 200px;">No. Transaksi</th>
                                <th style="min-width: 150px;">Tanggal</th>
                                <th style="min-width: 150px;">Total</th>
                                <th style="min-width: 150px;">Status</th>
                                <th style="min-width: 150px;">Aksi</th>
                            </tr>
            </thead>
                        <tbody id="orderTableBody">
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    Loading orders...
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
                <nav aria-label="Order pagination">
                    <ul class="pagination pagination-sm mb-0" id="paginationControls"></ul>
                </nav>
            </div>
        `;
    }

    // Display orders
    function displayOrders() {
        const tbody = document.getElementById('orderTableBody');
        if (!tbody) return;

        tbody.innerHTML = '';

        if (allOrders.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">
                        <p class="mb-0">Tidak ada data transaksi</p>
                    </td>
                </tr>
            `;

            document.getElementById('paginationInfo').textContent = 'Showing 0 - 0 of 0 entries';
            document.getElementById('paginationControls').innerHTML = '';
            return;
        }

        // Calculate pagination
        const totalPages = Math.ceil(allOrders.length / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, allOrders.length);
        const paginatedOrders = allOrders.slice(startIndex, endIndex);

        // Display paginated orders
        paginatedOrders.forEach((order, index) => {
            const row = document.createElement('tr');
            const statusBadge = getStatusBadge(order.order_status);
            const paymentBadge = getPaymentBadge(order.payment_status);
            const rowNumber = startIndex + index + 1;

            row.innerHTML = `
                <td>${rowNumber}</td>
                <td><strong>${order.order_number}</strong></t           d>
                <td>${formatDate(order.created_at)}</td>
                <td><strong class="text-success">Rp ${formatPrice(order.total_amount)}</strong></td>
                <td>
                    ${statusBadge}
                    <div class="mt-1">${paymentBadge}</div>
                </td>
                <td>
                    <button class="btn btn-sm btn-info me-1" onclick="viewOrderDetail('${order.id}')" title="Lihat Detail">
                        <i class="ti ti-eye"></i>
                    </button>
                    ${order.order_status === 'pending' && order.payment_status === 'pending' ? `
                        ${order.payment_method === 'midtrans' ? `
                            <button class="btn btn-sm btn-primary me-1" onclick="reopenMidtransSnap('${order.order_number}', '${order.id}')" title="Bayar dengan Midtrans">
                                <i class="ti ti-credit-card"></i>
                            </button>
                        ` : `
                            <button class="btn btn-sm btn-success me-1" onclick="confirmPaymentAndComplete('${order.id}')" title="Konfirmasi Pembayaran & Selesaikan">
                                <i class="ti ti-check"></i>
                            </button>
                        `}
                        <button class="btn btn-sm btn-danger" onclick="cancelOrder('${order.id}')" title="Batalkan">
                            <i class="ti ti-x"></i>
                        </button>
                    ` : ''}
                </td>
            `;
            tbody.appendChild(row);
        });

        // Update pagination
        updatePagination();
    }

    // Get status badge
    function getStatusBadge(status) {
        const badges = {
            'pending': '<span class="badge bg-warning">Pending</span>',
            'processing': '<span class="badge bg-info">Processing</span>',
            'completed': '<span class="badge bg-success">Selesai</span>',
            'cancelled': '<span class="badge bg-danger">Dibatalkan</span>'
        };
        return badges[status] || '';
    }

    // Get payment badge
    function getPaymentBadge(status) {
        const badges = {
            'pending': '<span class="badge bg-light-warning text-warning">Belum Bayar</span>',
            'paid': '<span class="badge bg-light-success text-success">Sudah Bayar</span>',
            'failed': '<span class="badge bg-light-danger text-danger">Gagal</span>'
        };
        return badges[status] || '';
    }

    // Format price
    function formatPrice(price) {
        return new Intl.NumberFormat('id-ID').format(price || 0);
    }

    // Format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    // Pagination functions
    function updatePagination() {
        const totalItems = allOrders.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);

        const startItem = totalItems > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0;
        const endItem = Math.min(currentPage * itemsPerPage, totalItems);
        document.getElementById('paginationInfo').textContent =
            `Showing ${startItem} - ${endItem} of ${totalItems} entries`;

        generatePaginationControls(totalPages);
    }

    function generatePaginationControls(totalPages) {
        const paginationContainer = document.getElementById('paginationControls');
        if (!paginationContainer) return;

        paginationContainer.innerHTML = '';

        if (totalPages <= 1) return;

        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        `;
        paginationContainer.appendChild(prevLi);

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
        const totalPages = Math.ceil(allOrders.length / itemsPerPage);
        if (page < 1 || page > totalPages || page === currentPage) return;
        currentPage = page;
        displayOrders();
    }

    // Format time
    function formatTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }

    // Show notification
    function showNotification(message, type = 'success') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type,
                title: type === 'success' ? 'Berhasil!' : 'Error!',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            // Fallback to alert if SweetAlert2 not loaded
            alert((type === 'success' ? 'Berhasil! ' : 'Error! ') + message);
        }
    }

    // Search orders with better filtering
    function searchOrders() {
        const searchInput = document.getElementById('searchOrder');
        if (!searchInput) return;

        const searchTerm = searchInput.value.toLowerCase().trim();

        if (!searchTerm) {
            // If search is empty, display all orders
            displayOrders();
            return;
        }

        const filteredOrders = allOrders.filter(order => {
            const orderNumber = (order.order_number || '').toLowerCase();
            const customerName = (order.customer_name || '').toLowerCase();
            const tableNumber = (order.table_number || '').toLowerCase();

            return orderNumber.includes(searchTerm) ||
                customerName.includes(searchTerm) ||
                tableNumber.includes(searchTerm);
        });

        // Update display with filtered orders
        displayFilteredOrders(filteredOrders);
    }

    // Display filtered orders
    function displayFilteredOrders(orders) {
        const tbody = document.getElementById('orderTableBody');
        if (!tbody) return;

        tbody.innerHTML = '';

        if (orders.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <i class="ti ti-search-off" style="font-size: 48px; opacity: 0.3; color: #9ca3af;"></i>
                        <p class="mb-0 mt-2" style="color: #6b7280;">Tidak ada transaksi yang ditemukan</p>
                        <small style="color: #9ca3af;">Coba kata kunci yang lain</small>
                    </td>
                </tr>
            `;
            return;
        }

        orders.forEach((order, index) => {
            const row = document.createElement('tr');
            const statusBadge = getStatusBadge(order.order_status);
            const paymentBadge = getPaymentBadge(order.payment_status);

            row.innerHTML = `
                <td>${index + 1}</td>
                <td><strong>${order.order_number}</strong></td>
                <td>
                    <div>${formatDate(order.created_at)}</div>
                    <small class="text-muted">${formatTime(order.created_at)}</small>
                </td>
                <td>
                    <div>${order.items_count || 0} item</div>
                </td>
                <td><strong class="text-success">Rp ${formatPrice(order.total_amount)}</strong></td>
                <td>
                    ${statusBadge}
                    <br>
                    <small>${paymentBadge}</small>
                </td>
                <td>
                    <button class="btn btn-sm btn-info me-1" onclick="viewOrder('${order.id}')" title="Lihat Detail">
                        <i class="ti ti-eye"></i>
                    </button>
                    ${order.order_status === 'pending' ? `
                        <button class="btn btn-sm btn-success me-1" onclick="completeOrder('${order.id}')" title="Selesaikan">
                            <i class="ti ti-check"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="cancelOrder('${order.id}')" title="Batalkan">
                            <i class="ti ti-x"></i>
                        </button>
                    ` : ''}
                </td>
            `;
            tbody.appendChild(row);
        });
    }
</script>

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

    /* Filter section */
    .filter-section {
        flex-shrink: 0;
    }

    /* Enhanced form controls styling */
    .filter-select-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .filter-icon {
        position: absolute;
        left: 8px;
        color: #6c757d;
        font-size: 14px;
        z-index: 1;
    }

    .filter-select {
        background: white;
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        padding: 8px 12px 8px 30px;
        font-size: 13px;
        min-width: 150px;
        transition: all 0.3s ease;
        appearance: none;
        background-image: url('data:image/svg+xml;utf8,<svg fill="%23666" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 12px;
    }

    .filter-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .date-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .date-icon {
        position: absolute;
        left: 8px;
        color: #6c757d;
        font-size: 14px;
        z-index: 1;
    }

    .filter-date {
        background: white;
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        padding: 8px 12px 8px 30px;
        font-size: 13px;
        min-width: 150px;
        transition: all 0.3s ease;
    }

    .filter-date:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .refresh-btn {
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
        min-width: 120px;
        font-size: 13px;
    }

    .refresh-btn:hover {
        border-color: #667eea;
        background: #f8f9fa;
        color: #667eea;
    }
</style>