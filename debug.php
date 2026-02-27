<?php
/**
 * Super Debugger - SSC Batch '94
 * This tool inspects the LIVE environment specifically to find SMTP and DB issues.
 */
require_once 'config/config.php';

header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Live Debugger - SSC 94</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            color: #333;
            padding: 40px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            color: #1a73e8;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-top: 0;
        }

        .section {
            margin-bottom: 30px;
        }

        .label {
            font-weight: bold;
            color: #555;
            width: 140px;
            display: inline-block;
        }

        .value {
            font-family: monospace;
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .status-ok {
            color: green;
            font-weight: bold;
        }

        .status-err {
            color: red;
            font-weight: bold;
        }

        .log-box {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 12px;
            overflow-x: auto;
            white-space: pre-wrap;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <div class="card">
        <h1>System Diagnostic Tool</h1>

        <div class="section">
            <h3>1. Path & Environment Status</h3>
            <p><span class="label">Base Path:</span> <span class="value">
                    <?php echo BASE_PATH; ?>
                </span></p>
            <p><span class="label">.env File:</span>
                <?php
                $envPath = BASE_PATH . '/.env';
                if (file_exists($envPath)) {
                    echo "<span class='status-ok'>✔ Found</span> (" . filesize($envPath) . " bytes)";
                } else {
                    echo "<span class='status-err'>✘ NOT FOUND</span> - Email/DB will fail!";
                }
                ?>
            </p>
            <p><span class="label">SITE_URL:</span> <span class="value">
                    <?php echo SITE_URL; ?>
                </span></p>
        </div>

        <div class="section">
            <h3>2. SMTP Inspection (The Priority)</h3>
            <p>This shows exactly what the code is currently using to send emails.</p>
            <p><span class="label">Host:</span> <span class="value">
                    <?php echo SMTP_HOST; ?>
                </span>
                <?php if (SMTP_HOST == 'smtp.gmail.com')
                    echo " <small style='color:orange;'>(Using server default Gmail)</small>"; ?>
            </p>
            <p><span class="label">Port:</span> <span class="value">
                    <?php echo SMTP_PORT; ?>
                </span></p>
            <p><span class="label">User:</span> <span class="value">
                    <?php echo SMTP_USER; ?>
                </span></p>
            <p><span class="label">Pass Set:</span> <span class="value">
                    <?php echo empty(SMTP_PASS) ? 'NO' : 'YES (Confidental)'; ?>
                </span></p>
        </div>

        <div class="section">
            <h3>3. Database Connection</h3>
            <?php
            try {
                $db = new Database();
                $conn = $db->getConnection();
                echo "<p class='status-ok'>✔ Connection Successful!</p>";
                echo "<p><span class='label'>Database:</span> <span class='value'>" . DB_NAME . "</span></p>";
            } catch (Exception $e) {
                echo "<p class='status-err'>✘ Connection Failed:</p>";
                echo "<div class='log-box'>" . $e->getMessage() . "</div>";
            }
            ?>
        </div>

        <div class="section">
            <h3>4. Error Log (Last 15 Lines)</h3>
            <p>The real secret of why email fails is here:</p>
            <div class="log-box">
                <?php
                $logFile = BASE_PATH . '/logs/error.log';
                if (file_exists($logFile)) {
                    $lines = file($logFile);
                    $lastLines = array_slice($lines, -15);
                    echo htmlspecialchars(implode("", $lastLines));
                } else {
                    echo "Log file not found at: " . $logFile;
                }
                ?>
            </div>
        </div>

        <div style="text-align: center; margin-top: 40px; font-size: 11px; color: #999;">
            Please DELETE this file (debug.php) after checking for security.
        </div>
    </div>

</body>

</html>