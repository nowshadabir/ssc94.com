<?php
/**
 * Admin Registration API
 * SSC Batch '94
 */

require_once '../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

// Extract and sanitize inputs
$fullName = sanitize($_POST['full_name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$mobile = sanitize($_POST['mobile'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$role = sanitize($_POST['role'] ?? 'admin');

// Basic Validation
if (empty($fullName) || empty($email) || empty($mobile) || empty($password)) {
    jsonResponse(false, 'All required fields must be filled');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(false, 'Invalid email format');
}

if ($password !== $confirmPassword) {
    jsonResponse(false, 'Passwords do not match');
}

if (strlen($password) < 6) {
    jsonResponse(false, 'Password must be at least 6 characters long');
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Check if admin already exists
    $stmt = $conn->prepare("SELECT admin_id FROM admins WHERE email = ? OR mobile = ? LIMIT 1");
    $stmt->execute([$email, $mobile]);
    if ($stmt->fetch()) {
        jsonResponse(false, 'Admin with this email or mobile already exists');
    }

    // Handle Image Upload
    $photoPath = '';
    if (isset($_FILES['admin_photo']) && $_FILES['admin_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../assets/uploads/profiles/'; // Using existing profiles dir for simplicity
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExtension = strtolower(pathinfo($_FILES['admin_photo']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $fileName = uniqid('admin_', true) . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['admin_photo']['tmp_name'], $targetPath)) {
                $photoPath = $fileName;
            }
        }
    }

    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert Admin
    $stmt = $conn->prepare("
        INSERT INTO admins (full_name, email, mobile, password_hash, profile_photo, role, status)
        VALUES (?, ?, ?, ?, ?, ?, 'active')
    ");

    $success = $stmt->execute([
        $fullName,
        $email,
        $mobile,
        $passwordHash,
        $photoPath,
        $role
    ]);

    if ($success) {
        jsonResponse(true, 'Admin account created successfully. You can now log in.');
    } else {
        jsonResponse(false, 'Failed to create admin account');
    }

} catch (Exception $e) {
    logError('Admin registration error: ' . $e->getMessage());
    jsonResponse(false, 'An internal server error occurred');
}
