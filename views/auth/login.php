<?php
/**
 * Member Login Page (Traditional PHP Version)
 * SSC Batch '94
 */

require_once '../../config/config.php';

// Show all errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = '';
$success = '';

// Handle Traditional Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_action'])) {
    $mobile = sanitize($_POST['mobile'] ?? '');
    $password = $_POST['password'] ?? '';

    // Simple validation
    if (empty($mobile) || empty($password)) {
        $error = 'Please enter both mobile and password.';
    } else {
        try {
            $db = new Database();
            $conn = $db->getConnection();

            // Clean mobile
            $mobile = preg_replace('/[\s\-]/', '', $mobile);

            $stmt = $conn->prepare("SELECT user_id, full_name, email, password_hash, status FROM users WHERE mobile = ? LIMIT 1");
            $stmt->execute([$mobile]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && verifyPassword($password, $user['password_hash'])) {
                // Check status
                if ($user['status'] === 'pending') {
                    $error = 'Your account is pending payment. Please complete your registration payment first.';
                } elseif ($user['status'] !== 'active') {
                    $error = 'Your account is not active. Please contact support.';
                } else {
                    // Success!
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_name'] = $user['full_name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['logged_in'] = true;

                    // Update last login
                    $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
                    $stmt->execute([$user['user_id']]);

                    header('Location: ../profile.php?login=success');
                    exit;
                }
            } else {
                $error = 'Invalid mobile number or password.';
            }
        } catch (Exception $e) {
            logError("Traditional Login Error: " . $e->getMessage());
            $error = 'A system error occurred. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SSC Batch '94</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Righteous&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
        }

        .brand-font {
            font-family: 'Righteous', cursive;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full">
        <!-- Brand -->
        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-16 h-16 bg-yellow-400 text-slate-900 rounded-2xl font-black text-2xl mb-4 shadow-xl shadow-yellow-400/20">
                94</div>
            <h1 class="text-white text-3xl font-bold brand-font">Member Login</h1>
            <p class="text-slate-400 mt-2">Friends For Friends since 1994</p>
        </div>

        <div class="bg-white rounded-3xl shadow-2xl p-8 overflow-hidden relative">

            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-lg">
                    <strong>Error:</strong>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-6">
                <input type="hidden" name="login_action" value="1">

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Mobile
                        Number</label>
                    <input type="text" name="mobile" required placeholder="01712345678"
                        class="w-full h-14 px-4 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium text-slate-900"
                        value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>">
                </div>

                <div>
                    <label
                        class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Password</label>
                    <input type="password" name="password" required placeholder="••••••••"
                        class="w-full h-14 px-4 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium text-slate-900">
                </div>

                <div class="flex items-center justify-between px-1">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox"
                            class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-slate-600 group-hover:text-slate-900 transition-colors">Remember
                            me</span>
                    </label>
                    <a href="#" class="text-sm font-bold text-indigo-600 hover:text-indigo-500">Forgot?</a>
                </div>

                <button type="submit"
                    class="w-full h-14 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/20 transition-all transform active:scale-95 flex items-center justify-center text-lg">
                    Secure Login
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-slate-100 text-center">
                <p class="text-slate-500 text-sm italic">New to Batch '94?</p>
                <a href="login.html#signup" class="inline-block mt-2 text-indigo-600 font-bold hover:underline">Register
                    New Account</a>
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="../../index.html"
                class="text-slate-500 hover:text-white text-xs font-bold uppercase tracking-widest transition-colors">←
                Back to Homepage</a>
        </div>
    </div>

</body>

</html>