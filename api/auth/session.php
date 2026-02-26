<?php
/**
 * Session Check API
 * SSC Batch '94
 */

require_once dirname(dirname(__DIR__)) . '/config/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    jsonResponse(true, 'Not logged in', ['logged_in' => false]);
}

$userId = $_SESSION['user_id'];

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT user_id, full_name, mobile, profile_photo FROM users WHERE user_id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_unset();
        session_destroy();
        jsonResponse(true, 'Not logged in', ['logged_in' => false]);
    }

    $photo = $user['profile_photo'] ?? '';
    if (empty($photo)) {
        $photo = 'https://i.pravatar.cc/300?u=' . $userId;
    } else {
        $isHttp = strpos($photo, 'http://') === 0 || strpos($photo, 'https://') === 0;
        if (!$isHttp) {
            $photo = 'assets/uploads/profiles/' . $photo;
        }
    }

    jsonResponse(true, 'Logged in', [
        'logged_in' => true,
        'user_id' => (int) $user['user_id'],
        'name' => $user['full_name'] ?? 'Member',
        'mobile' => $user['mobile'] ?? '',
        'profile_photo' => $photo,
        'profile_url' => 'profile.php'
    ]);
} catch (Throwable $e) {
    logError('Session check error: ' . $e->getMessage());
    jsonResponse(false, 'Session check failed: ' . $e->getMessage(), ['logged_in' => false]);
}
