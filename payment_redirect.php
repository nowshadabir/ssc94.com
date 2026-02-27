<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment - SSC Batch '94</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="assets/js/main.js"></script>
</head>

<body class="bg-gradient-to-br from-slate-900 to-slate-800 min-h-screen flex items-center justify-center">

    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
                <i data-lucide="credit-card" class="w-10 h-10 text-blue-600"></i>
            </div>

            <h1 class="text-2xl font-bold text-slate-900 mb-3">Initiating Payment</h1>
            <p class="text-slate-600 mb-6">Please wait while we redirect you to the payment gateway...</p>

            <div class="flex items-center justify-center gap-2 text-sm text-slate-500">
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Initiate payment on page load
        window.addEventListener('DOMContentLoaded', async function () {
            try {
                const response = await fetch('api/payment/initiate_payment.php', {
                    method: 'POST',
                    credentials: 'same-origin'
                });

                const result = await response.json();

                if (result.success && result.paymentURL) {
                    // Redirect to payment gateway
                    setTimeout(() => {
                        window.location.href = result.paymentURL;
                    }, 1500);
                } else {
                    // Show error and redirect to failed page
                    showToast(result.message || 'Failed to initiate payment', 'error');
                    setTimeout(() => {
                        window.location.href = 'payment_failed.php';
                    }, 2000);
                }
            } catch (error) {
                console.error('Payment initiation error:', error);
                showToast('An error occurred. Please try again.', 'error');
                setTimeout(() => {
                    window.location.href = 'payment_failed.php';
                }, 2000);
            }
        });
    </script>
</body>

</html>