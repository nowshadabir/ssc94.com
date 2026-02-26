<?php
/**
 * Admin Reunion Update API
 * SSC Batch '94
 */

require_once '../../config/config.php';

header('Content-Type: application/json');

// Security Check: Only admins allowed
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    jsonResponse(false, 'Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

$title = sanitize($_POST['title'] ?? '');
$date = sanitize($_POST['reunion_date'] ?? '');
$time = sanitize($_POST['reunion_time'] ?? '');
$venue = sanitize($_POST['venue'] ?? '');
$venueDetails = sanitize($_POST['venue_details'] ?? '');
$foodMenu = sanitize($_POST['food_menu'] ?? '');
$activities = sanitize($_POST['activities'] ?? '');
$costAlumnus = (float) ($_POST['cost_alumnus'] ?? 0);
$costGuest = (float) ($_POST['cost_guest'] ?? 0);
$deadline = sanitize($_POST['registration_deadline'] ?? '');
$status = sanitize($_POST['status'] ?? 'active');

if (empty($title) || empty($date) || empty($venue)) {
    jsonResponse(false, 'Required fields missing');
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Check if an active reunion already exists
    $stmt = $conn->prepare("SELECT reunion_id FROM reunions WHERE status = 'active' LIMIT 1");
    $stmt->execute();
    $existing = $stmt->fetch();

    if ($existing) {
        // Update existing
        $id = $existing['reunion_id'];
        $stmt = $conn->prepare("
            UPDATE reunions SET 
                title = ?, reunion_date = ?, reunion_time = ?, 
                venue = ?, venue_details = ?, food_menu = ?, 
                activities = ?, cost_alumnus = ?, cost_guest = ?, 
                registration_deadline = ?, status = ?
            WHERE reunion_id = ?
        ");
        $stmt->execute([$title, $date, $time, $venue, $venueDetails, $foodMenu, $activities, $costAlumnus, $costGuest, $deadline, $status, $id]);
        jsonResponse(true, 'Reunion details updated successfully');
    } else {
        // Create new
        $stmt = $conn->prepare("
            INSERT INTO reunions (title, reunion_date, reunion_time, venue, venue_details, food_menu, activities, cost_alumnus, cost_guest, registration_deadline, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$title, $date, $time, $venue, $venueDetails, $foodMenu, $activities, $costAlumnus, $costGuest, $deadline, $status]);
        jsonResponse(true, 'Reunion declared successfully');
    }

} catch (Exception $e) {
    logError('Admin reunion update error: ' . $e->getMessage());
    jsonResponse(false, 'Failed to update reunion details');
}
