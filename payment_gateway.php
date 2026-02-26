<?php
/**
 * Bkash Payment Gateway Page
 * Loads Bkash payment widget
 */

session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/bkash.php';

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$paymentID = $_GET['payment_id'] ?? '';

if (empty($paymentID)) {
    header('Location: login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - SSC Batch '94</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bkash Scripts -->
    <?php if (BKASH_ENVIRONMENT === 'sandbox'): ?>
        <script src="https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js"></script>
    <?php else: ?>
        <script src="https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js"></script>
    <?php endif; ?>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Registration Payment</h1>
                <p class="text-gray-600">Complete your registration by paying BDT 100</p>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-700">Registration Fee</span>
                    <span class="text-xl font-bold text-gray-900">৳100.00</span>
                </div>
                <p class="text-sm text-gray-600">One-time payment required for account activation</p>
            </div>

            <div id="bkash-button" class="text-center mb-4">
                <button id="bKash_button"
                    class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-3 px-8 rounded-lg transition duration-200 w-full">
                    Pay with bKash
                </button>
            </div>

            <div id="loading" class="hidden text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-pink-500"></div>
                <p class="mt-2 text-gray-600">Processing payment...</p>
            </div>

            <div class="text-center text-sm text-gray-500 mt-6">
                <p>Secure payment powered by bKash</p>
            </div>
        </div>
    </div>

    <script>
        var paymentID = '<?php echo htmlspecialchars($paymentID); ?>';
        var paymentRequest = {
            paymentID: paymentID
        };

        bKash.init({
            paymentMode: 'checkout',
            paymentRequest: paymentRequest,
            createRequest: function () {
                console.log('Payment created with ID:', paymentID);
            },
            executeRequestOnAuthorization: function () {
                document.getElementById('bkash-button').classList.add('hidden');
                document.getElementById('loading').classList.remove('hidden');

                bKash.execute(paymentID);
            },
            onClose: function () {
                console.log('Payment window closed');
                window.location.href = '<?php echo BKASH_CANCEL_URL; ?>';
            }
        });
    </script>
</body>

</html>