<?php
require_once 'config/config.php';
try {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->query("SELECT admin_id, email, role FROM admins");
    $admins = $stmt->fetchAll();
    echo json_encode($admins);
} catch (Exception $e) {
    echo $e->getMessage();
}
