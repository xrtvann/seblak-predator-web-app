# Tombol Konfirmasi Pembayaran di Dashboard Transaksi

## 📋 Fitur Overview

Tombol konfirmasi pembayaran pada tabel data transaksi sekarang **otomatis menyesuaikan dengan metode pembayaran**:

### 1. **Payment Method: MIDTRANS** 💳
- **Tombol:** 🔵 Biru (Credit Card Icon)
- **Fungsi:** Membuka Snap Popup untuk pembayaran
- **Icon:** `<i class="ti ti-credit-card"></i>`
- **Title:** "Bayar dengan Midtrans"
- **Action:** `reopenMidtransSnap(orderNumber, orderId)`

### 2. **Payment Method: CASH** 💵
- **Tombol:** 🟢 Hijau (Check Icon)
- **Fungsi:** Konfirmasi manual pembayaran cash
- **Icon:** `<i class="ti ti-check"></i>`
- **Title:** "Konfirmasi Pembayaran & Selesaikan"
- **Action:** `confirmPaymentAndComplete(orderId)`

---

## 🔧 Implementasi Teknis

### File yang Dimodifikasi
**File:** `dist/dashboard/pages/transaksi.php`

### 1. Tombol Action Dinamis (Line ~3358)

```javascript
// Dalam displayOrders() function
row.innerHTML = `
    ...
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
```

### 2. Fungsi `reopenMidtransSnap()` (Line ~3145)

**Purpose:** Membuka Snap popup untuk order existing dengan payment_method midtrans

**Flow:**
1. Fetch order detail dari API: `GET /api/orders.php?id={orderId}`
2. Convert items ke format create-transaction.php:
   - `spice_level_id` → `spice_level`
   - Array customizations → Object `{type: id}`
   - Array toppings → Format dengan topping_id, quantity, unit_price
3. Call `POST /api/midtrans/create-transaction.php` untuk generate snap_token baru
4. Buka Snap popup dengan `window.snap.pay(snap_token)`
5. Handle callbacks:
   - **onSuccess:** Reload orders setelah 3 detik (status auto-update via webhook)
   - **onPending:** Reload orders setelah 3 detik
   - **onError:** Show error notification
   - **onClose:** Reload orders (check status update)

```javascript
async function reopenMidtransSnap(orderNumber, orderId) {
    try {
        // Show loading
        Swal.fire({
            title: 'Memuat...',
            html: 'Sedang menyiapkan pembayaran Midtrans',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        // 1. Fetch order details
        const response = await fetch(`api/orders.php?id=${orderId}`);
        const result = await response.json();

        if (result.success && result.data) {
            const order = result.data;
            
            // 2. Convert items format
            const formattedItems = order.items.map(item => {
                const customizations = {};
                if (item.customizations && Array.isArray(item.customizations)) {
                    item.customizations.forEach(c => {
                        customizations[c.customization_type] = c.customization_id;
                    });
                }

                return {
                    quantity: parseInt(item.quantity),
                    spice_level: item.spice_level_id,
                    customizations: customizations,
                    toppings: (item.toppings || []).map(t => ({
                        topping_id: t.topping_id,
                        topping_name: t.topping_name,
                        quantity: parseInt(t.quantity),
                        unit_price: parseFloat(t.unit_price)
                    })),
                    notes: item.notes || ''
                };
            });
            
            // 3. Get new snap token
            const snapResponse = await fetch('api/midtrans/create-transaction.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    customer_name: order.customer_name,
                    phone: order.phone || '0000000000',
                    table_number: order.table_number || '',
                    order_type: order.order_type,
                    notes: order.notes || '',
                    items: formattedItems
                })
            });

            const snapResult = await snapResponse.json();

            if (snapResult.success && snapResult.snap_token) {
                Swal.close();

                // 4. Open Snap popup
                if (typeof window.snap === 'undefined') {
                    showNotification('Snap.js tidak dimuat! Periksa kredensial Midtrans.', 'error');
                    return;
                }

                window.snap.pay(snapResult.snap_token, {
                    onSuccess: function (result) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Pembayaran Berhasil!',
                            text: 'Status pembayaran akan diperbarui otomatis oleh sistem',
                            timer: 3000,
                            showConfirmButton: false
                        });
                        setTimeout(() => loadOrders(), 3000);
                    },
                    onPending: function (result) {
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
                        showNotification('Pembayaran gagal! Silakan coba lagi.', 'error');
                    },
                    onClose: function () {
                        showNotification('Pembayaran dibatalkan', 'warning');
                        loadOrders();
                    }
                });
            } else {
                Swal.close();
                showNotification(snapResult.message || 'Gagal mendapatkan token pembayaran', 'error');
            }
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
```

### 3. Fungsi `confirmPaymentAndComplete()` (Line ~3220)

**Update:** Menambahkan kata "CASH" pada konfirmasi dialog

```javascript
async function confirmPaymentAndComplete(orderId) {
    const confirm = await Swal.fire({
        title: 'Konfirmasi Pembayaran & Selesaikan?',
        text: 'Tandai pembayaran CASH sudah diterima dan pesanan selesai?', // ← Updated
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="ti ti-check me-1"></i>Ya, Sudah Dibayar & Selesai',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#10b981'
    });
    
    if (!confirm.isConfirmed) return;

    // PATCH request to update status
    const response = await fetch('api/orders.php', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id: orderId,
            payment_status: 'paid',
            order_status: 'completed'
        })
    });
    
    // ... handle response
}
```

---

## 🧪 Testing Scenarios

### Test 1: Order Midtrans Pending
```bash
# 1. Buat order midtrans
curl -X POST http://localhost/seblak-predator/api/midtrans/create-transaction.php \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "Test Snap Button",
    "phone": "081234567890",
    "payment_method": "midtrans",
    "items": [{
      "quantity": 1,
      "spice_level": "lvl_690c6f766f9ec",
      "customizations": {},
      "toppings": []
    }]
  }'

