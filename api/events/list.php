<?php
/**
 * List API for Events
 * SSC Batch '94
 */

header('Content-Type: application/json');

// Include config and database
require_once '../../config/config.php';
// Redundant database.php require removed as it is handled in config.php

// Redundant session_start removed as it is handled in config.php

try {
    $db = new Database();
    $conn = $db->getConnection();

    $userId = $_SESSION['user_id'] ?? 0;

    // Check if google_maps_link column exists
    $checkCol = $conn->query("SHOW COLUMNS FROM events LIKE 'google_maps_link'");
    $hasMapLink = $checkCol->rowCount() > 0;
    $mapLinkSelect = $hasMapLink ? "e.google_maps_link," : "";

    $query = "
        SELECT 
            e.event_id,
            e.event_name as title,
            e.event_date as date, 
            e.event_time as time,
            e.venue as location,
            e.venue_address as city,
            $mapLinkSelect
            e.organizer_id,
            u.full_name as host_name,
            u.profile_photo as host_photo,
            u.mobile as host_mobile,
            e.event_date as raw_date,
            e.event_time as raw_time,
            (SELECT COUNT(*) FROM event_attendees WHERE event_id = e.event_id AND registration_status = 'registered') as attendee_count,
            (SELECT COUNT(*) FROM event_attendees WHERE event_id = e.event_id AND user_id = :current_user_id AND registration_status = 'registered') as is_attending
        FROM events e
        LEFT JOIN users u ON e.organizer_id = u.user_id
        WHERE e.status != 'cancelled'
        ORDER BY e.event_date ASC
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute([':current_user_id' => $userId]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format for frontend
    $formattedEvents = [];
    foreach ($events as $event) {
        // Fetch a few attendees for the avatar stack
        $attendeeQuery = "
            SELECT 
                u.full_name as name, 
                u.profile_photo as img 
            FROM event_attendees ea
            JOIN users u ON ea.user_id = u.user_id
            WHERE ea.event_id = :event_id AND ea.registration_status = 'registered'
            LIMIT 5
        ";
        $attStmt = $conn->prepare($attendeeQuery);
        $attStmt->execute([':event_id' => $event['event_id']]);
        $attendees = $attStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fix image paths
        foreach ($attendees as &$att) {
            if ($att['img'] && strpos($att['img'], 'http') !== 0) {
                $att['img'] = '../../assets/uploads/profiles/' . $att['img'];
            } else if (!$att['img']) {
                $att['img'] = 'https://ui-avatars.com/api/?name=' . urlencode($att['name']);
            }
        }

        // Fix host image
        $hostImg = $event['host_photo'];
        if ($hostImg && strpos($hostImg, 'http') !== 0) {
            $hostImg = '../../assets/uploads/profiles/' . $hostImg;
        } else if (!$hostImg) {
            $hostImg = 'https://ui-avatars.com/api/?name=' . urlencode($event['host_name']);
        }

        $formattedEvents[] = [
            'id' => $event['event_id'],
            'title' => $event['title'],
            'host' => $event['host_name'],
            'hostImg' => $hostImg,
            'date' => date('l, M j', strtotime($event['date'])),
            'time' => date('g:i A', strtotime($event['time'])),
            'location' => $event['location'],
            'city' => $event['city'] ?? 'Online',
            'attendees' => $attendees,
            'totalGoing' => $event['attendee_count'],
            'isAttending' => (bool) $event['is_attending'],
            'organizerId' => (int) $event['organizer_id'],
            'hostMobile' => $event['host_mobile'],
            'rawDate' => $event['raw_date'],
            'rawTime' => $event['raw_time'],
            'mapLink' => $event['google_maps_link'] ?? '',
            'type' => 'Adda'
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $formattedEvents
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
