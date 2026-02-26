<?php
/**
 * Rupantorpay Webhook Handler
 * SSC Batch '94
 * Handles server-to-server payment notifications
 */

require_once '../../config/config.php';
require_once '../../config/rupantorpay.php';

header('Content-Type: application/json');

// Get webhook payload
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

// Log webhook for debugging
logError('Rupantorpay Webhook Received: ' . $payload);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    $transactionId = sanitize($data['transaction_id'] ?? $data['transactionId'] ?? $data['payment_id'] ?? $data['paymentID'] ?? '');
    $status = sanitize($data['status'] ?? '');
    $amount = sanitize($data['amount'] ?? '');

    if (!$transactionId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing transaction ID']);
        exit;
    }

    // Verify payment with Rupantorpay API
    $rupantorpay = new RupantorpayPayment();
    $verificationResult = $rupantorpay->verifyPayment($transactionId);

    if ($verificationResult['success'] && ($status === 'success' || $status === 'completed')) {
        // Find payment record
        $stmt = $conn->prepare("
            SELECT * FROM payments 
            WHERE transaction_id = ? OR JSON_EXTRACT(payment_data, '$.transactionId') = ?
            LIMIT 1
        ");
        $stmt->execute([$transactionId, $transactionId]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($payment) {
            // Update payment status
            $stmt = $conn->prepare("
                UPDATE payments 
                SET payment_status = 'completed',
                    transaction_id = ?,
                    payment_date = NOW(),
                    payment_data = ?
                WHERE payment_id = ?
            ");
            $stmt->execute([
                $transactionId,
                json_encode(array_merge($verificationResult, ['webhook_data' => $data])),
                $payment['payment_id']
            ]);

            // Update user status to active
            $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE user_id = ?");
            $stmt->execute([$payment['user_id']]);

            // Process referral reward (only for registration payments)
            if ($payment['payment_type'] === 'registration') {
                processReferralReward($payment['user_id']);
            }

            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Webhook processed']);
        } else {
            logError('Webhook: Payment record not found for transaction: ' . $transactionId);
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Payment not found']);
        }
    } else {
        // Payment failed or cancelled
        $stmt = $conn->prepare("
            UPDATE payments 
            SET payment_status = 'failed'
            WHERE transaction_id = ? OR JSON_EXTRACT(payment_data, '$.transactionId') = ?
        ");
        $stmt->execute([$transactionId, $transactionId]);

        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Payment failed recorded']);
    }

} catch (Exception $e) {
    logError('Webhook error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal error']);
}
?>