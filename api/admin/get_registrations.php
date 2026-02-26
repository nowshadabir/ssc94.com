<?php
/**
 * Get User Registrations - Admin API
 * Returns list of registered users with referral and payment information
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../../config/config.php';

try {
    checkAdminAction('manage_users');
    $db = new Database();
    $conn = $db->getConnection();

    // Get all users with referral and payment information
    $query = "
        SELECT 
            u.user_id,
            u.full_name,
            u.mobile,
            u.email,
            u.profile_photo,
            u.referral_code,
            u.referred_by,
            u.status,
            u.created_at,
            referrer.full_name as referred_by_name,
            referrer.referral_code as referred_by_code,
            p.payment_status,
            p.amount as payment_amount,
            p.transaction_id,
            p.payment_method
        FROM users u
        LEFT JOIN users referrer ON u.referred_by = referrer.user_id
        LEFT JOIN payments p ON u.user_id = p.user_id AND p.payment_type = 'registration'
        ORDER BY u.created_at DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate statistics
    $stats = [
        'total' => count($users),
        'active' => 0,
        'pending' => 0,
        'with_referrals' => 0
    ];

    foreach ($users as $user) {
        if ($user['status'] === 'active') {
            $stats['active']++;
        } elseif ($user['status'] === 'pending') {
            $stats['pending']++;
        }

        if ($user['referred_by'] !== null) {
            $stats['with_referrals']++;
        }
    }

    // Format profile photo paths
    foreach ($users as &$user) {
        if ($user['profile_photo'] && !str_starts_with($user['profile_photo'], 'http')) {
            // Remove leading ../ or ./ or /
            $photoPath = preg_replace('/^(\.\.\/|\.\/|\/)/', '', $user['profile_photo']);
            $user['profile_photo'] = '../../' . $photoPath;
        }
    }

    echo json_encode([
        'success' => true,
        'users' => $users,
        'stats' => $stats,
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch users: ' . $e->getMessage()
    ]);
}
?>