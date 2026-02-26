<?php
/**
 * Get Reunion History API
 * SSC Batch '94
 */

require_once '../../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, 'Unauthorized');
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Fetch all reunions and check if user registered for each
    // We only show 'completed' reunions in history
    $stmt = $conn->prepare("
        SELECT 
            r.reunion_id, r.title, r.reunion_date, r.venue, r.status,
            rr.registration_id, rr.payment_status
        FROM reunions r
        LEFT JOIN reunion_registrations rr ON r.reunion_id = rr.reunion_id AND rr.user_id = ?
        WHERE r.status = 'completed' OR r.reunion_date < CURDATE()
        ORDER BY r.reunion_date DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse(true, 'History fetched', $history);

} catch (Exception $e) {
    logError('Get history error: ' . $e->getMessage());
    jsonResponse(false, 'Failed to fetch history');
}
