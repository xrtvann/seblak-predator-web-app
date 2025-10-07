# 🚀 Quick Setup Guide - Seblak Predator API Testing

## 📋 Files Created

1. **`POSTMAN_API_DOCUMENTATION.md`** - Complete API documentation
2. **`Seblak_Predator_API.postman_collection.json`** - Postman collection file
3. **`Seblak_Predator_Environment.postman_environment.json`** - Postman environment file

## 🔧 Setup Instructions

### 1. Import into Postman

#### Import Collection:
1. Open Postman
2. Click **Import** button
3. Select **Upload Files** 
4. Choose `Seblak_Predator_API.postman_collection.json`
5. Click **Import**

#### Import Environment:
1. Click **Import** button again
2. Select **Upload Files**
3. Choose `Seblak_Predator_Environment.postman_environment.json`  
4. Click **Import**

#### Activate Environment:
1. In top-right corner, select **"Seblak Predator Environment"** from dropdown
2. Verify `baseUrl` is set to: `http://localhost/seblak-predator/api`

### 2. Update Base URL (if needed)

If your local server runs on different port/path:
1. Go to **Environments** tab
2. Select **"Seblak Predator Environment"**
3. Update `baseUrl` value (e.g., `http://localhost:8000/seblak-predator/api`)
4. Save changes

### 3. Start Testing

#### Basic Test Flow:
1. **Test Connection**: Run `Get All Categories`
2. **Create Category**: Run `Create Category` 
3. **Copy Category ID**: From response, copy the `id` field
4. **Set Variable**: In environment, paste ID into `categoryId` variable
5. **Create Product**: Run `Create Product` (uses `{{categoryId}}` variable)
6. **Copy Product ID**: From response, copy the `id` field  
7. **Set Variable**: In environment, paste ID into `productId` variable
8. **Test CRUD**: Now test Update/Delete operations

## 🧪 Test Scenarios Available

### ✅ Happy Path Tests
- **Categories**: Create → Read → Update → Delete
- **Products**: Create → Read → Update → Delete  
- **Filtering**: By category, topping status, pagination
- **Sync**: Incremental synchronization with timestamps

### ❌ Error Tests
- **Invalid JSON**: Malformed request bodies
- **Missing Fields**: Required field validation
- **Invalid Methods**: Unsupported HTTP methods
- **Non-existent Resources**: 404 error handling

### 🔍 Advanced Tests
- **Pagination**: Test with `page` and `per_page` parameters
- **Filtering**: Multiple filter combinations
- **Validation**: Edge cases (empty strings, negative values)
- **Performance**: Response time validation (< 2000ms)

## 📊 Collection Features

### Global Tests (Applied to All Requests):
- ✅ Response time validation (< 2000ms)
- ✅ Required `success` field check
- ✅ Required `message` field check

### Variables:
- `{{baseUrl}}` - API base URL
- `{{categoryId}}` - For category-specific operations
- `{{productId}}` - For product-specific operations

### Pre-configured Requests:
- **16 endpoints** with proper headers and sample data
- **Response examples** for successful requests
- **Error scenarios** for validation testing

## 🔗 API Endpoints Summary

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/menu/categories.php` | Get all categories |
| POST | `/menu/categories.php` | Create category |
| PUT | `/menu/categories.php?id={id}` | Update category |
| DELETE | `/menu/categories.php?id={id}` | Delete category |
| GET | `/menu/products.php` | Get all products |
| GET | `/menu/products.php?id={id}` | Get product by ID |
| POST | `/menu/products.php` | Create product |
| PUT | `/menu/products.php?id={id}` | Update product |
| DELETE | `/menu/products.php?id={id}` | Delete product |
| GET | `/sync/categories.php` | Sync categories |
| GET | `/sync/products.php` | Sync products |

## 🎯 Tips for Testing

### 1. Sequential Testing:
- Always create categories before products
- Use returned IDs for subsequent operations
- Test in order: Create → Read → Update → Delete

### 2. Variable Management:
- Copy IDs from responses into environment variables
- Use `{{variableName}}` syntax in requests
- Update variables as you create new resources

### 3. Response Validation:
- Check `success` field for operation status
- Verify `data` structure matches documentation
- Validate error messages for failed requests

### 4. Performance Testing:
- Global tests automatically check response times
- All requests should complete within 2 seconds
- Monitor for consistent performance

## 🚨 Troubleshooting

### Common Issues:

**Connection Refused:**
- ✅ Check if XAMPP/Laragon is running
- ✅ Verify database connection in `config/koneksi.php`
- ✅ Ensure correct `baseUrl` in environment

**404 Not Found:**
- ✅ Verify file paths exist: `/api/menu/categories.php`
- ✅ Check Apache/nginx configuration
- ✅ Confirm mod_rewrite is enabled

**500 Internal Server Error:**
- ✅ Check PHP error logs
- ✅ Verify database connection
- ✅ Ensure required database tables exist

**Validation Errors:**
- ✅ Check required fields in request body
- ✅ Verify JSON format is valid
- ✅ Ensure data types match requirements

## 📚 Additional Resources

- **Full Documentation**: Read `POSTMAN_API_DOCUMENTATION.md`
- **Database Schema**: Check `/sql/` directory for table structures  
- **Source Code**: Review `/api/` directory for implementation details

Happy Testing! 🎉