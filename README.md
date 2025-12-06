# 🍲 Seblak Predator - Restaurant Management System

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Bootstrap-5.3.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License">
</p>

## 📖 Deskripsi Project

**Seblak Predator** adalah sistem manajemen restoran berbasis web yang komprehensif untuk mengelola operasional restoran prasmanan seblak. Sistem ini menyediakan dashboard admin untuk mengelola menu, pesanan, pengeluaran, laporan keuangan, serta integrasi pembayaran digital dengan Midtrans.

Project ini dibangun dengan arsitektur **RESTful API** menggunakan PHP native dan MySQL, dilengkapi dengan dashboard admin yang modern dan responsif menggunakan Bootstrap 5.

### 🎯 Tujuan Project
- Digitalisasi manajemen operasional restoran prasmanan
- Otomasi proses pemesanan dan pembayaran
- Monitoring keuangan real-time dengan laporan PDF
- Integrasi pembayaran digital (Midtrans Snap)
- Manajemen multi-role user (Owner, Kasir, dan Customer)
- Support untuk delivery orders via mobile API

---

## 🚀 Fitur Utama

### 🔐 Authentication & Authorization
- **JWT-based Authentication** dengan access & refresh tokens
- **Role-based Access Control** (Owner, Kasir, dan Customer)
- **Session Management** dengan encryption
- Password reset via email
- Login/Register dengan validasi

### 🍽️ Menu Management
- CRUD Categories (Dasar Seblak & Topping)
- CRUD Dasar Seblak dengan image upload
- CRUD Toppings dengan harga dinamis
- Spice Levels customization
- Customization Options (Kencur, Kuah, Telur, dll)
- Soft delete dengan restore capability

### 🧾 Order Management (Transaksi)
- **3 Tipe Pesanan**: Dine In, Take Away, Delivery
- Multi-item orders dengan customizations
- Real-time order tracking (Pending → Processing → Completed)
- Invoice generation dengan print receipt
- Midtrans Snap integration untuk pembayaran digital
- Token regeneration untuk expired Midtrans transactions
- Mobile API endpoint untuk delivery orders

### 💰 Financial Management
- Expense tracking dengan categories
- Income vs Expense analysis
- Financial reports dengan filter (Today, Week, Month, Year, Custom)
- **Export to PDF** dengan dompdf (full-page layout)
- Dashboard statistics (Total Revenue, Expenses, Net Profit)
- Profit margin calculation

### 📊 Dashboard & Analytics
- Real-time statistics cards
- Order status overview (Pending, Completed, Cancelled)
- Revenue charts dengan ApexCharts
- Daily/Weekly/Monthly trends
- Top performing products/toppings

### 👥 User Management
- User CRUD dengan role assignment
- Profile management (upload avatar, update info)
- Account settings (optional phone number)
- Activity logs & security features

### 📱 Mobile App Support
- **Mobile API** untuk delivery orders
- Sync endpoints untuk categories & products
- Image upload API untuk menu items

### 🔔 Additional Features
- File upload dengan validation
- Responsive design (mobile-friendly)
- SweetAlert2 notifications
- Real-time search & filtering
- Pagination dengan customizable page size
- Timezone support (WIB/Asia Jakarta)

---

## 🛠️ Tech Stack

### Backend
| Technology | Version | Description |
|------------|---------|-------------|
| **PHP** | 7.4+ | Server-side scripting language |
| **MySQL** | 8.0+ | Relational database management |
| **MySQLi** | - | Database driver dengan prepared statements |
| **Apache** | 2.4+ | Web server (Laragon/XAMPP) |

### Frontend
| Technology | Version | Description |
|------------|---------|-------------|
| **HTML5** | - | Markup language |
| **CSS3** | - | Styling dengan custom variables |
| **JavaScript ES6** | - | Client-side scripting |
| **Bootstrap** | 5.3.3 | UI framework |
| **SCSS/Sass** | 1.77.6 | CSS preprocessor |

### Libraries & Plugins

#### PHP Dependencies (Composer)
```json
{
  "firebase/php-jwt": "^6.11",      // JWT authentication
  "phpmailer/phpmailer": "^6.8",    // Email service
  "dompdf/dompdf": "^3.1",          // PDF generation
  "phpunit/phpunit": "^12.4"        // Unit testing
}
```

