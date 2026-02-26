<?php
/**
 * Admin — Get All Reunions with aggregated stats
 * SSC Batch '94
 */

header('Content-Type: application/json');
require_once '../../config/config.php';

checkAdminAction('manage_reunions');

try {
    $db = new Database();
    $conn = $db->getConnection();

    // All reunions with registration aggregates
    $stmt = $conn->query("
        SELECT
            r.*,
            COUNT(rr.registration_id)                                       AS total_registrations,
            COALESCE(SUM(rr.guest_count), 0)                                AS total_guests,
            COALESCE(SUM(rr.total_amount), 0)                               AS gross_revenue,
            COUNT(CASE WHEN rr.payment_status = 'completed' THEN 1 END)     AS paid_count,
            COUNT(CASE WHEN rr.payment_status = 'pending'   THEN 1 END)     AS pending_count,
            COUNT(CASE WHEN rr.payment_status = 'failed'    THEN 1 END)     AS failed_count,
            COALESCE(SUM(CASE WHEN rr.payment_status = 'completed' THEN rr.total_amount ELSE 0 END), 0) AS confirmed_revenue
        FROM reunions r
        LEFT JOIN reunion_registrations rr ON rr.reunion_id = r.reunion_id
        GROUP BY r.reunion_id
        ORDER BY r.reunion_date DESC
    ");
    $reunions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // For each reunion, fetch the t-shirt breakdown
    foreach ($reunions as &$r) {
        $s = $conn->prepare("
            SELECT tshirt_size, COUNT(*) as cnt
            FROM reunion_registrations
            WHERE reunion_id = ? AND payment_status = 'completed'
            GROUP BY tshirt_size
        ");
        $s->execute([$r['reunion_id']]);
        $r['tshirt_breakdown'] = $s->fetchAll(PDO::FETCH_KEY_PAIR); // size => count
    }

    echo json_encode([
        'success' => true,
        'data' => $reunions,
    ]);

} catch (Exception $e) {
    logError('get_all_reunions error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to load reunions']);
}
