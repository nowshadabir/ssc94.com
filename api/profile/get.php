<?php
/**
 * Get Profile API
 * SSC Batch '94
 */

require_once '../../config/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    jsonResponse(false, 'Unauthorized');
}

$userId = $_SESSION['user_id'];

try {
    $db = new Database();
    $conn = $db->getConnection();

    $personalInfoColsStmt = $conn->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'user_personal_info'
          AND COLUMN_NAME IN ('last_donation_date', 'willing_to_donate')
    ");
    $personalInfoColsStmt->execute();
    $personalInfoCols = $personalInfoColsStmt->fetchAll(PDO::FETCH_COLUMN);
    $hasLastDonation = in_array('last_donation_date', $personalInfoCols, true);
    $hasWillingToDonate = in_array('willing_to_donate', $personalInfoCols, true);
    $lastDonationSelect = $hasLastDonation ? 'pi.last_donation_date' : 'NULL AS last_donation_date';
    $willingSelect = $hasWillingToDonate ? 'pi.willing_to_donate' : 'NULL AS willing_to_donate';

    $stmt = $conn->prepare("
        SELECT 
            u.user_id,
            u.user_code,
            u.balance,
            u.full_name,
            u.email,
            u.mobile,
            u.profile_photo,
            pi.blood_group,
            pi.father_name,
            pi.mother_name,
            pi.permanent_address,
            {$lastDonationSelect},
            {$willingSelect},
            pr.job_business,
            pr.institute_working_station,
            pr.current_location,
            si.school_name,
            si.zilla,
            si.union_upozilla
        FROM users u
        LEFT JOIN user_personal_info pi ON u.user_id = pi.user_id
        LEFT JOIN user_present_info pr ON u.user_id = pr.user_id
        LEFT JOIN user_school_info si ON u.user_id = si.user_id
        WHERE u.user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_unset();
        session_destroy();
        jsonResponse(false, 'Unauthorized');
    }

    $photo = $user['profile_photo'] ?? '';
    if (empty($photo)) {
        $photo = 'https://i.pravatar.cc/300?u=' . $userId;
    } else {
        $isHttp = strpos($photo, 'http://') === 0 || strpos($photo, 'https://') === 0;
        if (!$isHttp) {
            $photo = '../assets/uploads/profiles/' . $photo;
        }
    }

    jsonResponse(true, 'Profile loaded', [
        'id' => $user['user_id'],
        'memberId' => $user['user_code'] ?? 'N/A',
        'balance' => $user['balance'] ?? '0.00',
        'name' => $user['full_name'] ?? 'User',
        'email' => $user['email'] ?? '',
        'mobile' => $user['mobile'] ?? '',
        'job' => $user['job_business'] ?? 'Not specified',
        'institute' => $user['institute_working_station'] ?? 'Not specified',
        'currentLocation' => $user['current_location'] ?? 'Bangladesh',
        'bloodGroup' => $user['blood_group'] ?? 'Not specified',
        'schoolName' => $user['school_name'] ?? 'Not specified',
        'zilla' => $user['zilla'] ?? 'Not specified',
        'upozilla' => $user['union_upozilla'] ?? 'Not specified',
        'fatherName' => $user['father_name'] ?? '',
        'motherName' => $user['mother_name'] ?? '',
        'permanentAddress' => $user['permanent_address'] ?? '',
        'lastDonation' => $user['last_donation_date'] ?? date('Y-m-d'),
        'willingToDonate' => (bool) ($user['willing_to_donate'] ?? true),
        'profileImage' => $photo
    ]);
} catch (Exception $e) {
    logError('Profile load error: ' . $e->getMessage());
    jsonResponse(false, 'Failed to load profile');
}
?>