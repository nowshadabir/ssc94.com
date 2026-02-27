<?php
/**
 * Diagnostic tool to check environment variable loading
 */
require_once 'config/env_loader.php';

header('Content-Type: text/plain');

echo "ENVIRONMENT LOADING DIAGNOSTIC\n";
echo "============================\n\n";

$envPath = __DIR__ . '/.env';
echo "Checking .env file path: $envPath\n";
if (file_exists($envPath)) {
    echo "FILE EXISTS: YES\n";
    echo "FILE SIZE: " . filesize($envPath) . " bytes\n";
    echo "FILE PERMISSIONS: " . substr(sprintf('%o', fileperms($envPath)), -4) . "\n";

    echo "\nRAW CONTENT PREVIEW (Keys only):\n";
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0)
            continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            echo trim($name) . "= [REDACTED]\n";
        }
    }
} else {
    echo "FILE EXISTS: NO (This is why it's falling back or using server defaults)\n";
}

echo "\nCURRENT LOADED VALUES (Masked):\n";
$keys_to_check = ['SMTP_HOST', 'SMTP_PORT', 'SMTP_USER', 'SMTP_PASS', 'DB_HOST', 'DB_NAME'];
foreach ($keys_to_check as $key) {
    $val = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?? 'NOT SET';
    echo "$key: ";
    if ($val === 'NOT SET') {
        echo "NOT SET\n";
    } else {
        // Show first 3 and last 3 chars if long enough
        if (strlen($val) > 6) {
            echo substr($val, 0, 3) . "..." . substr($val, -3) . " (" . strlen($val) . " chars)\n";
        } else {
            echo "*** (" . strlen($val) . " chars)\n";
        }
    }
}

echo "\nSERVER INFO:\n";
echo "PHP Version: " . phpversion() . "\n";
echo "SAPI: " . php_sapi_name() . "\n";
