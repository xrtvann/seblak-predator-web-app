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
                        <button class="btn btn-primary" id="btnNewOrder" onclick="showNewOrderForm()">
                            <i class="ti ti-plus"></i> Transaksi Baru
                        </button>
                        <button class="btn btn-secondary d-none" id="btnBackToList" onclick="showOrderList()">
                            <i class="ti ti-arrow-left"></i> Kembali
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

<script>
    // Global variables
    let allOrders = [];
    let allProducts = [];
    let allToppings = [];
    let currentOrder = {
        customer_name: '',
        table_number: '',
        phone: '',
        notes: '',
        payment_method: 'cash',
        items: []
    };

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        loadProducts();
        loadToppings();
        showOrderList();
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
            const response = await fetch('api/menu/products.php?is_active=1&is_topping=1');

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

    // Load orders
    async function loadOrders() {
        try {
            const response = await fetch('api/orders.php');

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
        document.getElementById('cardTitleText').textContent = 'Form Transaksi Baru';
        document.getElementById('cardSubtitleText').classList.remove('d-none');
        document.getElementById('btnNewOrder').classList.add('d-none');
        document.getElementById('btnBackToList').classList.remove('d-none');

        // Reset current order
        currentOrder = {
            customer_name: '',
            table_number: '',
            phone: '',
            notes: '',
            payment_method: 'cash',
            items: []
        };

        const mainContent = document.getElementById('mainContentArea');
        mainContent.innerHTML = getNewOrderFormHTML();
    }

    // Get new order form HTML
    function getNewOrderFormHTML() {
        return `
            <form id="formNewOrder" onsubmit="submitNewOrder(event)">
                <div class="row">
                    <!-- Left Column - Order Info -->
                    <div class="col-lg-8">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Informasi Customer</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Customer <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="customerName" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">No. Meja</label>
                                        <input type="text" class="form-control" id="tableNumber">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">No. Telepon</label>
                                        <input type="text" class="form-control" id="phone">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Catatan</label>
                                        <textarea class="form-control" id="notes" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Items Pesanan</h6>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="showProductSelection()">
                                        <i class="ti ti-plus"></i> Tambah Item
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="orderItemsList">
                                    <p class="text-muted text-center">Belum ada item dipilih</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Summary & Payment -->
                    <div class="col-lg-4">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Ringkasan Pesanan</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <strong id="summarySubtotal">Rp 0</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Pajak (0%):</span>
                                    <strong id="summaryTax">Rp 0</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Diskon:</span>
                                    <strong id="summaryDiscount">Rp 0</strong>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <h5>Total:</h5>
                                    <h5 class="text-success" id="summaryTotal">Rp 0</h5>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Metode Pembayaran</h6>
                            </div>
                            <div class="card-body">
                                <select class="form-select" id="paymentMethod" required>
                                    <option value="cash">Tunai</option>
                                    <option value="card">Kartu Debit/Kredit</option>
                                    <option value="qris">QRIS</option>
                                    <option value="transfer">Transfer</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 mb-2">
                            <i class="ti ti-check"></i> Proses Transaksi
                        </button>
                        <button type="button" class="btn btn-secondary w-100" onclick="showOrderList()">
                            Batal
                        </button>
                    </div>
                </div>
            </form>
        `;
    }

    // Show product selection modal
    function showProductSelection() {
        const productsHTML = allProducts.map(product => `
            <div class="col-md-6 mb-3">
                <div class="card product-card" onclick="addProductToOrder('${product.id}')">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="${product.image_url || 'assets/images/default-product.png'}" alt="${product.name}" 
                                 class="img-thumbnail me-3" style="width: 60px; height: 60px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${product.name}</h6>
                                <p class="text-success mb-0"><strong>Rp ${formatPrice(product.price)}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        Swal.fire({
            title: 'Pilih Produk',
            html: `
                <div class="row">
                    ${productsHTML}
                </div>
            `,
            width: '800px',
            showConfirmButton: false,
            showCloseButton: true
        });
    }

    // Add product to order
    function addProductToOrder(productId) {
        const product = allProducts.find(p => p.id === productId);
        if (!product) return;

        Swal.close(); // Close product selection modal

        const item = {
            id: 'item_' + Date.now(),
            product_id: product.id,
            product_name: product.name,
            unit_price: product.price,
            quantity: 1,
            toppings: []
        };

        currentOrder.items.push(item);
        renderOrderItems();
        updateOrderSummary();
    }

    // Render order items
    function renderOrderItems() {
        const container = document.getElementById('orderItemsList');
        if (currentOrder.items.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">Belum ada item dipilih</p>';
            return;
        }

        container.innerHTML = currentOrder.items.map((item, index) => {
            const itemSubtotal = item.unit_price * item.quantity;
            const toppingsSubtotal = item.toppings.reduce((sum, t) => sum + (t.unit_price * t.quantity), 0);
            const totalSubtotal = itemSubtotal + toppingsSubtotal;

            return `
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${item.product_name}</h6>
                                <small class="text-muted">Rp ${formatPrice(item.unit_price)}</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeOrderItem('${item.id}')">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                        
                        <div class="row align-items-center mb-2">
                            <div class="col-4">
                                <div class="input-group input-group-sm">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateItemQuantity('${item.id}', -1)">-</button>
                                    <input type="number" class="form-control text-center" value="${item.quantity}" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateItemQuantity('${item.id}', 1)">+</button>
                                </div>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-sm btn-outline-primary w-100" onclick="addToppingToItem('${item.id}')">
                                    <i class="ti ti-plus"></i> Topping
                                </button>
                            </div>
                            <div class="col-4 text-end">
                                <strong class="text-success">Rp ${formatPrice(totalSubtotal)}</strong>
                            </div>
                        </div>

                        ${item.toppings.length > 0 ? `
                            <div class="border-top pt-2">
                                <small class="text-muted d-block mb-1">Topping:</small>
                                ${item.toppings.map(topping => `
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small>+ ${topping.topping_name} (${topping.quantity}x)</small>
                                        <div>
                                            <small class="text-success me-2">Rp ${formatPrice(topping.unit_price * topping.quantity)}</small>
                                            <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeToppingFromItem('${item.id}', '${topping.id}')">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        }).join('');
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

    // Select topping
    function selectTopping(itemId, toppingId) {
        const item = currentOrder.items.find(i => i.id === itemId);
        const topping = allToppings.find(t => t.id === toppingId);

        if (!item || !topping) return;

        const existingTopping = item.toppings.find(t => t.topping_id === toppingId);
        if (existingTopping) {
            existingTopping.quantity++;
        } else {
            item.toppings.push({
                id: 'topping_' + Date.now(),
                topping_id: topping.id,
                topping_name: topping.name,
                unit_price: topping.price,
                quantity: 1
            });
        }

        Swal.close();
        renderOrderItems();
        updateOrderSummary();
    }

    // Remove topping from item
    function removeToppingFromItem(itemId, toppingId) {
        const item = currentOrder.items.find(i => i.id === itemId);
        if (!item) return;

        item.toppings = item.toppings.filter(t => t.id !== toppingId);
        renderOrderItems();
        updateOrderSummary();
    }

    // Update item quantity
    function updateItemQuantity(itemId, change) {
        const item = currentOrder.items.find(i => i.id === itemId);
        if (!item) return;

        item.quantity = Math.max(1, item.quantity + change);
        renderOrderItems();
        updateOrderSummary();
    }

    // Remove order item
    function removeOrderItem(itemId) {
        currentOrder.items = currentOrder.items.filter(i => i.id !== itemId);
        renderOrderItems();
        updateOrderSummary();
    }

    // Update order summary
    function updateOrderSummary() {
        const subtotal = currentOrder.items.reduce((sum, item) => {
            const itemTotal = item.unit_price * item.quantity;
            const toppingsTotal = item.toppings.reduce((tSum, t) => tSum + (t.unit_price * t.quantity), 0);
            return sum + itemTotal + toppingsTotal;
        }, 0);

        const tax = 0; // 0% tax
        const discount = 0; // No discount for now
        const total = subtotal + tax - discount;

        document.getElementById('summarySubtotal').textContent = 'Rp ' + formatPrice(subtotal);
        document.getElementById('summaryTax').textContent = 'Rp ' + formatPrice(tax);
        document.getElementById('summaryDiscount').textContent = 'Rp ' + formatPrice(discount);
        document.getElementById('summaryTotal').textContent = 'Rp ' + formatPrice(total);
    }

    // Submit new order
    async function submitNewOrder(event) {
        event.preventDefault();

        if (currentOrder.items.length === 0) {
            showNotification('Tambahkan minimal 1 item produk', 'error');
            return;
        }

        const orderData = {
            customer_name: document.getElementById('customerName').value,
            table_number: document.getElementById('tableNumber').value,
            phone: document.getElementById('phone').value,
            notes: document.getElementById('notes').value,
            payment_method: document.getElementById('paymentMethod').value,
            items: currentOrder.items.map(item => ({
                product_id: item.product_id,
                product_name: item.product_name,
                quantity: item.quantity,
                unit_price: item.unit_price,
                toppings: item.toppings.map(t => ({
                    topping_id: t.topping_id,
                    topping_name: t.topping_name,
                    quantity: t.quantity,
                    unit_price: t.unit_price
                }))
            }))
        };

        try {
            const response = await fetch('api/orders.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(orderData)
            });

            const result = await response.json();

            if (result.success) {
                showNotification('Transaksi berhasil dibuat!', 'success');
                showOrderList();
            } else {
                showNotification(result.message || 'Gagal membuat transaksi', 'error');
            }
        } catch (error) {
            console.error('Error submitting order:', error);
            showNotification('Terjadi kesalahan saat membuat transaksi', 'error');
        }
    }

    // View order detail
    async function viewOrder(orderId) {
        try {
            const response = await fetch(`api/orders.php?id=${orderId}`);
            const result = await response.json();

            if (result.success) {
                const order = result.data;

                const itemsHTML = order.items.map(item => `
                    <div class="mb-3 pb-2 border-bottom">
                        <div class="d-flex justify-content-between mb-1">
                            <strong>${item.product_name}</strong>
                            <span>${item.quantity}x Rp ${formatPrice(item.unit_price)}</span>
                        </div>
                        <div class="text-end">
                            <strong class="text-success">Rp ${formatPrice(item.subtotal)}</strong>
                        </div>
                        ${item.toppings && item.toppings.length > 0 ? `
                            <div class="mt-2">
                                <small class="text-muted">Topping:</small>
                                ${item.toppings.map(t => `
                                    <div class="d-flex justify-content-between">
                                        <small>+ ${t.topping_name} (${t.quantity}x)</small>
                                        <small class="text-success">Rp ${formatPrice(t.subtotal)}</small>
                                    </div>
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>
                `).join('');

                Swal.fire({
                    title: `Detail Transaksi`,
                    html: `
                        <div class="text-start">
                            <table class="table table-sm">
                                <tr>
                                    <td>No. Transaksi:</td>
                                    <td><strong>${order.order_number}</strong></td>
                                </tr>
                                <tr>
                                    <td>Tanggal:</td>
                                    <td>${formatDate(order.created_at)} ${formatTime(order.created_at)}</td>
                                </tr>
                                <tr>
                                    <td>Customer:</td>
                                    <td>${order.customer_name}</td>
                                </tr>
                                ${order.table_number ? `
                                <tr>
                                    <td>No. Meja:</td>
                                    <td>${order.table_number}</td>
                                </tr>
                                ` : ''}
                                <tr>
                                    <td>Status:</td>
                                    <td>${getStatusBadge(order.order_status)}</td>
                                </tr>
                                <tr>
                                    <td>Pembayaran:</td>
                                    <td>${getPaymentBadge(order.payment_status)}</td>
                                </tr>
                            </table>
                            
                            <hr>
                            <h6>Items Pesanan:</h6>
                            ${itemsHTML}
                            
                            <hr>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Subtotal:</span>
                                <span>Rp ${formatPrice(order.subtotal)}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Pajak:</span>
                                <span>Rp ${formatPrice(order.tax)}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Diskon:</span>
                                <span>Rp ${formatPrice(order.discount)}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <h5>Total:</h5>
                                <h5 class="text-success">Rp ${formatPrice(order.total_amount)}</h5>
                            </div>
                        </div>
                    `,
                    width: '600px',
                    confirmButtonText: 'Tutup'
                });
            }
        } catch (error) {
            console.error('Error loading order detail:', error);
            showNotification('Gagal memuat detail transaksi', 'error');
        }
    }

    // Complete order
    async function completeOrder(orderId) {
        const confirm = await Swal.fire({
            title: 'Selesaikan Transaksi?',
            text: 'Tandai transaksi ini sebagai selesai?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Selesaikan',
            cancelButtonText: 'Batal'
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
                    order_status: 'completed',
                    payment_status: 'paid'
                })
            });

            const result = await response.json();

            if (result.success) {
                showNotification('Transaksi berhasil diselesaikan', 'success');
                loadOrders();
            } else {
                showNotification(result.message || 'Gagal menyelesaikan transaksi', 'error');
            }
        } catch (error) {
            console.error('Error completing order:', error);
            showNotification('Terjadi kesalahan', 'error');
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
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" id="searchOrder" placeholder="Cari transaksi..." onkeyup="searchOrders()">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterStatus" onchange="loadOrders()">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="filterDate" value="${new Date().toISOString().split('T')[0]}" onchange="loadOrders()">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-secondary w-100" onclick="loadOrders()">
                        <i class="ti ti-refresh"></i> Refresh
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>No. Transaksi</th>
                            <th>Tanggal & Waktu</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="orderTableBody">
                        <tr>
                            <td colspan="8" class="text-center">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                Loading orders...
                            </td>
                        </tr>
                    </tbody>
                </table>
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
                    <td colspan="8" class="text-center">
                        <p class="mb-0">Tidak ada data transaksi</p>
                    </td>
                </tr>
            `;
            return;
        }

        allOrders.forEach((order, index) => {
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
                    <div>${order.customer_name}</div>
                    ${order.table_number ? `<small class="text-muted">Meja #${order.table_number}</small>` : ''}
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

    // Search orders
    function searchOrders() {
        const searchTerm = document.getElementById('searchOrder').value.toLowerCase();
        const filteredOrders = allOrders.filter(order =>
            order.order_number.toLowerCase().includes(searchTerm) ||
            order.customer_name.toLowerCase().includes(searchTerm)
        );

        // Temporarily replace allOrders for display
        const temp = allOrders;
        allOrders = filteredOrders;
        displayOrders();
        allOrders = temp;
    }
</script>