<?php
/**
 * Admin — Create OR Update a Reunion
 * POST with reunion_id = update a specific row
 * POST without reunion_id = create new
 * SSC Batch '94
 */

header('Content-Type: application/json');
require_once '../../config/config.php';

checkAdminAction('edit_reunions');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST only']);
    exit;
}

$reunionId = (int) ($_POST['reunion_id'] ?? 0);
$title = trim(sanitize($_POST['title'] ?? ''));
$date = trim(sanitize($_POST['reunion_date'] ?? ''));
$time = trim(sanitize($_POST['reunion_time'] ?? ''));
$venue = trim(sanitize($_POST['venue'] ?? ''));
$venueDetails = trim(sanitize($_POST['venue_details'] ?? ''));
$foodMenu = trim(sanitize($_POST['food_menu'] ?? ''));
$activities = trim(sanitize($_POST['activities'] ?? ''));
$costAlumnus = (float) ($_POST['cost_alumnus'] ?? 0);
$costGuest = (float) ($_POST['cost_guest'] ?? 0);
$deadline = trim(sanitize($_POST['registration_deadline'] ?? ''));
$status = in_array($_POST['status'] ?? '', ['active', 'inactive', 'completed'])
    ? $_POST['status'] : 'active';

if (empty($title) || empty($date) || empty($venue)) {
    echo json_encode(['success' => false, 'message' => 'Title, date and venue are required']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    if ($reunionId > 0) {
        // ── UPDATE specific row ──────────────────────────────────────
        $stmt = $conn->prepare("SELECT reunion_id FROM reunions WHERE reunion_id = ? LIMIT 1");
        $stmt->execute([$reunionId]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Reunion not found']);
            exit;
        }

        $stmt = $conn->prepare("
            UPDATE reunions SET
                title = ?, reunion_date = ?, reunion_time = ?,
                venue = ?, venue_details = ?,
                food_menu = ?, activities = ?,
                cost_alumnus = ?, cost_guest = ?,
                registration_deadline = ?, status = ?
            WHERE reunion_id = ?
        ");
        $stmt->execute([
            $title,
            $date,
            $time,
            $venue,
            $venueDetails,
            $foodMenu,
            $activities,
            $costAlumnus,
            $costGuest,
            $deadline ?: null,
            $status,
            $reunionId
        ]);

        echo json_encode(['success' => true, 'message' => 'Reunion updated successfully', 'reunion_id' => $reunionId]);

    } else {
        // ── CREATE new reunion ───────────────────────────────────────
        $stmt = $conn->prepare("
            INSERT INTO reunions
                (title, reunion_date, reunion_time, venue, venue_details,
                 food_menu, activities, cost_alumnus, cost_guest,
                 registration_deadline, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $title,
            $date,
            $time,
            $venue,
            $venueDetails,
            $foodMenu,
            $activities,
            $costAlumnus,
            $costGuest,
            $deadline ?: null,
            $status
        ]);

        $newId = (int) $conn->lastInsertId();
        echo json_encode(['success' => true, 'message' => 'Reunion created successfully', 'reunion_id' => $newId]);
    }

} catch (Exception $e) {
    logError('Reunion save error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
