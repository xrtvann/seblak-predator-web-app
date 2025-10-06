## Architecture Overview

This is a **hybrid web and mobile application** built on top of the Berry Bootstrap admin template for a restaurant management system ("Seblak Predator"). The project features both a PHP web dashboard and REST API endpoints for Android mobile synchronization.

**Key Architecture Patterns:**
- **Single Entry Point**: `index.php` serves as both the router and the main application file
- **Query-Based Routing**: Pages are determined by `$_GET['page']` parameter (e.g., `index.php?page=menu`)
- **Security-First Routing**: Uses whitelist validation via `$allowed_pages` array and `in_array()` check
- **Modular Page System**: Individual pages stored in `dist/dashboard/pages/*.php` and included dynamically
- **REST API Layer**: Dedicated API endpoints for Android app communication using JSON responses
- **Mobile-Web Sync**: Real-time data synchronization between web dashboard and Android application
- **Static Asset Pipeline**: SCSS from `src/assets/scss/` is compiled to `dist/assets/css/` via Gulp
- **Modern UI Components**: Enhanced forms with real-time preview, responsive design, and modern styling

**Directory Structure:**
- `index.php`: Main application file containing all HTML, PHP logic, and routing
- `api/`: REST API endpoints for Android application communication
  - `api/auth/`: Authentication endpoints (login, register, password reset)
  - `api/menu/`: Product and category management endpoints
  - `api/orders/`: Transaction and order management endpoints
  - `api/users/`: User management endpoints
  - `api/helpers.php`: Common API utility functions
- `dist/dashboard/pages/`: Individual page components (dashboard.php, menu.php, kategori.php, transaksi.php, user.php)
- `dist/assets/`: Compiled CSS, JavaScript, images, and fonts (production assets)
- `src/assets/`: Source SCSS files and development assets
- `src/html/`: Static HTML templates (reference/prototype files)
- `sql/`: Database schema and migration files
- `gulpfile.js`: Asset compilation configuration
- `setup_database.php`: Database initialization script
- `api_tester.html`: API testing tool

## Critical Developer Workflows

**Frontend Asset Development:**
```bash
npm install          # Install Gulp and build dependencies
gulp                 # Compile SCSS to CSS and watch for changes
gulp build-prod      # Production build with minification
```

**Page Development Pattern:**
1. Add new page route to both `$PageTitle` and `$allowed_pages` arrays in `index.php`
2. Create corresponding PHP file in `dist/dashboard/pages/`
3. Add navigation link with `href="index.php?page=pagename"` format
4. Include active menu logic: `<?php echo ($page === 'pagename') ? 'active' : ''; ?>`
5. Use consistent breadcrumb structure from existing pages
6. **ALWAYS use prepared statements** for any database operations in the page

**API Development Pattern:**
1. Create API endpoint files in `api/` directory with appropriate subdirectories
2. Implement proper HTTP method handling (GET, POST, PUT, DELETE)
3. Use JSON for all API responses with consistent structure
4. Include proper HTTP status codes (200, 201, 400, 401, 404, 500)
5. Implement JWT or session-based authentication for protected endpoints
6. Add input validation and sanitization for all API inputs
7. Use prepared statements for all database operations in API endpoints

## Code Conventions

**Routing & Security:**
- Always add new pages to `$allowed_pages` whitelist array for security
- Use `in_array($page, $allowed_pages)` pattern for route validation
- Page files must match exactly with route names (e.g., `menu` route → `menu.php` file)

**Database Security & Best Practices:**
- **ALWAYS use prepared statements** for ALL SQL queries to prevent SQL injection
- Use `mysqli_prepare()`, `mysqli_stmt_bind_param()`, and `mysqli_stmt_execute()` pattern
- Never concatenate user input directly into SQL strings
- Example pattern:
  ```php
  $query = "SELECT * FROM users WHERE email = ? AND status = ?";
  $stmt = mysqli_prepare($koneksi, $query);
  mysqli_stmt_bind_param($stmt, "ss", $email, $status);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  ```
- For INSERT operations: Use prepared statements with proper parameter binding
- For UPDATE operations: Always include WHERE clause with prepared statement
- For DELETE operations: Always use WHERE clause with prepared statement
- Validate and sanitize ALL user inputs before database operations
- Use `htmlspecialchars()` for output escaping to prevent XSS attacks