#### JavaScript Dependencies (NPM)
```json
{
  "bootstrap": "5.3.3",             // UI framework
  "apexcharts": "^3.54.0",          // Interactive charts
  "@popperjs/core": "^2.11.8",      // Tooltip positioning
  "simplebar": "^6.2.7",            // Custom scrollbars
  "feather-icons": "^4.29.2",       // Icon library
  "clipboard": "^2.0.11"            // Copy to clipboard
}
```

#### Frontend Libraries (CDN)
- **Tabler Icons** - Modern icon set
- **SweetAlert2** - Beautiful alert modals
- **Chart.js 4.4.0** - Canvas-based charts
- **Midtrans Snap.js** - Payment gateway integration

### Build Tools
- **Gulp** 4.0.2 - Task automation
- **Babel** - JavaScript transpiler
- **Autoprefixer** - CSS vendor prefixing
- **Sass Compiler** - SCSS to CSS compilation
- **Prettier** - Code formatter

### Payment Gateway
- **Midtrans Snap** - Digital payment integration (Production & Sandbox)

### PDF Generation
- **Dompdf 3.1** - HTML to PDF converter dengan custom styling

---

## 📂 Struktur Project

```
seblak-predator/
├── api/                          # REST API endpoints
│   ├── auth/                    # Authentication (login, register, JWT)
│   ├── menu/                    # Menu management (categories, products, toppings)
│   ├── midtrans/                # Midtrans payment integration
│   ├── mobile/                  # Mobile app endpoints
│   ├── orders/                  # Order management
│   ├── sync/                    # Data synchronization
│   ├── upload/                  # File upload handlers
│   ├── dashboard-stats.php      # Dashboard statistics
│   ├── expense-categories.php   # Expense category management
│   ├── expenses.php             # Expense tracking
│   ├── export-financial-report.php  # PDF export
│   ├── financial-report.php     # Financial reports API
│   ├── orders.php               # Order CRUD operations
│   └── users.php                # User management
│
├── config/                       # Configuration files
│   ├── config.php               # General configuration
│   ├── database.php             # Database connection manager
│   ├── env.php                  # Environment loader
│   ├── koneksi.php              # MySQLi connection
│   └── session.php              # Session management
│
├── dist/                         # Compiled dashboard assets
│   └── dashboard/               # Admin dashboard pages
│       └── pages/               # Dashboard UI pages
│
├── handler/                      # Request handlers
│   ├── auth.php                 # Authentication handler
│   ├── forgot_password.php      # Password reset
│   └── logout.php               # Logout handler
│
├── pages/                        # Public pages
│   └── auth/                    # Login, register, forgot password
│
├── services/                     # Service classes
│   ├── EmailService.php         # Email sending service
│   ├── SessionEncryption.php    # Session security
│   └── WebAuthService.php       # Web authentication
│
├── src/                          # Source files (SCSS, JS)
│   ├── assets/                  # Static assets
│   │   ├── fonts/              # Custom fonts
│   │   ├── images/             # Images
│   │   ├── js/                 # JavaScript modules
│   │   └── scss/               # SCSS stylesheets
│   └── html/                    # HTML templates
│
├── tests/                        # PHPUnit test files
├── uploads/                      # User uploaded files
│   └── menu-images/             # Product images
│
├── vendor/                       # Composer dependencies
├── composer.json                 # PHP dependencies
├── package.json                  # NPM dependencies
├── gulpfile.js                   # Gulp build configuration
└── index.php                     # Application entry point
```

---

## ⚙️ Cara Install

### Prerequisites
Pastikan sistem Anda sudah terinstall:
- **PHP** >= 7.4 (dengan ekstensi: mysqli, pdo, gd, mbstring, json)
- **MySQL** >= 8.0 atau **MariaDB** >= 10.4
- **Composer** (untuk PHP dependencies)
- **Node.js & NPM** (untuk frontend build)
- **Apache/Nginx** web server
- **Git** (optional)

### Recommended: Menggunakan Laragon (Windows)
Laragon sudah include PHP, MySQL, Apache, dan Composer.
Download: https://laragon.org/download/

---

### 🔧 Langkah Instalasi

#### 1. Clone atau Download Project
```bash
# Clone dengan Git
git clone https://github.com/xrtvann/seblak-predator-web-app.git

# Atau download ZIP dan extract ke folder web server
# Contoh: C:/laragon/www/seblak-predator (Laragon)
#         C:/xampp/htdocs/seblak-predator (XAMPP)
```

#### 2. Install PHP Dependencies
```bash
cd seblak-predator
composer install
```

#### 3. Install NPM Dependencies (Optional - untuk development)
```bash
npm install
```

