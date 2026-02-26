<?php
/**
 * Admin – Get Reunion Registrations (paginated, with full detail)
 * SSC Batch '94
 */

header('Content-Type: application/json');
require_once '../../config/config.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = min(100, max(10, (int) ($_GET['limit'] ?? 20)));
$offset = ($page - 1) * $limit;

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Total count
    $stmt = $conn->query("SELECT COUNT(*) FROM reunion_registrations");
    $total = (int) $stmt->fetchColumn();

    // Full list with user info
    $stmt = $conn->prepare("
        SELECT
            rr.registration_id,
            rr.ticket_number,
            rr.full_name,
            rr.mobile,
            rr.tshirt_size,
            rr.gender,
            rr.guest_count,
            rr.guests_data,
            rr.total_amount,
            rr.payment_status,
            rr.transaction_id,
            rr.created_at,
            u.email,
            u.profile_photo,
            upi.current_location,
            upi.blood_group,
            usi.school_name,
            usi.zilla
        FROM reunion_registrations rr
        LEFT JOIN users u             ON u.user_id   = rr.user_id
        LEFT JOIN user_present_info upi ON upi.user_id = rr.user_id
        LEFT JOIN user_school_info usi  ON usi.user_id  = rr.user_id
        ORDER BY rr.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$limit, $offset]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Decode guests_data JSON
    foreach ($rows as &$row) {
        $row['guests_data'] = json_decode($row['guests_data'] ?? '[]', true) ?? [];
        if ($row['profile_photo'] && !str_starts_with($row['profile_photo'], 'http')) {
            $row['profile_photo'] = '../../assets/uploads/profiles/' . $row['profile_photo'];
        }
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'registrations' => $rows,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => (int) ceil($total / $limit),
                'total_count' => $total,
                'limit' => $limit,
            ]
        ]
    ]);

} catch (Exception $e) {
    logError('Get reunion registrations error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to load registrations']);
}
