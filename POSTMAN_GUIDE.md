# Seblak Predator - Postman API Testing Guide

## 📦 Files Created

1. **Seblak_Predator_Complete.postman_collection.json** - Complete API collection
2. **Seblak_Predator.postman_environment.json** - Local environment variables
3. **Seblak_Predator_Production.postman_environment.json** - Production environment variables

## 🚀 How to Import

### Import Collection
1. Open Postman
2. Click **Import** button (top left)
3. Select `Seblak_Predator_Complete.postman_collection.json`
4. Click **Import**

### Import Environment
1. Click **Environments** tab (left sidebar)
2. Click **Import** button
3. Select environment files:
   - `Seblak_Predator.postman_environment.json` (for local testing)
   - `Seblak_Predator_Production.postman_environment.json` (for production)
4. Click **Import**

### Select Environment
- Click environment dropdown (top right)
- Select **Seblak Predator - Local Environment** for local testing
- Or select **Seblak Predator - Production** for production testing

## 📋 API Endpoints Overview

### 1. Authentication (6 endpoints)
- ✅ Register - Create new user account
- ✅ Login - Login and get JWT tokens (auto-saves tokens to environment)
- ✅ Get Profile - View user profile (requires auth)
- ✅ Update Profile - Update user information (requires auth)
- ✅ Refresh Token - Renew access token
- ✅ Logout - End session (requires auth)

### 2. Transactions (6 endpoints)
- ✅ Get All Transactions - List with filters (status, date, pagination)
- ✅ Get Transaction by ID - View detailed transaction
- ✅ Create Transaction - Dine-in order with toppings & customizations
- ✅ Create Transaction - Take Away - Scheduled pickup order
- ✅ Update Transaction Status - Change order & payment status
- ✅ Cancel Transaction - Cancel pending order

### 3. Menu Management (15 endpoints)

#### Categories (5 endpoints)
- ✅ Get All Categories
- ✅ Get Category by ID
- ✅ Create Category
- ✅ Update Category
- ✅ Delete Category

#### Products (5 endpoints)
- ✅ Get All Products
- ✅ Get Product by ID
- ✅ Create Product
- ✅ Update Product
- ✅ Delete Product

#### Toppings (5 endpoints)
- ✅ Get All Toppings
- ✅ Get Topping by ID
- ✅ Create Topping
- ✅ Update Topping
- ✅ Delete Topping

### 4. Spice Levels & Customization (7 endpoints)
- ✅ Get All Spice Levels
- ✅ Get Spice Level by ID
- ✅ Create Spice Level
- ✅ Update Spice Level
- ✅ Delete Spice Level
- ✅ Get All Customization Options
- ✅ Create Customization Option

### 5. Financial Reports (5 endpoints)
- ✅ Get Financial Report - Today
- ✅ Get Financial Report - This Week
- ✅ Get Financial Report - This Month
- ✅ Get Financial Report - Custom Date Range
- ✅ Export Financial Report - PDF

### 6. Expenses (7 endpoints)
- ✅ Get All Expenses
- ✅ Get Expense by ID
- ✅ Create Expense
- ✅ Update Expense
- ✅ Delete Expense
- ✅ Get All Expense Categories
- ✅ Create Expense Category

### 7. Dashboard (1 endpoint)
- ✅ Get Dashboard Stats - Overview statistics

### 8. Users & Roles (6 endpoints)
- ✅ Get All Users (requires auth)
- ✅ Get User by ID (requires auth)
- ✅ Create User (requires auth)
- ✅ Update User (requires auth)
- ✅ Delete User (requires auth)
- ✅ Get All Roles (requires auth)

### 9. Midtrans Payment (2 endpoints)
- ✅ Get Snap Token - Get payment token for Midtrans
- ✅ Midtrans Notification Webhook - Receive payment status

### 10. Mobile API (1 endpoint)
- ✅ Create Delivery Order - Mobile app delivery orders

### 11. Account Settings (2 endpoints)
- ✅ Get Account Settings (requires auth)
- ✅ Update Account Settings (requires auth)

**Total: 58 API Endpoints**

## 🔐 Authentication Flow

### Step 1: Login
```bash
POST {{base_url}}/api/auth/login.php
{
    "username": "admin",
    "password": "admin123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
        "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
        "user": {
            "id": "1",
            "username": "admin",
            "full_name": "Administrator"
        }
    }
}
```

✅ **Tokens are automatically saved to environment variables!**

### Step 2: Use Protected Endpoints
The collection automatically uses `{{access_token}}` from environment for protected endpoints.

### Step 3: Refresh Token (when access token expires)
```bash
POST {{base_url}}/api/auth/refresh.php
{
    "refresh_token": "{{refresh_token}}"
}
```

## 🧪 Testing Workflow

### 1. Basic Flow
```
Login → Get Profile → Browse Menu → Create Transaction
```

### 2. Transaction Flow
```
1. Get Spice Levels
2. Get Customization Options
3. Get Toppings
4. Create Transaction (with items, levels, customizations, toppings)
5. Get Transaction by ID
6. Update Transaction Status (to completed)
```

