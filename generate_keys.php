<?php
/**
 * Secure Key Generator
 * Generates secure keys for JWT and encryption
 */

require_once 'config/env.php';

echo "🔐 Secure Key Generator for Seblak Predator\n";
echo "==========================================\n\n";

// Generate JWT Secret Key
echo "1. JWT Secret Key (64 characters):\n";
$jwt_secret = EnvLoader::generateSecureKey(64);
echo "JWT_SECRET_KEY=" . $jwt_secret . "\n\n";

// Generate Session Encryption Key
echo "2. Session Encryption Key (32 characters):\n";
$session_key = EnvLoader::generateSecureKey(32);
echo "SESSION_ENCRYPTION_KEY=" . $session_key . "\n\n";

// Generate CSRF Token Key
echo "3. CSRF Token Key (32 characters):\n";
$csrf_key = EnvLoader::generateSecureKey(32);
echo "CSRF_TOKEN_KEY=" . $csrf_key . "\n\n";

// Generate App Key
echo "4. Application Key (32 characters):\n";
$app_key = EnvLoader::generateSecureKey(32);
echo "APP_KEY=" . $app_key . "\n\n";

echo "🔒 SECURITY INSTRUCTIONS:\n";
echo "========================\n";
echo "1. Copy the keys above to your .env file\n";
echo "2. NEVER commit .env file to version control\n";
echo "3. Use different keys for production environment\n";
echo "4. Store production keys securely (password manager, vault)\n";
echo "5. Rotate keys periodically for enhanced security\n\n";

echo "📝 .env File Update Commands:\n";
echo "============================\n";
echo "Add these lines to your .env file:\n\n";
echo "JWT_SECRET_KEY=" . $jwt_secret . "\n";
echo "SESSION_ENCRYPTION_KEY=" . $session_key . "\n";
echo "CSRF_TOKEN_KEY=" . $csrf_key . "\n";
echo "APP_KEY=" . $app_key . "\n";

echo "\n✅ Keys generated successfully!\n";
echo "Remember: These keys are shown only once. Store them securely!\n";
?>