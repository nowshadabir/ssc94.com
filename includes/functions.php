<?php
/**
 * Common Functions
 * SSC Batch '94 - Helper Functions
 */

/**
 * Sanitize input data
 */
function sanitize($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Generate CSRF token
 */
function generateCSRFToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check if user is logged in
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if admin is logged in and has required permissions
 */
function requireAdmin($permission = null)
{
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header("Location: ../auth/admin_login.html");
        exit();
    }

    if ($permission && !hasPermission($permission)) {
        header("Location: dashboard.php?error=unauthorized");
        exit();
    }
}

/**
 * Check if current admin has a specific permission
 */
function hasPermission($permission)
{
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        return false;
    }

    // Super admin has all permissions
    if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin') {
        return true;
    }

    $permissions = $_SESSION['admin_permissions'] ?? [];
    return in_array($permission, $permissions);
}

/**
 * API check for admin permissions - returns JSON error if unauthorized
 */
function checkAdminAction($permission = null)
{
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        jsonResponse(false, 'Unauthorized. Please log in again.');
    }

    if ($permission && !hasPermission($permission)) {
        jsonResponse(false, 'Unauthorized. You do not have permission for this action.');
    }
}

/**
 * Redirect to URL
 */
function redirect($url)
{
    header("Location: " . $url);
    exit();
}

/**
 * Send JSON response
 */
function jsonResponse($success, $message, $data = null)
{
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

/**
 * Validate mobile number (Bangladesh)
 */
function validateMobile($mobile)
{
    return preg_match('/^01[3-9]\d{8}$/', $mobile);
}

/**
 * Validate email
 */
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Hash password
 */
function hashPassword($password)
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash)
{
    return password_verify($password, $hash);
}

/**
 * Generate random token
 */
function generateToken($length = 32)
{
    return bin2hex(random_bytes($length));
}

/**
 * Upload file
 */
function uploadFile($file, $destination)
{
    $allowed = ALLOWED_EXTENSIONS;
    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];

    if ($fileError !== 0) {
        return ['success' => false, 'message' => 'Error uploading file'];
    }

    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExt, $allowed)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }

    if ($fileSize > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File too large'];
    }

    $newFileName = uniqid('', true) . '.' . $fileExt;
    $uploadPath = $destination . $newFileName;

    if (move_uploaded_file($fileTmp, $uploadPath)) {
        return ['success' => true, 'filename' => $newFileName];
    }

    return ['success' => false, 'message' => 'Failed to upload file'];
}

/**
 * Log error
 */
function logError($message)
{
    $logDir = BASE_PATH . '/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }

    $logFile = $logDir . '/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
    @file_put_contents($logFile, $logMessage, FILE_APPEND);
}
/**
 * Process referral reward for a completed payment
 */
function processReferralReward($referredUserId)
{
    try {
        $db = new Database();
        $conn = $db->getConnection();

        // Get the referred user's info
        $stmt = $conn->prepare("SELECT referred_by, user_code FROM users WHERE user_id = ?");
        $stmt->execute([$referredUserId]);
        $referredUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$referredUser || !$referredUser['referred_by']) {
            return false; // No referral to process
        }

        $referrerId = $referredUser['referred_by'];
        $rewardAmount = 20.00;

        // Check if reward already given for this user
        $stmt = $conn->prepare("SELECT transaction_id FROM balance_transactions WHERE reference_id = ? AND reference_type = 'referral' LIMIT 1");
        $stmt->execute([$referredUserId]);
        if ($stmt->fetch()) {
            return false; // Reward already processed
        }

        // Get referrer's current balance
        $stmt = $conn->prepare("SELECT balance FROM users WHERE user_id = ?");
        $stmt->execute([$referrerId]);
        $referrerData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$referrerData)
            return false;

        $balanceBefore = $referrerData['balance'];
        $balanceAfter = $balanceBefore + $rewardAmount;

        // Update referrer's balance
        $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE user_id = ?");
        $stmt->execute([$rewardAmount, $referrerId]);

        // Record transaction
        $stmt = $conn->prepare("
            INSERT INTO balance_transactions (
                user_id, transaction_type, amount, balance_before, balance_after, 
                description, reference_type, reference_id
            ) VALUES (?, 'credit', ?, ?, ?, ?, 'referral', ?)
        ");
        $stmt->execute([
            $referrerId,
            $rewardAmount,
            $balanceBefore,
            $balanceAfter,
            "Referral reward for user #" . $referredUser['user_code'],
            $referredUserId
        ]);

        logError("Referral reward processed: Referrer $referrerId received $rewardAmount for referred user $referredUserId");
        return true;

    } catch (Exception $e) {
        logError("Error processing referral reward: " . $e->getMessage());
        return false;
    }
}
