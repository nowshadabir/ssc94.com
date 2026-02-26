<?php
require_once 'config/config.php';
try {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("UPDATE admins SET role = 'super_admin' WHERE role = 'Admin' OR role = 'admin' LIMIT 1");
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo "Successfully updated one admin to 'super_admin'.";
    } else {
        echo "No admin found or already updated.";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
