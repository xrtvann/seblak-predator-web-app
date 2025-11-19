<?php
/**
 * Get Snap Token for Existing Transaction
 * 
 * Generate Midtrans Snap token for an existing order
 * without creating a new transaction in database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]);
    exit();
}

// Load configuration
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        throw new Exception('Invalid JSON input');
    }

    // Validate required fields
    $required = ['order_number', 'customer_name', 'phone', 'total_amount', 'items'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            throw new Exception("Field '$field' is required");
        }
    }

    $orderNumber = $input['order_number'];
    $customerName = $input['customer_name'];
    $phone = $input['phone'];
    $totalAmount = floatval($input['total_amount']);
    $items = $input['items'];

    // Prepare item details for Midtrans
    $itemDetails = [];
    foreach ($items as $item) {
        $itemDetails[] = [
            'id' => $item['id'] ?? uniqid('item-'),
            'price' => intval($item['price']),
            'quantity' => intval($item['quantity']),
            'name' => $item['name']
        ];
    }

    // Generate unique Midtrans order_id by appending timestamp
    // This allows multiple Snap attempts for the same order without "already taken" error
    $midtransOrderId = $orderNumber . '-' . time();

    // Prepare transaction data for Midtrans
    $transactionData = [
        'transaction_details' => [
            'order_id' => $midtransOrderId, // Unique ID for each Snap attempt
            'gross_amount' => intval($totalAmount)
        ],
        'item_details' => $itemDetails,
        'customer_details' => [
            'first_name' => $customerName,
            'phone' => $phone
        ]
    ];

    // Log request to Midtrans
    error_log('=== Midtrans Snap Token Request ===');
    error_log('URL: ' . MIDTRANS_SNAP_URL . '/transactions');
    error_log('Mode: ' . (MIDTRANS_IS_PRODUCTION ? 'PRODUCTION' : 'SANDBOX'));
    error_log('Server Key: ' . substr(MIDTRANS_SERVER_KEY, 0, 15) . '...');
    error_log('Transaction Data: ' . json_encode($transactionData));

    // Call Midtrans Snap API
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
        CURLOPT_SSL_VERIFYPEER => false, // Disable SSL verification for local testing
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Log response
    error_log('HTTP Code: ' . $httpCode);
    error_log('Response: ' . $response);
    if ($curlError) {
        error_log('CURL Error: ' . $curlError);
    }

    if ($curlError) {
        throw new Exception('Curl error: ' . $curlError);
    }

    $result = json_decode($response, true);

    if ($httpCode !== 201 && $httpCode !== 200) {
        $errorMessage = isset($result['error_messages'])
            ? implode(', ', $result['error_messages'])
            : 'Failed to get snap token';

        // Log detailed error
        error_log('Midtrans Error: ' . $errorMessage);
        error_log('Full Response: ' . json_encode($result));

        throw new Exception($errorMessage);
    }

    if (!isset($result['token'])) {
        throw new Exception('Snap token not found in response');
    }

    // Return success with snap token
    echo json_encode([
        'success' => true,
        'snap_token' => $result['token'],
        'redirect_url' => $result['redirect_url'] ?? null,
        'order_number' => $orderNumber, // Original order number in our database
        'midtrans_order_id' => $midtransOrderId // Unique ID sent to Midtrans
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
