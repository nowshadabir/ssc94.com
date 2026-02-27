<?php
/**
 * Password Reset Debugger
 * Use this to verify why emails might be failing
 */
require_once 'config/config.php';
require_once 'includes/EmailService.php';

echo "<h2>Password Reset Debugger</h2>";
echo "<p>This script will test if a specific email exists in the database and attempt to send a test code.</p>";

$testEmail = $_GET['email'] ?? 'knabirofficial@gmail.com';
$testType = $_GET['type'] ?? 'admin';

echo "<div style='background: #f1f5f9; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>";
echo "<strong>Testing:</strong> $testEmail ($testType)<br>";
echo "<strong>Tip:</strong> Add ?email=your@email.com&type=user to the URL to test others.";
echo "</div>";

try {
    $db = new Database();
    $conn = $db->getConnection();

    // 1. Check Database Account
    echo "<h3>1. Database Check</h3>";
    if ($testType === 'admin') {
        $stmt = $conn->prepare("SELECT admin_id, full_name, email FROM admins WHERE email = ?");
    } else {
        $stmt = $conn->prepare("SELECT user_id, full_name, email FROM users WHERE email = ?");
    }
    $stmt->execute([$testEmail]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($account) {
        echo "<span style='color: green;'>✔ Account found!</span> Name: " . $account['full_name'] . "<br>";
    } else {
        echo "<span style='color: red;'>✘ Account NOT found in database.</span><br>";
        echo "<strong>Actual Query:</strong> Check " . ($testType === 'admin' ? 'admins' : 'users') . " table for email '$testEmail'<br>";
    }

    // 2. SMTP Connectivity Check
    echo "<h3>2. SMTP Config Check</h3>";
    echo "Host: " . SMTP_HOST . "<br>";
    echo "Port: " . SMTP_PORT . "<br>";
    echo "User: " . SMTP_USER . "<br>";

    // 3. Attempt Send
    echo "<h3>3. Live Send Test</h3>";
    if ($account) {
        echo "Attempting to send a real 6-digit code...<br>";
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // We'll temporarily capture logError messages by overriding the error_log or just checking our custom log
        $success = EmailService::sendPasswordReset($testEmail, $account['full_name'], $code, ($testType === 'admin'));

        if ($success) {
            echo "<h3 style='color: green;'>✔ Success!</h3> The email server accepted the message. Check your inbox.";
        } else {
            echo "<h3 style='color: red;'>✘ Failed!</h3> The email was not sent. Check <strong>logs/error.log</strong> for the specific SMTP error.";
        }
    } else {
        echo "Skipping send test because account doesn't exist.";
    }

} catch (Exception $e) {
    echo "<h3 style='color: red;'>Critical Error:</h3> " . $e->getMessage();
}
