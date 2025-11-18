# Setup Webhook Midtrans untuk Auto-Update Database

## ✅ Webhook Sudah Siap!

File `api/midtrans/notification.php` sudah dikonfigurasi dan siap menerima notifikasi dari Midtrans.

---

## 🚀 Cara Setup (Development/Sandbox)

### **Opsi 1: Menggunakan ngrok (Recommended untuk Testing)**

1. **Download dan Install ngrok**
   - Download dari: https://ngrok.com/download
   - Extract dan jalankan

2. **Jalankan ngrok**
   ```bash
   ngrok http 80
   ```

3. **Copy URL dari ngrok**
   - Contoh output: `https://abc123.ngrok.io`

4. **Set Notification URL di Midtrans Dashboard**
   - Login ke: https://dashboard.sandbox.midtrans.com
   - Menu: **Settings → Configuration**
   - **Payment Notification URL**: 
     ```
     https://abc123.ngrok.io/seblak-predator/api/midtrans/notification.php
     ```
   - Klik **Update**

5. **Test Pembayaran**
   - Buat transaksi baru dari aplikasi Anda
   - Bayar menggunakan Snap Midtrans
   - Gunakan test card: `4811 1111 1111 1114`
   - Setelah bayar, cek database → status otomatis berubah! ✅

---

### **Opsi 2: Test Manual (Tanpa ngrok)**

Jika belum setup ngrok, Anda bisa test manual dengan curl:

```bash
curl -X POST http://localhost/seblak-predator/api/midtrans/notification.php \
  -H "Content-Type: application/json" \
  -d '{
    "order_id": "ORD-XXXXX",
    "status_code": "200",
    "transaction_status": "settlement",
    "gross_amount": "2000.00",
    "payment_type": "credit_card",
    "fraud_status": "accept"
  }'
```

Ganti `ORD-XXXXX` dengan order number yang ingin diupdate.

---

## 📊 Cara Kerja Webhook

1. **Customer membayar di Midtrans** → Status berubah di dashboard Midtrans
2. **Midtrans kirim notifikasi** → POST request ke URL webhook Anda
3. **Webhook menerima notifikasi** → Parsing data pembayaran
4. **Update database otomatis** → Status order berubah:
   - `settlement` → `payment_status = paid`, `order_status = processing`
   - `pending` → tetap `pending`
   - `deny/cancel/expire` → `payment_status = failed`, `order_status = cancelled`

---

## 🔍 Monitoring Webhook

Cek log untuk memastikan webhook bekerja:

**Windows (Laragon):**
```bash
tail -f D:/laragon/tmp/php_errors.log
```

Anda akan melihat log seperti:
```
=== Midtrans Notification Received ===
Body: {"order_id":"ORD-...","transaction_status":"settlement",...}
✓ Signature verified
Notification processed successfully for order: ORD-...
```

---

## 🔐 Security

- **Sandbox Mode**: Signature verification di-skip untuk kemudahan testing
- **Production Mode**: Signature otomatis diverifikasi untuk keamanan
- Notifikasi disimpan di tabel `payment_notifications` untuk audit trail

---

## 🌐 Setup Production

Saat deploy ke production server:

1. Update `.env`:
   ```
   MIDTRANS_IS_PRODUCTION=true
   MIDTRANS_SERVER_KEY=Mid-server-PRODUCTION-KEY
   MIDTRANS_CLIENT_KEY=Mid-client-PRODUCTION-KEY
   ```

2. Set Notification URL di **Production Dashboard**:
   ```
   https://yourdomain.com/seblak-predator/api/midtrans/notification.php
   ```

3. Pastikan server production bisa diakses dari internet (tidak pakai localhost)

---

## ✅ Status Mapping

| Midtrans Status | Payment Status | Order Status |
|----------------|----------------|--------------|
| `settlement` | `paid` | `processing` |
| `capture` | `paid` | `processing` |
| `pending` | `pending` | `pending` |
| `deny` | `failed` | `cancelled` |
| `cancel` | `failed` | `cancelled` |
| `expire` | `failed` | `cancelled` |

---

**🎉 Selesai! Database Anda sekarang akan auto-update saat pembayaran Midtrans selesai!**
