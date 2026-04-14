<?php
/**
 * Get My Referrals - Admin API
 * Returns list of users referred by the currently logged-in admin (matching by email)
 */

header('Content-Type: application/json');

require_once '../../config/config.php';

try {
    checkAdminAction('view_referrals'); // Permission check
    
    $db = new Database();
    $conn = $db->getConnection();

    // 1. Get member ID from request
    $lookupMemberId = sanitize($_GET['member_id'] ?? '');

    if (empty($lookupMemberId)) {
        jsonResponse(false, 'Please provide a Member ID to look up.');
    }

    // 2. Find matching user in users table to get their user_id
    $stmt = $conn->prepare("SELECT user_id, full_name, user_code FROM users WHERE user_code = ? OR user_id = ? LIMIT 1");
    $stmt->execute([$lookupMemberId, $lookupMemberId]);
    $referrerRecord = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$referrerRecord) {
        jsonResponse(false, 'No user found with the provided Member ID.');
    }

    $referrerUserId = $referrerRecord['user_id'];
    $referrerName = $referrerRecord['full_name'];
    $referrerCode = $referrerRecord['user_code'];

    // 3. Get all users referred by this admin's user account
    $query = "
        SELECT 
            u.user_id,
            u.full_name,
            u.mobile,
            u.email,
            u.profile_photo,
            u.user_code,
            u.status,
            u.created_at,
            p.payment_status,
            p.amount as payment_amount
        FROM users u
        LEFT JOIN payments p ON u.user_id = p.user_id AND p.payment_type = 'registration'
        WHERE u.referred_by = ?
        ORDER BY u.created_at DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute([$referrerUserId]);
    $referredUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format profile photo paths
    foreach ($referredUsers as &$user) {
        if ($user['profile_photo'] && !str_starts_with($user['profile_photo'], 'http')) {
            $photoPath = preg_replace('/^(\.\.\/|\.\/|\/)/', '', $user['profile_photo']);
            $user['profile_photo'] = '../../' . $photoPath;
        }
    }

    echo json_encode([
        'success' => true,
        'users' => $referredUsers,
        'referrer_name' => $referrerName,
        'referrer_code' => $referrerCode,
        'count' => count($referredUsers)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch referrals: ' . $e->getMessage()
    ]);
}
