<?php
/**
 * Admin Dashboard Stats API
 * Returns: total members, members per division, reunion summary
 * SSC Batch '94
 */

header('Content-Type: application/json');
require_once '../../config/config.php';

// Admin check
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // ── 1. Total members ──────────────────────────────────────────────────────
    $stmt = $conn->query("SELECT COUNT(*) FROM users");
    $totalMembers = (int) $stmt->fetchColumn();

    $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE status = 'active'");
    $activeMembers = (int) $stmt->fetchColumn();

    // ── 2. Members per Bangladesh Division ───────────────────────────────────
    // Divisions stored in user_present_info.current_location (free text)
    // We do a case-insensitive LIKE match for each division name
    $divisions = [
        'Dhaka' => ['Dhaka'],
        'Chattagram' => ['Chattagram', 'Chittagong', 'Chottogram'],
        'Rajshahi' => ['Rajshahi'],
        'Khulna' => ['Khulna'],
        'Barishal' => ['Barishal', 'Barisal'],
        'Sylhet' => ['Sylhet'],
        'Rangpur' => ['Rangpur'],
        'Mymensingh' => ['Mymensingh'],
    ];

    $divisionCounts = [];
    foreach ($divisions as $label => $keywords) {
        $conditions = array_map(fn($k) => "upi.current_location LIKE ?", $keywords);
        $sql = "
            SELECT COUNT(DISTINCT u.user_id)
            FROM users u
            LEFT JOIN user_present_info upi ON upi.user_id = u.user_id
            WHERE " . implode(' OR ', $conditions);
        $stmt = $conn->prepare($sql);
        $stmt->execute(array_map(fn($k) => "%$k%", $keywords));
        $divisionCounts[$label] = (int) $stmt->fetchColumn();
    }

    // ── 3. Reunion summary ────────────────────────────────────────────────────
    $stmt = $conn->query("SELECT * FROM reunions WHERE status = 'active' LIMIT 1");
    $reunion = $stmt->fetch(PDO::FETCH_ASSOC);

    $reunionStats = ['total' => 0, 'completed' => 0, 'pending' => 0, 'failed' => 0, 'revenue' => 0];
    if ($reunion) {
        $stmt = $conn->prepare("
            SELECT payment_status, COUNT(*) as cnt, COALESCE(SUM(total_amount),0) as revenue
            FROM reunion_registrations
            WHERE reunion_id = ?
            GROUP BY payment_status
        ");
        $stmt->execute([$reunion['reunion_id']]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reunionStats['total'] += $row['cnt'];
            $reunionStats[$row['payment_status']] = (int) $row['cnt'];
            if ($row['payment_status'] === 'completed') {
                $reunionStats['revenue'] = (float) $row['revenue'];
            }
        }
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'total_members' => $totalMembers,
            'active_members' => $activeMembers,
            'divisions' => $divisionCounts,
            'reunion' => $reunion,
            'reunion_stats' => $reunionStats,
        ]
    ]);

} catch (Exception $e) {
    logError('Dashboard stats error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to load stats']);
}
