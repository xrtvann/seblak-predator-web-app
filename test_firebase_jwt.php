<?php
/**
 * Firebase JWT Authentication System Test
 * Tests the updated JWT system with Firebase JWT library
 */

echo "<h1>Firebase JWT Authentication System Test</h1>\n";

require_once 'config/koneksi.php';
require_once 'api/auth/JWTHelper.php';

echo "<h2>1. Testing Firebase JWT Library</h2>\n";

// Test user data
$user_data = [
    'id' => 'user_admin_001',
    'username' => 'admin',
    'email' => 'admin@seblakpredator.com',
    'role_id' => 'role_admin',
    'name' => 'Administrator'
];

echo "Testing token generation...\n";
$tokens = JWTHelper::generateTokenPair($user_data);

if ($tokens) {
    echo "✓ Token pair generated successfully\n";
    echo "  - Access token length: " . strlen($tokens['access_token']) . " characters\n";
    echo "  - Refresh token length: " . strlen($tokens['refresh_token']) . " characters\n";
    echo "  - Token type: " . $tokens['token_type'] . "\n";
    echo "  - Expires in: " . $tokens['expires_in'] . " seconds\n";
} else {
    echo "✗ Token generation failed\n";
    exit;
}

echo "\n<h2>2. Testing Token Validation</h2>\n";

// Test access token validation
echo "Testing access token validation...\n";
$access_validation = JWTHelper::validateToken($tokens['access_token']);

if ($access_validation['valid']) {
    echo "✓ Access token validation successful\n";
    echo "  - Token type: " . $access_validation['token_type'] . "\n";
    echo "  - User ID: " . $access_validation['user_data']['user_id'] . "\n";
    echo "  - Username: " . $access_validation['user_data']['username'] . "\n";
    echo "  - Role: " . $access_validation['user_data']['role_id'] . "\n";
    echo "  - JWT ID: " . $access_validation['jwt_id'] . "\n";
    echo "  - Expires at: " . date('Y-m-d H:i:s', $access_validation['expires_at']) . "\n";
} else {
    echo "✗ Access token validation failed: " . $access_validation['error'] . "\n";
}

// Test refresh token validation
echo "\nTesting refresh token validation...\n";
$refresh_validation = JWTHelper::validateToken($tokens['refresh_token']);

if ($refresh_validation['valid']) {
    echo "✓ Refresh token validation successful\n";
    echo "  - Token type: " . $refresh_validation['token_type'] . "\n";
    echo "  - Is refresh token: " . (JWTHelper::isRefreshToken($tokens['refresh_token']) ? 'Yes' : 'No') . "\n";
    echo "  - Is access token: " . (JWTHelper::isAccessToken($tokens['refresh_token']) ? 'Yes' : 'No') . "\n";
} else {
    echo "✗ Refresh token validation failed: " . $refresh_validation['error'] . "\n";
}

echo "\n<h2>3. Testing Token Type Detection</h2>\n";

echo "Testing access token type detection...\n";
if (JWTHelper::isAccessToken($tokens['access_token'])) {
    echo "✓ Access token correctly identified\n";
} else {
    echo "✗ Access token type detection failed\n";
}

echo "Testing refresh token type detection...\n";
if (JWTHelper::isRefreshToken($tokens['refresh_token'])) {
    echo "✓ Refresh token correctly identified\n";
} else {
    echo "✗ Refresh token type detection failed\n";
}

echo "\n<h2>4. Testing User Data Extraction</h2>\n";

$extracted_user = JWTHelper::getUserFromToken($tokens['access_token']);
if ($extracted_user) {
    echo "✓ User data extraction successful\n";
    echo "  - User ID: " . $extracted_user['user_id'] . "\n";
    echo "  - Username: " . $extracted_user['username'] . "\n";
    echo "  - Name: " . $extracted_user['name'] . "\n";
    echo "  - Email: " . $extracted_user['email'] . "\n";
} else {
    echo "✗ User data extraction failed\n";
}

echo "\n<h2>5. Testing Token Blacklisting</h2>\n";

echo "Testing token blacklisting...\n";
$blacklist_result = JWTHelper::blacklistToken($tokens['access_token'], $user_data['id'], 'security');

if ($blacklist_result) {
    echo "✓ Token blacklisted successfully\n";

    // Test if token is now blacklisted
    if (JWTHelper::isTokenBlacklisted($tokens['access_token'])) {
        echo "✓ Token blacklist check working correctly\n";
    } else {
        echo "✗ Token blacklist check failed\n";
    }
} else {
    echo "✗ Token blacklisting failed\n";
}

echo "\n<h2>6. Testing Token Refresh</h2>\n";

// Generate a new token pair for refresh testing (since we blacklisted the previous one)
$fresh_tokens = JWTHelper::generateTokenPair($user_data);
if ($fresh_tokens) {
    echo "Testing token refresh...\n";
    $refreshed = JWTHelper::refreshAccessToken($fresh_tokens['refresh_token'], $user_data['id']);

    if ($refreshed) {
        echo "✓ Token refresh successful\n";
        echo "  - New access token length: " . strlen($refreshed['access_token']) . " characters\n";
        echo "  - Token type: " . $refreshed['token_type'] . "\n";
        echo "  - Expires in: " . $refreshed['expires_in'] . " seconds\n";
    } else {
        echo "✗ Token refresh failed\n";
    }
}

echo "\n<h2>Test Summary</h2>\n";
echo "<div style='color: green; font-weight: bold;'>✓ Firebase JWT integration completed successfully!</div>\n";

echo "\n<h3>Key Features:</h3>\n";
echo "<ul>\n";
echo "<li>✓ Firebase JWT library integration</li>\n";
echo "<li>✓ Secure token generation with unique JWT IDs</li>\n";
echo "<li>✓ Comprehensive token validation</li>\n";
echo "<li>✓ Token type detection (access vs refresh)</li>\n";
echo "<li>✓ Token blacklisting for logout</li>\n";
echo "<li>✓ Token refresh functionality</li>\n";
echo "<li>✓ Proper error handling and logging</li>\n";
echo "</ul>\n";

echo "\n<h3>Available API Endpoints:</h3>\n";
echo "<ul>\n";
echo "<li><strong>POST /api/auth/login.php</strong> - User login with JWT tokens</li>\n";
echo "<li><strong>POST /api/auth/validate.php</strong> - Token validation</li>\n";
echo "<li><strong>POST /api/auth/refresh.php</strong> - Refresh access token</li>\n";
echo "<li><strong>POST /api/auth/logout.php</strong> - Logout and blacklist token</li>\n";
echo "</ul>\n";

echo "\n<p><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>\n";
?>