<?php
/**
 * Reunion Registration Step 1: Create Order
 * SSC Batch '94
 */

require_once '../../config/config.php';

header('Content-Type: application/json');

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, 'Please login to register for the reunion');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

$userId = $_SESSION['user_id'];
$fullName = sanitize($_POST['full_name'] ?? '');
$mobile = sanitize($_POST['mobile'] ?? '');
$tshirtSize = sanitize($_POST['tshirt_size'] ?? '');
$gender = sanitize($_POST['gender'] ?? 'male');
$guestCount = (int) ($_POST['guest_count'] ?? 0);
$guestsData = $_POST['guests_data'] ?? '[]';  // JSON string from JS

if (empty($fullName) || empty($mobile) || empty($tshirtSize)) {
    jsonResponse(false, 'Please fill all required fields');
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // 1. Get Active Reunion
    $stmt = $conn->prepare("SELECT * FROM reunions WHERE status = 'active' LIMIT 1");
    $stmt->execute();
    $reunion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reunion) {
        jsonResponse(false, 'No active reunion found for registration');
    }

    // 2. Calculate Total
    $baseFee = (float) $reunion['cost_alumnus'];
    $guestFee = (float) $reunion['cost_guest'];
    $totalAmount = $baseFee + ($guestCount * $guestFee);

    // 3. Generate Ticket Number
    $random = str_pad(mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);
    $ticketNumber = "#94-" . $random;

    // 4. Insert Record (Pending)
    $stmt = $conn->prepare("
        INSERT INTO reunion_registrations 
        (user_id, reunion_id, ticket_number, full_name, mobile, tshirt_size, gender, guest_count, guests_data, total_amount, payment_status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");

    $stmt->execute([
        $userId,
        $reunion['reunion_id'],
        $ticketNumber,
        $fullName,
        $mobile,
        $tshirtSize,
        $gender,
        $guestCount,
        $guestsData,
        $totalAmount
    ]);

    $registrationId = $conn->lastInsertId();

    // 5. Return success and info for payment
    jsonResponse(true, 'Registration initiated', [
        'registration_id' => $registrationId,
        'ticket_number' => $ticketNumber,
        'amount' => $totalAmount,
        'callback_url' => 'api/reunion/payment_callback.php'
    ]);

} catch (Exception $e) {
    logError('Reunion registration error: ' . $e->getMessage());
    jsonResponse(false, 'Failed to process registration');
}
