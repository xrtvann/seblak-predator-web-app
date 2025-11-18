# Midtrans Integration - Testing Guide

## 🎯 Setup Midtrans Sandbox

### 1. Dapatkan API Keys
1. Buka [Midtrans Sandbox Dashboard](https://dashboard.sandbox.midtrans.com/)
2. Login atau Register akun baru
3. Pergi ke **Settings > Access Keys**
4. Copy **Server Key** dan **Client Key**

### 2. Update File .env
Buka file `.env` di root project dan update:

```env
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_SERVER_KEY=SB-Mid-server-YOUR_SERVER_KEY_HERE
MIDTRANS_CLIENT_KEY=SB-Mid-client-YOUR_CLIENT_KEY_HERE
MIDTRANS_MERCHANT_ID=G123456789
```

**Note:** Server Key dan Client Key harus diisi dengan kredensial dari Midtrans Dashboard.

---

## 🧪 Testing Payment

### Akses Halaman Test
Buka browser dan akses:
```
http://localhost/seblak-predator/test-midtrans.php
```

### Test Credentials (Sandbox)

#### 💳 Credit Card - Success
```
Card Number: 4811 1111 1111 1114
CVV: 123
Exp Date: 01/25 (atau bulan/tahun di masa depan)
OTP/3DS: 112233
```

#### 💳 Credit Card - Denied
```
Card Number: 4911 1111 1111 1113
CVV: 123
Exp Date: 01/25
```

#### 💳 Credit Card - Challenge by FDS
```
Card Number: 4411 1111 1111 1118
CVV: 123
Exp Date: 01/25
```

#### 🏦 BCA Virtual Account
```
VA Number: Akan di-generate otomatis
Payment: Login ke Midtrans Simulator untuk approve payment
```

#### 📱 GoPay
```
Action: Akan redirect ke halaman simulator
Payment: Klik "Success" untuk approve
```

#### 🏪 Indomaret/Alfamart
```
Payment Code: Akan di-generate otomatis
```

---

## 📊 Payment Flow

### 1. Create Transaction
```javascript
// POST /api/midtrans/create-transaction.php
{
    "customer_name": "John Doe",
    "phone": "08123456789",
    "table_number": "5",
    "items": [{
        "product_id": "SEBLAK001",
        "product_name": "Seblak Pedas",
        "quantity": 2,
        "unit_price": 25000,
        "toppings": [...]
    }]
}
```

**Response:**
```json
{
    "success": true,
    "snap_token": "abc123xyz...",
    "order_id": "ORD-20251117123456-1234",
    "total_amount": 50000
}
```

### 2. Open Payment Popup
```javascript
snap.pay('snap_token', {
    onSuccess: function(result) {
        // Payment success
    },
    onPending: function(result) {
        // Payment pending (VA, convenience store)
    },
    onError: function(result) {
        // Payment error
    },
    onClose: function() {
        // User closed popup
    }
});
```

### 3. Receive Notification (Webhook)
Midtrans akan mengirim HTTP notification ke:
```
POST http://yourdomain.com/api/midtrans/notification.php
```

**Notification Body:**
```json
{
    "transaction_status": "settlement",
    "order_id": "ORD-20251117123456-1234",
    "gross_amount": "50000.00",
    "payment_type": "credit_card",
    "signature_key": "abc123..."
}
```

---

## 🔍 Testing Checklist

### ✅ Credit Card Flow
- [ ] Create transaction berhasil
- [ ] Snap popup terbuka
- [ ] Input card number test
- [ ] Input CVV dan exp date
- [ ] Input OTP 112233
- [ ] Payment success callback triggered
- [ ] Database order status updated to 'paid'

### ✅ BCA Virtual Account Flow
- [ ] Create transaction berhasil
- [ ] VA number di-generate
- [ ] Copy VA number
- [ ] Simulate payment di Midtrans Dashboard
- [ ] Notification diterima
- [ ] Order status updated

### ✅ GoPay Flow
- [ ] Create transaction berhasil
- [ ] Redirect ke simulator
- [ ] Klik "Success"
- [ ] Return ke aplikasi
- [ ] Order status updated

### ✅ Pending Payment Flow
- [ ] Create VA transaction
- [ ] Order status 'pending'
- [ ] Simulate payment after 5 minutes
- [ ] Notification received
- [ ] Status updated to 'paid'

### ✅ Failed Payment Flow
- [ ] Use denied card (4911...)
- [ ] Payment failed
- [ ] onError callback triggered
- [ ] Order status 'failed'

---

## 🛠️ Debugging

### Check API Response
Buka Browser Console (F12) dan lihat:
```javascript
console.log('Transaction response:', result);
```

### Check Server Logs
Lihat file log:
```bash
tail -f /path/to/apache/logs/error.log
```

### Check Midtrans Dashboard
1. Login ke [Sandbox Dashboard](https://dashboard.sandbox.midtrans.com/)
2. Pergi ke **Transactions**
3. Cari order_id Anda
4. Lihat detail dan logs

### Test Notification Manually
```bash
curl -X POST http://localhost/seblak-predator/api/midtrans/notification.php \
  -H "Content-Type: application/json" \
  -d '{
    "transaction_status": "settlement",
    "order_id": "ORD-20251117123456-1234",
    "gross_amount": "50000.00",
    "payment_type": "credit_card",
    "signature_key": "calculated_signature"
  }'
```

---

## 🚀 Production Deployment

### 1. Update .env untuk Production
```env
MIDTRANS_IS_PRODUCTION=true
MIDTRANS_SERVER_KEY=Mid-server-PRODUCTION_KEY
MIDTRANS_CLIENT_KEY=Mid-client-PRODUCTION_KEY
```

### 2. Setup Notification URL
Di Midtrans Dashboard > Settings > Configuration:
```
Notification URL: https://yourdomain.com/api/midtrans/notification.php
```

### 3. Enable Payment Methods
Pilih payment methods yang ingin diaktifkan:
- Credit Card
- Bank Transfer (BCA, Mandiri, BNI, BRI, Permata)
- E-Wallet (GoPay, ShopeePay, QRIS)
- Convenience Store (Indomaret, Alfamart)

### 4. SSL Certificate Required
Pastikan website menggunakan HTTPS untuk production.

---

## 📚 Resources

- [Midtrans Docs](https://docs.midtrans.com/)
- [Snap Documentation](https://docs.midtrans.com/docs/snap)
- [Testing Payment](https://docs.midtrans.com/docs/testing-payment)
- [Notification Handling](https://docs.midtrans.com/docs/http-notification-webhooks)
- [Sandbox Dashboard](https://dashboard.sandbox.midtrans.com/)
- [Production Dashboard](https://dashboard.midtrans.com/)

---

## ❓ FAQ

**Q: Snap popup tidak muncul?**
A: Check browser console untuk error. Pastikan Client Key sudah benar di .env

**Q: Transaction failed dengan error 400?**
A: Check Server Key di .env dan pastikan format request body benar

**Q: Notification tidak diterima?**
A: Setup ngrok untuk local testing atau deploy ke server dengan public URL

**Q: Order status tidak update setelah payment?**
A: Check notification.php logs dan pastikan signature verification passed

**Q: Testing di local dengan ngrok**
```bash
ngrok http 80
# Update notification URL di Midtrans Dashboard dengan ngrok URL
```
