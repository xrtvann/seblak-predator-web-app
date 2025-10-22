# Alur Kerja Sistem - Aplikasi Seblak Predator

Diagram ini menjelaskan cara kerja aplikasi Seblak Predator dari awal sampai akhir, dengan bahasa yang mudah dipahami.

```mermaid
flowchart TD
  %% Mulai
  Mulai((MULAI)) --> BukaAplikasi["Buka Aplikasi"]
  BukaAplikasi -->|Belum Login| HalamanLogin["Tampil Halaman Login"]
  BukaAplikasi -->|Sudah Login| Dashboard["Tampil Dashboard<br/>(Ringkasan Data)"]

  %% Login
  HalamanLogin --> InputLogin["Masukkan Username<br/>dan Password"]
  InputLogin -->|Data Benar| Dashboard
  InputLogin -->|Data Salah| PesanError["Tampil Pesan Error"]
  PesanError --> HalamanLogin

  %% Menu Utama
  Dashboard --> PilihMenu{"Pilih Menu"}
  PilihMenu --> MenuProduk["Kelola Menu Produk"]
  PilihMenu --> MenuTopping["Kelola Topping"]
  PilihMenu --> MenuUser["Kelola Pengguna"]
  PilihMenu --> MenuRole["Kelola Hak Akses"]
  PilihMenu --> MenuTransaksi["Kelola Transaksi"]

  %% Kelola Produk
  MenuProduk --> LihatProduk["Lihat Daftar Produk"]
  LihatProduk --> AksiProduk{"Pilih Aksi"}
  AksiProduk -->|Tambah| TambahProduk["Isi Form Produk Baru<br/>(Nama, Harga, Kategori)"]
  AksiProduk -->|Edit| EditProduk["Ubah Data Produk"]
  AksiProduk -->|Hapus| HapusProduk["Hapus Produk"]
  TambahProduk --> LihatProduk
  EditProduk --> LihatProduk
  HapusProduk --> LihatProduk

  %% Kelola Topping
  MenuTopping --> LihatTopping["Lihat Daftar Topping"]
  LihatTopping --> AksiTopping{"Pilih Aksi"}
  AksiTopping -->|Tambah| TambahTopping["Isi Form Topping Baru<br/>(Nama, Harga)"]
  AksiTopping -->|Edit| EditTopping["Ubah Data Topping"]
  AksiTopping -->|Hapus| HapusTopping["Hapus Topping"]
  TambahTopping --> LihatTopping
  EditTopping --> LihatTopping
  HapusTopping --> LihatTopping

  %% Transaksi
  MenuTransaksi --> LihatTransaksi["Lihat Daftar Transaksi<br/>(Total, Selesai, Pending)"]
  MenuTransaksi --> TransaksiBaru["Klik 'Transaksi Baru'"]

  TransaksiBaru --> InputCustomer["1. Isi Data Customer:<br/>- Nama<br/>- No. Meja<br/>- No. Telepon<br/>- Catatan"]
  InputCustomer --> PilihProduk["2. Pilih Produk yang Dipesan"]
  PilihProduk --> TambahKeKeranjang["3. Tambah ke Keranjang"]
  TambahKeKeranjang --> TanyaTopping{"Mau Tambah<br/>Topping?"}
  
  TanyaTopping -->|Ya| PilihTopping["4. Pilih Topping"]
  TanyaTopping -->|Tidak| AturJumlah["5. Atur Jumlah Pesanan"]
  PilihTopping --> AturJumlah
  
  AturJumlah --> TambahLagi{"Mau Tambah<br/>Produk Lagi?"}
  TambahLagi -->|Ya| PilihProduk
  TambahLagi -->|Tidak| LihatRingkasan["6. Lihat Ringkasan:<br/>- Subtotal<br/>- Pajak<br/>- Diskon<br/>- TOTAL"]
  
  LihatRingkasan --> PilihPembayaran["7. Pilih Metode Pembayaran:<br/>• Tunai<br/>• Kartu Debit/Kredit<br/>• QRIS<br/>• Transfer"]
  PilihPembayaran --> ProsesTransaksi["8. Klik 'Proses Transaksi'"]
  
  ProsesTransaksi --> SimpanData{"Simpan Data<br/>Transaksi"}
  SimpanData -->|Berhasil| TransaksiBerhasil["✓ Transaksi Berhasil!<br/>(No. Transaksi: ORD-YYYYMMDD-XXXX)"]
  SimpanData -->|Gagal| TransaksiGagal["✗ Transaksi Gagal<br/>(Tampil Pesan Error)"]
  
  TransaksiBerhasil --> CetakStruk["9. Cetak Struk (Opsional)"]
  TransaksiGagal --> TransaksiBaru
  TransaksiBerhasil --> LihatTransaksi
  CetakStruk --> LihatTransaksi

  %% Kelola Transaksi
  LihatTransaksi --> AksiTransaksi{"Pilih Aksi"}
  AksiTransaksi -->|Lihat Detail| DetailTransaksi["Lihat Detail Pesanan:<br/>- Item<br/>- Topping<br/>- Total"]
  AksiTransaksi -->|Selesaikan| SelesaikanOrder["Tandai 'Selesai'<br/>(Sudah Bayar)"]
  AksiTransaksi -->|Batalkan| BatalkanOrder["Batalkan Transaksi"]
  
  DetailTransaksi --> LihatTransaksi
  SelesaikanOrder --> CetakStruk
  BatalkanOrder --> LihatTransaksi

  %% Kelola User & Role
  MenuUser --> KelolaUser["Tambah/Edit/Hapus<br/>Pengguna Aplikasi"]
  MenuRole --> KelolaRole["Atur Hak Akses<br/>Pengguna"]
  KelolaUser --> Dashboard
  KelolaRole --> Dashboard

  %% Selesai
  LihatTransaksi --> Selesai((SELESAI))
  LihatProduk --> Dashboard
  LihatTopping --> Dashboard

  %% Styling
  classDef proses fill:#e3f2fd,stroke:#1976d2,stroke-width:2px,color:#000;
  classDef input fill:#fff3e0,stroke:#f57c00,stroke-width:2px,color:#000;
  classDef sukses fill:#e8f5e9,stroke:#388e3c,stroke-width:2px,color:#000;
  classDef error fill:#ffebee,stroke:#d32f2f,stroke-width:2px,color:#000;
  
  class Dashboard,LihatProduk,LihatTopping,LihatTransaksi proses;
  class InputLogin,InputCustomer,TambahProduk,TambahTopping input;
  class TransaksiBerhasil,SelesaikanOrder sukses;
  class PesanError,TransaksiGagal,BatalkanOrder error;
```

