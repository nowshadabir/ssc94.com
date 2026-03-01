<?php
/**
 * Update Profile API
 * SSC Batch '94
 */

require_once '../../config/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    jsonResponse(false, 'Unauthorized');
}

$userId = $_SESSION['user_id'];

$name = sanitize($_POST['name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$mobile = sanitize($_POST['mobile'] ?? '');
$job = sanitize($_POST['job'] ?? '');
$institute = sanitize($_POST['institute'] ?? '');
$currentLocation = sanitize($_POST['current_location'] ?? '');
$linkedin = sanitize($_POST['linkedin'] ?? '');
$facebook = sanitize($_POST['facebook'] ?? '');
$bloodGroup = sanitize($_POST['blood_group'] ?? '');
$schoolName = sanitize($_POST['school_name'] ?? '');
$zilla = sanitize($_POST['zilla'] ?? '');
$upozilla = sanitize($_POST['upozilla'] ?? '');
$fatherName = sanitize($_POST['father_name'] ?? '');
$motherName = sanitize($_POST['mother_name'] ?? '');
$permanentAddress = sanitize($_POST['permanent_address'] ?? '');
$gender = sanitize($_POST['gender'] ?? 'Male');
$dob = sanitize($_POST['dob'] ?? '');
$lastDonation = sanitize($_POST['last_donation_date'] ?? '');
$willingToDonate = isset($_POST['willing_to_donate']) && $_POST['willing_to_donate'] === '1' ? 1 : 0;

if (empty($name) || empty($email) || empty($mobile)) {
    jsonResponse(false, 'Please provide Name, Email and Mobile.');
}

if (!validateEmail($email)) {
    jsonResponse(false, 'Invalid email address');
}

if (!validateMobile($mobile)) {
    jsonResponse(false, 'Mobile number must be 11 digits');
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    $photoPath = null;
    if (!empty($_FILES['profile_image']['name'])) {
        $profilesDir = UPLOAD_PATH . 'profiles/';
        if (!is_dir($profilesDir)) {
            mkdir($profilesDir, 0775, true);
        }
        $uploadResult = uploadFile($_FILES['profile_image'], $profilesDir);
        if (!$uploadResult['success']) {
            jsonResponse(false, $uploadResult['message']);
        }
        $photoPath = $uploadResult['filename'];
    }

    $conn->beginTransaction();

    if ($photoPath) {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, mobile = ?, profile_photo = ? WHERE user_id = ?");
        $stmt->execute([$name, $email, $mobile, $photoPath, $userId]);
    } else {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, mobile = ? WHERE user_id = ?");
        $stmt->execute([$name, $email, $mobile, $userId]);
    }

    $stmt = $conn->prepare("SELECT info_id FROM user_personal_info WHERE user_id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $personalExists = (bool) $stmt->fetchColumn();

    $personalFields = [
        'father_name' => $fatherName,
        'mother_name' => $motherName,
        'blood_group' => !empty($bloodGroup) ? $bloodGroup : 'O+',
        'permanent_address' => $permanentAddress,
        'date_of_birth' => !empty($dob) ? $dob : null,
        'gender' => $gender,
        'last_donation' => !empty($lastDonation) ? $lastDonation : null,
        'willing_to_donate' => $willingToDonate
    ];

    if ($personalExists) {
        $setParts = [];
        $values = [];
        foreach ($personalFields as $field => $value) {
            $setParts[] = "$field = ?";
            $values[] = $value;
        }
        $values[] = $userId;
        $stmt = $conn->prepare("UPDATE user_personal_info SET " . implode(', ', $setParts) . " WHERE user_id = ?");
        $stmt->execute($values);
    } else {
        $fields = array_merge(['user_id' => $userId], $personalFields);
        $columns = implode(', ', array_keys($fields));
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $stmt = $conn->prepare("INSERT INTO user_personal_info ($columns) VALUES ($placeholders)");
        $stmt->execute(array_values($fields));
    }

    $stmt = $conn->prepare("SELECT present_id FROM user_present_info WHERE user_id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $presentExists = (bool) $stmt->fetchColumn();

    if ($presentExists) {
        $stmt = $conn->prepare("UPDATE user_present_info SET job_business = ?, institute_working_station = ?, current_location = ?, linkedin_profile = ?, facebook_profile = ? WHERE user_id = ?");
        $stmt->execute([$job, $institute, $currentLocation, $linkedin, $facebook, $userId]);
    } else {
        $stmt = $conn->prepare("INSERT INTO user_present_info (user_id, job_business, institute_working_station, current_location, linkedin_profile, facebook_profile) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $job, $institute, $currentLocation, $linkedin, $facebook]);
    }

    $stmt = $conn->prepare("SELECT school_id, batch_year FROM user_school_info WHERE user_id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $schoolRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $batchYear = $schoolRow['batch_year'] ?? 1994;

    if ($schoolRow) {
        $stmt = $conn->prepare("UPDATE user_school_info SET school_name = ?, zilla = ?, union_upozilla = ?, batch_year = ? WHERE user_id = ?");
        $stmt->execute([$schoolName, $zilla, $upozilla, $batchYear, $userId]);
    } else {
        $stmt = $conn->prepare("INSERT INTO user_school_info (user_id, school_name, zilla, union_upozilla, batch_year) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $schoolName, $zilla, $upozilla, $batchYear]);
    }

    $conn->commit();

    // Fetch latest user data for response
    $stmt = $conn->prepare("SELECT user_code, balance, profile_photo FROM users WHERE user_id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

    $photo = $userRow['profile_photo'] ?? '';
    if (empty($photo)) {
        $photo = 'https://i.pravatar.cc/300?u=' . $userId;
    } else {
        $isHttp = strpos($photo, 'http://') === 0 || strpos($photo, 'https://') === 0;
        if (!$isHttp) {
            $photo = '../assets/uploads/profiles/' . $photo;
        }
    }

    jsonResponse(true, 'Profile updated', [
        'id' => $userId,
        'memberId' => $userRow['user_code'] ?? 'N/A',
        'balance' => $userRow['balance'] ?? '0.00',
        'name' => $name,
        'email' => $email,
        'mobile' => $mobile,
        'job' => $job,
        'institute' => $institute,
        'currentLocation' => $currentLocation,
        'linkedin' => $linkedin,
        'facebook' => $facebook,
        'bloodGroup' => $bloodGroup,
        'schoolName' => $schoolName,
        'zilla' => $zilla,
        'upozilla' => $upozilla,
        'fatherName' => $fatherName,
        'motherName' => $motherName,
        'permanentAddress' => $permanentAddress,
        'gender' => $gender,
        'dob' => $dob,
        'lastDonation' => $lastDonation,
        'willingToDonate' => (bool) $willingToDonate,
        'profileImage' => $photo
    ]);
} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    logError('Profile update error: ' . $e->getMessage());
    jsonResponse(false, 'Failed to update profile');
}
?>