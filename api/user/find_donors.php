<?php
/**
 * Find Donors API
 * SSC Batch '94
 */

require_once '../../config/config.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $conn = $db->getConnection();

    $blood_group = $_GET['blood_group'] ?? 'All';
    $district = $_GET['district'] ?? 'All';
    $name = $_GET['name'] ?? '';

    $query = "
        SELECT 
            u.user_id,
            u.full_name as name,
            u.mobile as phone,
            u.profile_photo as img,
            pi.blood_group as blood,
            pri.current_location as district,
            pri.current_address as area,
            pi.last_donation_date as profile_last_donated,
            pi.willing_to_donate,
            (SELECT MAX(issued_at) FROM event_tickets et 
             JOIN events e ON et.event_id = e.event_id 
             WHERE et.user_id = u.user_id AND e.event_type = 'donation_drive' AND et.ticket_status = 'used') as event_last_donated
        FROM users u
        LEFT JOIN user_personal_info pi ON u.user_id = pi.user_id
        LEFT JOIN user_present_info pri ON u.user_id = pri.user_id
        WHERE u.status = 'active' AND (pi.willing_to_donate = 1 OR pi.willing_to_donate IS NULL)
    ";

    $params = [];

    if ($blood_group !== 'All') {
        $query .= " AND pi.blood_group = :blood";
        $params[':blood'] = $blood_group;
    }

    if ($district !== 'All' && !empty($district)) {
        $query .= " AND pri.current_location LIKE :district";
        $params[':district'] = "%$district%";
    }

    if (!empty($name)) {
        $query .= " AND u.full_name LIKE :name";
        $params[':name'] = "%$name%";
    }

    $query .= " GROUP BY u.user_id ORDER BY pi.blood_group ASC, u.full_name ASC";

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $donors = $stmt->fetchAll();

    foreach ($donors as &$donor) {
        if ($donor['img'] && strpos($donor['img'], 'http') !== 0) {
            $donor['img'] = '../../assets/uploads/profiles/' . $donor['img'];
        } else if (!$donor['img']) {
            $donor['img'] = 'https://ui-avatars.com/api/?name=' . urlencode($donor['name']) . '&background=random';
        }

        $donor['blood'] = $donor['blood'] ?? 'Unknown';
        $donor['district'] = $donor['district'] ?? 'Not Specified';
        $donor['area'] = $donor['area'] ?? '';

        $profileDate = $donor['profile_last_donated'] ? strtotime($donor['profile_last_donated']) : 0;
        $eventDate = $donor['event_last_donated'] ? strtotime($donor['event_last_donated']) : 0;

        $mostRecent = max($profileDate, $eventDate);

        if ($mostRecent > 0) {
            $donor['last_donated'] = date('Y-m-d', $mostRecent);
        } else {
            $donor['last_donated'] = "2020-01-01";
        }
    }

    echo json_encode($donors);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
