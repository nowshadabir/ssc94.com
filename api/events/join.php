<?php
/**
 * Join Event API
 * SSC Batch '94
 */

header('Content-Type: application/json');

require_once '../../config/config.php';
// require_once '../../config/database.php'; // Already in config.php

// session_start() is already called in config.php

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access. Please login.'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);
    exit;
}

try {
    $eventId = $_POST['event_id'] ?? null;
    $action = $_POST['action'] ?? 'join'; // join or leave

    if (!$eventId) {
        throw new Exception('Event ID is required.');
    }

    $db = new Database();
    $conn = $db->getConnection();

    if ($action === 'join') {
        $query = "INSERT INTO event_attendees (event_id, user_id, registration_status) 
                  VALUES (:event_id, :user_id, 'registered')
                  ON DUPLICATE KEY UPDATE registration_status = 'registered'";
    } else {
        $query = "DELETE FROM event_attendees WHERE event_id = :event_id AND user_id = :user_id";
    }

    $stmt = $conn->prepare($query);
    $params = [
        ':event_id' => $eventId,
        ':user_id' => $_SESSION['user_id']
    ];

    if ($stmt->execute($params)) {
        echo json_encode([
            'success' => true,
            'message' => $action === 'join' ? 'You are going!' : 'RSVP removed.'
        ]);
    } else {
        throw new Exception('Failed to update RSVP.');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
