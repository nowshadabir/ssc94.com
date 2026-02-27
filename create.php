<?php
/**
 * Bypass Registration Tool
 * Creates an active user directly without payment
 * SSC Batch '94
 */

require_once 'config/config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $mobile = sanitize($_POST['mobile'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($mobile) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        try {
            $db = new Database();
            $conn = $db->getConnection();

            // Check if exists
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE mobile = ? OR email = ?");
            $stmt->execute([$mobile, $email]);
            if ($stmt->fetch()) {
                $error = "Mobile or Email already exists.";
            } else {
                $conn->beginTransaction();

                // Hash password
                $password_hash = hashPassword($password);

                // Generate unique 6-digit user code
                $userCode = null;
                for ($i = 0; $i < 10; $i++) {
                    $code = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
                    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_code = ?");
                    $stmt->execute([$code]);
                    if (!$stmt->fetch()) {
                        $userCode = $code;
                        break;
                    }
                }

                // Insert user as active
                $stmt = $conn->prepare("
                    INSERT INTO users (full_name, mobile, email, password_hash, status, user_code, balance) 
                    VALUES (?, ?, ?, ?, 'active', ?, 0.00)
                ");
                $stmt->execute([$name, $mobile, $email, $password_hash, $userCode]);
                $user_id = $conn->lastInsertId();

                // Insert required related info
                $stmt = $conn->prepare("INSERT INTO user_personal_info (user_id, blood_group, permanent_address) VALUES (?, 'O+', 'Default Address')");
                $stmt->execute([$user_id]);

                $stmt = $conn->prepare("INSERT INTO user_present_info (user_id, job_business, institute_working_station, current_location) VALUES (?, 'Bypasser', 'System', 'Dhaka')");
                $stmt->execute([$user_id]);

                $stmt = $conn->prepare("INSERT INTO user_school_info (user_id, school_name, zilla, union_upozilla, batch_year) VALUES (?, 'System School', 'Dhaka', 'Dhaka', 1994)");
                $stmt->execute([$user_id]);

                $conn->commit();
                $message = "User created successfully! <br><strong>Name:</strong> $name <br><strong>Mobile:</strong> $mobile <br><strong>Code:</strong> $userCode <br><strong>Status:</strong> Active. <br><a href='views/auth/login.html' style='color:#0ea5e9; font-weight:bold;'>Login Now</a>";
            }
        } catch (Exception $e) {
            if (isset($conn) && $conn->inTransaction())
                $conn->rollBack();
            $error = "System Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bypass Registration - SSC Batch '94</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&family=Inter:wght@400;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a;
        }

        .brand-font {
            font-family: 'Righteous', cursive;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden">
        <div class="bg-indigo-600 p-8 text-center">
            <h1 class="text-3xl font-bold text-white mb-2 brand-font">Bypass Key</h1>
            <p class="text-indigo-100 text-sm">Create an active account without paying</p>
        </div>

        <div class="p-8">
            <?php if ($message): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl mb-6 text-sm">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl mb-6 text-sm">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Full
                        Name</label>
                    <input type="text" name="name" required placeholder="Your Name"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Mobile
                        Number</label>
                    <input type="text" name="mobile" required placeholder="017..."
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition">
                </div>
                <div>
                    <label
                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Email</label>
                    <input type="email" name="email" required placeholder="you@example.com"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition">
                </div>
                <div>
                    <label
                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Password</label>
                    <input type="password" name="password" required placeholder="Min 6 characters"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition">
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                    Generate Active Account
                </button>
            </form>

            <p class="text-center text-[10px] text-slate-400 mt-6 uppercase tracking-widest">Dev Tool — Use with caution
            </p>
        </div>
    </div>
</body>

</html>