<?php
/**
 * Rupantorpay Payment Cancel Callback
 * SSC Batch '94
 */

session_start();
require_once '../../config/config.php';

$transactionId = sanitize(
    $_GET['transaction_id'] ?? $_POST['transaction_id'] ??
    $_GET['payment_id'] ?? $_POST['payment_id'] ??
    $_GET['trx_id'] ?? $_POST['trx_id'] ??
    $_GET['transactionId'] ?? $_POST['transactionId'] ??
    $_GET['id'] ?? $_POST['id'] ?? ''
);

// Fallback to invoice number
$invoice = sanitize($_GET['invoice'] ?? $_POST['invoice'] ?? '');

try {
    if (!$transactionId && $invoice) {
        $transactionId = $invoice;
    }

    if ($transactionId) {
        $db = new Database();
        $conn = $db->getConnection();

        // Update payment status to failed/cancelled
        $stmt = $conn->prepare("
            UPDATE payments 
            SET payment_status = 'failed'
            WHERE transaction_id = ? OR JSON_EXTRACT(payment_data, '$.transactionId') = ?
        ");
        $stmt->execute([$transactionId, $transactionId]);
    }

    header('Location: ../../payment_failed.php?reason=cancelled');
    exit;

} catch (Exception $e) {
    logError('Payment cancel callback error: ' . $e->getMessage());
    header('Location: ../../payment_failed.php?reason=error');
    exit;
}
?>