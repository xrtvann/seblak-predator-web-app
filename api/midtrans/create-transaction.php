<?php
/**
 * Midtrans Create Transaction - Generate Snap Token
 * API untuk membuat transaksi dan mendapatkan Snap Token dari Midtrans
 */

header('Content-Type: application/json');
require_once '../../config/koneksi.php';
require_once '../../config/session.php';
require_once 'config.php';

// Get current user (optional - for logging purposes)
$current_user = getCurrentSessionUser();

// Log request for debugging
error_log('=== Midtrans Create Transaction Request ===');
error_log('User: ' . ($current_user['username'] ?? 'Guest'));
error_log('Input: ' . file_get_contents('php://input'));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    // Get request body
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        throw new Exception('Invalid request data');
    }

    // Validate required fields
    if (empty($input['customer_name']) || empty($input['items']) || count($input['items']) === 0) {
        throw new Exception('Customer name and items are required');
    }

    // Calculate totals
    $subtotal = 0;
    $item_details = [];

    foreach ($input['items'] as $item) {
        // Item price
        $item_total = $item['unit_price'] * $item['quantity'];
        $subtotal += $item_total;

        $item_details[] = [
            'id' => $item['product_id'],
            'price' => (int) $item['unit_price'],
            'quantity' => (int) $item['quantity'],
            'name' => $item['product_name']
        ];

        // Topping prices
        if (isset($item['toppings']) && is_array($item['toppings'])) {
            foreach ($item['toppings'] as $topping) {
                $topping_total = $topping['unit_price'] * $topping['quantity'];
                $subtotal += $topping_total;

                $item_details[] = [
                    'id' => $topping['topping_id'],
                    'price' => (int) $topping['unit_price'],
                    'quantity' => (int) $topping['quantity'],
                    'name' => '+ ' . $topping['topping_name']
                ];
            }
        }
    }

    $tax = 0; // No tax
    $discount = 0; // No discount
    $total = $subtotal + $tax - $discount;

    // Generate unique order ID
    $order_id = 'ORD-' . date('YmdHis') . '-' . rand(1000, 9999);

    // Prepare customer email (Midtrans requires valid email format)
    $customer_email = 'customer@seblakpredator.com'; // Default email
    if (!empty($input['phone']) && trim($input['phone']) !== '') {
        // Clean phone number (remove spaces, dashes, etc)
        $clean_phone = preg_replace('/[^0-9]/', '', $input['phone']);
        if (strlen($clean_phone) >= 8) {
            $customer_email = $clean_phone . '@seblakpredator.com';
        }
    }

    // Prepare transaction data for Midtrans
    $transaction_data = [
        'transaction_details' => [
            'order_id' => $order_id,
            'gross_amount' => (int) $total
        ],
        'customer_details' => [
            'first_name' => $input['customer_name'],
            'email' => $customer_email,
            'phone' => !empty($input['phone']) ? $input['phone'] : '08123456789'
        ],
        'item_details' => $item_details,
        'callbacks' => [
            'finish' => 'https://yourdomain.com/payment-finish'
        ]
    ];

    // Add customer address if table number provided
    if (!empty($input['table_number'])) {
        $transaction_data['customer_details']['billing_address'] = [
            'first_name' => $input['customer_name'],
            'address' => 'Table Number: ' . $input['table_number'],
            'city' => 'Restaurant',
            'postal_code' => '00000',
            'country_code' => 'IDN'
        ];
    }

    // Call Midtrans Snap API
    $snap_result = createMidtransSnapToken($transaction_data);

    if ($snap_result['success']) {
        echo json_encode([
            'success' => true,
            'snap_token' => $snap_result['token'],
            'order_id' => $order_id,
            'total_amount' => $total
        ]);
    } else {
        throw new Exception($snap_result['message'] ?? 'Failed to create Snap token');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Create Midtrans Snap Token
 */
function createMidtransSnapToken($transaction_data)
{
    $url = MIDTRANS_SNAP_URL . '/transactions';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transaction_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode(MIDTRANS_SERVER_KEY . ':')
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    // Check for CURL errors
    if ($curl_error) {
        error_log('Midtrans CURL Error: ' . $curl_error);
        return [
            'success' => false,
            'message' => 'Connection error: ' . $curl_error
        ];
    }

    // Parse response
    $result = json_decode($response, true);

    if ($http_code === 201 || $http_code === 200) {
        if (isset($result['token'])) {
            return [
                'success' => true,
                'token' => $result['token']
            ];
        } else {
            error_log('Midtrans API Error: No token in response');
            return [
                'success' => false,
                'message' => 'No token received from Midtrans'
            ];
        }
    }

    // Log error for debugging
    error_log('Midtrans API Error (HTTP ' . $http_code . '): ' . $response);

    // Return detailed error
    $error_message = 'Midtrans API Error';
    if ($result && isset($result['error_messages'])) {
        $error_message = implode(', ', $result['error_messages']);
    } elseif ($result && isset($result['message'])) {
        $error_message = $result['message'];
    }

    return [
        'success' => false,
        'message' => $error_message . ' (HTTP ' . $http_code . ')',
        'http_code' => $http_code,
        'response' => $result
    ];
}
