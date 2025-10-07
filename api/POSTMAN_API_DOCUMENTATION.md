# Seblak Predator API Documentation
## Restaurant Management System API

### Base URL
```
http://localhost/seblak-predator/api
```

### Response Format
All API responses follow this standard format:
```json
{
  "success": true|false,
  "message": "Response message",
  "data": {}, // Only present on successful requests with data
  "meta": {}, // Only present when pagination or additional info is included
  "total": 0  // Total count for list endpoints
}
```

### HTTP Status Codes
- `200 OK` - Successful GET, PUT requests
- `201 Created` - Successful POST requests  
- `400 Bad Request` - Invalid request data
- `404 Not Found` - Resource not found
- `405 Method Not Allowed` - HTTP method not supported
- `500 Internal Server Error` - Server error

---

## 🏷️ Categories API

### Get All Categories
**GET** `/menu/categories.php`

**Description:** Retrieve all active categories

**Headers:**
```
Content-Type: application/json
```

**Response Example:**
```json
{
  "success": true,
  "data": [
    {
      "id": "cat_product_1",
      "name": "Makanan",
      "type": "product",
      "is_active": true,
      "created_at": "2024-01-01 12:00:00",
      "updated_at": "2024-01-01 12:00:00"
    },
    {
      "id": "cat_topping_1", 
      "name": "Topping",
      "type": "topping",
      "is_active": true,
      "created_at": "2024-01-01 12:00:00",
      "updated_at": "2024-01-01 12:00:00"
    }
  ],
  "total": 2,
  "message": "Categories retrieved successfully"
}
```

### Create Category
**POST** `/menu/categories.php`

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "Minuman",
  "type": "product"
}
```

**Field Validation:**
- `name` (required): Category name (string, max 30 chars)
- `type` (optional): Either "product" or "topping" (default: "product")

**Response Example:**
```json
{
  "success": true,
  "message": "Category created successfully",
  "data": {
    "id": "cat_67890abc",
    "name": "Minuman",
    "type": "product"
  }
}
```

### Update Category
**PUT** `/menu/categories.php?id={category_id}`

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "Minuman Updated",
  "type": "product"
}
```

**Response Example:**
```json
{
  "success": true,
  "message": "Category updated successfully"
}
```

### Delete Category (Soft Delete)
**DELETE** `/menu/categories.php?id={category_id}`

**Headers:**
```
Content-Type: application/json
```

**Response Example:**
```json
{
  "success": true,
  "message": "Category deleted successfully"
}
```

---

## 🍽️ Products API

### Get All Products
**GET** `/menu/products.php`

**Description:** Retrieve all active products with optional filtering

**Headers:**
```
Content-Type: application/json
```

**Query Parameters:**
- `category_id` (optional): Filter by category ID
- `is_topping` (optional): Filter by topping status ("true" | "false")
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Items per page (default: 20, max: 1000)

**Example URLs:**
```
GET /menu/products.php
GET /menu/products.php?category_id=cat_product_1
GET /menu/products.php?is_topping=false
GET /menu/products.php?page=1&per_page=10
GET /menu/products.php?category_id=cat_product_1&page=2&per_page=5
```

**Response Example:**
```json
{
  "success": true,
  "data": [
    {
      "id": "prod_12345abc",
      "category_id": "cat_product_1",
      "name": "Seblak Pedas",
      "description": "Seblak dengan level kepedasan tinggi",
      "image_url": "https://example.com/seblak.jpg",
      "price": "15000.00",
      "is_topping": false,
      "is_active": true,
      "created_at": "2024-01-01 12:00:00",
      "updated_at": "2024-01-01 12:00:00",
      "category_name": "Makanan",
      "category_type": "product"
    }
  ],
  "total": 1,
  "meta": {
    "page": 1,
    "per_page": 20,
    "last_page": 1,
    "total": 1
  },
  "message": "Products retrieved successfully"
}
```

### Get Product by ID
**GET** `/menu/products.php?id={product_id}`

**Headers:**
```
Content-Type: application/json
```

**Response Example:**
```json
{
  "success": true,
  "data": {
    "id": "prod_12345abc",
    "category_id": "cat_product_1",
    "name": "Seblak Pedas",
    "description": "Seblak dengan level kepedasan tinggi",
    "image_url": "https://example.com/seblak.jpg",
    "price": "15000.00",
    "is_topping": false,
    "is_active": true,
    "created_at": "2024-01-01 12:00:00",
    "updated_at": "2024-01-01 12:00:00",
    "category_name": "Makanan",
    "category_type": "product"
  },
  "message": "Product retrieved successfully"
}
```

