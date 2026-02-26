<?php
/**
 * User Registration API
 * SSC Batch '94
 */

session_start();
require_once '../../config/config.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

// Get POST data
$name = sanitize($_POST['name'] ?? '');
$mobile = sanitize($_POST['mobile'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';
$referral_code = sanitize($_POST['referral_code'] ?? '');

// Personal info
$father_name = sanitize($_POST['father_name'] ?? '');
$mother_name = sanitize($_POST['mother_name'] ?? '');
$blood_group = sanitize($_POST['blood_group'] ?? '');
$permanent_address = sanitize($_POST['permanent_address'] ?? '');

// Present info
$job = sanitize($_POST['job'] ?? '');
$institute = sanitize($_POST['institute'] ?? '');
$current_location = sanitize($_POST['current_location'] ?? '');

// School info
$school_name = sanitize($_POST['school_name'] ?? '');
$zilla = sanitize($_POST['zilla'] ?? '');
$upozilla = sanitize($_POST['upozilla'] ?? '');
$batch = 1994;

// Validate required fields
if (empty($name) || empty($mobile) || empty($email) || empty($password)) {
    jsonResponse(false, 'All required fields must be filled');
}

// Validate mobile
if (!validateMobile($mobile)) {
    jsonResponse(false, 'Invalid mobile number');
}

// Validate email
if (!validateEmail($email)) {
    jsonResponse(false, 'Invalid email address');
}

// Validate password
if (strlen($password) < PASSWORD_MIN_LENGTH) {
    jsonResponse(false, 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters');
}

if ($password !== $password_confirm) {
    jsonResponse(false, 'Passwords do not match');
}

// Handle file upload
$profile_photo = null;
if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
    $upload = uploadFile($_FILES['profile_photo'], UPLOAD_PATH . 'profiles/');
    if ($upload['success']) {
        $profile_photo = $upload['filename'];
    } else {
        jsonResponse(false, $upload['message']);
    }
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Check if mobile or email already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE mobile = ? OR email = ?");
    $stmt->execute([$mobile, $email]);
    if ($stmt->fetch()) {
        jsonResponse(false, 'Mobile number or email already registered');
    }

    // Begin transaction
    $conn->beginTransaction();

    // Check if referral code is valid (if provided)
    // Referral code is now the 6-digit user_code
    $referredBy = null;
    if (!empty($referral_code)) {
        $stmt = $conn->prepare("SELECT user_id, balance FROM users WHERE user_code = ?");
        $stmt->execute([$referral_code]);
        $referrer = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($referrer) {
            $referredBy = $referrer['user_id'];
        }
    }

    // Generate unique 6-digit user code
    $userCode = null;
    $maxAttempts = 10;
    for ($i = 0; $i < $maxAttempts; $i++) {
        $userCode = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        // Check if code already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_code = ?");
        $stmt->execute([$userCode]);
        if (!$stmt->fetch()) {
            break; // Unique code found
        }
    }

    // Insert user with user_code as referral_code
    $password_hash = hashPassword($password);
    $stmt = $conn->prepare("
        INSERT INTO users (full_name, mobile, email, password_hash, profile_photo, status, user_code, referral_code, referred_by, balance) 
        VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, ?, 0.00)
    ");
    $stmt->execute([$name, $mobile, $email, $password_hash, $profile_photo, $userCode, $userCode, $referredBy]);
    $user_id = $conn->lastInsertId();

    // Insert personal info
    if (!empty($blood_group) && !empty($permanent_address)) {
        $stmt = $conn->prepare("
            INSERT INTO user_personal_info (user_id, father_name, mother_name, blood_group, permanent_address) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $father_name, $mother_name, $blood_group, $permanent_address]);
    }

    // Insert present info
    if (!empty($job) && !empty($institute) && !empty($current_location)) {
        $stmt = $conn->prepare("
            INSERT INTO user_present_info (user_id, job_business, institute_working_station, current_location) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $job, $institute, $current_location]);
    }

    // Insert school info
    if (!empty($school_name) && !empty($zilla) && !empty($upozilla)) {
        $stmt = $conn->prepare("
            INSERT INTO user_school_info (user_id, school_name, zilla, union_upozilla, batch_year) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $school_name, $zilla, $upozilla, $batch]);
    }

    // Commit transaction
    $conn->commit();

    // Set session for payment
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;

    // Clear form cache
    jsonResponse(true, 'Registration successful! Redirecting to payment...', [
        'user_id' => $user_id,
        'redirect' => 'payment',
        'next_step' => 'initiate_payment'
    ]);

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    logError('Registration error: ' . $e->getMessage());
    jsonResponse(false, 'Registration failed. Please try again.');
}
?>