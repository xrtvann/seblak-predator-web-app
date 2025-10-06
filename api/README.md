# Seblak Predator API Testing Examples

## Base URL
```
http://localhost/seblak-predator/api
```

## Categories API

### 1. Get All Categories
```bash
curl -X GET "http://localhost/seblak-predator/api/menu/categories.php"
```

### 2. Create New Category
```bash
curl -X POST "http://localhost/seblak-predator/api/menu/categories.php" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Makanan Utama",
    "type": "product"
  }'
```

### 3. Update Category
```bash
curl -X PUT "http://localhost/seblak-predator/api/menu/categories.php?id=cat_123" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Makanan Pembuka"
  }'
```

### 4. Delete Category
```bash
curl -X DELETE "http://localhost/seblak-predator/api/menu/categories.php?id=cat_123"
```

## Products API

### 1. Get All Products
```bash
curl -X GET "http://localhost/seblak-predator/api/menu/products.php"
```

### 2. Get Products by Category
```bash
curl -X GET "http://localhost/seblak-predator/api/menu/products.php?category_id=cat_123"
```

### 3. Get Products with Pagination
```bash
curl -X GET "http://localhost/seblak-predator/api/menu/products.php?page=1&per_page=10"
```

### 4. Get Single Product
```bash
curl -X GET "http://localhost/seblak-predator/api/menu/products.php?id=prod_123"
```

### 5. Create New Product
```bash
curl -X POST "http://localhost/seblak-predator/api/menu/products.php" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Seblak Pedas",
    "category_id": "cat_123",
    "description": "Seblak dengan tingkat kepedasan tinggi",
    "price": 15000,
    "image_url": "https://example.com/seblak.jpg",
    "is_topping": false
  }'
```

### 6. Update Product
```bash
curl -X PUT "http://localhost/seblak-predator/api/menu/products.php?id=prod_123" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Seblak Super Pedas",
    "price": 18000
  }'
```

### 7. Delete Product
```bash
curl -X DELETE "http://localhost/seblak-predator/api/menu/products.php?id=prod_123"
```

## Synchronization API

### 1. Sync Categories (get updates since timestamp)
```bash
curl -X GET "http://localhost/seblak-predator/api/sync/categories.php?since=2025-01-01 00:00:00"
```

### 2. Sync Products (get updates since timestamp)
```bash
curl -X GET "http://localhost/seblak-predator/api/sync/products.php?since=2025-01-01 00:00:00"
```

## Response Format

### Success Response
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": {...},
  "meta": {
    "total": 10,
    "page": 1,
    "per_page": 20,
    "last_page": 1
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field": "Field-specific error message"
  }
}
```

## HTTP Status Codes
- `200 OK`: Successful GET/PUT requests
- `201 Created`: Successful POST requests
- `400 Bad Request`: Validation errors
- `401 Unauthorized`: Authentication required
- `404 Not Found`: Resource not found
- `405 Method Not Allowed`: Invalid HTTP method
- `500 Internal Server Error`: Server errors

## Android Integration

### Java Example (using OkHttp)
```java
// Get all categories
OkHttpClient client = new OkHttpClient();
Request request = new Request.Builder()
    .url("http://your-domain.com/seblak-predator/api/menu/categories.php")
    .build();

client.newCall(request).enqueue(new Callback() {
    @Override
    public void onFailure(Call call, IOException e) {
        e.printStackTrace();
    }

    @Override
    public void onResponse(Call call, Response response) throws IOException {
        if (response.isSuccessful()) {
            String responseData = response.body().string();
            // Parse JSON response
            JSONObject jsonObject = new JSONObject(responseData);
            boolean success = jsonObject.getBoolean("success");
            if (success) {
                JSONArray categories = jsonObject.getJSONArray("data");
                // Process categories data
            }
        }
    }
});
```

### Create Product Example
```java
// Create new product
OkHttpClient client = new OkHttpClient();
MediaType JSON = MediaType.get("application/json; charset=utf-8");

JSONObject json = new JSONObject();
json.put("name", "Seblak Pedas");
json.put("category_id", "cat_123");
json.put("price", 15000);
json.put("description", "Seblak dengan tingkat kepedasan tinggi");

RequestBody body = RequestBody.create(json.toString(), JSON);
Request request = new Request.Builder()
    .url("http://your-domain.com/seblak-predator/api/menu/products.php")
    .post(body)
    .build();

client.newCall(request).enqueue(new Callback() {
    @Override
    public void onFailure(Call call, IOException e) {
        e.printStackTrace();
    }

    @Override
    public void onResponse(Call call, Response response) throws IOException {
        String responseData = response.body().string();
        // Handle response
    }
});
```