# RupantorPay Integration Guide

This document explains the implementation of the RupantorPay payment gateway for the SSC Batch '94 website.

## 1. Overview
RupantorPay is used as the primary payment gateway for:
- User Registration Fees
- Reunion Registrations
- Donations (Planned/Implemented)

The integration is designed to be secure, handling both client-side redirects and server-to-server webhook notifications to ensure payment records are always accurate.

## 2. Core Implementation (`config/rupantorpay.php`)
The core logic is encapsulated in the `RupantorpayPayment` class. This class handles communication with the RupantorPay API.

- **`createPayment($amount, $invoiceNumber, $userId, $metadata, $successUrl, $cancelUrl)`**: 
  Initiates a payment session. It sends the amount and metadata to RupantorPay and returns a `paymentURL` where the user is redirected.
- **`verifyPayment($transactionId)`**: 
  Verifies the status of a transaction server-to-server. This is called during the success callback and webhook handling to prevent spoofing.
- **`isActive()`**: 
  Statistically checks if the gateway is enabled in the database settings.

## 3. Payment Flows

### A. General Registration Fee (`api/payment/`)
1. **Initiation (`initiate_payment.php`)**:
   - Creates a pending record in the `payments` table.
   - Redirects the user to the RupantorPay checkout page.
2. **Success Callback (`success.php`)**:
   - Receives the user back from RupantorPay.
   - Calls `verifyPayment()` to confirm the transaction with the gateway.
   - Updates the `payments` record to `completed`.
   - Activates the user account (`status = 'active'`).
3. **Webhook (`webhook.php`)**:
   - Handles asynchronous notifications from RupantorPay.
   - Useful if the user closes their browser before returning to the success page.

### B. Reunion Registration (`api/reunion/`)
This flow uses a **"Session-First"** pattern to avoid cluttering the database with unpaid registration records.
1. **Initiation (`initiate_payment.php`)**:
   - Validates form data (Full Name, Guests, T-shirt size, etc.).
   - Stores the data in `$_SESSION['reunion_pending']`.
   - **Does NOT** write to the database yet.
   - Redirects to RupantorPay.
2. **Success Callback (`payment_success.php`)**:
   - Verifies the payment via API.
   - On success, reads registration data from the session.
   - Performs the **first and only** database write to `reunion_registrations`.
   - Generates a unique Ticket Number (e.g., `#94-1234`) and QR code data.

## 4. Database Schema
Payment settings are managed via the `payment_gateway_settings` table:
- `gateway_name`: 'rupantorpay'
- `api_key`: Your secret API key.
- `api_url`: `https://payment.rupantorpay.com/api/payment`
- `is_active`: Toggle for enabling/disabling the gateway.

## 5. Security Measures
- **Server-Side Verification**: We never trust the status parameter sent in the URL; we always verify the transaction ID directly with RupantorPay's API.
- **SSL Verification**: In production, the system uses standard cURL SSL checks. For local development (XAMPP), custom CA bundles were configured to allow local testing.
- **Unique Invoices**: Every transaction generates a unique ID (e.g., `INV173...` or `RUN1U5T173...`) to prevent replay attacks.

## 6. Error Logging
All payment-related errors and API responses are logged to the system logs for troubleshooting:
- `logs/error.log` (or handled via the `logError()` function)

## 7. How the Code was Developed
The integration was built following modern PHP best practices, focusing on **security**, **modularization**, and **developer experience**. Here is the logic behind the implementation:

### A. Modular Wrapper Class (`RupantorpayPayment`)
Instead of writing repetitive cURL requests in every API file, I developed a standalone `RupantorpayPayment` class. 
- **Rationale**: This centralizes API keys, base URLs, and error handling. If the gateway provider updates their API version, we only need to change one file.
- **Logic**: The constructor automatically fetches credentials from the database, ensuring that the application never hardcodes sensitive keys.

### B. The "Session-First" Pattern (Safety for Reunions)
For the Reunion registration, I made a conscious choice **not to write** to the database at the point of initiation (`initiate_payment.php`).
- **How it works**: All user-filled data (T-shirt size, guests, etc.) is stashed in `$_SESSION['reunion_pending']`.
- **The Benefit**: This prevents "Database Litter." If I had inserted a row with `status='pending'`, the database would fill up with unfinished registrations from users who just wanted to check the final price or closed their browser. Now, a database record is created **only** when money is confirmed.

