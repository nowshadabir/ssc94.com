<?php
/**
 * Update Admin Permissions API
 * Only for Super Admins
 */
require_once '../../config/config.php';
header('Content-Type: application/json');

// Security check
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'super_admin') {
    jsonResponse(false, 'Unauthorized. Super Admin access required.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

$admin_id = (int) ($_POST['admin_id'] ?? 0);
$permissions = $_POST['permissions'] ?? []; // Array of permission strings
$status = sanitize($_POST['status'] ?? 'active');

if (!$admin_id) {
    jsonResponse(false, 'Admin ID is required');
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Check if target is super_admin
    $checkStmt = $conn->prepare("SELECT role FROM admins WHERE admin_id = ?");
    $checkStmt->execute([$admin_id]);
    $target = $checkStmt->fetch();

    if ($target && $target['role'] === 'super_admin' && $admin_id !== $_SESSION['admin_id']) {
        jsonResponse(false, 'Cannot modify another super admin');
    }

    $permissionsJson = json_encode($permissions);

    $stmt = $conn->prepare("UPDATE admins SET permissions = ?, status = ? WHERE admin_id = ?");
    $stmt->execute([$permissionsJson, $status, $admin_id]);

    jsonResponse(true, 'Permissions updated successfully');

} catch (Throwable $e) {
    jsonResponse(false, 'Error updating permissions: ' . $e->getMessage());
}
