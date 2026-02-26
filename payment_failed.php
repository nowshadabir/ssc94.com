<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - SSC Batch '94</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="mb-6">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Payment Required</h1>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'pending_activation'): ?>
                    <p class="text-gray-600">Your account is created but <strong>awaiting payment activation</strong>.
                        Please
                        complete the payment to access your profile.</p>
                <?php else: ?>
                    <p class="text-gray-600">Your payment could not be processed. Please try again.</p>
                <?php endif; ?>
            </div>

            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 text-left">
                <h3 class="font-semibold text-red-900 mb-2">Possible Reasons:</h3>
                <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
                    <li>Insufficient balance</li>
                    <li>Transaction timeout</li>
                    <li>Network connectivity issue</li>
                    <li>Invalid PIN entered</li>
                </ul>
            </div>

            <div class="space-y-3">
                <a href="payment_redirect.php"
                    class="block w-full bg-pink-500 hover:bg-pink-600 text-white font-medium py-3 px-4 rounded-lg transition duration-200">
                    Try Again
                </a>
                <a href="index.html"
                    class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-3 px-4 rounded-lg transition duration-200">
                    Back to Home
                </a>
            </div>

            <p class="text-sm text-gray-500 mt-6">
                Need help? Contact us at <a href="mailto:support@ssc94.com"
                    class="text-blue-600 hover:underline">support@ssc94.com</a>
            </p>
        </div>
    </div>
</body>

</html>