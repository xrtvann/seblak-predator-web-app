# Setup Midtrans Payment Gateway (Sandbox)

Panduan lengkap untuk mengintegrasikan Midtrans Sandbox ke aplikasi Seblak Predator.

## Langkah 1: Buat Akun Midtrans Sandbox

1. Buka https://dashboard.sandbox.midtrans.com/register
2. Daftar dengan email Anda
3. Verifikasi email
4. Login ke dashboard

## Langkah 2: Dapatkan API Keys

1. Login ke https://dashboard.sandbox.midtrans.com/
2. Klik **Settings** > **Access Keys**
3. Copy 2 keys berikut:
   - **Server Key** (contoh: `SB-Mid-server-abc123...`)
   - **Client Key** (contoh: `SB-Mid-client-xyz789...`)

## Langkah 3: Update Configuration

Edit file `api/midtrans/config.php`:

```php
// Ganti dengan Server Key Anda
define('MIDTRANS_SERVER_KEY', 'SB-Mid-server-YOUR_SERVER_KEY_HERE');

// Ganti dengan Client Key Anda
define('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-YOUR_CLIENT_KEY_HERE');
```

**PENTING:** Jangan share API keys Anda ke public!

## Langkah 4: Test Payment

Untuk testing di Sandbox, gunakan kartu test berikut:

### Kartu Kredit Test (Success)
- **Card Number:** 4811 1111 1111 1114
- **Expiry:** 01/25 (atau bulan/tahun di masa depan)
- **CVV:** 123

### Kartu Kredit Test (Failed)
- **Card Number:** 4911 1111 1111 1113
- **CVV:** 123

### GoPay Test
- Gunakan nomor HP apapun
- OTP akan muncul di simulator

### Bank Transfer Test
- Pilih bank (BCA, Mandiri, BNI, BRI, Permata)
- Virtual Account akan di-generate otomatis
- Simulasi pembayaran di dashboard Midtrans

## Langkah 5: Cara Menggunakan

### Di Aplikasi (Kasir/Admin):

1. **Buat Transaksi Baru**
   - Klik "Transaksi Baru"
   - Step 1: Isi data pelanggan
   - Step 2: Pilih produk seblak
   - Step 3: Tambah topping (opsional)
   - Step 4: Pilih metode pembayaran

2. **Pilih Midtrans**
   - Di Step 4, pilih "Midtrans (Kartu/E-Wallet/Bank Transfer)"
   - Klik "Proses Transaksi"

3. **Popup Midtrans Muncul**
   - Popup payment Midtrans akan terbuka
   - Pilih metode pembayaran (Kartu/GoPay/Bank Transfer/dll)
   - Ikuti instruksi pembayaran

4. **Hasil Pembayaran**
   - **Success:** Order tersimpan dengan status "paid"
   - **Pending:** Order tersimpan dengan status "pending" (untuk bank transfer)
   - **Failed:** Transaksi dibatalkan, order tidak tersimpan

## File-file Penting

```
api/midtrans/
├── config.php                  # Konfigurasi API keys
├── create-transaction.php      # Generate Snap Token
└── README.md                   # Dokumentasi (file ini)

dist/dashboard/pages/
└── transaksi.php              # Halaman transaksi dengan integrasi Midtrans
```

## Metode Pembayaran Tersedia

### 1. **Tunai (Cash)**
- Pembayaran manual di kasir
- Langsung tersimpan sebagai "pending"

### 2. **Midtrans (Online Payment)**
- **Kartu Kredit/Debit:** Visa, Mastercard, JCB, Amex
- **E-Wallet:** GoPay, ShopeePay, DANA, OVO, LinkAja
- **Bank Transfer:** BCA, Mandiri, BNI, BRI, Permata
- **Retail:** Indomaret, Alfamart
- **Paylater:** Kredivo, Akulaku

## Testing Scenarios

### Scenario 1: Pembayaran Sukses (Kartu Kredit)
```
1. Pilih metode: Midtrans
2. Di popup, pilih "Credit Card"
3. Input: 4811 1111 1111 1114, Exp: 01/25, CVV: 123
4. Klik Pay
5. Result: Order saved dengan status "paid"
```

### Scenario 2: Pembayaran Pending (Bank Transfer)
```
1. Pilih metode: Midtrans
2. Di popup, pilih "Bank Transfer" > "BCA"
3. Virtual Account akan di-generate
4. Simulasi pembayaran di Midtrans Dashboard
5. Result: Order saved dengan status "pending"
```

### Scenario 3: Pembayaran Dibatalkan
```
1. Pilih metode: Midtrans
2. Di popup, klik tombol "X" (close)
3. Result: "Pembayaran dibatalkan", order tidak tersimpan
```

## Simulasi Pembayaran di Dashboard

Untuk simulasi pembayaran Bank Transfer / E-Wallet:

1. Login ke https://dashboard.sandbox.midtrans.com/
2. Klik **Transactions**
3. Cari order berdasarkan Order ID
4. Klik **Actions** > **Change Status**
5. Pilih status yang diinginkan (Settlement/Expire/Cancel)

## Production Ready

Untuk menggunakan di production (live):

1. Daftar di https://dashboard.midtrans.com/ (bukan sandbox)
2. Submit dokumen bisnis untuk verifikasi
3. Update `config.php`:
   ```php
   define('MIDTRANS_IS_PRODUCTION', true);
   define('MIDTRANS_SERVER_KEY', 'Mid-server-PRODUCTION_KEY');
   define('MIDTRANS_CLIENT_KEY', 'Mid-client-PRODUCTION_KEY');
   ```
4. Ganti URL Snap script di `transaksi.php` dari sandbox ke production

## Troubleshooting

### Problem: "Failed to create Snap token"
**Solusi:**
- Cek API keys sudah benar
- Cek koneksi internet
- Cek error log di `error_log`

### Problem: Popup Midtrans tidak muncul
**Solusi:**
- Cek Client Key sudah di-set di `config.php`
- Cek browser console untuk error
- Pastikan Snap JS berhasil di-load

### Problem: Payment success tapi order tidak tersimpan
**Solusi:**
- Cek koneksi database
- Cek API `orders.php` berfungsi
- Cek browser console dan network tab

## Support & Documentation

- Midtrans Docs: https://docs.midtrans.com/
- Midtrans Dashboard Sandbox: https://dashboard.sandbox.midtrans.com/
- Midtrans API Reference: https://api-docs.midtrans.com/

---

**CATATAN PENTING:**
- Ini adalah Sandbox/Testing environment
- Transaksi TIDAK REAL
- Untuk production, perlu verifikasi bisnis
- Jangan share API keys ke public/git repository!