#### 4. Setup Database

**a. Buat Database**
```sql
CREATE DATABASE seblak_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**b. Import Database Schema**
- Locate file SQL schema (biasanya `database.sql` atau `seblak_app.sql`)
- Import via phpMyAdmin atau command line:
```bash
mysql -u root -p seblak_app < database.sql
```

**c. Konfigurasi Database Connection**

Buat file `.env` di root folder (copy dari `.env.example` jika ada):
```env
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=seblak_app
DB_PORT=3306

JWT_SECRET=your-secret-key-here
JWT_EXPIRY=3600
```

Atau edit langsung `config/database.php` jika tidak menggunakan .env.

#### 5. Generate JWT Keys (Optional)
```bash
php generate_keys.php
```

#### 6. Setup Midtrans (Payment Gateway)

Edit `api/midtrans/config.php`:
```php
// Sandbox (Testing)
$serverKey = 'SB-Mid-server-xxxxxxxxxxxxx';
$clientKey = 'SB-Mid-client-xxxxxxxxxxxxx';
$isProduction = false;

// Production (Live)
// $serverKey = 'Mid-server-xxxxxxxxxxxxx';
// $clientKey = 'Mid-client-xxxxxxxxxxxxx';
// $isProduction = true;
```

Dapatkan credentials dari: https://dashboard.midtrans.com/

#### 7. Set Permissions (Linux/Mac)
```bash
chmod -R 755 uploads/
chmod -R 755 logs/
```

#### 8. Build Frontend Assets (Optional - untuk development)
```bash
# Development mode dengan watch
npm start

# Production build (minified)
npm run build-prod
```

#### 9. Akses Aplikasi

**Web Dashboard:**
```
http://localhost/seblak-predator/
```

**API Base URL:**
```
http://localhost/seblak-predator/api/
```

#### 10. Login ke Dashboard

Default credentials (setelah seeding database):
```
Username: admin
Password: admin123
```

⚠️ **PENTING**: Ganti password default setelah login pertama!

---

## 🧪 Testing

### Run PHPUnit Tests
```bash
vendor/bin/phpunit tests/
```

### API Testing dengan Postman
1. Import collection: `api/Seblak_Predator_API.postman_collection.json`
2. Import environment: `api/Seblak_Predator_Environment.postman_environment.json`
3. Update environment variables (base_url, access_token)
4. Run requests

---

## 📚 Database Schema

### Tabel Utama
- **users** - User accounts dengan roles
- **orders** - Transaksi pesanan (dine_in, take_away, delivery)
- **order_items** - Item pesanan dengan spice level
- **order_item_customizations** - Kustomisasi per item
- **order_item_toppings** - Topping per item
- **categories** - Kategori menu
- **products** - Produk menu
- **toppings** - Daftar topping
- **spice_levels** - Level kepedasan (10 levels)
- **customization_options** - Opsi kustomisasi
- **expenses** - Pengeluaran
- **expense_categories** - Kategori pengeluaran

---

## 🔒 Security Features
- ✅ JWT Authentication dengan token expiry
- ✅ Password hashing (bcrypt)
- ✅ SQL Injection prevention (Prepared Statements)
- ✅ XSS Protection (Input sanitization)
- ✅ CSRF Protection
- ✅ Session encryption
- ✅ Rate limiting
- ✅ Input validation
- ✅ Secure file upload

---

## 📱 Mobile API Endpoints

### Create Delivery Order (Mobile Only)
```http
POST /api/mobile/create-order.php
Content-Type: application/json

{
  "user_id": "user_123",
  "delivery_address": "Jl. Merdeka No. 123",
  "items": [
    {
      "spice_level_id": "lvl_001",
      "quantity": 2,
      "customizations": [...],
      "toppings": [...]
    }
  ]
}
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:
1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📄 License

This project is licensed under the MIT License.

---

## 👨‍💻 Developer

**Project Name**: Seblak Predator Web App  
**Repository**: https://github.com/xrtvann/seblak-predator-web-app  
**Developer**: xrtvann  
**Version**: 1.0.0  

---

## 📞 Support

