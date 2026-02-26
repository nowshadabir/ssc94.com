<?php
/**
 * Get All Admins API
 * Only for Super Admins
 */
require_once '../../config/config.php';
header('Content-Type: application/json');

// Security check
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'super_admin') {
    jsonResponse(false, 'Unauthorized. Super Admin access required.');
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Fetch all admins
    $stmt = $conn->prepare("SELECT admin_id, full_name, email, role, status, permissions, last_login FROM admins ORDER BY admin_id ASC");
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process permissions
    foreach ($admins as &$admin) {
        $admin['permissions'] = $admin['permissions'] ? json_decode($admin['permissions'], true) : [];
    }

    jsonResponse(true, 'Admins fetched successfully', $admins);

} catch (Throwable $e) {
    jsonResponse(false, 'Error fetching admins: ' . $e->getMessage());
}
