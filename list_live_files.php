<?php
/**
 * Live File Explorer
 */
header('Content-Type: text/plain');

function listDir($dir, $depth = 0)
{
    if ($depth > 2)
        return;
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..')
            continue;
        $path = $dir . '/' . $file;
        echo str_repeat("  ", $depth) . $file . (is_dir($path) ? '/' : '') . "\n";
        if (is_dir($path)) {
            listDir($path, $depth + 1);
        }
    }
}

echo "Root: " . __DIR__ . "\n\n";
listDir(__DIR__);
?>