**Active Menu State Management:**
- Navigation items use PHP ternary operators for active state
- Pattern: `<li class="pc-item <?php echo ($page === 'menu') ? 'active' : ''; ?>">`
- Dashboard uses `$page === 'dashboard'` (not '/' anymore)
- All menu links follow `index.php?page=route` format for consistency

**Page Structure Patterns:**
- Start with breadcrumb section using consistent HTML structure
- Use Berry Bootstrap card components for content sections
- Statistics cards follow specific pattern with `avtar` icons and color classes
- Table views use `table-responsive` wrapper with `table-hover` class
- Dual view components (table/card) use Bootstrap Pills navigation with `tab-pane` content

**Asset References:**
- All production assets must reference `dist/assets/` path
- Primary stylesheet: `dist/assets/css/style.css`
- Theme presets: `dist/assets/css/style-preset.css`
- Icons: Multiple icon libraries (Tabler, Feather, FontAwesome, Material, Phosphor)
- Image handling: Use Unsplash URLs with proper sizing parameters

**Restaurant-Specific Features:**
- Menu items support dual view (table + card layouts) with tab switching
- Use food/beverage appropriate imagery from Unsplash
- Statistics cards display relevant metrics (menu count, transactions, revenue, customers)
- Color coding: Primary for food items, Info for beverages, Success for active status

## Modern UI Components & Styling

**Enhanced Form Design:**
- **Real-time Preview**: Live preview cards showing how data will appear
- **2-Column Layout**: Form fields on left (8 cols), preview on right (4 cols)
- **Card-based Sections**: Organized form sections (Basic Info, Additional Settings)
- **Sticky Preview**: Preview panel stays visible during scroll (desktop)
- **Responsive Design**: Mobile-friendly layout that adapts to screen size

**CSS Styling Standards:**
```css
/* Primary gradient for headers */
.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

/* Focus states for form elements */
.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Button hover effects */
.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Card hover animations */
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
}
```

**Interactive Elements:**
- **Live Updates**: Form changes reflect in preview immediately
- **Image Preview**: URL validation with fallback images
- **Price Formatting**: Auto-format Rupiah currency display
- **Status Indicators**: Visual badges for active/inactive states
- **Loading States**: Spinner animations during API calls
- **Hover Effects**: Smooth transitions and transforms

**Form Enhancement Patterns:**
```javascript
// Real-time preview updates
function updatePreview() {
    const name = document.getElementById('menuName').value;
    document.getElementById('previewName').textContent = name || 'Nama Menu';
    
    const price = document.getElementById('menuPrice').value;
    document.getElementById('previewPrice').textContent = 'Rp ' + formatPrice(price);
}

// Image preview with fallback
function updateImagePreview() {
    const imageUrl = document.getElementById('menuImage').value;
    const previewImage = document.getElementById('previewImage');
    
    if (imageUrl && isValidUrl(imageUrl)) {
        previewImage.src = imageUrl;
        previewImage.onerror = function() {
            this.src = 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=250&fit=crop';
        };
    }
}
```

## Database Schema

This application uses a MySQL database with the following schema structure. The schema is defined in DBML format and converted to MySQL for implementation.

### Core Tables

**users** - User management and authentication
```dbml
Table users {
  id varchar(100) [pk, not null]
  name varchar(70) [not null]
  email varchar(100) [not null, unique]
  username varchar(20) [not null, unique]
  password_hash varchar(100) [not null]
  phone_number varchar(15) [null]
  role_id varchar(100) [not null]
  is_active bool [default: `TRUE`, not null]
  created_at timestamp [default: `CURRENT_TIMESTAMP`, not null]
  updated_at timestamp [default: `CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP`, not null]
}
```

**roles** - User role definitions
```dbml
Table roles {
  id varchar(100) [pk, not null]
  name varchar(30) [not null, unique]
  created_at timestamp [default: `CURRENT_TIMESTAMP`, not null]
}
```

**password_resets** - Password reset token management
```dbml
Table password_resets {
  id varchar(100) [pk, not null]
  user_id varchar(100) [not null]
  otp_code char(6) [not null]
  expires_at timestamp [not null]
  used_at timestamp [null]
  created_at timestamp [default: `CURRENT_TIMESTAMP`, not null]
}
```

### Product Management Tables