### Create Product
**POST** `/menu/products.php`

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "Seblak Original",
  "description": "Seblak dengan bumbu original",
  "category_id": "cat_product_1",
  "price": 12000,
  "image_url": "https://example.com/seblak-original.jpg",
  "is_topping": false
}
```

**Field Validation:**
- `name` (required): Product name (string, max 50 chars)
- `category_id` (required): Valid category ID
- `price` (required): Product price (number, min 0)
- `description` (optional): Product description (text)
- `image_url` (optional): Product image URL (valid URL format)
- `is_topping` (optional): Whether item is a topping (boolean, default: false)

**Response Example:**
```json
{
  "success": true,
  "message": "Product created successfully",
  "data": {
    "id": "prod_67890def",
    "name": "Seblak Original",
    "category_id": "cat_product_1",
    "price": "12000.00"
  }
}
```

### Update Product
**PUT** `/menu/products.php?id={product_id}`

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "Seblak Original Updated",
  "description": "Seblak dengan bumbu original yang diperbarui",
  "category_id": "cat_product_1", 
  "price": 13000,
  "image_url": "https://example.com/seblak-updated.jpg",
  "is_topping": false
}
```

**Response Example:**
```json
{
  "success": true,
  "message": "Product updated successfully"
}
```

### Delete Product (Soft Delete)
**DELETE** `/menu/products.php?id={product_id}`

**Headers:**
```
Content-Type: application/json
```

**Response Example:**
```json
{
  "success": true,
  "message": "Product deleted successfully"
}
```

---

## 🔄 Sync API (Mobile App Support)

### Get Categories for Sync
**GET** `/sync/categories.php`

**Description:** Get categories for mobile app synchronization

**Headers:**
```
Content-Type: application/json
```

**Query Parameters:**
- `since` (optional): Timestamp for incremental sync (YYYY-MM-DD HH:MM:SS)

**Example URLs:**
```
GET /sync/categories.php
GET /sync/categories.php?since=2024-01-01 12:00:00
```

**Response Example:**
```json
{
  "success": true,
  "data": [
    {
      "id": "cat_product_1",
      "name": "Makanan",
      "type": "product",
      "is_active": true,
      "created_at": "2024-01-01 12:00:00",
      "updated_at": "2024-01-01 12:00:00"
    }
  ],
  "last_sync": "2024-01-01 13:00:00",
  "message": "Categories synchronized successfully"
}
```

### Get Products for Sync
**GET** `/sync/products.php`

**Description:** Get products for mobile app synchronization

**Headers:**
```
Content-Type: application/json
```

**Query Parameters:**
- `since` (optional): Timestamp for incremental sync (YYYY-MM-DD HH:MM:SS)

**Example URLs:**
```
GET /sync/products.php
GET /sync/products.php?since=2024-01-01 12:00:00
```

**Response Example:**
```json
{
  "success": true,
  "data": [
    {
      "id": "prod_12345abc",
      "category_id": "cat_product_1",
      "name": "Seblak Pedas",
      "description": "Seblak dengan level kepedasan tinggi",
      "image_url": "https://example.com/seblak.jpg",
      "price": "15000.00",
      "is_topping": false,
      "is_active": true,
      "created_at": "2024-01-01 12:00:00",
      "updated_at": "2024-01-01 12:00:00",
      "category_name": "Makanan",
      "category_type": "product"
    }
  ],
  "last_sync": "2024-01-01 13:00:00",
  "message": "Products synchronized successfully"
}
```

---

## 🧪 Testing Scenarios

### Test Data Creation Flow
1. **Create Categories First:**
   ```
   POST /menu/categories.php
   Body: {"name": "Makanan", "type": "product"}
   ```

2. **Create Products:**
   ```
   POST /menu/products.php  
   Body: {
     "name": "Seblak Pedas",
     "category_id": "cat_12345",
     "price": 15000,
     "description": "Seblak level pedas tinggi"
   }
   ```

3. **Test Filtering:**
   ```
   GET /menu/products.php?category_id=cat_12345
   GET /menu/products.php?is_topping=false
   ```

4. **Test Pagination:**
   ```
   GET /menu/products.php?page=1&per_page=5
   ```

### Error Testing
- **Invalid JSON:** Send malformed JSON
- **Missing Required Fields:** Omit required fields
- **Invalid Category ID:** Use non-existent category_id
- **Invalid Product ID:** Use non-existent product_id for GET/PUT/DELETE
- **Invalid Method:** Use unsupported HTTP methods

### Validation Testing
- **Empty Names:** Send empty strings for name fields
- **Negative Prices:** Send negative values for price
- **Invalid URLs:** Send malformed URLs for image_url
- **Invalid Type:** Send invalid category type (not "product" or "topping")

---

## 📝 Notes

### Database Schema
- Categories: `id`, `name`, `type`, `is_active`, `created_at`, `updated_at`
- Products: `id`, `category_id`, `name`, `description`, `image_url`, `price`, `is_topping`, `is_active`, `created_at`, `updated_at`

### Security
- All APIs use prepared statements to prevent SQL injection
- Input validation and sanitization implemented
- CORS headers configured for cross-origin requests

### Performance
- Pagination implemented for large datasets
- Proper indexing on database tables
- Efficient query optimization with JOIN operations

### Mobile App Integration
- Sync endpoints support incremental synchronization
- Timestamp-based change tracking
- Optimized for offline-first mobile applications