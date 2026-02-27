<?php
/**
 * Git Deploy Fixer
 * This script forces the local server repository to reset and clear any blocking changes.
 */

header('Content-Type: text/plain');

echo "GIT RESET TOOL\n";
echo "==============\n\n";

// Attempt to reset git state
$commands = [
    'git status',
    'git reset --hard HEAD',
    'git checkout config/env_loader.php',
    'git status'
];

foreach ($commands as $cmd) {
    echo "Running: $cmd\n";
    $output = [];
    $resultCode = 0;
    exec($cmd . " 2>&1", $output, $resultCode);
    echo implode("\n", $output) . "\n";
    echo "Result Code: $resultCode\n";
    echo "--------------------------\n";
}

echo "\nDone. Now try to Deploy again in your cPanel Git Version Control interface.\n";
echo "IMPORTANT: Delete this file after use for security!";
