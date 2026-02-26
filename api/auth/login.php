<?php
/**
 * User Login API
 * SSC Batch '94
 */

require_once '../../config/config.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logError("Login attempt with invalid method: " . $_SERVER['REQUEST_METHOD'] . " from " . $_SERVER['REMOTE_ADDR']);
    jsonResponse(false, 'Invalid request method: ' . $_SERVER['REQUEST_METHOD'] . '. Please ensure you are not being redirected (e.g., from HTTP to HTTPS) during login.');
}

// Get POST data
$mobile = sanitize($_POST['mobile'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']) && $_POST['remember'] === 'on';

// Validate inputs
if (empty($mobile) || empty($password)) {
    jsonResponse(false, 'Mobile and password are required');
}

// Remove any spaces or dashes from mobile number
$mobile = preg_replace('/[\s\-]/', '', $mobile);


try {
    $db = new Database();
    $conn = $db->getConnection();

    // Get user by mobile
    $stmt = $conn->prepare("
        SELECT user_id, full_name, email, password_hash, status 
        FROM users 
        WHERE mobile = ?
    ");
    $stmt->execute([$mobile]);
    $user = $stmt->fetch();


    if (!$user) {
        jsonResponse(false, 'Invalid mobile or password');
    }

    // Check if account is pending payment
    if ($user['status'] === 'pending') {
        jsonResponse(false, 'Your account is pending payment verification. Please contact the administrator to complete your payment and activate your account.');
    }

    // Check if account is active
    if ($user['status'] !== 'active') {
        jsonResponse(false, 'Account is not active. Please contact administrator.');
    }

    // Verify password
    $passwordValid = verifyPassword($password, $user['password_hash']);


    if (!$passwordValid) {
        // Log failed attempt
        $stmt = $conn->prepare("
            INSERT INTO login_history (user_id, ip_address, user_agent, status, failure_reason) 
            VALUES (?, ?, ?, 'failed', 'Invalid password')
        ");
        $stmt->execute([
            $user['user_id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

        jsonResponse(false, 'Invalid mobile or password');
    }

    // Login successful
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['logged_in'] = true;

    // Update last login
    $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
    $stmt->execute([$user['user_id']]);

    // Log successful login
    $stmt = $conn->prepare("
        INSERT INTO login_history (user_id, ip_address, user_agent, status) 
        VALUES (?, ?, ?, 'success')
    ");
    $stmt->execute([
        $user['user_id'],
        $_SERVER['REMOTE_ADDR'] ?? null,
        $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);

    // Handle remember me
    if ($remember) {
        $token = generateToken();
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));

        $stmt = $conn->prepare("
            INSERT INTO user_sessions (session_id, user_id, expires_at, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $token,
            $user['user_id'],
            $expires,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

        // Set cookie
        setcookie('remember_token', $token, [
            'expires' => strtotime('+30 days'),
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    jsonResponse(true, 'Login successful', [
        'redirect' => 'profile.php'
    ]);

} catch (Exception $e) {
    logError('Login error: ' . $e->getMessage());
    jsonResponse(false, 'Login failed. Please try again.');
}
?>