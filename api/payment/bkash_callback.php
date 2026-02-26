<?php
/**
 * Bkash Payment Callback Handler
 * This is called by Bkash after user completes payment
 */

session_start();
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../config/bkash.php';

// Get parameters from callback
$status = $_GET['status'] ?? '';
$paymentID = $_GET['paymentID'] ?? '';
$userId = $_GET['user_id'] ?? '';

// Validate required parameters
if (empty($status) || empty($paymentID)) {
    header('Location: ' . BKASH_FAILURE_URL);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Find payment record
    $stmt = $conn->prepare("
        SELECT payment_id, user_id, amount, payment_status 
        FROM payments 
        WHERE bkash_payment_id = ? 
        LIMIT 1
    ");
    $stmt->execute([$paymentID]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$payment) {
        error_log("Payment record not found for paymentID: $paymentID");
        header('Location: ' . BKASH_FAILURE_URL);
        exit;
    }

    // Check callback status
    if ($status === 'success') {
        // Execute payment with Bkash
        $bkash = new BkashPayment();
        $executeResult = $bkash->executePayment($paymentID);

        if ($executeResult['success']) {
            // Update payment record
            $stmt = $conn->prepare("
                UPDATE payments 
                SET payment_status = 'completed',
                    bkash_trx_id = ?,
                    payment_date = NOW(),
                    payment_data = ?,
                    updated_at = NOW()
                WHERE payment_id = ?
            ");
            $stmt->execute([
                $executeResult['transactionID'],
                json_encode($executeResult),
                $payment['payment_id']
            ]);

            // Update user status to active
            $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE user_id = ?");
            $stmt->execute([$payment['user_id']]);

            // Clear payment session data
            unset($_SESSION['payment_id']);
            unset($_SESSION['payment_record_id']);

            // Redirect to success page
            header('Location: ' . BKASH_SUCCESS_URL);
            exit;

        } else {
            // Payment execution failed
            $stmt = $conn->prepare("
                UPDATE payments 
                SET payment_status = 'failed',
                    payment_data = ?,
                    updated_at = NOW()
                WHERE payment_id = ?
            ");
            $stmt->execute([
                json_encode($executeResult),
                $payment['payment_id']
            ]);

            error_log("Payment execution failed for paymentID: $paymentID - " . $executeResult['message']);
            header('Location: ' . BKASH_FAILURE_URL);
            exit;
        }

    } elseif ($status === 'cancel') {
        // User cancelled the payment
        $stmt = $conn->prepare("UPDATE payments SET payment_status = 'failed' WHERE payment_id = ?");
        $stmt->execute([$payment['payment_id']]);

        header('Location: ' . BKASH_CANCEL_URL);
        exit;

    } elseif ($status === 'failure') {
        // Payment failed
        $stmt = $conn->prepare("UPDATE payments SET payment_status = 'failed' WHERE payment_id = ?");
        $stmt->execute([$payment['payment_id']]);

        header('Location: ' . BKASH_FAILURE_URL);
        exit;
    }

} catch (Exception $e) {
    error_log("Bkash callback error: " . $e->getMessage());
    header('Location: ' . BKASH_FAILURE_URL);
    exit;
}
