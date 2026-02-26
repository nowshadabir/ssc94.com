<?php
/**
 * Update API for Events
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
    $title = trim($_POST['title'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $time = trim($_POST['time'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $mapLink = trim($_POST['map_link'] ?? '');

    if (!$eventId || empty($title) || empty($date) || empty($time) || empty($location)) {
        throw new Exception('Missing required fields.');
    }

    $db = new Database();
    $conn = $db->getConnection();

    // Verify ownership
    $checkStmt = $conn->prepare("SELECT organizer_id FROM events WHERE event_id = ?");
    $checkStmt->execute([$eventId]);
    $event = $checkStmt->fetch();

    if (!$event || $event['organizer_id'] != $_SESSION['user_id']) {
        throw new Exception('Unauthorized to edit this event.');
    }

    // Check if google_maps_link column exists
    $checkCol = $conn->query("SHOW COLUMNS FROM events LIKE 'google_maps_link'");
    $hasMapLink = $checkCol->rowCount() > 0;

    if ($hasMapLink) {
        $query = "UPDATE events SET 
                    event_name = :title, 
                    event_date = :date, 
                    event_time = :time, 
                    venue = :location, 
                    venue_address = :city,
                    google_maps_link = :map_link 
                  WHERE event_id = :event_id";
        $params = [
            ':title' => $title,
            ':date' => $date,
            ':time' => $time,
            ':location' => $location,
            ':city' => $city,
            ':map_link' => $mapLink,
            ':event_id' => $eventId
        ];
    } else {
        $query = "UPDATE events SET 
                    event_name = :title, 
                    event_date = :date, 
                    event_time = :time, 
                    venue = :location, 
                    venue_address = :city 
                  WHERE event_id = :event_id";
        $params = [
            ':title' => $title,
            ':date' => $date,
            ':time' => $time,
            ':location' => $location,
            ':city' => $city,
            ':event_id' => $eventId
        ];
    }

    $stmt = $conn->prepare($query);

    if ($stmt->execute($params)) {
        jsonResponse(true, 'Event updated successfully!');
    } else {
        throw new Exception('Database error: Failed to update event.');
    }

} catch (Exception $e) {
    jsonResponse(false, $e->getMessage());
}