## Keterangan
- **Kotak Biru**: Proses melihat/menampilkan data
- **Kotak Oranye**: Input data / formulir
- **Kotak Hijau**: Proses berhasil
- **Kotak Merah**: Error / pembatalan
- **Belah Ketupat**: Pilihan keputusan

## Cara Kerja Aplikasi (Ringkasan)

### 1. Login
- Buka aplikasi → Masukkan username & password → Jika benar, masuk ke Dashboard

### 2. Kelola Menu & Topping
- Dari Dashboard, pilih "Menu Produk" atau "Topping"
- Bisa tambah, edit, atau hapus data
- Produk: Seblak, Minuman, dll dengan harga
- Topping: Tambahan yang bisa dipilih customer

### 3. Buat Transaksi Baru
1. Klik "Transaksi Baru"
2. Isi nama customer, nomor meja
3. Pilih produk yang dipesan (misal: Seblak Original)
4. Tambah topping jika perlu (misal: Keju, Sosis)
5. Atur jumlah pesanan
6. Bisa tambah produk lain lagi
7. Lihat total harga
8. Pilih cara bayar (Tunai/QRIS/Transfer/Kartu)
9. Klik "Proses Transaksi"
10. Cetak struk (opsional)

### 4. Kelola Transaksi
- Lihat daftar semua transaksi
- Bisa filter: Hari ini, Pending, Selesai
- Bisa lihat detail pesanan
- Tandai "Selesai" jika sudah bayar
- Batalkan jika ada kesalahan

### 5. Kelola User & Hak Akses
- Tambah pengguna baru (kasir, admin)
- Atur siapa saja yang boleh akses menu tertentu

---

## Catatan Penting
- ✅ Harga produk & topping tersimpan saat transaksi dibuat (tidak berubah meski harga di menu diubah)
- ✅ Sistem prasmanan: tidak perlu hitung stok barang
- ✅ Nomor transaksi otomatis: ORD-20251022-0001 (tanggal + nomor urut)

---

## Cara Export Diagram ke Gambar (PNG/SVG)

### Opsi 1: Menggunakan Website Online (Paling Mudah)
1. Buka https://mermaid.live/
2. Copy semua kode diagram (yang ada di antara \`\`\`mermaid dan \`\`\`)
3. Paste ke editor di website
4. Klik tombol "Download PNG" atau "Download SVG"

### Opsi 2: Menggunakan VS Code
1. Install extension "Markdown Preview Mermaid Support"
2. Buka file ini di VS Code
3. Tekan `Ctrl+Shift+V` untuk preview
4. Klik kanan pada diagram → "Copy Image" atau screenshot

### Opsi 3: Menggunakan Command Line (Untuk Developer)
```bash
# Install tool Mermaid CLI
npm install -g @mermaid-js/mermaid-cli

# Simpan kode mermaid ke file flowchart.mmd
# Lalu convert:
mmdc -i flowchart.mmd -o system_flowchart.png
```

---

## Informasi Tambahan
- Diagram ini bisa dilihat langsung di GitHub (tanpa export)
- Cocok untuk dokumentasi, presentasi, atau training karyawan baru
- Flowchart ini menjelaskan SEMUA fitur yang ada di aplikasi Seblak Predator