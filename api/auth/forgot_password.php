<?php
/**
 * Forgot Password API
 * Handles both users and admins
 * SSC Batch '94
 */

session_start();
require_once dirname(dirname(__DIR__)) . '/config/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/EmailService.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

$email = sanitize($_POST['email'] ?? '');
$type = sanitize($_POST['type'] ?? 'user'); // 'user' or 'admin'

if (empty($email)) {
    jsonResponse(false, 'Email address is required');
}

if (!validateEmail($email)) {
    jsonResponse(false, 'Invalid email address');
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Check if account exists
    if ($type === 'admin') {
        $stmt = $conn->prepare("SELECT admin_id as id, full_name FROM admins WHERE email = ? LIMIT 1");
    } else {
        $stmt = $conn->prepare("SELECT user_id as id, full_name FROM users WHERE email = ? LIMIT 1");
    }

    $stmt->execute([$email]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$account) {
        // For security, don't confirm if account exists or not
        jsonResponse(true, 'If an account exists with this email, you will receive a reset link shortly.');
    }

    $id = $account['id'];
    $name = $account['full_name'];
    $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Invalidate old tokens
    if ($type === 'admin') {
        $stmt = $conn->prepare("UPDATE password_reset_tokens SET used = 1 WHERE admin_id = ?");
    } else {
        $stmt = $conn->prepare("UPDATE password_reset_tokens SET used = 1 WHERE user_id = ?");
    }
    $stmt->execute([$id]);

    // Insert new code
    if ($type === 'admin') {
        $stmt = $conn->prepare("INSERT INTO password_reset_tokens (admin_id, token, expires_at) VALUES (?, ?, ?)");
    } else {
        $stmt = $conn->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
    }
    $stmt->execute([$id, $code, $expires]);

    // Send email
    $mailed = EmailService::sendPasswordReset($email, $name, $code, ($type === 'admin'));

    if ($mailed) {
        jsonResponse(true, 'Reset link sent! Please check your email inbox (and spam folder).');
    } else {
        jsonResponse(false, 'Failed to send reset email. Please contact support.');
    }

} catch (Exception $e) {
    logError("Forgot password error: " . $e->getMessage());
    jsonResponse(false, 'An internal error occurred');
}
