<?php
/**
 * Reunion Payment Initiation — NO DB WRITE
 * Accepts all registration form fields, stores them in SESSION,
 * then creates a Rupantorpay payment session.
 * The registration row is only INSERTed by payment_success.php
 * after the gateway confirms payment.
 * SSC Batch '94
 */

require_once '../../config/config.php';
require_once '../../config/rupantorpay.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, 'Please login to continue');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

$userId = (int) $_SESSION['user_id'];

// ── 1. Validate & collect form fields ────────────────────────────────────────
$fullName = trim(sanitize($_POST['full_name'] ?? ''));
$mobile = trim(sanitize($_POST['mobile'] ?? ''));
$tshirtSize = trim(sanitize($_POST['tshirt_size'] ?? ''));
$gender = in_array($_POST['gender'] ?? '', ['male', 'female', 'other'])
    ? $_POST['gender'] : 'male';
$guestCount = max(0, (int) ($_POST['guest_count'] ?? 0));
$guestsJson = $_POST['guests_data'] ?? '[]';

if (empty($fullName) || empty($mobile) || empty($tshirtSize)) {
    jsonResponse(false, 'Please fill all required fields (name, mobile, t-shirt size)');
}

// Validate guests_data is valid JSON
$guestsArr = json_decode($guestsJson, true);
if (!is_array($guestsArr)) {
    $guestsJson = '[]';
    $guestCount = 0;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // ── 2. Get the active reunion & calculate amount ──────────────────────────
    $stmt = $conn->prepare("SELECT * FROM reunions WHERE status = 'active' LIMIT 1");
    $stmt->execute();
    $reunion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reunion) {
        jsonResponse(false, 'No active reunion is currently open for registration');
    }

    // Check: user hasn't already paid for this reunion
    $stmt = $conn->prepare("
        SELECT registration_id FROM reunion_registrations
        WHERE user_id = ? AND reunion_id = ? AND payment_status = 'completed'
        LIMIT 1
    ");
    $stmt->execute([$userId, $reunion['reunion_id']]);
    if ($stmt->fetch()) {
        jsonResponse(false, 'You have already registered and paid for this reunion');
    }

    // Check registration deadline
    if (!empty($reunion['registration_deadline']) && $reunion['registration_deadline'] < date('Y-m-d')) {
        jsonResponse(false, 'Registration deadline has passed');
    }

    $baseFee = (float) $reunion['cost_alumnus'];
    $guestFee = (float) $reunion['cost_guest'];
    $totalAmount = $baseFee + ($guestCount * $guestFee);

    // ── 3. Generate unique invoice number (no DB row yet) ─────────────────────
    $invoiceNumber = 'RUN' . $reunion['reunion_id'] . 'U' . $userId . 'T' . time();

    // ── 4. Stash everything in session — no DB write ──────────────────────────
    $_SESSION['reunion_pending'] = [
        'user_id' => $userId,
        'reunion_id' => (int) $reunion['reunion_id'],
        'full_name' => $fullName,
        'mobile' => $mobile,
        'tshirt_size' => $tshirtSize,
        'gender' => $gender,
        'guest_count' => $guestCount,
        'guests_data' => $guestsJson,
        'total_amount' => $totalAmount,
        'invoice' => $invoiceNumber,
        'cost_alumnus' => $baseFee,
        'cost_guest' => $guestFee,
    ];

    // ── 5. Initiate Rupantorpay payment ───────────────────────────────────────
    $successUrl = SITE_URL . '/api/reunion/payment_success.php';
    $cancelUrl = SITE_URL . '/views/pages/reunion.php?payment=cancelled';

    $gateway = new RupantorpayPayment();
    $result = $gateway->createPayment(
        $totalAmount,
        $invoiceNumber,
        $userId,
        [
            'type' => 'reunion',
            'reunion_id' => $reunion['reunion_id'],
            'invoice' => $invoiceNumber,
        ],
        $successUrl,
        $cancelUrl
    );

    if ($result['success']) {
        jsonResponse(true, 'Payment initiated', [
            'paymentURL' => $result['paymentURL'],
            'paymentID' => $result['paymentID'] ?? '',
            'amount' => $totalAmount,
        ]);
    } else {
        // Clear session on gateway failure
        unset($_SESSION['reunion_pending']);
        jsonResponse(false, $result['message'] ?? 'Failed to initiate payment');
    }

} catch (Exception $e) {
    unset($_SESSION['reunion_pending']);
    logError('Reunion initiate_payment error: ' . $e->getMessage());
    jsonResponse(false, 'Failed to initiate payment. Please try again.');
}
