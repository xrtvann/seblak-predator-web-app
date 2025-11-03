## 1. Struktur Folder dan File

```
D:\laragon\www\seblak-predator\
├───.babelrc
├───.env.example
├───.gitignore
├───composer.json
├───composer.lock
├───generate_keys.php
├───gulpfile.js
├───index.php
├───package-lock.json
├───package.json
├───phpunit.xml
├───project-structure.md
├───README.md
├───test_db.php
├───TODO.md
├───yarn.lock
├───.git\
├───.github\
│   └───workflows\
├───api\
│   ├───check-image.php
│   ├───expense-categories.php
│   ├───expenses.php
│   ├───helpers.php
│   ├───orders.php
│   ├───roles.php
│   ├───Seblak_Predator_API.postman_collection.json
│   ├───Seblak_Predator_Environment.postman_environment.json
│   ├───users.php
│   ├───auth\
│   │   ├───JWTHelper.php
│   │   ├───login.php
│   │   ├───logout.php
│   │   ├───middleware.php
│   │   ├───profile.php
│   │   ├───refresh.php
│   │   └───validate.php
│   ├───menu\
│   │   ├───categories.php
│   │   └───products.php
│   ├───midtrans\
│   │   ├───config.php
│   │   ├───create-transaction.php
│   │   └───README.md
│   ├───sync\
│   │   ├───categories.php
│   │   └───products.php
│   └───upload\
│       └───image.php
├───config\
│   ├───config.php
│   ├───database.php
│   ├───env.php
│   ├───koneksi.php
│   └───session.php
├───dist\
├───docs\
│   ├───KATEGORI_DELETED_ITEMS_FEATURE.md
│   └───system_flowchart.md
├───handler\
│   ├───auth.php
│   ├───forgot_password.php
│   └───logout.php
├───logs\
├───pages\
│   └───auth\
│       ├───access-denied-customer.php
│       ├───access-denied-permissions.php
│       ├───forgot-password.php
│       ├───login.php
│       └───register.php
├───services\
│   ├───DevelopmentEmailService.php
│   ├───EmailService.php
│   ├───SessionEncryption.php
│   ├───SimpleDevelopmentEmailService.php
│   ├───SimpleEmailService.php
│   └───WebAuthService.php
├───sql\
│   ├───create_transactions.sql
│   ├───jwt_auth_schema.sql
│   ├───setup_roles.sql
│   └───transactions_schema.sql
├───src\
│   ├───assets\
│   │   ├───css\
│   │   ├───fonts\
│   │   ├───images\
│   │   ├───js\
│   │   └───scss\
│   └───html\
├───tests\
│   ├───LoginTest.php
│   └───_output\
├───uploads\
│   ├───.htaccess
│   └───menu-images\
└───vendor\
```

### Penjelasan Folder dan File Penting

*   **`/api`**: Berisi file-file PHP untuk menangani logika API, seperti otentikasi, manajemen menu, pesanan, dll.
*   **`/config`**: File konfigurasi proyek, termasuk koneksi database (`database.php`, `koneksi.php`).
*   **`/dist`**: Folder untuk file-file hasil kompilasi (CSS, JS) yang siap digunakan di production.
*   **`/docs`**: Dokumentasi proyek.
*   **`/handler`**: File PHP yang menangani permintaan form, seperti login, logout, dan lupa password.
*   **`/pages`**: Halaman-halaman antarmuka pengguna (UI) dalam format PHP.
*   **`/services`**: Kelas-kelas layanan (service classes) untuk fungsionalitas seperti pengiriman email dan otentikasi web.
*   **`/sql`**: Skema database dan file SQL untuk setup awal.
*   **`/src`**: Kode sumber utama, termasuk aset (CSS, JS, gambar) dan template HTML.
*   **`/tests`**: Unit test untuk aplikasi.
*   **`/uploads`**: Folder untuk menyimpan file yang di-upload, seperti gambar menu.
*   **`/vendor`**: Dependensi PHP yang dikelola oleh Composer.
*   **`composer.json`**: Mendefinisikan dependensi PHP.
*   **`package.json`**: Mendefinisikan dependensi JavaScript dan script untuk build.
*   **`gulpfile.js`**: Konfigurasi untuk Gulp, sebuah task runner untuk otomatisasi alur kerja pengembangan (seperti kompilasi SASS ke CSS).
*   **`index.php`**: File utama yang menjadi entry point aplikasi web.

