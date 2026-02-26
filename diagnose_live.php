<?php
/**
 * Live Server Environment Diagnostician
 * Run this on your live server to find the cause of 500 errors.
 */

// Force display errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>SSC Batch '94 - Live Diagnostics</h1>";

echo "<h2>1. Path Checks</h2>";
$root = __DIR__;
echo "Current Directory: " . $root . "<br>";
echo "Config Path: " . $root . "/config/config.php - " . (file_exists($root . "/config/config.php") ? "EXISTS" : "MISSING") . "<br>";
echo ".env Path: " . $root . "/.env - " . (file_exists($root . "/.env") ? "EXISTS" : "MISSING") . "<br>";

echo "<h2>2. PHP Environment</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "putenv() enabled: " . (function_exists('putenv') ? "YES" : "NO") . "<br>";
echo "PDO MySQL enabled: " . (extension_loaded('pdo_mysql') ? "YES" : "NO") . "<br>";

echo "<h2>3. Loading Config...</h2>";
try {
    require_once 'config/config.php';
    echo "Config loaded successfully!<br>";
} catch (Throwable $e) {
    echo "<b>FATAL ERROR LOADING CONFIG:</b> " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "<br>";
}

echo "<h2>4. Testing Database...</h2>";
try {
    if (class_exists('Database')) {
        $db = new Database();
        $conn = $db->getConnection();
        if ($conn) {
            echo "Database connection successful!<br>";
            $stmt = $conn->query("SELECT COUNT(*) FROM users");
            echo "User count: " . $stmt->fetchColumn() . "<br>";
        } else {
            echo "<b>Database connection failed!</b> (getConnection returned null)<br>";
        }
    } else {
        echo "<b>Database class not found!</b><br>";
    }
} catch (Throwable $e) {
    echo "<b>FATAL DATABASE ERROR:</b> " . $e->getMessage() . "<br>";
}

echo "<h2>5. Test JSON Output</h2>";
echo "Testing if any output happened before JSON header...<br>";
// End of test
?>