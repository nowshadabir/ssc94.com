<?php
/**
 * Admin Login API
 * SSC Batch '94
 */

require_once '../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    jsonResponse(false, 'Please provide both email and password');
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Fetch admin by email
    $stmt = $conn->prepare("SELECT admin_id, full_name, password_hash, role, status, permissions FROM admins WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        jsonResponse(false, 'Invalid credentials');
    }

    if ($admin['status'] !== 'active') {
        jsonResponse(false, 'Your account is suspended. Please contact the super admin.');
    }

    // Verify password
    if (password_verify($password, $admin['password_hash'])) {
        // Admin session is already started in config.php
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        $_SESSION['admin_role'] = $admin['role'];
        $_SESSION['admin_permissions'] = $admin['permissions'] ? json_decode($admin['permissions'], true) : [];
        $_SESSION['is_admin'] = true;

        // Update last login
        $updateStmt = $conn->prepare("UPDATE admins SET last_login = CURRENT_TIMESTAMP WHERE admin_id = ?");
        $updateStmt->execute([$admin['admin_id']]);

        jsonResponse(true, 'Login successful', [
            'admin_id' => (int) $admin['admin_id'],
            'name' => $admin['full_name'],
            'role' => $admin['role']
        ]);
    } else {
        jsonResponse(false, 'Invalid credentials');
    }

} catch (Throwable $e) {
    logError('Admin login error: ' . $e->getMessage());
    jsonResponse(false, 'Admin login error: ' . $e->getMessage());
}
