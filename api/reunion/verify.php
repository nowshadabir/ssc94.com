<?php
/**
 * Public Ticket Verification API
 * SSC Batch '94
 */

require_once '../../config/config.php';

header('Content-Type: application/json');

$ticket = sanitize($_GET['ticket'] ?? '');

if (empty($ticket)) {
    jsonResponse(false, 'Ticket number is required');
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Fetch registration and reunion details
    $stmt = $conn->prepare("
        SELECT rr.*, r.title as reunion_title, r.reunion_date, r.venue, u.full_name, u.mobile, u.profile_photo
        FROM reunion_registrations rr
        JOIN reunions r ON rr.reunion_id = r.reunion_id
        JOIN users u ON rr.user_id = u.user_id
        WHERE rr.ticket_number = ? AND rr.payment_status = 'completed'
        LIMIT 1
    ");
    $stmt->execute([$ticket]);
    $registration = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($registration) {
        jsonResponse(true, 'Ticket verified', $registration);
    } else {
        jsonResponse(false, 'Invalid or unconfirmed ticket');
    }

} catch (Exception $e) {
    logError('Ticket verification error: ' . $e->getMessage());
    jsonResponse(false, 'Verification service error');
}
