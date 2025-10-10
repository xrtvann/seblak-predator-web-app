# Firebase JWT Authentication - Quick Reference

## Overview
Your Seblak Predator application now uses Google's Firebase JWT library for secure, industry-standard token-based authentication.

## Key Features
- ✅ Firebase JWT v6.11.1 integration
- ✅ Secure token generation with unique JWT IDs
- ✅ Token blacklisting for logout
- ✅ Role-based access control
- ✅ Token refresh functionality
- ✅ Comprehensive error handling

## API Endpoints

### 1. Login
```http
POST /api/auth/login.php
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
    "access_token": "eyJ0eXAiOiJKV1Q...",
    "refresh_token": "eyJ0eXAiOiJKV1Q...",
    "token_type": "Bearer",
    "expires_in": 3600,
    "user": {
        "id": "user_admin_001",
        "username": "admin",
        "name": "Administrator",
        "email": "admin@seblakpredator.com",
        "role": "Administrator"
    }
}
```

### 2. Token Validation
```http
POST /api/auth/validate.php
Authorization: Bearer {access_token}
```

**Response:**
```json
{
    "success": true,
    "message": "Token is valid",
    "data": {
        "user": {...},
        "token_type": "access",
        "expires_at": 1696851456,
        "issued_at": 1696847856,
        "jwt_id": "jwt_67890..."
    }
}
```

### 3. Token Refresh
```http
POST /api/auth/refresh.php
Content-Type: application/json

{
    "refresh_token": "eyJ0eXAiOiJKV1Q..."
}
```

**Response:**
```json
{
    "success": true,
    "message": "Token refreshed successfully",
    "access_token": "eyJ0eXAiOiJKV1Q...",
    "token_type": "Bearer",
    "expires_in": 3600
}
```

### 4. Logout
```http
POST /api/auth/logout.php
Authorization: Bearer {access_token}

{
    "refresh_token": "eyJ0eXAiOiJKV1Q..." // optional
}
```

**Response:**
```json
{
    "success": true,
    "message": "Logged out successfully",
    "data": {
        "user_id": "user_admin_001",
        "logged_out_at": "2025-10-09 12:30:00"
    }
}
```

### 5. Protected Endpoint Example
```http
GET /api/auth/profile.php
Authorization: Bearer {access_token}
```

## Using the JWT Helper Class

### Basic Token Operations
```php
// Generate token pair
$tokens = JWTHelper::generateTokenPair($user_data);

// Validate token
$validation = JWTHelper::validateToken($token);
if ($validation['valid']) {
    $user_data = $validation['user_data'];
}

// Check token type
$isAccess = JWTHelper::isAccessToken($token);
$isRefresh = JWTHelper::isRefreshToken($token);

// Extract user data
$user = JWTHelper::getUserFromToken($token);

// Blacklist token (logout)
JWTHelper::blacklistToken($token, $user_id, 'logout');

// Check if blacklisted
$isBlacklisted = JWTHelper::isTokenBlacklisted($token);
```

## Using the Middleware

### Protect API Endpoints
```php
require_once 'api/auth/middleware.php';

// Authenticate any user
$user = JWTMiddleware::authenticate();

// Authenticate with role requirement
$user = JWTMiddleware::authenticate(['role_admin', 'role_staff']);

// Check current user
$current_user = JWTMiddleware::getCurrentUser();

// Check roles
$isAdmin = JWTMiddleware::hasRole('role_admin');
$isStaff = JWTMiddleware::hasAnyRole(['role_admin', 'role_staff']);

// Log API access
JWTMiddleware::logApiAccess('/api/endpoint', 'GET', true);
```

## Mobile App Integration

### Android Example (Java/Kotlin)
```java
// Login request
JSONObject loginData = new JSONObject();
loginData.put("username", "admin");
loginData.put("password", "password");

// Send POST request to /api/auth/login.php
// Store access_token and refresh_token

// Use token in subsequent requests
HttpURLConnection connection = (HttpURLConnection) url.openConnection();
connection.setRequestProperty("Authorization", "Bearer " + accessToken);
```

### Token Refresh Strategy
```java
// When API returns 401, refresh token
if (response.getResponseCode() == 401) {
    String newAccessToken = refreshToken(refreshToken);
    if (newAccessToken != null) {
        // Retry original request with new token
    } else {
        // Redirect to login
    }
}
```

## Security Best Practices

### Environment Configuration
```php
// Use environment variables for secrets
$secret_key = $_ENV['JWT_SECRET_KEY'] ?? 'fallback-secret';
$algorithm = $_ENV['JWT_ALGORITHM'] ?? 'HS256';
```

### Token Storage
- ✅ Store access tokens in memory (mobile apps)
- ✅ Store refresh tokens securely (Android Keystore)
- ❌ Never store tokens in localStorage (web)
- ❌ Never log tokens to console

### Error Handling
- Always check token validity before use
- Handle expired tokens gracefully
- Implement automatic token refresh
- Log authentication failures for monitoring

## Database Tables

The Firebase JWT system uses these database tables:
- `blacklisted_tokens` - Revoked tokens
- `token_refresh_log` - Token refresh history
- `api_access_log` - API access monitoring
- `login_attempts` - Login attempt tracking
- `user_sessions` - Web session management

## Testing

Use the provided test script:
```bash
php test_firebase_jwt.php
```

Or test individual endpoints with cURL:
```bash
# Login
curl -X POST http://localhost:8000/api/auth/login.php \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"password"}'

# Validate token
curl -X POST http://localhost:8000/api/auth/validate.php \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Troubleshooting

### Common Issues
1. **"Token has expired"** - Use refresh token to get new access token
2. **"Invalid token signature"** - Check JWT secret key configuration
3. **"Token has been revoked"** - Token was blacklisted, user needs to login again
4. **"Insufficient permissions"** - User role doesn't have required access

### Debug Mode
Enable error reporting in development:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Migration from Custom JWT
If migrating from the previous custom implementation:
1. Tokens generated with old system will be invalid
2. Users need to login again to get new Firebase JWT tokens
3. Update mobile apps to handle new token format
4. Test all authentication flows thoroughly

---

**Your authentication system is now powered by Firebase JWT - industry-standard, secure, and production-ready!** 🚀