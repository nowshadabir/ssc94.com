<?php
/**
 * Admin Account Creation / Fix Script
 * SSC Batch '94
 */
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // 1. Ensure Admins table exists
    $conn->exec("CREATE TABLE IF NOT EXISTS admins (
        admin_id INT PRIMARY KEY AUTO_INCREMENT,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
        status ENUM('active', 'inactive') DEFAULT 'active',
        last_login TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $name = "Global Admin";
    $email = "admin@ssc94.com";
    $password = "Admin@123";
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // 2. Insert or Update Admin
    $stmt = $conn->prepare("
        INSERT INTO admins (full_name, email, password_hash, role, status) 
        VALUES (?, ?, ?, 'super_admin', 'active')
        ON DUPLICATE KEY UPDATE 
            password_hash = VALUES(password_hash),
            status = 'active'
    ");
    $stmt->execute([$name, $email, $password_hash]);

    echo "<h1>Admin Account Fixed/Created!</h1>";
    echo "<p><strong>Email:</strong> $email</p>";
    echo "<p><strong>Password:</strong> $password</p>";
    echo "<p><strong>Role:</strong> Super Admin</p>";
    echo "<a href='views/auth/admin_login.html'>Go to Admin Login</a>";

} catch (Exception $e) {
    echo "<h1>Error:</h1><p>" . $e->getMessage() . "</p>";
}
?>