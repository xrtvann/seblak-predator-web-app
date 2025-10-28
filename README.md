# Seblak Predator API Documentation

## Overview

Seblak Predator is a comprehensive restaurant management system API built with PHP and MySQL. The system provides complete functionality for managing restaurant operations including menu management, order processing, expense tracking, user authentication, and payment integration.

### Base URL
```
http://localhost/seblak-predator/api
```

### Features
- **Menu Management**: Categories and products with toppings support
- **Order Management**: Complete order lifecycle with transaction processing
- **Authentication**: JWT-based authentication system
- **Expense Tracking**: Financial management and reporting
- **Payment Integration**: Midtrans payment gateway integration
- **Synchronization**: Mobile app sync capabilities
- **User Management**: Role-based access control

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
