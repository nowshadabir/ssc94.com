<?php
/**
 * Bkash Payment Gateway Configuration
 * API Version: 1.2.0-beta
 */

require_once __DIR__ . '/env_loader.php';

// Bkash Credentials (Replace with your actual credentials)
define('BKASH_APP_KEY', getenv('BKASH_APP_KEY'));
define('BKASH_APP_SECRET', getenv('BKASH_APP_SECRET'));
define('BKASH_USERNAME', getenv('BKASH_USERNAME'));
define('BKASH_PASSWORD', getenv('BKASH_PASSWORD'));

// Environment (sandbox or production)
define('BKASH_ENVIRONMENT', getenv('BKASH_ENVIRONMENT'));

// Base URLs
if (BKASH_ENVIRONMENT === 'sandbox') {
    define('BKASH_BASE_URL', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta');
    define('BKASH_CHECKOUT_URL', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout');
} else {
    define('BKASH_BASE_URL', 'https://tokenized.pay.bka.sh/v1.2.0-beta');
    define('BKASH_CHECKOUT_URL', 'https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/checkout');
}

// Payment Configuration
define('REGISTRATION_FEE', 100.00); // BDT

// Callback URLs (Update with your domain)
define('BKASH_CALLBACK_URL', SITE_URL . '/api/payment/bkash_callback.php');
define('BKASH_SUCCESS_URL', SITE_URL . '/views/profile.php?payment=success');
define('BKASH_FAILURE_URL', SITE_URL . '/payment_failed.php');
define('BKASH_CANCEL_URL', SITE_URL . '/views/auth/login.html?payment=cancelled');

/**
 * Bkash Payment Class
 */
class BkashPayment
{
    private $token;
    private $tokenExpiry;

    /**
     * Get Bkash Grant Token
     */
    public function getToken()
    {
        // Check if token is still valid
        if ($this->token && $this->tokenExpiry && time() < $this->tokenExpiry) {
            return $this->token;
        }

        $url = BKASH_BASE_URL . '/token/grant';

        $data = [
            'app_key' => BKASH_APP_KEY,
            'app_secret' => BKASH_APP_SECRET
        ];

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'username: ' . BKASH_USERNAME,
            'password: ' . BKASH_PASSWORD
        ];

        $response = $this->makeCurlRequest($url, 'POST', $data, $headers);

        if ($response && isset($response['id_token'])) {
            $this->token = $response['id_token'];
            $this->tokenExpiry = time() + 3600; // Token valid for 1 hour
            return $this->token;
        }

        return false;
    }

    /**
     * Create Payment
     */
    public function createPayment($amount, $invoiceNumber, $userId)
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Failed to get Bkash token'];
        }

        $url = BKASH_CHECKOUT_URL . '/create';

        $data = [
            'mode' => '0011',
            'payerReference' => 'USER_' . $userId,
            'callbackURL' => BKASH_CALLBACK_URL . '?user_id=' . $userId,
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $invoiceNumber
        ];

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: ' . $token,
            'X-APP-Key: ' . BKASH_APP_KEY
        ];

        $response = $this->makeCurlRequest($url, 'POST', $data, $headers);

        if ($response && isset($response['paymentID'])) {
            return [
                'success' => true,
                'paymentID' => $response['paymentID'],
                'bkashURL' => $response['bkashURL'] ?? null
            ];
        }

        return [
            'success' => false,
            'message' => $response['errorMessage'] ?? 'Payment creation failed'
        ];
    }

    /**
     * Execute Payment
     */
    public function executePayment($paymentID)
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Failed to get Bkash token'];
        }

        $url = BKASH_CHECKOUT_URL . '/execute';

        $data = [
            'paymentID' => $paymentID
        ];

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: ' . $token,
            'X-APP-Key: ' . BKASH_APP_KEY
        ];

        $response = $this->makeCurlRequest($url, 'POST', $data, $headers);

        if ($response && $response['transactionStatus'] === 'Completed') {
            return [
                'success' => true,
                'transactionID' => $response['trxID'],
                'paymentID' => $response['paymentID'],
                'amount' => $response['amount'],
                'customerMsisdn' => $response['customerMsisdn'] ?? null,
                'transactionStatus' => $response['transactionStatus']
            ];
        }

        return [
            'success' => false,
            'message' => $response['errorMessage'] ?? 'Payment execution failed'
        ];
    }

    /**
     * Query Payment
     */
    public function queryPayment($paymentID)
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Failed to get Bkash token'];
        }

        $url = BKASH_CHECKOUT_URL . '/query';

        $data = [
            'paymentID' => $paymentID
        ];

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: ' . $token,
            'X-APP-Key: ' . BKASH_APP_KEY
        ];

        return $this->makeCurlRequest($url, 'POST', $data, $headers);
    }

    /**
     * Make cURL Request
     */
    private function makeCurlRequest($url, $method = 'POST', $data = [], $headers = [])
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For development only

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpCode === 200) {
            return json_decode($response, true);
        }

        return json_decode($response, true) ?: ['error' => 'Request failed', 'http_code' => $httpCode];
    }
}
