<?php
/**
 * Rupantorpay Payment Success Callback
 * SSC Batch '94
 */

session_start();
require_once '../../config/config.php';
require_once '../../config/rupantorpay.php';

// Log request for debugging
logError('Rupantorpay Success Callback GET: ' . json_encode($_GET));
logError('Rupantorpay Success Callback POST: ' . json_encode($_POST));

// 1. Identify IDs (Favor actual Gateway ID over internal Invoice)
$trxKeys = ['transactionId', 'transaction_id', 'trx_id', 'payment_id', 'paymentID', 'id'];
$invoiceKeys = ['invoice', 'invoiceID', 'orderID'];

$gatewayTrxId = '';
foreach ($trxKeys as $key) {
    if (isset($_GET[$key]) && trim($_GET[$key]) !== '') {
        $gatewayTrxId = sanitize($_GET[$key]);
        break;
    }
    if (isset($_POST[$key]) && trim($_POST[$key]) !== '') {
        $gatewayTrxId = sanitize($_POST[$key]);
        break;
    }
}

$invoiceNo = '';
foreach ($invoiceKeys as $key) {
    if (isset($_GET[$key]) && trim($_GET[$key]) !== '') {
        $invoiceNo = sanitize($_GET[$key]);
        break;
    }
    if (isset($_POST[$key]) && trim($_POST[$key]) !== '') {
        $invoiceNo = sanitize($_POST[$key]);
        break;
    }
}

// Final fallback for identification
$transactionId = $gatewayTrxId ?: $invoiceNo ?: ($_SESSION['payment_id'] ?? '');

// 2. Check Gateway Status from URL (The "Easier" way)
$statusInUrl = strtolower(sanitize($_GET['status'] ?? $_POST['status'] ?? $_GET['payment_status'] ?? $_POST['payment_status'] ?? ''));
$isGatewaySuccess = in_array($statusInUrl, ['success', 'completed', 'paid']);

if ($transactionId) {
    logError("Processing Success: Status=$statusInUrl, TrxId=$gatewayTrxId, Invoice=$invoiceNo");
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // 3. Find the Payment Record (Try everything to match)
    $stmt = $conn->prepare("
        SELECT p.*, u.full_name, u.email 
        FROM payments p
        JOIN users u ON p.user_id = u.user_id
        WHERE p.transaction_id = ? 
           OR p.transaction_id = ? 
           OR JSON_EXTRACT(p.payment_data, '$.paymentID') = ?
           OR (p.user_id = ? AND p.payment_status = 'processing')
        ORDER BY p.created_at DESC LIMIT 1
    ");
    $stmt->execute([$gatewayTrxId, $invoiceNo, $transactionId, $_SESSION['user_id'] ?? 0]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$payment) {
        logError('Payment record not found for: ' . $transactionId);
        header('Location: ../../payment_failed.php?error=payment_not_found');
        exit;
    }

    // 4. Verify Payment (API check + URL Status check)
    $rupantorpay = new RupantorpayPayment();
    // Use gateway ID if we have it, else use search ID
    $verifyTarget = $gatewayTrxId ?: $payment['transaction_id'];
    $verificationResult = $rupantorpay->verifyPayment($verifyTarget);

    // IF API verification is successful OR the URLs strictly say success, we COMPLETE it.
    if ($verificationResult['success'] || $isGatewaySuccess) {

        // Update payment status
        $stmt = $conn->prepare("
            UPDATE payments 
            SET payment_status = 'completed',
                transaction_id = ?,
                payment_date = NOW(),
                payment_data = ?
            WHERE payment_id = ?
        ");

        // We prefer the real gateway ID for our records
        $finalTrxId = $gatewayTrxId ?: ($verificationResult['transactionId'] ?? $payment['transaction_id']);

        $stmt->execute([
            $finalTrxId,
            json_encode(array_merge($verificationResult, ['url_status' => $statusInUrl])),
            $payment['payment_id']
        ]);

        // Update user status to active
        $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE user_id = ?");
        $stmt->execute([$payment['user_id']]);

        // Process referral reward (only for registration payments)
        if ($payment['payment_type'] === 'registration') {
            processReferralReward($payment['user_id']);
        }

        // Update Session
        $_SESSION['user_id'] = $payment['user_id'];
        $_SESSION['user_name'] = $payment['full_name'];
        $_SESSION['user_email'] = $payment['email'];
        $_SESSION['payment_success'] = true;

        header('Location: ../../views/profile.php?payment=success');
        exit;
    } else {
        logError('Payment verification failed for ' . $transactionId . ': ' . json_encode($verificationResult));
        header('Location: ../../payment_failed.php?error=verification_failed');
        exit;
    }

} catch (Exception $e) {
    logError('Payment success callback error: ' . $e->getMessage());
    header('Location: ../../payment_failed.php?error=system_error');
    exit;
}
?>