Untuk pertanyaan atau bantuan teknis:
- 📧 Email: support@seblakpredator.com
- 🐛 Issues: [GitHub Issues](https://github.com/xrtvann/seblak-predator-web-app/issues)
- 📖 Documentation: [API Docs](#authentication)

---

## 🎉 Acknowledgments

- **Berry Admin Template** - Dashboard UI framework
- **Midtrans** - Payment gateway integration
- **Dompdf** - PDF generation library
- **Firebase PHP-JWT** - JWT authentication
- **Bootstrap Team** - UI framework

---

<p align="center">Made with ❤️ for Indonesian Seblak Lovers</p>

## Authentication

The API uses JWT (JSON Web Tokens) for authentication. All protected endpoints require a valid JWT token in the Authorization header.

### Login
```http
POST /auth/login.php
Content-Type: application/json

{
  "username": "admin",
  "password": "password"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "Bearer",
  "expires_in": 3600,
  "user": {
    "id": "user_123",
    "username": "admin",
    "name": "Administrator",
    "email": "admin@example.com",
    "role": "Administrator"
  }
}
```

### Using JWT Tokens
Include the access token in the Authorization header:
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

### Token Refresh
```http
POST /auth/refresh.php
Content-Type: application/json

{
  "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

### Logout
```http
POST /auth/logout.php
Authorization: Bearer {access_token}
```

## API Endpoints

### Menu Management

#### Categories

##### Get All Categories
```http
GET /menu/categories.php
```

**Query Parameters:**
- `page` (int): Page number (default: 1)
- `per_page` (int): Items per page (default: 20)
- `search` (string): Search term
- `type` (string): Filter by type ('product' or 'topping')
- `status` (string): Filter by status ('active', 'deleted', 'all')

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "cat_123",
      "name": "Makanan",
      "type": "product",
      "is_active": true,
      "created_at": "2024-01-01 12:00:00",
      "updated_at": "2024-01-01 12:00:00"
    }
  ],
  "meta": {
    "total": 1,
    "page": 1,
    "per_page": 20,
    "last_page": 1,
    "from": 1,
    "to": 1
  },
  "message": "Categories retrieved successfully"
}
```

##### Create Category
```http
POST /menu/categories.php
Content-Type: application/json

{
  "name": "Minuman",
  "type": "product",
  "description": "Kategori untuk semua jenis minuman"
}
```

##### Update Category
```http
PUT /menu/categories.php?id=cat_123
Content-Type: application/json

{
  "name": "Minuman Updated",
  "type": "product"
}
```

##### Delete Category
```http
DELETE /menu/categories.php?id=cat_123
```

##### Restore Category
```http
PATCH /menu/categories.php?action=restore&id=cat_123
```

#### Products

##### Get All Products
```http
GET /menu/products.php
```

**Query Parameters:**
- `page` (int): Page number
- `per_page` (int): Items per page
- `category_id` (string): Filter by category
- `is_topping` (boolean): Filter toppings only
- `is_active` (boolean): Filter by active status
- `search` (string): Search term

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "prod_123",
      "category_id": "cat_123",
      "name": "Seblak Original",
      "description": "Seblak dengan bumbu original",
      "image_url": "menu_123456789.jpg",
      "price": 12000,
      "is_topping": false,
      "is_active": true,
      "category_name": "Makanan",
      "category_type": "product",
      "created_at": "2024-01-01 12:00:00",
      "updated_at": "2024-01-01 12:00:00",
      "variants": [],
      "toppings": []
    }
  ],
  "meta": {
    "total": 1,
    "page": 1,
    "per_page": 20,
    "last_page": 1
  },
  "message": "Produk berhasil diambil"
}
```

##### Get Product by ID
```http
GET /menu/products.php?id=prod_123
```

##### Create Product
```http
POST /menu/products.php
Content-Type: application/json

{
  "name": "Seblak Original",
  "description": "Seblak dengan bumbu original",
  "category_id": "cat_123",
  "price": 12000,
  "image_url": "uploads/menu-images/menu_123456789.jpg",
  "is_topping": false
}
```

##### Update Product
```http
PUT /menu/products.php?id=prod_123
Content-Type: application/json