## 2. Teknologi yang Digunakan

*   **Bahasa Pemrograman Utama**: PHP dan JavaScript.
*   **Framework/Library**:
    *   **Backend**: PHP native, dengan `phpmailer/phpmailer` untuk email dan `firebase/php-jwt` untuk otentikasi JWT.
    *   **Frontend**: Bootstrap 5, ApexCharts, feather-icons, dan jQuery (berdasarkan `package.json` dan struktur file).
    *   **Build Tool**: Gulp.js untuk kompilasi aset (SASS, JS).
*   **Environment**:
    *   Aplikasi ini tampaknya dirancang untuk berjalan di environment web server seperti Apache atau Nginx dengan PHP dan MySQL. Penggunaan Laragon sangat mungkin.
*   **File Konfigurasi**:
    *   **`.env.example`**: Contoh file environment. Konfigurasi spesifik (seperti kredensial database) disimpan di file `.env` yang dibuat dari contoh ini.
    *   **`composer.json`**: Mengelola dependensi PHP.
    ```json
    {
        "require": {
            "php": ">=7.4",
            "phpmailer/phpmailer": "^6.8",
            "firebase/php-jwt": "^6.11"
        },
        "require-dev": {
            "phpunit/phpunit": "^12.4"
        }
    }
    ```
    *   **`package.json`**: Mengelola dependensi Node.js untuk frontend dan build tools.
    ```json
    {
        "dependencies": {
            "bootstrap": "5.3.3",
            "apexcharts": "^3.54.0",
            ...
        },
        "devDependencies": {
            "gulp": "^4.0.2",
            "sass": "1.77.6",
            ...
        }
    }
    ```

## 3. Analisis Database

*   **Koneksi Database**: Koneksi ke database MySQL diatur dalam file `config/database.php`. File ini membaca konfigurasi dari environment variables (file `.env`) seperti `DB_HOST`, `DB_USER`, `DB_PASSWORD`, dan `DB_NAME`. File `config/koneksi.php` kemudian menggunakan kelas `DatabaseConnection` untuk membuat koneksi global `$koneksi`.

*   **Struktur Tabel**: Berdasarkan file-file di direktori `sql/`, berikut adalah beberapa tabel utama:

    *   **`orders`**: Menyimpan data transaksi/pesanan utama.
        *   `id` (PK), `user_id`, `customer_name`, `total_amount`, `status`, `created_at`, `updated_at`
    *   **`order_items`**: Menyimpan item-item dalam sebuah pesanan.
        *   `id` (PK), `order_id` (FK), `product_id` (FK), `item_name`, `quantity`, `unit_price`, `subtotal`
    *   **`order_item_details`**: Menyimpan detail tambahan untuk setiap item, seperti varian atau topping.
        *   `id` (PK), `order_item_id` (FK), `type`, `item_id`, `item_name`, `price_adjustment`
    *   **`users`**: Menyimpan data pengguna.
    *   **`roles`**: Menyimpan peran pengguna (owner, admin, customer).
    *   **`login_attempts`**, **`token_refresh_log`**, **`api_access_log`**, **`blacklisted_tokens`**, **`user_sessions`**: Tabel-tabel untuk keamanan dan logging otentikasi JWT.

### 🔍 Panduan Mengecek Database via Terminal atau PHP
1. **Cek lewat Terminal (MySQL)**
   ```bash
   mysql -u root -p
   show databases;
   use nama_database;
   show tables;
   describe nama_tabel;

2. **Cek lewat PHP CLI (MySQL)**
```bash
php -r 'include "config/koneksi.php"; $result = $koneksi->query("SHOW TABLES"); while($row = $result->fetch_row()){echo $row[0].PHP_EOL;}'
```