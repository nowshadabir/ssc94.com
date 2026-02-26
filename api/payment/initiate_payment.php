<?php
/**
 * Initiate Payment (Dynamic Gateway Support)
 * Called after successful registration
 * Supports: Rupantorpay, Bkash
 */

session_start();
require_once '../../config/config.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Check if user already has a completed payment
    $stmt = $conn->prepare("
        SELECT payment_id, payment_status 
        FROM payments 
        WHERE user_id = ? AND payment_type = 'registration' AND payment_status = 'completed'
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $existingPayment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingPayment) {
        echo json_encode([
            'success' => false,
            'message' => 'Registration fee already paid',
            'redirect' => '../profile.php'
        ]);
        exit;
    }

    // Get active payment gateway
    $stmt = $conn->prepare("SELECT * FROM payment_gateway_settings WHERE is_active = 1 LIMIT 1");
    $stmt->execute();
    $activeGateway = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$activeGateway) {
        echo json_encode([
            'success' => false,
            'message' => 'No payment gateway is currently active. Please contact admin.'
        ]);
        exit;
    }

    $gatewayName = $activeGateway['gateway_name'];

    // Generate unique invoice number
    $invoiceNumber = 'INV' . time() . rand(100, 999);

    // Create payment record in database
    $stmt = $conn->prepare("
        INSERT INTO payments (user_id, payment_type, amount, currency, payment_method, transaction_id, payment_status)
        VALUES (?, 'registration', ?, 'BDT', ?, ?, 'pending')
    ");
    $stmt->execute([$userId, REGISTRATION_FEE, $gatewayName, $invoiceNumber]);
    $paymentRecordId = $conn->lastInsertId();

    // Initialize payment based on active gateway
    $paymentResult = null;

    if ($gatewayName === 'rupantorpay') {
        require_once '../../config/rupantorpay.php';
        $gateway = new RupantorpayPayment();
        $paymentResult = $gateway->createPayment(REGISTRATION_FEE, $invoiceNumber, $userId);
    } elseif ($gatewayName === 'bkash') {
        require_once '../../config/bkash.php';
        $gateway = new BkashPayment();
        $paymentResult = $gateway->createPayment(REGISTRATION_FEE, $invoiceNumber, $userId);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Unsupported payment gateway: ' . $gatewayName
        ]);
        exit;
    }

    if ($paymentResult['success']) {
        // Update payment record with gateway payment ID
        $stmt = $conn->prepare("
            UPDATE payments 
            SET payment_status = 'processing', payment_data = ?
            WHERE payment_id = ?
        ");
        $stmt->execute([
            json_encode($paymentResult),
            $paymentRecordId
        ]);

        // Store payment info in session
        $_SESSION['payment_id'] = $paymentResult['paymentID'];
        $_SESSION['payment_record_id'] = $paymentRecordId;
        $_SESSION['payment_gateway'] = $gatewayName;

        echo json_encode([
            'success' => true,
            'gateway' => $gatewayName,
            'paymentID' => $paymentResult['paymentID'],
            'paymentURL' => $paymentResult['paymentURL'] ?? ($paymentResult['bkashURL'] ?? null),
            'message' => 'Payment initiated successfully'
        ]);
    } else {
        // Update payment status to failed
        $stmt = $conn->prepare("UPDATE payments SET payment_status = 'failed' WHERE payment_id = ?");
        $stmt->execute([$paymentRecordId]);

        echo json_encode([
            'success' => false,
            'message' => $paymentResult['message'] ?? 'Failed to initiate payment'
        ]);
    }

} catch (Exception $e) {
    error_log("Payment initiation error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to initiate payment. Please try again.'
    ]);
}
?>