{
  "name": "Seblak Original Updated",
  "price": 13000
}
```

##### Delete Product
```http
DELETE /menu/products.php?id=prod_123
```

##### Restore Product
```http
PATCH /menu/products.php?action=restore&id=prod_123
```

### Order Management

#### Orders

##### Get All Orders
```http
GET /orders.php
```

**Query Parameters:**
- `page` (int): Page number
- `per_page` (int): Items per page
- `status` (string): Filter by order status
- `payment_status` (string): Filter by payment status
- `date` (string): Filter by date (YYYY-MM-DD)
- `search` (string): Search term

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "ord_123",
      "order_number": "ORD-20240101-0001",
      "customer_name": "John Doe",
      "table_number": "A1",
      "phone": "08123456789",
      "notes": "Extra pedas",
      "subtotal": 12000,
      "tax": 0,
      "discount": 0,
      "total_amount": 12000,
      "payment_method": "cash",
      "payment_status": "paid",
      "order_status": "completed",
      "created_by": "user_123",
      "created_at": "2024-01-01 12:00:00",
      "completed_at": "2024-01-01 12:30:00",
      "items_count": 1
    }
  ],
  "pagination": {
    "page": 1,
    "per_page": 20,
    "total": 1,
    "total_pages": 1
  },
  "statistics": {
    "total_orders": 1,
    "completed_orders": 1,
    "pending_orders": 0,
    "total_revenue": 12000,
    "today_revenue": 12000
  }
}
```

##### Get Order by ID
```http
GET /orders.php?id=ord_123
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "ord_123",
    "order_number": "ORD-20240101-0001",
    "customer_name": "John Doe",
    "table_number": "A1",
    "phone": "08123456789",
    "notes": "Extra pedas",
    "subtotal": 12000,
    "tax": 0,
    "discount": 0,
    "total_amount": 12000,
    "payment_method": "cash",
    "payment_status": "paid",
    "order_status": "completed",
    "created_by": "user_123",
    "created_by_name": "Admin",
    "created_at": "2024-01-01 12:00:00",
    "completed_at": "2024-01-01 12:30:00",
    "items": [
      {
        "id": "item_123",
        "order_id": "ord_123",
        "product_id": "prod_123",
        "product_name": "Seblak Original",
        "quantity": 1,
        "unit_price": 12000,
        "subtotal": 12000,
        "notes": null,
        "image_url": "menu_123456789.jpg",
        "toppings": []
      }
    ]
  }
}
```

##### Create Order
```http
POST /orders.php
Content-Type: application/json

{
  "customer_name": "John Doe",
  "table_number": "A1",
  "phone": "08123456789",
  "notes": "Extra pedas",
  "items": [
    {
      "product_id": "prod_123",
      "product_name": "Seblak Original",
      "quantity": 1,
      "unit_price": 12000,
      "notes": "Extra pedas",
      "toppings": [
        {
          "topping_id": "top_123",
          "topping_name": "Keju",
          "quantity": 1,
          "unit_price": 3000
        }
      ]
    }
  ],
  "tax": 0,
  "discount": 0,
  "payment_method": "cash",
  "payment_status": "pending"
}
```

##### Update Order
```http
PUT /orders.php?id=ord_123
Content-Type: application/json

{
  "customer_name": "Jane Doe",
  "table_number": "A2"
}
```

##### Update Order Status
```http
PATCH /orders.php?id=ord_123
Content-Type: application/json

{
  "status": "completed"
}
```

##### Delete Order
```http
DELETE /orders.php?id=ord_123
```

### Expense Management

#### Expenses

##### Get All Expenses
```http
GET /expenses.php
```

**Query Parameters:**
- `page` (int): Page number
- `per_page` (int): Items per page
- `category_id` (string): Filter by category
- `is_active` (boolean): Filter by active status
- `start_date` (string): Start date (YYYY-MM-DD)
- `end_date` (string): End date (YYYY-MM-DD)
- `search` (string): Search term

##### Create Expense
```http
POST /expenses.php
Content-Type: application/json

{
  "title": "Beli Bumbu",
  "description": "Pembelian bumbu dapur untuk sebulan",
  "category_id": "cat_exp_123",
  "amount": 50000,
  "expense_date": "2024-01-01",
  "payment_method": "cash",
  "receipt_image": "receipt_123.jpg"
}
```

#### Expense Categories

##### Get All Expense Categories
```http
GET /expense-categories.php
```

##### Create Expense Category
```http
POST /expense-categories.php
Content-Type: application/json

{
  "name": "Bahan Baku",
  "description": "Kategori untuk pembelian bahan baku",
  "color": "#FF6B6B",
  "icon": "ti ti-shopping-cart"
}
```

### User Management

#### Roles

##### Get All Roles
```http
GET /roles.php
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "role_admin",
      "name": "Administrator",
      "user_count": 2,
      "created_at": "2024-01-01 12:00:00"
    }
  ],
  "pagination": {
    "page": 1,
    "per_page": 20,
    "total": 3,
    "total_pages": 1
  },
  "statistics": {
    "total": 3,
    "owner_users": 1,
    "admin_users": 2,
    "customer_users": 5
  }
}
```

