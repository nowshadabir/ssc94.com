<?php
/**
 * Get My Referrals - Admin API
 * Returns list of users referred by the currently logged-in admin (matching by email)
 */

header('Content-Type: application/json');

require_once '../../config/config.php';

try {
    checkAdminAction(); // Basic admin check
    
    $db = new Database();
    $conn = $db->getConnection();

    // 1. Get current admin details from DB
    $adminId = $_SESSION['admin_id'];
    $stmt = $conn->prepare("SELECT email FROM admins WHERE admin_id = ?");
    $stmt->execute([$adminId]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        jsonResponse(false, 'Admin details not found');
    }

    // 2. Find matching user in users table
    $stmt = $conn->prepare("SELECT user_id, user_code FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$admin['email']]);
    $userRecord = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userRecord) {
        // If no matching user, return empty list with a message
        echo json_encode([
            'success' => true,
            'users' => [],
            'message' => 'No matching membership account found for this admin email.',
            'admin_member_id' => 'N/A'
        ]);
        exit();
    }

    $referrerUserId = $userRecord['user_id'];
    $adminMemberId = $userRecord['user_code'];

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
        'admin_member_id' => $adminMemberId,
        'count' => count($referredUsers)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch referrals: ' . $e->getMessage()
    ]);
}