# 2. Buka dashboard transaksi
# 3. Lihat tombol BIRU (credit card) untuk order midtrans pending
# 4. Klik tombol → Snap popup terbuka
# 5. Bayar dengan test card: 4811 1111 1111 1114
# 6. Verifikasi status auto-update ke "paid" + "processing"
```

### Test 2: Order Cash Pending
```bash
# 1. Buat order cash manual
mysql -u root seblak_app -e "
INSERT INTO orders (id, order_number, customer_name, payment_method, payment_status, order_status, total_amount, created_at, updated_at)
VALUES ('ord_test_001', 'ORD-TEST-CASH', 'Test Cash', 'cash', 'pending', 'pending', 5000, NOW(), NOW());
"

# 2. Buka dashboard transaksi
# 3. Lihat tombol HIJAU (check) untuk order cash pending
# 4. Klik tombol → Dialog konfirmasi manual
# 5. Konfirmasi → Status update ke "paid" + "completed"
```

---

## 📊 Database Structure

### Tabel: `orders`
```sql
payment_method ENUM('cash', 'midtrans') -- Menentukan tombol yang ditampilkan
payment_status ENUM('pending', 'paid', 'failed')
order_status ENUM('pending', 'processing', 'completed', 'cancelled')
```

### Logic Flow
```
IF payment_status = 'pending' AND order_status = 'pending' THEN
    IF payment_method = 'midtrans' THEN
        Show: [Lihat Detail] [💳 Bayar Midtrans] [❌ Batalkan]
    ELSE
        Show: [Lihat Detail] [✅ Konfirmasi Cash] [❌ Batalkan]
    END IF
ELSE
    Show: [Lihat Detail] only
END IF
```

---

## ✅ Hasil Implementasi

### Before:
- Semua order pending hanya ada tombol ✅ hijau (konfirmasi manual)
- Order midtrans tidak bisa dibayar ulang dari dashboard
- User harus generate Snap token baru manual

### After:
- ✅ Order **MIDTRANS pending**: Tombol 💳 BIRU → Buka Snap popup langsung
- ✅ Order **CASH pending**: Tombol ✅ HIJAU → Konfirmasi manual
- ✅ Auto-sync status setelah payment via webhook
- ✅ User experience lebih baik (1 klik untuk bayar Midtrans)

---

## 🔗 Related Files

1. **Frontend:** `dist/dashboard/pages/transaksi.php`
   - `displayOrders()` - Render tombol dinamis
   - `reopenMidtransSnap()` - Handle Midtrans payment
   - `confirmPaymentAndComplete()` - Handle cash payment

2. **Backend:**
   - `api/orders.php` - GET order detail
   - `api/midtrans/create-transaction.php` - Generate snap token
   - `api/midtrans/notification.php` - Webhook auto-update

3. **Documentation:**
   - `WEBHOOK_SETUP.md` - Webhook configuration guide
   - `api/midtrans/README.md` - Midtrans integration guide

---

## 🚀 Production Checklist

- [x] Tombol berbeda untuk midtrans vs cash
- [x] Fungsi reopenMidtransSnap untuk fetch & reopen Snap
- [x] Auto-reload orders setelah payment success
- [x] Error handling untuk Snap.js not loaded
- [x] Webhook auto-update payment status
- [x] Test dengan sandbox Midtrans
- [ ] Setup ngrok untuk webhook testing
- [ ] Configure production Midtrans keys
- [ ] Deploy ke production server

---

**Status:** ✅ **COMPLETED & READY TO USE**

**Tanggal:** 18 November 2025  
**Developer:** GitHub Copilot
