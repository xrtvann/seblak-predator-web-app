<?php
/**
 * Direct Test Midtrans API
 * Test langsung ke Midtrans API untuk debug
 */

require_once 'api/midtrans/config.php';

echo "<h2>Midtrans Configuration Test</h2>";
echo "<pre>";
echo "Mode: " . (MIDTRANS_IS_PRODUCTION ? 'PRODUCTION' : 'SANDBOX') . "\n";
echo "API URL: " . MIDTRANS_SNAP_URL . "\n";
echo "Server Key: " . substr(MIDTRANS_SERVER_KEY, 0, 20) . "...\n";
echo "Client Key: " . substr(MIDTRANS_CLIENT_KEY, 0, 20) . "...\n";
echo "\n";

// Prepare test transaction data
$transactionData = [
    'transaction_details' => [
        'order_id' => 'TEST-' . time(),
        'gross_amount' => 10000
    ],
    'item_details' => [
        [
            'id' => 'ITEM-1',
            'price' => 10000,
            'quantity' => 1,
            'name' => 'Test Item'
        ]
    ],
    'customer_details' => [
        'first_name' => 'Test Customer',
        'email' => 'test@example.com',
        'phone' => '08123456789'
    ]
];

echo "Request Data:\n";
echo json_encode($transactionData, JSON_PRETTY_PRINT) . "\n\n";

// Call Midtrans API
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => MIDTRANS_SNAP_URL . '/transactions',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($transactionData),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode(MIDTRANS_SERVER_KEY . ':')
    ],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_VERBOSE => true
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
$curlInfo = curl_getinfo($ch);
curl_close($ch);

echo "=== RESPONSE ===\n";
echo "HTTP Code: " . $httpCode . "\n";
if ($curlError) {
    echo "CURL Error: " . $curlError . "\n";
}
echo "\nResponse Body:\n";
echo json_encode(json_decode($response, true), JSON_PRETTY_PRINT) . "\n";

echo "\n=== CURL INFO ===\n";
echo "URL: " . $curlInfo['url'] . "\n";
echo "Content Type: " . $curlInfo['content_type'] . "\n";
echo "Total Time: " . $curlInfo['total_time'] . "s\n";

echo "</pre>";