##### Create Role
```http
POST /roles.php
Content-Type: application/json

{
  "name": "Manager"
}
```

##### Update Role
```http
PUT /roles.php?id=role_123
Content-Type: application/json

{
  "name": "Senior Manager"
}
```

##### Delete Role
```http
DELETE /roles.php?id=role_123
```

### Authentication Endpoints

#### Validate Token
```http
GET /auth/validate.php
Authorization: Bearer {access_token}
```

#### Get User Profile
```http
GET /auth/profile.php
Authorization: Bearer {access_token}
```

### File Upload

#### Upload Image
```http
POST /upload/image.php
Content-Type: multipart/form-data

image: [file]
```

**Response:**
```json
{
  "success": true,
  "message": "Image uploaded successfully",
  "data": {
    "filename": "menu_123456789_1234567890.jpg",
    "url": "http://localhost/seblak-predator/uploads/menu-images/menu_123456789_1234567890.jpg",
    "relative_url": "uploads/menu-images/menu_123456789_1234567890.jpg",
    "size": 245760,
    "type": "image/jpeg"
  }
}
```

#### Check Image Existence
```http
GET /check-image.php?filename=menu_123456789.jpg
```

### Synchronization (Mobile App)

#### Sync Categories
```http
GET /sync/categories.php?since=2024-01-01 12:00:00
```

#### Sync Products
```http
GET /sync/products.php?since=2024-01-01 12:00:00
```

### Payment Integration

#### Create Midtrans Transaction
```http
POST /midtrans/create-transaction.php
Content-Type: application/json

{
  "customer_name": "John Doe",
  "phone": "08123456789",
  "table_number": "A1",
  "items": [
    {
      "product_id": "prod_123",
      "product_name": "Seblak Original",
      "quantity": 1,
      "unit_price": 12000,
      "toppings": []
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "snap_token": "SNAP_TOKEN_HERE",
  "order_id": "ORD-20240101-0001",
  "total_amount": 12000
}
```

## Data Models

### Order Statuses
- `pending`: Order created, waiting for processing
- `processing`: Order being prepared
- `completed`: Order completed and delivered
- `cancelled`: Order cancelled

### Payment Statuses
- `pending`: Payment not yet received
- `paid`: Payment received
- `failed`: Payment failed
- `refunded`: Payment refunded

### Payment Methods
- `cash`: Cash payment
- `card`: Credit/debit card
- `transfer`: Bank transfer
- `ewallet`: E-wallet (GoPay, OVO, etc.)

### User Roles
- `role_owner`: Restaurant owner (full access)
- `role_admin`: Administrator (most features)
- `role_staff`: Staff (limited access)
- `role_customer`: Customer (read-only)

## Error Handling

All API responses include a `success` field and `message` field. Error responses include appropriate HTTP status codes:

- `200`: Success
- `201`: Created
- `400`: Bad Request (validation errors)
- `401`: Unauthorized (invalid/missing token)
- `403`: Forbidden (insufficient permissions)
- `404`: Not Found
- `405`: Method Not Allowed
- `409`: Conflict (duplicate data)
- `500`: Internal Server Error

**Error Response Format:**
```json
{
  "success": false,
  "message": "Error description",
  "error": "Detailed error information (development only)"
}
```

## Rate Limiting

The API implements rate limiting to prevent abuse:
- Authenticated requests: 1000 requests per hour
- Unauthenticated requests: 100 requests per hour
- File uploads: 50 uploads per hour

## Security

- All sensitive endpoints require JWT authentication
- Passwords are hashed using secure algorithms
- Input validation and sanitization
- CORS headers configured for web/mobile access
- SQL injection prevention using prepared statements
- XSS protection through input escaping

## Development

### Environment Setup
1. Clone the repository
2. Configure database in `config/koneksi.php`
3. Set up JWT secret in authentication config
4. Configure Midtrans credentials for payments
5. Run database migrations

### Testing
Use the provided Postman collection (`Seblak_Predator_API.postman_collection.json`) for testing all endpoints.

### Contributing
1. Follow PHP PSR standards
2. Add proper error handling
3. Include input validation
4. Update documentation for new features
5. Test thoroughly before committing

## Support

For technical support or questions:
- Check the API documentation
- Review error messages for details
- Contact the development team

## Changelog

### Version 1.0.0
- Initial release
- Basic CRUD operations for all entities
- JWT authentication
- Midtrans payment integration
- Mobile app synchronization
- File upload functionality
- Expense management
- User role management
