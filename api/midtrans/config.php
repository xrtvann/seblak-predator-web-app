<?php
/**
 * Midtrans Configuration
 * 
 * Credentials are loaded from .env file for security
 * Update .env file with your Midtrans API keys
 * 
 * Get your keys from:
 * - Sandbox: https://dashboard.sandbox.midtrans.com/settings/config_info
 * - Production: https://dashboard.midtrans.com/settings/config_info
 */

// Load environment configuration
require_once __DIR__ . '/../../config/env.php';
EnvLoader::load();

// Set production mode from .env (true for production, false for sandbox/testing)
define('MIDTRANS_IS_PRODUCTION', EnvLoader::get('MIDTRANS_IS_PRODUCTION', false));

// Midtrans Server Key - loaded from .env
define('MIDTRANS_SERVER_KEY', EnvLoader::get('MIDTRANS_SERVER_KEY', 'SB-Mid-server-YOUR_SERVER_KEY_HERE'));

// Midtrans Client Key - loaded from .env
define('MIDTRANS_CLIENT_KEY', EnvLoader::get('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-YOUR_CLIENT_KEY_HERE'));

// Midtrans Merchant ID (optional)
define('MIDTRANS_MERCHANT_ID', EnvLoader::get('MIDTRANS_MERCHANT_ID', ''));

// Midtrans API Base URL
define(
    'MIDTRANS_API_URL',
    MIDTRANS_IS_PRODUCTION
    ? 'https://api.midtrans.com/v2'
    : 'https://api.sandbox.midtrans.com/v2'
);

// Midtrans Snap API URL
define(
    'MIDTRANS_SNAP_URL',
    MIDTRANS_IS_PRODUCTION
    ? 'https://app.midtrans.com/snap/v1'
    : 'https://app.sandbox.midtrans.com/snap/v1'
);

// Validation check (optional warning)
if (
    MIDTRANS_SERVER_KEY === 'SB-Mid-server-YOUR_SERVER_KEY_HERE' ||
    MIDTRANS_CLIENT_KEY === 'SB-Mid-client-YOUR_CLIENT_KEY_HERE'
) {
    // Only show warning in development
    if (EnvLoader::get('APP_ENV') === 'development' && php_sapi_name() !== 'cli') {
        error_log('WARNING: Midtrans API keys not configured. Please update .env file.');
    }
}

