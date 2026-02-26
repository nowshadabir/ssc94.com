<?php
/**
 * Quick User Creation Script
 * SSC Batch '94
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    $name = "Test User";
    $mobile = "01700000000";
    $email = "user@example.com";
    $password = "User@123";
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Generate unique 6-digit user code
    $userCode = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);

    $conn->beginTransaction();

    // 1. Create User
    $stmt = $conn->prepare("
        INSERT INTO users (full_name, mobile, email, password_hash, status, user_code, referral_code) 
        VALUES (?, ?, ?, ?, 'active', ?, ?)
        ON DUPLICATE KEY UPDATE 
            password_hash = VALUES(password_hash),
            status = 'active'
    ");
    $stmt->execute([$name, $mobile, $email, $password_hash, $userCode, $userCode]);

    $userId = $conn->lastInsertId();
    if (!$userId) {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE mobile = ?");
        $stmt->execute([$mobile]);
        $userId = $stmt->fetchColumn();
    }

    // 2. Personal Info (Required for some profile views)
    $stmt = $conn->prepare("
        INSERT INTO user_personal_info (user_id, blood_group, permanent_address) 
        VALUES (?, 'B+', 'Bangladesh')
        ON DUPLICATE KEY UPDATE blood_group = 'B+'
    ");
    $stmt->execute([$userId]);

    // 3. School Info
    $stmt = $conn->prepare("
        INSERT INTO user_school_info (user_id, school_name, zilla, union_upozilla, batch_year) 
        VALUES (?, 'Test School', 'Dhaka', 'Test Upazila', 1994)
        ON DUPLICATE KEY UPDATE batch_year = 1994
    ");
    $stmt->execute([$userId]);

    $conn->commit();

    echo "<h1>User Account Fixed/Created!</h1>";
    echo "<p><strong>Mobile:</strong> $mobile</p>";
    echo "<p><strong>Password:</strong> $password</p>";
    echo "<p><strong>Status:</strong> Active</p>";
    echo "<a href='views/auth/login.html'>Go to Login</a>";

} catch (Exception $e) {
    if (isset($conn))
        $conn->rollBack();
    echo "<h1>Error:</h1><p>" . $e->getMessage() . "</p>";
}
?>