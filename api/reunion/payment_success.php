<?php
/**
 * Reunion Payment Success Callback
 * Rupantorpay redirects the user here after successful payment.
 *
 * Flow:
 *  1. Extract & verify payment details from gateway
 *  2. Read registration data from $_SESSION['reunion_pending']
 *  3. INSERT the registration row as 'completed' (first and only DB write)
 *  4. Clear session, redirect to reunion page with ticket number
 *
 * SSC Batch '94
 */

require_once '../../config/config.php';
require_once '../../config/rupantorpay.php';

// ── Helper redirects ──────────────────────────────────────────────────────────
function redirectFail(string $msg): void
{
    header('Location: ../../views/pages/reunion.php?payment=failed&msg=' . urlencode($msg));
    exit;
}

function redirectOk(string $ticket): void
{
    header('Location: ../../views/pages/reunion.php?payment=success&ticket=' . urlencode($ticket));
    exit;
}

// ── 1. Extract transaction identifiers sent by the gateway ───────────────────
$trxKeys = ['transactionId', 'transaction_id', 'trx_id', 'payment_id', 'paymentID', 'id'];
$invoiceKeys = ['invoice', 'invoiceID', 'orderID'];

$gatewayTrxId = '';
foreach ($trxKeys as $key) {
    $v = trim($_GET[$key] ?? $_POST[$key] ?? '');
    if ($v !== '') {
        $gatewayTrxId = sanitize($v);
        break;
    }
}

$invoiceNo = '';
foreach ($invoiceKeys as $key) {
    $v = trim($_GET[$key] ?? $_POST[$key] ?? '');
    if ($v !== '') {
        $invoiceNo = sanitize($v);
        break;
    }
}

$statusInUrl = strtolower(sanitize($_GET['status'] ?? $_POST['status'] ?? $_GET['payment_status'] ?? $_POST['payment_status'] ?? ''));
$isUrlSuccess = in_array($statusInUrl, ['success', 'completed', 'paid']);

// Fallback: invoice stored in session
$invoiceNo = $invoiceNo ?: ($_SESSION['reunion_pending']['invoice'] ?? '');

logError('Reunion Success GET: ' . json_encode($_GET));
logError('Reunion Success POST: ' . json_encode($_POST));
logError("Reunion Success: trxId=$gatewayTrxId, invoice=$invoiceNo, urlStatus=$statusInUrl");

// ── 2. Validate session data ──────────────────────────────────────────────────
$pending = $_SESSION['reunion_pending'] ?? null;

if (!$pending || empty($pending['user_id']) || empty($pending['reunion_id'])) {
    logError('Reunion success: no pending session data. Trying to recover...');
    // If there's nothing in session but we have an invoice, we might already have
    // processed this (e.g. webhook ran first). Check DB.
    if ($invoiceNo) {
        // Attempt to find an already-completed record via transaction_id
        try {
            $db = new Database();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("SELECT ticket_number FROM reunion_registrations WHERE transaction_id = ? AND payment_status = 'completed' LIMIT 1");
            $stmt->execute([$invoiceNo]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                redirectOk($row['ticket_number']);
            }
        } catch (Exception $e) {
        }
    }
    redirectFail('Session expired — please start registration again');
}

// ── 3. Verify with Rupantorpay ────────────────────────────────────────────────
$rupantorpay = new RupantorpayPayment();
$verifyTarget = $gatewayTrxId ?: $invoiceNo;
$verifyResult = $rupantorpay->verifyPayment($verifyTarget);

$isVerified = $verifyResult['success'] || $isUrlSuccess;

if (!$isVerified) {
    logError('Reunion payment verification failed: ' . json_encode($verifyResult));
    unset($_SESSION['reunion_pending']);
    redirectFail('Payment verification failed — please contact support if money was deducted');
}

// ── 4. INSERT registration (first and only DB write for this registration) ────
try {
    $db = new Database();
    $conn = $db->getConnection();

    // Double-check: prevent duplicate if user somehow hits this URL twice
    $stmt = $conn->prepare("
        SELECT registration_id, ticket_number
        FROM   reunion_registrations
        WHERE  transaction_id = ? OR (user_id = ? AND reunion_id = ? AND payment_status = 'completed')
        LIMIT  1
    ");
    $stmt->execute([
        $invoiceNo,
        $pending['user_id'],
        $pending['reunion_id'],
    ]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Already processed (e.g. webhook beat us here) — just redirect to ticket
        unset($_SESSION['reunion_pending']);
        redirectOk($existing['ticket_number']);
    }

    // Generate ticket number
    $ticketNumber = '#94-' . str_pad(mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);

    $finalTrxId = $gatewayTrxId ?: ($verifyResult['transactionId'] ?? $invoiceNo);
    $qrData = 'TIK:' . $ticketNumber
        . '|NAME:' . $pending['full_name']
        . '|STAT:PAID'
        . '|TRX:' . $finalTrxId;

    $stmt = $conn->prepare("
        INSERT INTO reunion_registrations
            (user_id, reunion_id, ticket_number, full_name, mobile,
             tshirt_size, gender, guest_count, guests_data,
             total_amount, payment_status, transaction_id, qr_code_data)
        VALUES
            (?, ?, ?, ?, ?,
             ?, ?, ?, ?,
             ?, 'completed', ?, ?)
    ");
    $stmt->execute([
        $pending['user_id'],
        $pending['reunion_id'],
        $ticketNumber,
        $pending['full_name'],
        $pending['mobile'],
        $pending['tshirt_size'],
        $pending['gender'],
        $pending['guest_count'],
        $pending['guests_data'],
        $pending['total_amount'],
        $finalTrxId,
        $qrData,
    ]);

    logError("Reunion registration created: ticket=$ticketNumber, trx=$finalTrxId, user={$pending['user_id']}");

    // ── 5. Clear session & redirect ───────────────────────────────────────────
    unset($_SESSION['reunion_pending']);
    redirectOk($ticketNumber);

} catch (Exception $e) {
    logError('Reunion payment_success INSERT error: ' . $e->getMessage());
    // Payment WAS verified but DB failed — don't lose the ticket number
    unset($_SESSION['reunion_pending']);
    redirectFail('Payment confirmed but registration save failed. Please contact support with reference: ' . $invoiceNo);
}
