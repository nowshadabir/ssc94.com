<?php
require_once 'config/config.php';
try {
    $db = new Database();
    $conn = $db->getConnection();
    // Check if permissions column exists
    $stmt = $conn->query("SHOW COLUMNS FROM admins LIKE 'permissions'");
    $exists = $stmt->fetch();
    if (!$exists) {
        $conn->exec("ALTER TABLE admins ADD COLUMN permissions TEXT DEFAULT NULL");
        echo "Column 'permissions' added successfully.";
    } else {
        echo "Column 'permissions' already exists.";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
