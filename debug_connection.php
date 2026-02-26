<?php
/**
 * Live Deployment Connection Debugger
 * SSC Batch '94
 */

// Enable error reporting for this script to catch issues
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/config.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Debugger - Batch '94</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .mono {
            font-family: 'Space Mono', monospace;
        }
    </style>
</head>

<body class="bg-slate-900 text-slate-300 min-h-screen flex items-center justify-center p-6">

    <div class="max-w-2xl w-full bg-slate-800 rounded-2xl shadow-2xl border border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-slate-700 bg-slate-800/50 flex justify-between items-center">
            <h1 class="text-xl font-bold text-white flex items-center">
                <span class="w-3 h-3 bg-blue-500 rounded-full mr-3 animate-pulse"></span>
                System Diagnostics
            </h1>
            <span class="text-xs font-mono text-slate-500">v1.0.live</span>
        </div>

        <div class="p-8 space-y-6">

            <!-- Connection Test -->
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-4">Database Connection</h3>
                <?php
                try {
                    $db = new Database();
                    $conn = $db->getConnection();

                    if ($conn) {
                        echo '
                        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4 flex items-center space-x-4">
                            <div class="bg-emerald-500 rounded-full p-2">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-emerald-400 font-bold">Connection Successful!</h4>
                                <p class="text-sm text-emerald-400/70">Handshake established with MySQL server.</p>
                            </div>
                        </div>';
                    }
                } catch (Exception $e) {
                    echo '
                    <div class="bg-rose-500/10 border border-rose-500/20 rounded-xl p-4 flex items-center space-x-4">
                        <div class="bg-rose-500 rounded-full p-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-rose-400 font-bold">Connection Failed</h4>
                            <p class="text-xs text-rose-300/70 mono mt-1">' . htmlspecialchars($e->getMessage()) . '</p>
                        </div>
                    </div>';
                }
                ?>
            </div>

            <!-- Environment Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-slate-900/50 rounded-xl p-4 border border-slate-700/50">
                    <span class="text-[10px] font-bold text-slate-500 uppercase">PHP Version</span>
                    <p class="text-white font-mono">
                        <?php echo phpversion(); ?>
                    </p>
                </div>
                <div class="bg-slate-900/50 rounded-xl p-4 border border-slate-700/50">
                    <span class="text-[10px] font-bo
                        ld text-slate-500 uppercase">DB Host (Config)</span>
                    <p class="text-white font-mono">
                        <?php echo defined('DB_HOST') ? DB_HOST : 'Not Defined'; ?>
                    </p>
                </div>
                <div class="bg-slate-900/50 rounded-
                        xl p-4 border border-slate-700/50">
                    <span class="text-[10px] font-bold text-slate-500 uppercase">Database Name</span>
                    <p class="text-white font-mono">
                        <?php echo defined('DB_NAME') ? DB_NAME : 'Not Defined'; ?>
                    </p>
                </div>

                                        <div class="bg-slate-900/50 rounded-xl p-4 border border-slate-700/50">
                    <span class="text-[10px] font-bold text-slate-500 uppercase">Protocol</span>
                    <p class="text-white font-mono">
                        <?php echo isset($_SERVER['HTTPS']) ? 'HTTPS' : 'HTTP'; ?>
                    </p>
                </div>
            </div>

            <!-- Security Warning -->
            <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-amber-500 mr-3 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <div>
                        <h4 class="text-amber-500 font-bold text-sm uppercase italic">Live Security Warning</h4>
                        <p class="text-xs text-amber-400/80 leading-relaxed mt-1">
                            For security reasons, <strong>DELETE THIS FILE</strong> immediately after your connection is
                            confirmed. Leaving this file on your live server can reveal sensitive path information.
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <div class="p-4 bg-slate-900/30 text-center">
            <a href="index.html"
                class="text-xs text-slate-500 hover:text-white transition uppercase font-bold tracking-widest">Back to
                Home</a>
        </div>
    </div>

</body>

</html>