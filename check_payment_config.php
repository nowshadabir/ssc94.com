<?php
/**
 * Payment Gateway Configuration Checker
 * SSC Batch '94
 * 
 * Run this file to check if your payment gateway is properly configured
 * Access: http://localhost/SSC-94/ssc94.com/check_payment_config.php
 */

require_once 'config/config.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway Configuration Checker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-slate-100 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h1 class="text-3xl font-bold text-slate-900 mb-6 flex items-center gap-3">
                <i data-lucide="check-circle" class="w-8 h-8 text-blue-600"></i>
                Payment Gateway Configuration Checker
            </h1>

            <?php
            $checks = [];
            $allPassed = true;

            // Check 1: Database Connection
            try {
                $db = new Database();
                $conn = $db->getConnection();
                $checks[] = ['name' => 'Database Connection', 'status' => true, 'message' => 'Connected successfully'];
            } catch (Exception $e) {
                $checks[] = ['name' => 'Database Connection', 'status' => false, 'message' => 'Failed: ' . $e->getMessage()];
                $allPassed = false;
            }

            // Check 2: Payment Gateway Settings Table
            try {
                $stmt = $conn->prepare("SELECT COUNT(*) as count FROM payment_gateway_settings");
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $checks[] = ['name' => 'Payment Gateway Settings Table', 'status' => true, 'message' => $result['count'] . ' gateway(s) configured'];
            } catch (Exception $e) {
                $checks[] = ['name' => 'Payment Gateway Settings Table', 'status' => false, 'message' => 'Table not found. Run database/quick_setup_payment.sql'];
                $allPassed = false;
            }

            // Check 3: Active Gateway
            try {
                $stmt = $conn->prepare("SELECT * FROM payment_gateway_settings WHERE is_active = 1 LIMIT 1");
                $stmt->execute();
                $activeGateway = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($activeGateway) {
                    $checks[] = ['name' => 'Active Gateway', 'status' => true, 'message' => ucfirst($activeGateway['gateway_name']) . ' is active'];

                    // Check 4: API Key Configuration
                    if (!empty($activeGateway['api_key']) && $activeGateway['api_key'] !== 'YOUR_API_KEY_HERE') {
                        $checks[] = ['name' => 'API Key', 'status' => true, 'message' => 'API key is configured'];
                    } else {
                        $checks[] = ['name' => 'API Key', 'status' => false, 'message' => 'API key not configured. Update in admin dashboard.'];
                        $allPassed = false;
                    }

                    // Check 5: Callback URLs
                    if (!empty($activeGateway['success_url']) && !empty($activeGateway['webhook_url'])) {
                        $checks[] = ['name' => 'Callback URLs', 'status' => true, 'message' => 'Callback URLs configured'];
                    } else {
                        $checks[] = ['name' => 'Callback URLs', 'status' => false, 'message' => 'Callback URLs not configured'];
                        $allPassed = false;
                    }
                } else {
                    $checks[] = ['name' => 'Active Gateway', 'status' => false, 'message' => 'No gateway is active. Activate one in admin dashboard.'];
                    $allPassed = false;
                }
            } catch (Exception $e) {
                $checks[] = ['name' => 'Active Gateway', 'status' => false, 'message' => 'Error: ' . $e->getMessage()];
                $allPassed = false;
            }

            // Check 6: Users Table Referral Columns
            try {
                $stmt = $conn->prepare("SHOW COLUMNS FROM users LIKE 'referral_code'");
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    $checks[] = ['name' => 'Referral System', 'status' => true, 'message' => 'Referral columns exist in users table'];
                } else {
                    $checks[] = ['name' => 'Referral System', 'status' => false, 'message' => 'Referral columns missing. Run database migration.'];
                    $allPassed = false;
                }
            } catch (Exception $e) {
                $checks[] = ['name' => 'Referral System', 'status' => false, 'message' => 'Error checking users table'];
                $allPassed = false;
            }

            // Check 7: Required Files
            $requiredFiles = [
                'config/rupantorpay.php' => 'Rupantorpay Class',
                'api/payment/initiate_payment.php' => 'Payment Initiation',
                'api/payment/success.php' => 'Success Callback',
                'api/payment/webhook.php' => 'Webhook Handler',
                'api/admin/payment_gateway_settings.php' => 'Admin API',
                'views/admin/payment_gateway_settings.html' => 'Admin Dashboard'
            ];

            foreach ($requiredFiles as $file => $name) {
                if (file_exists($file)) {
                    $checks[] = ['name' => $name . ' File', 'status' => true, 'message' => 'File exists'];
                } else {
                    $checks[] = ['name' => $name . ' File', 'status' => false, 'message' => 'File missing: ' . $file];
                    $allPassed = false;
                }
            }

            // Display Results
            foreach ($checks as $check) {
                $icon = $check['status'] ? 'check-circle' : 'x-circle';
                $color = $check['status'] ? 'green' : 'red';
                $bgColor = $check['status'] ? 'bg-green-50' : 'bg-red-50';
                $borderColor = $check['status'] ? 'border-green-200' : 'border-red-200';

                echo "<div class='mb-3 p-4 rounded-lg border {$borderColor} {$bgColor}'>";
                echo "<div class='flex items-center gap-3'>";
                echo "<i data-lucide='{$icon}' class='w-6 h-6 text-{$color}-600'></i>";
                echo "<div class='flex-1'>";
                echo "<h3 class='font-bold text-slate-900'>{$check['name']}</h3>";
                echo "<p class='text-sm text-slate-600'>{$check['message']}</p>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }

            // Overall Status
            if ($allPassed) {
                echo "<div class='mt-6 p-6 bg-green-100 border-2 border-green-500 rounded-xl'>";
                echo "<h2 class='text-2xl font-bold text-green-900 mb-2 flex items-center gap-2'>";
                echo "<i data-lucide='check-circle-2' class='w-8 h-8'></i>";
                echo "All Checks Passed!";
                echo "</h2>";
                echo "<p class='text-green-800'>Your payment gateway is properly configured and ready to use.</p>";
                echo "<a href='views/auth/registration.html' class='inline-block mt-4 px-6 py-3 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700'>Test Registration Flow</a>";
                echo "</div>";
            } else {
                echo "<div class='mt-6 p-6 bg-yellow-100 border-2 border-yellow-500 rounded-xl'>";
                echo "<h2 class='text-2xl font-bold text-yellow-900 mb-2 flex items-center gap-2'>";
                echo "<i data-lucide='alert-triangle' class='w-8 h-8'></i>";
                echo "Configuration Incomplete";
                echo "</h2>";
                echo "<p class='text-yellow-800 mb-4'>Some checks failed. Please fix the issues above before using the payment gateway.</p>";
                echo "<div class='space-y-2'>";
                echo "<p class='text-sm text-yellow-900'><strong>Next Steps:</strong></p>";
                echo "<ol class='list-decimal list-inside text-sm text-yellow-900 space-y-1'>";
                echo "<li>Run <code class='bg-yellow-200 px-2 py-1 rounded'>database/quick_setup_payment.sql</code></li>";
                echo "<li>Configure your Rupantorpay API key</li>";
                echo "<li>Access admin dashboard to update settings</li>";
                echo "<li>Refresh this page to check again</li>";
                echo "</ol>";
                echo "</div>";
                echo "</div>";
            }
            ?>

            <div class="mt-6 flex gap-3">
                <a href="views/admin/payment_gateway_settings.html"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700">
                    Go to Admin Dashboard
                </a>
                <button onclick="location.reload()"
                    class="px-6 py-3 bg-slate-600 text-white rounded-lg font-bold hover:bg-slate-700">
                    Refresh Check
                </button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>