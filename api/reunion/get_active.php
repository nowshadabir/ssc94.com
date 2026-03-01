<?php
/**
 * Get Active Reunion API
 * SSC Batch '94
 */

require_once '../../config/config.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Get the latest active reunion
    $stmt = $conn->prepare("SELECT * FROM reunions WHERE status = 'active' ORDER BY reunion_date DESC LIMIT 1");
    $stmt->execute();
    $reunion = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reunion) {
        $registration = null;
        if (isset($_SESSION['user_id'])) {
            $stmt = $conn->prepare("
                SELECT ticket_number, qr_code_data, payment_status, tshirt_size, guest_count
                FROM reunion_registrations 
                WHERE reunion_id = ? AND user_id = ? AND payment_status = 'completed'
                LIMIT 1
            ");
            $stmt->execute([$reunion['reunion_id'], $_SESSION['user_id']]);
            $registration = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        jsonResponse(true, 'Active reunion found', [
            'reunion' => $reunion,
            'user_registration' => $registration
        ]);
    } else {
        jsonResponse(false, 'No active reunion found');
    }

} catch (Exception $e) {
    logError('Get reunion error: ' . $e->getMessage());
    jsonResponse(false, 'Failed to fetch reunion details');
}
