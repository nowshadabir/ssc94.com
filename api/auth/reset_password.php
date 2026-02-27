<?php
/**
 * Reset Password API
 * SSC Batch '94
 */

session_start();
require_once dirname(dirname(__DIR__)) . '/config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

$token = sanitize($_POST['token'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';
$type = sanitize($_POST['type'] ?? 'user');

$email = sanitize($_POST['email'] ?? '');

if (empty($token) || empty($password) || empty($email)) {
    jsonResponse(false, 'Missing required fields');
}

if ($password !== $password_confirm) {
    jsonResponse(false, 'Passwords do not match');
}

if (strlen($password) < PASSWORD_MIN_LENGTH) {
    jsonResponse(false, 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters');
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Verify code against email and type
    if ($type === 'admin') {
        $stmt = $conn->prepare("
            SELECT prt.* FROM password_reset_tokens prt
            JOIN admins a ON prt.admin_id = a.admin_id
            WHERE prt.token = ? AND a.email = ? AND prt.used = 0 AND prt.expires_at > NOW()
            LIMIT 1
        ");
    } else {
        $stmt = $conn->prepare("
            SELECT prt.* FROM password_reset_tokens prt
            JOIN users u ON prt.user_id = u.user_id
            WHERE prt.token = ? AND u.email = ? AND prt.used = 0 AND prt.expires_at > NOW()
            LIMIT 1
        ");
    }

    $stmt->execute([$token, $email]);
    $resetData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$resetData) {
        jsonResponse(false, 'Invalid or expired reset code. Please request a new one.');
    }

    $userId = $resetData['user_id'];
    $adminId = $resetData['admin_id'];

    // Begin transaction
    $conn->beginTransaction();

    $hashedPassword = hashPassword($password);

    if ($type === 'admin' && $adminId) {
        $stmt = $conn->prepare("UPDATE admins SET password_hash = ? WHERE admin_id = ?");
        $stmt->execute([$hashedPassword, $adminId]);
    } else if ($type === 'user' && $userId) {
        $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        $stmt->execute([$hashedPassword, $userId]);
    } else {
        throw new Exception("Invalid token association");
    }

    // Mark token as used
    $stmt = $conn->prepare("UPDATE password_reset_tokens SET used = 1 WHERE token_id = ?");
    $stmt->execute([$resetData['token_id']]);

    $conn->commit();
    jsonResponse(true, 'Password has been reset successfully! You can now log in.');

} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    logError("Reset password error: " . $e->getMessage());
    jsonResponse(false, 'Failed to reset password: ' . $e->getMessage());
}
