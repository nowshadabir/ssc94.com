<?php
/**
 * Create API for Events
 * SSC Batch '94
 */

header('Content-Type: application/json');

// Include config and database
require_once '../../config/config.php';
// require_once '../../config/database.php'; // Already in config.php

// Check if user is logged in
// session_start() is already called in config.php
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access. Please login.'
    ]);
    exit;
}

// Get POST data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);
    exit;
}

try {
    // Validate inputs
    $title = trim($_POST['title'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $time = trim($_POST['time'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $mapLink = trim($_POST['map_link'] ?? '');

    if (empty($title) || empty($date) || empty($time) || empty($location)) {
        throw new Exception('All fields (Title, Date, Time, Location) are required.');
    }

    // Connect DB
    $db = new Database();
    $conn = $db->getConnection();

    // Check if 'city' column exists, if not use 'venue_address' for city
    // In this implementation, we map:
    // title -> event_name
    // date -> event_date
    // time -> event_time
    // location -> venue
    // city -> venue_address (using as City for now)

    // Optional: Check available columns or rely on convention
    // For now we map city -> venue_address as discussed

    // Check if google_maps_link column exists
    $checkCol = $conn->query("SHOW COLUMNS FROM events LIKE 'google_maps_link'");
    $hasMapLink = $checkCol->rowCount() > 0;

    if ($hasMapLink) {
        $query = "INSERT INTO events (event_name, event_date, event_time, venue, venue_address, google_maps_link, organizer_id, status) 
                  VALUES (:title, :date, :time, :location, :city, :map_link, :organizer_id, 'upcoming')";
        $params = [
            ':title' => $title,
            ':date' => $date,
            ':time' => $time,
            ':location' => $location,
            ':city' => $city,
            ':map_link' => $mapLink,
            ':organizer_id' => $_SESSION['user_id']
        ];
    } else {
        $query = "INSERT INTO events (event_name, event_date, event_time, venue, venue_address, organizer_id, status) 
                  VALUES (:title, :date, :time, :location, :city, :organizer_id, 'upcoming')";
        $params = [
            ':title' => $title,
            ':date' => $date,
            ':time' => $time,
            ':location' => $location,
            ':city' => $city,
            ':organizer_id' => $_SESSION['user_id']
        ];
    }

    $stmt = $conn->prepare($query);

    if ($stmt->execute($params)) {
        $eventId = $conn->lastInsertId();

        // Also add the creating user as an attendee automatically
        $attendQuery = "INSERT INTO event_attendees (event_id, user_id, registration_status) VALUES (:event_id, :user_id, 'registered')";
        $attendStmt = $conn->prepare($attendQuery);
        $attendStmt->execute([
            ':event_id' => $eventId,
            ':user_id' => $_SESSION['user_id']
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Event created successfully!',
            'data' => [
                'event_id' => $eventId,
                'title' => $title,
                'date' => $date,
                'time' => $time,
                'location' => $location,
                'city' => $city
            ]
        ]);
    } else {
        throw new Exception('Database error: Failed to insert event.');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
