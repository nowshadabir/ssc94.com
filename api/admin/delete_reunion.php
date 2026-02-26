<?php
/**
 * Admin — Delete a Reunion
 * Only allows deletion if there are NO paid registrations.
 * SSC Batch '94
 */

header('Content-Type: application/json');
require_once '../../config/config.php';

checkAdminAction('delete_reunions');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST only']);
    exit;
}

$reunionId = (int) ($_POST['reunion_id'] ?? 0);
if (!$reunionId) {
    echo json_encode(['success' => false, 'message' => 'Missing reunion_id']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Safety check: block deletion if any paid registrations exist
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM reunion_registrations
        WHERE reunion_id = ? AND payment_status = 'completed'
    ");
    $stmt->execute([$reunionId]);
    $paidCount = (int) $stmt->fetchColumn();

    if ($paidCount > 0) {
        echo json_encode([
            'success' => false,
            'message' => "Cannot delete — {$paidCount} paid registration(s) exist. Mark the reunion as Completed or Inactive instead."
        ]);
        exit;
    }

    // Cascade will delete pending/failed registrations too (FK ON DELETE CASCADE)
    $stmt = $conn->prepare("DELETE FROM reunions WHERE reunion_id = ?");
    $stmt->execute([$reunionId]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Reunion not found']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Reunion deleted successfully']);

} catch (Exception $e) {
    logError('Delete reunion error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
