<?php
/**
 * Admin Payment Gateway Settings API
 * SSC Batch '94
 */

session_start();
require_once '../../config/config.php';

header('Content-Type: application/json');

// Check if admin is logged in (you may need to adjust this based on your admin auth system)
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['user_id'])) {
    jsonResponse(false, 'Unauthorized access');
}

$action = sanitize($_POST['action'] ?? $_GET['action'] ?? '');

try {
    $db = new Database();
    $conn = $db->getConnection();

    switch ($action) {
        case 'get_gateways':
            // Get all payment gateways
            $stmt = $conn->prepare("SELECT * FROM payment_gateway_settings ORDER BY gateway_name");
            $stmt->execute();
            $gateways = $stmt->fetchAll(PDO::FETCH_ASSOC);

            jsonResponse(true, 'Gateways retrieved', ['gateways' => $gateways]);
            break;

        case 'update_gateway':
            // Update gateway settings
            $gatewayName = sanitize($_POST['gateway_name'] ?? '');
            $isActive = (int) ($_POST['is_active'] ?? 0);
            $apiKey = sanitize($_POST['api_key'] ?? '');
            $apiSecret = sanitize($_POST['api_secret'] ?? '');
            $merchantNumber = sanitize($_POST['merchant_number'] ?? '');
            $webhookUrl = sanitize($_POST['webhook_url'] ?? '');
            $successUrl = sanitize($_POST['success_url'] ?? '');
            $cancelUrl = sanitize($_POST['cancel_url'] ?? '');

            if (empty($gatewayName)) {
                jsonResponse(false, 'Gateway name is required');
            }

            // If activating this gateway, deactivate all others
            if ($isActive) {
                $stmt = $conn->prepare("UPDATE payment_gateway_settings SET is_active = 0");
                $stmt->execute();
            }

            // Update or insert gateway
            $stmt = $conn->prepare("
                INSERT INTO payment_gateway_settings 
                (gateway_name, is_active, api_key, api_secret, merchant_number, webhook_url, success_url, cancel_url)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                is_active = VALUES(is_active),
                api_key = VALUES(api_key),
                api_secret = VALUES(api_secret),
                merchant_number = VALUES(merchant_number),
                webhook_url = VALUES(webhook_url),
                success_url = VALUES(success_url),
                cancel_url = VALUES(cancel_url)
            ");
            $stmt->execute([
                $gatewayName,
                $isActive,
                $apiKey,
                $apiSecret,
                $merchantNumber,
                $webhookUrl,
                $successUrl,
                $cancelUrl
            ]);

            jsonResponse(true, 'Gateway settings updated successfully');
            break;

        case 'toggle_gateway':
            // Toggle gateway active status
            $gatewayName = sanitize($_POST['gateway_name'] ?? '');

            if (empty($gatewayName)) {
                jsonResponse(false, 'Gateway name is required');
            }

            // Deactivate all gateways first
            $stmt = $conn->prepare("UPDATE payment_gateway_settings SET is_active = 0");
            $stmt->execute();

            // Activate the selected gateway
            $stmt = $conn->prepare("UPDATE payment_gateway_settings SET is_active = 1 WHERE gateway_name = ?");
            $stmt->execute([$gatewayName]);

            jsonResponse(true, ucfirst($gatewayName) . ' gateway activated successfully');
            break;

        case 'get_active_gateway':
            // Get currently active gateway
            $stmt = $conn->prepare("SELECT * FROM payment_gateway_settings WHERE is_active = 1 LIMIT 1");
            $stmt->execute();
            $gateway = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($gateway) {
                jsonResponse(true, 'Active gateway found', ['gateway' => $gateway]);
            } else {
                jsonResponse(false, 'No active gateway found');
            }
            break;

        default:
            jsonResponse(false, 'Invalid action');
    }

} catch (Exception $e) {
    logError('Payment gateway settings error: ' . $e->getMessage());
    jsonResponse(false, 'Failed to process request');
}
?>