**categories** - Product and topping categories
```dbml
Table categories {
  id varchar(100) [pk, not null]
  name varchar(30) [not null, unique]
  type enum('product', 'topping') [not null, default: 'product']
  is_active bool [default: `TRUE`, not null]
  created_at timestamp [default: `CURRENT_TIMESTAMP`, not null]
  updated_at timestamp [default: `CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP`, not null]
}
```

**products** - Main product catalog
```dbml
Table products {
  id varchar(100) [pk, not null]
  category_id varchar(100) [not null]
  name varchar(50) [not null]
  description text [null]
  image_url varchar(255) [null]
  price decimal(10, 2) [not null]
  is_topping bool [default: `FALSE`, not null]
  is_active bool [default: `TRUE`, not null]
  created_at timestamp [default: `CURRENT_TIMESTAMP`, not null]
  updated_at timestamp [default: `CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP`, not null]
}
```

### Product Variant System

**product_variant_groups** - Variant groupings (e.g., "Size", "Spice Level")
```dbml
Table product_variant_groups {
  id varchar(100) [pk, not null]
  product_id varchar(100) [not null]
  name varchar(50) [not null]
  is_required bool [default: `TRUE`, not null]
  allow_multiple bool [default: `FALSE`, not null]
  sort_order int [default: 0, not null]
  created_at timestamp [default: `CURRENT_TIMESTAMP`, not null]
  updated_at timestamp [default: `CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP`, not null]
}
```

**product_variant_options** - Individual variant options (e.g., "Large", "Medium", "Spicy")
```dbml
Table product_variant_options {
  id varchar(100) [pk, not null]
  variant_group_id varchar(100) [not null]
  name varchar(50) [not null]
  price_adjustment decimal(10, 2) [default: 0, not null]
  is_active bool [default: `TRUE`, not null]
  sort_order int [default: 0, not null]
  created_at timestamp [default: `CURRENT_TIMESTAMP`, not null]
  updated_at timestamp [default: `CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP`, not null]
}
```

**product_toppings** - Many-to-many relationship for available toppings per product
```dbml
Table product_toppings {
  id varchar(100) [pk, not null]
  product_id varchar(100) [not null]
  topping_id varchar(100) [not null]
  created_at timestamp [default: `CURRENT_TIMESTAMP`, not null]
}
```

### Database Relationships

**Foreign Key Constraints:**
- `users.role_id` → `roles.id` (restrict delete)
- `password_resets.user_id` → `users.id` (cascade delete)
- `products.category_id` → `categories.id` (restrict delete)
- `product_variant_groups.product_id` → `products.id` (cascade delete)
- `product_variant_options.variant_group_id` → `product_variant_groups.id` (cascade delete)
- `product_toppings.product_id` → `products.id` (cascade delete)
- `product_toppings.topping_id` → `products.id` (cascade delete)

### Key Indexes for Performance

**User Management:**
- `idx_users_email`, `idx_users_username`, `idx_users_role`, `idx_users_active`
- `idx_password_resets_user`, `idx_password_resets_otp`, `idx_password_resets_expires`

**Product Catalog:**
- `idx_categories_type_active`
- `idx_products_category_active`, `idx_products_topping_active`, `idx_products_name`
- `idx_variant_groups_product_sort`
- `idx_variant_options_group_active_sort`
- `idx_product_toppings_product_topping` (unique), `idx_product_toppings_topping`

### Database Development Guidelines

**Query Patterns:**
- Always use prepared statements for security
- Filter by `is_active = TRUE` for active records
- Use proper JOIN operations for related data
- Implement pagination for large result sets

**Schema Evolution:**
- This schema will be updated as features are added
- Use migrations for schema changes
- Maintain backward compatibility when possible
- Document all schema changes in this file

### Database Setup

**MySQL Schema Implementation:**

The following MySQL schema creates all the required tables with proper relationships, indexes, and constraints based on the DBML specification above.

