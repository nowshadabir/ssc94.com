<?php
/**
 * Reunion Payment Callback (Simulated bKash Merchant)
 * SSC Batch '94
 */

require_once '../../config/config.php';

header('Content-Type: application/json');

$regId = (int) ($_POST['registration_id'] ?? 0);
$trxId = sanitize($_POST['transaction_id'] ?? '');
$status = sanitize($_POST['status'] ?? ''); // 'success' or 'failed'

if (!$regId || !$trxId) {
    jsonResponse(false, 'Missing payment data');
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    if ($status === 'success') {
        // Fetch registration info to generate QR data
        $stmt = $conn->prepare("SELECT ticket_number, full_name FROM reunion_registrations WHERE registration_id = ?");
        $stmt->execute([$regId]);
        $reg = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reg) {
            jsonResponse(false, 'Registration not found');
        }

        // Generate QR Code Data (Basic info for now)
        $qrData = "TIK:" . $reg['ticket_number'] . "|NAME:" . $reg['full_name'] . "|STAT:PAID";

        // Update to completed
        $stmt = $conn->prepare("
            UPDATE reunion_registrations 
            SET payment_status = 'completed', 
                transaction_id = ?, 
                qr_code_data = ? 
            WHERE registration_id = ?
        ");
        $stmt->execute([$trxId, $qrData, $regId]);

        jsonResponse(true, 'Payment verified and ticket generated', [
            'ticket_number' => $reg['ticket_number'],
            'qr_data' => $qrData
        ]);
    } else {
        $stmt = $conn->prepare("UPDATE reunion_registrations SET payment_status = 'failed' WHERE registration_id = ?");
        $stmt->execute([$regId]);
        jsonResponse(false, 'Payment failed');
    }

} catch (Exception $e) {
    logError('Payment callback error: ' . $e->getMessage());
    jsonResponse(false, 'Failed to process payment callback');
}
