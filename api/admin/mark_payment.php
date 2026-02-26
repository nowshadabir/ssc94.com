<?php
/**
 * Admin API - Mark User Payment as Complete
 * SSC Batch '94
 */

session_start();
require_once '../../config/config.php';

header('Content-Type: application/json');

// Security Check: Only admins allowed
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    jsonResponse(false, 'Unauthorized access');
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

// Get POST data
$user_id = intval($_POST['user_id'] ?? 0);
$action = sanitize($_POST['action'] ?? '');

if ($user_id <= 0) {
    jsonResponse(false, 'Invalid user ID');
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    if ($action === 'mark_paid') {
        // Begin transaction
        $conn->beginTransaction();

        // Get user details
        $stmt = $conn->prepare("SELECT full_name, mobile, email FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            jsonResponse(false, 'User not found');
        }

        // Update user status to active
        $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Check if payment record exists
        $stmt = $conn->prepare("SELECT payment_id FROM payments WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($payment) {
            // Update existing payment record
            $stmt = $conn->prepare("
                UPDATE payments 
                SET payment_status = 'completed',
                    payment_method = 'manual',
                    updated_at = NOW()
                WHERE user_id = ?
            ");
            $stmt->execute([$user_id]);
        } else {
            // Create new payment record
            $stmt = $conn->prepare("
                INSERT INTO payments (
                    user_id, 
                    amount, 
                    payment_status, 
                    payment_method,
                    transaction_id,
                    created_at
                ) VALUES (?, ?, 'completed', 'manual', ?, NOW())
            ");
            $stmt->execute([
                $user_id,
                REGISTRATION_FEE,
                'MANUAL-' . time() . '-' . $user_id
            ]);
        }

        // Commit transaction
        $conn->commit();

        jsonResponse(true, 'Payment marked as complete. User account is now active.', [
            'user_id' => $user_id,
            'user_name' => $user['full_name'],
            'status' => 'active'
        ]);

    } elseif ($action === 'get_user_details') {
        // Get user details with payment info
        $stmt = $conn->prepare("
            SELECT 
                u.user_id,
                u.full_name,
                u.mobile,
                u.email,
                u.status,
                u.referral_code,
                u.created_at,
                p.payment_id,
                p.amount,
                p.payment_status,
                p.payment_method,
                p.transaction_id,
                p.created_at as payment_date
            FROM users u
            LEFT JOIN payments p ON u.user_id = p.user_id
            WHERE u.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            jsonResponse(false, 'User not found');
        }

        jsonResponse(true, 'User details retrieved', [
            'user' => $user
        ]);

    } else {
        jsonResponse(false, 'Invalid action');
    }

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    logError('Mark payment error: ' . $e->getMessage());
    jsonResponse(false, 'Failed to update payment status. Please try again.');
}
?>