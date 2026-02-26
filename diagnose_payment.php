<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    echo "--- Payment Gateway Settings ---\n";
    $stmt = $conn->query("SELECT * FROM payment_gateway_settings");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }

    echo "\n--- Recent Payments ---\n";
    $stmt = $conn->query("SELECT * FROM payments ORDER BY created_at DESC LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }

    echo "\n--- Recent Users ---\n";
    $stmt = $conn->query("SELECT user_id, full_name, mobile, status, user_code FROM users ORDER BY created_at DESC LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>