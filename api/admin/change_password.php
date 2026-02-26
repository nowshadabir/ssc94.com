<?php
/**
 * Change Admin Password API
 * SSC Batch '94
 */

require_once '../../config/config.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true || !isset($_SESSION['admin_id'])) {
    jsonResponse(false, 'Unauthorized access');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

$adminId = $_SESSION['admin_id'];
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    jsonResponse(false, 'Please fill in all fields');
}

if ($newPassword !== $confirmPassword) {
    jsonResponse(false, 'New passwords do not match');
}

if (strlen($newPassword) < 6) {
    jsonResponse(false, 'New password must be at least 6 characters long');
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Fetch admin's current password hash
    $stmt = $conn->prepare("SELECT password_hash FROM admins WHERE admin_id = ? LIMIT 1");
    $stmt->execute([$adminId]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        jsonResponse(false, 'Admin not found');
    }

    // Verify current password
    if (!password_verify($currentPassword, $admin['password_hash'])) {
        jsonResponse(false, 'Incorrect current password');
    }

    // Hash the new password
    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password in database
    $updateStmt = $conn->prepare("UPDATE admins SET password_hash = ?, updated_at = CURRENT_TIMESTAMP WHERE admin_id = ?");
    if ($updateStmt->execute([$newPasswordHash, $adminId])) {
        jsonResponse(true, 'Password updated successfully');
    } else {
        jsonResponse(false, 'Failed to update password');
    }

} catch (Throwable $e) {
    logError('Password change error: ' . $e->getMessage());
    jsonResponse(false, 'Database error or connection failure');
}
