<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Midtrans Payment - Seblak Predator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }

        .test-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }

        .btn-pay {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
        }

        .btn-pay:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
        }

        .info-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .test-credentials {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .config-status {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .config-ok {
            background: #d1e7dd;
            color: #0f5132;
        }

        .config-error {
            background: #f8d7da;
            color: #842029;
        }
    </style>
</head>

<body>
    <div class="test-container">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">🧪 Midtrans Payment Testing</h3>
                <p class="mb-0 mt-2 opacity-75">Sandbox Environment</p>
            </div>
            <div class="card-body">
                <?php
                require_once 'api/midtrans/config.php';

                // Check configuration
                $config_ok = MIDTRANS_SERVER_KEY !== 'SB-Mid-server-YOUR_SERVER_KEY_HERE' &&
                    MIDTRANS_CLIENT_KEY !== 'SB-Mid-client-YOUR_CLIENT_KEY_HERE';
                ?>

                <!-- Configuration Status -->
                <div class="config-status <?php echo $config_ok ? 'config-ok' : 'config-error'; ?>">
                    <strong>
                        <?php if ($config_ok): ?>
                            ✅ Midtrans Configured (<?php echo MIDTRANS_IS_PRODUCTION ? 'Production' : 'Sandbox'; ?> Mode)
                        <?php else: ?>
                            ❌ Midtrans Not Configured - Please update .env file
                        <?php endif; ?>
                    </strong>
                    <?php if ($config_ok): ?>
                        <div class="mt-2 small">
                            <div>Server Key: <?php echo substr(MIDTRANS_SERVER_KEY, 0, 20) . '...'; ?></div>
                            <div>Client Key: <?php echo substr(MIDTRANS_CLIENT_KEY, 0, 20) . '...'; ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Test Credentials Info -->
                <div class="test-credentials">
                    <h5>📌 Sandbox Test Credentials</h5>
                    <p class="mb-2">Gunakan kartu kredit test berikut untuk testing:</p>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td><strong>Card Number:</strong></td>
                            <td>4811 1111 1111 1114</td>
                        </tr>
                        <tr>
                            <td><strong>CVV:</strong></td>
                            <td>123</td>
                        </tr>
                        <tr>
                            <td><strong>Exp Date:</strong></td>
                            <td>01/25 (atau bulan/tahun apa saja di masa depan)</td>
                        </tr>
                        <tr>
                            <td><strong>OTP/3D Secure:</strong></td>
                            <td>112233</td>
                        </tr>
                    </table>
                    <div class="mt-2 small">
                        <a href="https://docs.midtrans.com/docs/testing-payment" target="_blank"
                            class="text-decoration-none">
                            📖 Lihat lebih banyak test credentials
                        </a>
                    </div>
                </div>

                <!-- Test Form -->
                <div class="info-box">
                    <h5>🛒 Test Order</h5>
                    <p class="mb-0">Klik tombol di bawah untuk membuat test transaction dan membuka Midtrans payment
                        popup.</p>
                </div>

                <form id="paymentForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Customer</label>
                            <input type="text" class="form-control" id="customerName" value="John Doe" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" id="customerPhone" value="08123456789" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Total Amount</label>
                            <input type="number" class="form-control" id="amount" value="50000" required>
                            <small class="text-muted">Minimum: Rp 10,000</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Meja (Optional)</label>
                            <input type="text" class="form-control" id="tableNumber" value="5">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan (Optional)</label>
                        <textarea class="form-control" id="notes"
                            rows="2">Test order untuk Midtrans integration</textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-pay btn-primary btn-lg">
                            💳 Pay with Midtrans
                        </button>
                    </div>
                </form>

                <!-- Response Area -->
                <div id="responseArea" class="mt-4" style="display: none;">
                    <div class="alert" id="responseMessage"></div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="text-center mt-3">
            <a href="index.php" class="btn btn-light">← Kembali ke Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Midtrans Snap.js -->
    <script
        src="<?php echo MIDTRANS_IS_PRODUCTION ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js'; ?>"
        data-client-key="<?php echo MIDTRANS_CLIENT_KEY; ?>"></script>

    <script>
        const paymentForm = document.getElementById('paymentForm');
        const responseArea = document.getElementById('responseArea');
        const responseMessage = document.getElementById('responseMessage');

        paymentForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
            button.disabled = true;

            try {
                // Prepare order data
                const orderData = {
                    customer_name: document.getElementById('customerName').value,
                    phone: document.getElementById('customerPhone').value,
                    table_number: document.getElementById('tableNumber').value || null,
                    notes: document.getElementById('notes').value || null,
                    items: [{
                        product_id: 'TEST-001',
                        product_name: 'Seblak Pedas Test',
                        quantity: 1,
                        unit_price: parseInt(document.getElementById('amount').value),
                        toppings: []
                    }]
                };

                console.log('Creating transaction...', orderData);

                // Call API to create transaction and get snap token
                const response = await fetch('api/midtrans/create-transaction.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                });

                const result = await response.json();
                console.log('Transaction response:', result);

                if (result.success && result.snap_token) {
                    // Open Midtrans payment popup
                    window.snap.pay(result.snap_token, {
                        onSuccess: function (result) {
                            console.log('Payment success:', result);
                            showResponse('success', '✅ Payment Successful!', JSON.stringify(result, null, 2));
                        },
                        onPending: function (result) {
                            console.log('Payment pending:', result);
                            showResponse('warning', '⏳ Payment Pending', JSON.stringify(result, null, 2));
                        },
                        onError: function (result) {
                            console.log('Payment error:', result);
                            showResponse('danger', '❌ Payment Error', JSON.stringify(result, null, 2));
                        },
                        onClose: function () {
                            console.log('Payment popup closed');
                            showResponse('info', 'ℹ️ Payment popup closed', 'User closed the payment popup without completing payment.');
                        }
                    });
                } else {
                    throw new Error(result.message || 'Failed to create transaction');
                }
            } catch (error) {
                console.error('Error:', error);
                showResponse('danger', '❌ Error', error.message);
            } finally {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        });

        function showResponse(type, title, message) {
            responseArea.style.display = 'block';
            responseMessage.className = `alert alert-${type}`;
            responseMessage.innerHTML = `
                <h5>${title}</h5>
                <pre class="mb-0" style="white-space: pre-wrap; font-size: 12px;">${message}</pre>
            `;
        }
    </script>
</body>

</html>