### C. Trust-No-One Verification
I implemented a multi-step verification process in `payment_success.php`.
- **No Reliance on URL Params**: Even if the URL says `status=success`, the code ignores it and performs a **Server-to-Server API call** to RupantorPay to verify the `transaction_Id`. This prevents users from manually typing `?status=success` in their browser to bypass payment.
- **Webhook Parallelism**: I ensured the logic accounts for race conditions where a Webhook might finish before the user's browser redirects. The code checks for existing transaction IDs before processing.

### D. Local Development Support (XAMPP/SSL)
During development on XAMPP, PHP often throws SSL certificate errors (cURL error 60). I configured the cURL requests in the class to handle these environments gracefully while maintaining strict SSL checks in production, ensuring a smooth transition from local to live servers.

### E. Dynamic SQL Configuration
I created a dedicated `payment_gateway_settings` table. This allows administrators to:
- Toggle the gateway on/off without touching code.
- Update API keys or callback URLs directly from a DB manager or (future) admin panel.
- Switch between gateways (e.g., RupantorPay vs. bKash) by simply changing the `is_active` flag.
## 8. Credentials & API Access
The following credentials are currently configured in the database (`payment_gateway_settings` table):

| Setting | Value |
| :--- | :--- |
| **Gateway Name** | `rupantorpay` |
| **Base API URL** | `https://payment.rupantorpay.com/api/payment` |
| **API Key (Secret)** | `g8wxKwv4ts2ToZO7siWuQsAAHcfafRnRRPAjMeOrcpbTuX8vys` |
| **Status** | `Enabled` (is_active = 1) |

### Callback URLs (Localhost Example)
- **Success**: `http://localhost/SSC-94/ssc94.com/api/reunion/payment_success.php`
- **Cancel**: `http://localhost/SSC-94/ssc94.com/views/pages/reunion.php?payment=cancelled`
- **Webhook**: `http://localhost/SSC-94/ssc94.com/api/payment/webhook.php`

---

## 9. Key Code Snippets

### A. Initiating a Payment (Reunion Example)
This snippet from `api/reunion/initiate_payment.php` shows how the metadata and URLs are prepared before calling the gateway.

```php
// Preparing data (stored in session first)
$_SESSION['reunion_pending'] = [
    'user_id' => $userId,
    'total_amount' => $totalAmount,
    'invoice' => 'RUN' . $reunion_id . 'U' . $userId . 'T' . time()
];

// Call the gateway
$gateway = new RupantorpayPayment();
$result = $gateway->createPayment(
    $totalAmount,
    $invoiceNumber,
    $userId,
    ['type' => 'reunion', 'id' => $reunion_id],
    $successUrl,
    $cancelUrl
);

if ($result['success']) {
    header('Location: ' . $result['paymentURL']);
}
```

### B. Verifying a Transaction
This snippet from `config/rupantorpay.php` shows the core cURL logic for server-side verification.

```php
public function verifyPayment($transactionId) {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://payment.rupantorpay.com/api/payment/verify-payment",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode(['transaction_id' => $transactionId]),
        CURLOPT_HTTPHEADER => [
            'X-API-KEY: ' . $this->apiKey,
            'Content-Type: application/json'
        ],
    ]);
    $response = curl_exec($curl);
    // ... logic to parse response['status']
}
```

### C. The "Session-First" Insert
From `api/reunion/payment_success.php`, this is where the database record is finally created after-and-only-after verification.

```php
$verifyResult = $rupantorpay->verifyPayment($trxId);

if ($verifyResult['success']) {
    $pending = $_SESSION['reunion_pending'];
    
    // The FIRST and ONLY database write for this registration
    $stmt = $conn->prepare("INSERT INTO reunion_registrations (...) VALUES (...)");
    $stmt->execute([...]);
    
    unset($_SESSION['reunion_pending']);
    redirectOk($ticketNumber);
}
```