```sql
-- Create database tables based on the provided DBML schema
-- Note: This script assumes a MySQL database. Booleans are implemented as TINYINT(1) for compatibility.
-- Foreign keys are defined with the specified ON DELETE actions.
-- Indexes are created as specified, including unique constraints where applicable.

-- Table: roles
CREATE TABLE roles (
    id VARCHAR(100) NOT NULL PRIMARY KEY,
    name VARCHAR(30) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Table: users
CREATE TABLE users (
    id VARCHAR(100) NOT NULL PRIMARY KEY,
    name VARCHAR(70) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(20) NOT NULL UNIQUE,
    password_hash VARCHAR(100) NOT NULL,
    phone_number VARCHAR(15) NULL,
    role_id VARCHAR(100) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_users_email (email),
    KEY idx_users_username (username),
    KEY idx_users_role (role_id),
    KEY idx_users_active (is_active),
    CONSTRAINT fk_users_role_id FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
);

-- Table: password_resets
CREATE TABLE password_resets (
    id VARCHAR(100) NOT NULL PRIMARY KEY,
    user_id VARCHAR(100) NOT NULL,
    otp_code CHAR(6) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_password_resets_user (user_id),
    KEY idx_password_resets_otp (otp_code),
    KEY idx_password_resets_expires (expires_at),
    CONSTRAINT fk_password_resets_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table: categories
CREATE TABLE categories (
    id VARCHAR(100) NOT NULL PRIMARY KEY,
    name VARCHAR(30) NOT NULL UNIQUE,
    type ENUM('product', 'topping') NOT NULL DEFAULT 'product',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_categories_type_active (type, is_active)
);

-- Table: products
CREATE TABLE products (
    id VARCHAR(100) NOT NULL PRIMARY KEY,
    category_id VARCHAR(100) NOT NULL,
    name VARCHAR(50) NOT NULL,
    description TEXT NULL,
    image_url VARCHAR(255) NULL,
    price DECIMAL(10, 2) NOT NULL,
    is_topping TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_products_category_active (category_id, is_active),
    KEY idx_products_topping_active (is_topping, is_active),
    KEY idx_products_name (name),
    CONSTRAINT fk_products_category_id FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

-- Table: product_variant_groups
CREATE TABLE product_variant_groups (
    id VARCHAR(100) NOT NULL PRIMARY KEY,
    product_id VARCHAR(100) NOT NULL,
    name VARCHAR(50) NOT NULL,
    is_required TINYINT(1) NOT NULL DEFAULT 1,
    allow_multiple TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_variant_groups_product_sort (product_id, sort_order),
    CONSTRAINT fk_product_variant_groups_product_id FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Table: product_variant_options
CREATE TABLE product_variant_options (
    id VARCHAR(100) NOT NULL PRIMARY KEY,
    variant_group_id VARCHAR(100) NOT NULL,
    name VARCHAR(50) NOT NULL,
    price_adjustment DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_variant_options_group_active_sort (variant_group_id, is_active, sort_order),
    CONSTRAINT fk_product_variant_options_variant_group_id FOREIGN KEY (variant_group_id) REFERENCES product_variant_groups(id) ON DELETE CASCADE
);

-- Table: product_toppings
CREATE TABLE product_toppings (
    id VARCHAR(100) NOT NULL PRIMARY KEY,
    product_id VARCHAR(100) NOT NULL,
    topping_id VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY idx_product_toppings_product_topping (product_id, topping_id),
    KEY idx_product_toppings_topping (topping_id),
    CONSTRAINT fk_product_toppings_product_id FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_product_toppings_topping_id FOREIGN KEY (topping_id) REFERENCES products(id) ON DELETE CASCADE
);
```

**Database Setup Instructions:**

1. **Create Database:**
   ```sql
   CREATE DATABASE seblak_predator CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   USE seblak_predator;
   ```

2. **Run Schema Script:**
   - Execute the SQL schema above to create all tables
   - Alternatively, use the `setup_database.php` script if available

3. **Insert Default Data:**
   ```sql
   -- Insert default roles
   INSERT INTO roles (id, name) VALUES 
   ('role_admin', 'Administrator'),
   ('role_staff', 'Staff'),
   ('role_customer', 'Customer');
   
   -- Insert default categories
   INSERT INTO categories (id, name, type) VALUES 
   ('cat_makanan', 'Makanan', 'product'),
   ('cat_minuman', 'Minuman', 'product'),
   ('cat_topping', 'Topping', 'topping');
   ```

4. **Verify Installation:**
   ```sql
   -- Check table creation
   SHOW TABLES;
   
   -- Verify foreign key constraints
   SELECT TABLE_NAME, CONSTRAINT_NAME, CONSTRAINT_TYPE 
   FROM information_schema.TABLE_CONSTRAINTS 
   WHERE TABLE_SCHEMA = 'seblak_predator';
   ```

## REST API Architecture

This application provides REST API endpoints for Android application synchronization. The API follows RESTful principles and uses JSON for data exchange.

