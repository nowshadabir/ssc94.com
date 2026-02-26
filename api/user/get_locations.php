<?php
/**
 * Get Unique Locations API
 * SSC Batch '94
 */

require_once '../../config/config.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Get unique locations from multiple sources
    $locations = [];

    // 1. From Present Info
    $stmt = $conn->query("SELECT DISTINCT current_location FROM user_present_info WHERE current_location IS NOT NULL AND current_location != ''");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $locations[] = trim($row['current_location']);
    }

    // 2. From School Info (Zilla)
    $stmt = $conn->query("SELECT DISTINCT zilla FROM user_school_info WHERE zilla IS NOT NULL AND zilla != ''");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $locations[] = trim($row['zilla']);
    }

    // 3. From School Info (Upozilla)
    $stmt = $conn->query("SELECT DISTINCT union_upozilla FROM user_school_info WHERE union_upozilla IS NOT NULL AND union_upozilla != ''");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $locations[] = trim($row['union_upozilla']);
    }

    // Clean up: unique, sorted, filter out common filler words
    $locations = array_unique($locations);
    sort($locations);

    echo json_encode(array_values($locations));

} catch (Exception $e) {
    echo json_encode([]);
}
