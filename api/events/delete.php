<?php
/**
 * Delete API for Events
 * SSC Batch '94
 */

header('Content-Type: application/json');

require_once '../../config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, 'Unauthorized access.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Method not allowed.');
}

try {
    $eventId = $_POST['event_id'] ?? null;

    if (!$eventId) {
        throw new Exception('Event ID is required.');
    }

    $db = new Database();
    $conn = $db->getConnection();

    // Verify ownership
    $checkStmt = $conn->prepare("SELECT organizer_id FROM events WHERE event_id = ?");
    $checkStmt->execute([$eventId]);
    $event = $checkStmt->fetch();

    if (!$event || $event['organizer_id'] != $_SESSION['user_id']) {
        throw new Exception('Unauthorized to delete this event.');
    }

    // Soft delete by setting status to cancelled
    $query = "UPDATE events SET status = 'cancelled' WHERE event_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt->execute([$eventId])) {
        jsonResponse(true, 'Event deleted successfully!');
    } else {
        throw new Exception('Database error: Failed to delete event.');
    }

} catch (Exception $e) {
    jsonResponse(false, $e->getMessage());
}
