<?php
/**
 * Rupantorpay Payment Gateway Integration
 * SSC Batch '94
 */

class RupantorpayPayment
{
    private $apiKey;
    private $apiUrl = 'https://payment.rupantorpay.com/api/payment';
    private $db;
    private $settings;

    public function __construct()
    {
        $this->db = new Database();
        $this->loadSettings();
    }

    /**
     * Load gateway settings from database
     */
    private function loadSettings()
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT * FROM payment_gateway_settings WHERE gateway_name = 'rupantorpay' AND is_active = 1 LIMIT 1");
            $stmt->execute();
            $this->settings = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$this->settings) {
                throw new Exception('Rupantorpay gateway is not active');
            }

            $this->apiKey = $this->settings['api_key'];
        } catch (Exception $e) {
            logError('Rupantorpay settings error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create payment checkout
     * 
     * @param float $amount Payment amount
     * @param string $invoiceNumber Unique invoice number
     * @param int $userId User ID
     * @param array $metadata Additional metadata
     * @return array Payment response
     */
    public function createPayment($amount, $invoiceNumber, $userId, $metadata = [], $successUrl = null, $cancelUrl = null)
    {
        try {
            // Get user phone number
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT mobile FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found'
                ];
            }

            // Prepare metadata
            $paymentMetadata = array_merge([
                'phone' => $user['mobile'],
                'user_id' => $userId,
                'invoice' => $invoiceNumber
            ], $metadata);

            $effectiveSuccessUrl = $successUrl ?? $this->settings['success_url'];
            $effectiveCancelUrl = $cancelUrl ?? $this->settings['cancel_url'];

            // Prepare payment data
            $paymentData = [
                'success_url' => $effectiveSuccessUrl . (strpos($effectiveSuccessUrl, '?') === false ? '?' : '&') . 'invoice=' . $invoiceNumber,
                'cancel_url' => $effectiveCancelUrl . (strpos($effectiveCancelUrl, '?') === false ? '?' : '&') . 'invoice=' . $invoiceNumber,
                'webhook_url' => $this->settings['webhook_url'],
                'metadata' => $paymentMetadata,
                'amount' => (string) $amount
            ];

            // Make API request
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->apiUrl . '/checkout',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($paymentData),
                CURLOPT_HTTPHEADER => [
                    'X-API-KEY: ' . $this->apiKey,
                    'Content-Type: application/json',
                    'X-CLIENT: ' . $_SERVER["HTTP_HOST"]
                ],
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            curl_close($curl);

            if ($error) {
                logError('Rupantorpay API Error: ' . $error);
                return [
                    'success' => false,
                    'message' => 'Payment gateway connection failed'
                ];
            }

            $result = json_decode($response, true);

            if ($httpCode === 200 && isset($result['payment_url'])) {
                return [
                    'success' => true,
                    'paymentID' => $result['transaction_id'] ?? $invoiceNumber,
                    'paymentURL' => $result['payment_url'],
                    'transactionId' => $result['transaction_id'] ?? null,
                    'message' => 'Payment initiated successfully',
                    'rawResponse' => $result
                ];
            } else {
                logError('Rupantorpay Response Error: ' . $response);
                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to create payment',
                    'rawResponse' => $result
                ];
            }

        } catch (Exception $e) {
            logError('Rupantorpay createPayment error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Payment creation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify payment transaction
     * 
     * @param string $transactionId Transaction ID from Rupantorpay
     * @return array Verification response
     */
    public function verifyPayment($transactionId)
    {
        try {
            $verifyData = [
                'transaction_id' => $transactionId
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->apiUrl . '/verify-payment',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($verifyData),
                CURLOPT_HTTPHEADER => [
                    'X-API-KEY: ' . $this->apiKey,
                    'Content-Type: application/json'
                ],
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            curl_close($curl);

            if ($error) {
                logError('Rupantorpay Verify Error: ' . $error);
                return [
                    'success' => false,
                    'message' => 'Verification failed'
                ];
            }

            $result = json_decode($response, true);

            if ($httpCode === 200 && isset($result['status'])) {
                return [
                    'success' => $result['status'] === 'success' || $result['status'] === 'completed',
                    'status' => $result['status'],
                    'transactionId' => $result['transaction_id'] ?? $transactionId,
                    'amount' => $result['amount'] ?? null,
                    'message' => $result['message'] ?? 'Payment verified',
                    'rawResponse' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Invalid verification response',
                    'rawResponse' => $result
                ];
            }

        } catch (Exception $e) {
            logError('Rupantorpay verifyPayment error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Verification error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get active gateway name
     * 
     * @return string Gateway name
     */
    public function getGatewayName()
    {
        return 'rupantorpay';
    }

    /**
     * Check if gateway is active
     * 
     * @return bool
     */
    public static function isActive()
    {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("SELECT is_active FROM payment_gateway_settings WHERE gateway_name = 'rupantorpay' LIMIT 1");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result && $result['is_active'] == 1;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>