<?php
/**
 * Get User Details - Admin API
 */
require_once '../../config/config.php';

header('Content-Type: application/json');

try {
    checkAdminAction('view_members');

    $userId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if (!$userId) {
        jsonResponse(false, 'User ID is required');
    }

    $db = new Database();
    $conn = $db->getConnection();

    // Get comprehensive user info
    $stmt = $conn->prepare("
        SELECT 
            u.*,
            pi.father_name, pi.mother_name, pi.blood_group, pi.permanent_address, 
            pi.date_of_birth, pi.gender, pi.willing_to_donate, pi.last_donation_date,
            pr.job_business, pr.institute_working_station, pr.current_location, 
            pr.current_address, pr.linkedin_profile, pr.facebook_profile,
            si.school_name, si.zilla, si.union_upozilla, si.batch_year,
            referrer.full_name as referred_by_name
        FROM users u
        LEFT JOIN user_personal_info pi ON u.user_id = pi.user_id
        LEFT JOIN user_present_info pr ON u.user_id = pr.user_id
        LEFT JOIN user_school_info si ON u.user_id = si.user_id
        LEFT JOIN users referrer ON u.referred_by = referrer.user_id
        WHERE u.user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        jsonResponse(false, 'User not found');
    }

    // Clean sensitive data
    unset($user['password_hash']);

    jsonResponse(true, 'User details loaded', $user);

} catch (Exception $e) {
    jsonResponse(false, 'Error: ' . $e->getMessage());
}
