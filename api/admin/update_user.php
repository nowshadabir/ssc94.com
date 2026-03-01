<?php
/**
 * Update User Details - Admin API
 */
require_once '../../config/config.php';

header('Content-Type: application/json');

try {
    if ($_SESSION['admin_role'] !== 'super_admin') {
        jsonResponse(false, 'Only Super Admin can update user details');
    }

    $userId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
    if (!$userId)
        jsonResponse(false, 'User ID is required');

    $db = new Database();
    $conn = $db->getConnection();

    $conn->beginTransaction();

    // 1. Update main users table
    $name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $mobile = sanitize($_POST['mobile']);
    $status = sanitize($_POST['status']);

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, mobile = ?, status = ? WHERE user_id = ?");
    $stmt->execute([$name, $email, $mobile, $status, $userId]);

    // 2. Update user_personal_info
    $father = sanitize($_POST['father_name'] ?? '');
    $mother = sanitize($_POST['mother_name'] ?? '');
    $blood = sanitize($_POST['blood_group'] ?? 'O+');
    $p_addr = sanitize($_POST['permanent_address'] ?? '');
    $dob = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
    $gender = sanitize($_POST['gender'] ?? 'Male');

    $stmt = $conn->prepare("SELECT info_id FROM user_personal_info WHERE user_id = ? LIMIT 1");
    $stmt->execute([$userId]);
    if ($stmt->fetch()) {
        $stmt = $conn->prepare("UPDATE user_personal_info SET father_name = ?, mother_name = ?, blood_group = ?, permanent_address = ?, date_of_birth = ?, gender = ? WHERE user_id = ?");
        $stmt->execute([$father, $mother, $blood, $p_addr, $dob, $gender, $userId]);
    } else {
        $stmt = $conn->prepare("INSERT INTO user_personal_info (user_id, father_name, mother_name, blood_group, permanent_address, date_of_birth, gender) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $father, $mother, $blood, $p_addr, $dob, $gender]);
    }

    // 3. Update user_present_info
    $job = sanitize($_POST['job_business'] ?? '');
    $institute = sanitize($_POST['institute_working_station'] ?? '');
    $cur_loc = sanitize($_POST['current_location'] ?? '');

    $stmt = $conn->prepare("SELECT present_id FROM user_present_info WHERE user_id = ? LIMIT 1");
    $stmt->execute([$userId]);
    if ($stmt->fetch()) {
        $stmt = $conn->prepare("UPDATE user_present_info SET job_business = ?, institute_working_station = ?, current_location = ? WHERE user_id = ?");
        $stmt->execute([$job, $institute, $cur_loc, $userId]);
    } else {
        $stmt = $conn->prepare("INSERT INTO user_present_info (user_id, job_business, institute_working_station, current_location) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $job, $institute, $cur_loc]);
    }

    $conn->commit();
    jsonResponse(true, 'User details updated successfully');

} catch (Exception $e) {
    if ($conn->inTransaction())
        $conn->rollBack();
    jsonResponse(false, 'Error: ' . $e->getMessage());
}