### 3. Payment Flow
```
1. Create Transaction (payment_method: "midtrans")
2. Get Snap Token
3. Simulate Payment (use Midtrans sandbox)
4. Webhook receives notification
5. Transaction status updated automatically
```

## 🔧 Environment Variables

### Local Environment
- `base_url`: `http://localhost/seblak-predator`
- `access_token`: Auto-filled after login
- `refresh_token`: Auto-filled after login
- `user_id`: Current user ID
- `midtrans_client_key`: Sandbox client key
- `midtrans_server_key`: Sandbox server key

### Production Environment
- `base_url`: `https://seblak-predator.infinityfree.me`
- Same token variables as local

## 📝 Sample Transaction Request

### Dine-in Order with Everything
```json
{
    "customer_name": "John Doe",
    "order_type": "dine_in",
    "table_number": "5",
    "notes": "Extra pedas",
    "payment_method": "cash",
    "items": [
        {
            "quantity": 2,
            "spice_level": "1",
            "customizations": {
                "kencur_level": "1",
                "broth_flavor": "2",
                "egg_type": "3"
            },
            "toppings": [
                {
                    "topping_id": "1",
                    "topping_name": "Bakso",
                    "quantity": 2,
                    "unit_price": 5000
                },
                {
                    "topping_id": "2",
                    "topping_name": "Keju",
                    "quantity": 1,
                    "unit_price": 8000
                }
            ]
        }
    ]
}
```

### Take Away Order
```json
{
    "customer_name": "Jane Doe",
    "order_type": "take_away",
    "pickup_time": "2025-12-05 18:00:00",
    "notes": "Bungkus terpisah",
    "payment_method": "midtrans",
    "items": [
        {
            "quantity": 1,
            "spice_level": "2",
            "customizations": {
                "kencur_level": "2"
            },
            "toppings": [
                {
                    "topping_id": "3",
                    "topping_name": "Sosis",
                    "quantity": 2,
                    "unit_price": 6000
                }
            ]
        }
    ]
}
```

### Delivery Order (Mobile API)
```json
{
    "user_id": "1",
    "delivery_address": "Jl. Merdeka No. 123, Jakarta",
    "notes": "Mohon bungkus rapi",
    "payment_method": "midtrans",
    "items": [
        {
            "quantity": 1,
            "spice_level": "2",
            "customizations": {
                "kencur_level": "1"
            },
            "toppings": [
                {
                    "topping_id": "1",
                    "topping_name": "Bakso",
                    "quantity": 2,
                    "unit_price": 5000
                }
            ]
        }
    ]
}
```

## 🎯 Query Parameters Guide

### Transactions
```
GET /api/transactions.php?status=pending&date=2025-12-05&page=1&limit=10

Parameters:
- status: pending|processing|completed|cancelled
- date: YYYY-MM-DD
- page: Page number (default: 1)
- limit: Items per page (default: 10)
```

### Financial Reports
```
GET /api/financial-report.php?period=custom&start_date=2025-12-01&end_date=2025-12-05

Parameters:
- period: today|week|month|custom
- start_date: YYYY-MM-DD (for custom period)
- end_date: YYYY-MM-DD (for custom period)
```

### Expense Categories
```
GET /api/expense-categories.php?is_active=true

Parameters:
- is_active: true|false (filter by active status)
```

## 🐛 Troubleshooting

### 401 Unauthorized
- **Problem**: Access token expired
- **Solution**: Use "Refresh Token" endpoint or login again

### 404 Not Found
- **Problem**: Incorrect base_url
- **Solution**: Check environment variable `base_url` matches your setup
  - Local: `http://localhost/seblak-predator`
  - Production: `https://seblak-predator.infinityfree.me`

### 500 Internal Server Error
- **Problem**: Database connection or server error
- **Solution**: Check database configuration in `config/database.php`

### CORS Error
- **Problem**: Cross-origin request blocked
- **Solution**: API already has CORS headers, check browser console

## 💡 Tips

1. **Auto-save tokens**: Login request automatically saves tokens to environment
2. **Bearer auth**: Protected endpoints use Bearer token from environment
3. **Test sequences**: Use Postman's Collection Runner for automated testing
4. **Variables**: Use `{{base_url}}`, `{{access_token}}` in all requests
5. **Environments**: Switch between Local and Production easily

## 📊 Expected Responses

### Success Response
```json
{
    "success": true,
    "message": "Operation successful",
    "data": { ... }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error description",
    "error": "Detailed error message"
}
```

### Paginated Response
```json
{
    "success": true,
    "data": [ ... ],
    "pagination": {
        "current_page": 1,
        "total_pages": 5,
        "total_items": 50,
        "items_per_page": 10
    }
}
```

## 🎉 Ready to Test!

Import the collection and environment files to Postman and start testing all 58 endpoints!

For questions or issues, check the API documentation or contact the development team.