### API Structure and Endpoints

**Base URL Structure:**
```
/api/{module}/{action}.php
```

**Core API Modules:**

### Authentication Module (`/api/auth/`)
```php
POST /api/auth/login.php
- Purpose: User authentication
- Request: {"username": "string", "password": "string"}
- Response: {"success": bool, "token": "jwt_token", "user": {...}}

POST /api/auth/register.php
- Purpose: User registration  
- Request: {"name": "string", "email": "string", "username": "string", "password": "string"}
- Response: {"success": bool, "message": "string", "user_id": "string"}

POST /api/auth/forgot-password.php
- Purpose: Password reset request
- Request: {"email": "string"}
- Response: {"success": bool, "message": "string", "otp_sent": bool}

POST /api/auth/reset-password.php
- Purpose: Password reset with OTP
- Request: {"email": "string", "otp_code": "string", "new_password": "string"}
- Response: {"success": bool, "message": "string"}
```

### Menu Management Module (`/api/menu/`)
```php
GET /api/menu/categories.php
- Purpose: Get all active categories
- Response: {"success": bool, "data": [...], "total": int}

GET /api/menu/products.php?category_id={id}
- Purpose: Get products by category
- Response: {"success": bool, "data": [...], "total": int}

POST /api/menu/products.php
- Purpose: Create new product
- Request: {"name": "string", "category_id": "string", "price": decimal, ...}
- Response: {"success": bool, "product_id": "string", "message": "string"}

PUT /api/menu/products.php?id={product_id}
- Purpose: Update existing product
- Request: {"name": "string", "price": decimal, ...}
- Response: {"success": bool, "message": "string"}

DELETE /api/menu/products.php?id={product_id}
- Purpose: Soft delete product (set is_active = false)
- Response: {"success": bool, "message": "string"}
```

### API Response Standards

**Consistent Response Format:**
```json
{
  "success": true|false,
  "message": "Human readable message",
  "data": {...}|[...], 
  "meta": {
    "total": 0,
    "page": 1,
    "per_page": 20,
    "last_page": 1
  },
  "errors": {...} // Only on validation errors
}
```

**HTTP Status Codes:**
- `200 OK`: Successful GET, PUT requests
- `201 Created`: Successful POST requests
- `400 Bad Request`: Validation errors, malformed JSON
- `401 Unauthorized`: Authentication required or failed
- `403 Forbidden`: User lacks permission
- `404 Not Found`: Resource not found
- `422 Unprocessable Entity`: Business logic validation errors
- `500 Internal Server Error`: Server-side errors

### API Security & Authentication

**Authentication Methods:**
- Session-based authentication for web dashboard
- JWT tokens for Android application
- API key validation for external integrations

**Security Best Practices:**
```php
// Example API endpoint structure
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Input validation
$input = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
}

// Authentication check
$token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!validateToken($token)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Use prepared statements for all database operations
$stmt = mysqli_prepare($koneksi, "SELECT * FROM products WHERE id = ? AND is_active = TRUE");
mysqli_stmt_bind_param($stmt, "s", $product_id);
mysqli_stmt_execute($stmt);
?>
```

### Data Synchronization Patterns

**Mobile-Web Sync Strategy:**
- **Real-time Updates**: Web dashboard changes immediately available via API
- **Offline Support**: Android app caches data locally, syncs when online
- **Conflict Resolution**: Last-write-wins with timestamp comparison
- **Incremental Sync**: Use `updated_at` timestamps for delta synchronization

**Sync Endpoints:**
```php
GET /api/sync/products.php?since={timestamp}
- Purpose: Get products updated since timestamp
- Response: {"success": bool, "data": [...], "last_sync": "timestamp"}

GET /api/sync/categories.php?since={timestamp}
- Purpose: Get categories updated since timestamp
- Response: {"success": bool, "data": [...], "last_sync": "timestamp"}
```

### API Development Guidelines

**File Organization:**
- Group endpoints by functional modules
- Use descriptive filenames matching actions
- Include proper error handling in all endpoints
- Implement consistent logging for debugging

**Testing & Documentation:**
- Test all endpoints with Android emulator
- Document expected request/response formats
- Include example cURL commands for testing
- Validate JSON schema for complex requests

**Performance Optimization:**
- Implement proper database indexing
- Use pagination for large data sets
- Cache frequently accessed data
- Minimize database queries per request
