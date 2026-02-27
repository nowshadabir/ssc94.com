<?php
/**
 * Email Service
 * Simple SMTP Mailer Class
 * SSC Batch '94
 */

class EmailService
{
    public static function send($to, $subject, $message, $from_email = SMTP_USER, $from_name = FROM_NAME)
    {
        $host = SMTP_HOST;
        $port = SMTP_PORT;
        $user = SMTP_USER;
        $pass = SMTP_PASS;

        // For local development or if SMTP is not fully configured, fallback to mail()
        if (empty($user) || empty($pass) || $host === 'localhost') {
            $headers = "From: $from_name <$from_email>\r\n";
            $headers .= "Reply-To: $from_email\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            return mail($to, $subject, $message, $headers);
        }

        // Professional SMTP sending logic would go here.
        // For production, it is STRONGLY RECOMMENDED to use PHPMailer.
        // Below is a simplified SMTP over SSL (port 465)

        try {
            $timeout = 30;
            $socket = @fsockopen(($port == 465 ? 'ssl://' : '') . $host, $port, $errno, $errstr, $timeout);

            if (!$socket) {
                logError("SMTP Connection Error: $errstr ($errno)");
                return false;
            }

            $getResponse = function ($s) {
                $r = "";
                while ($str = fgets($s, 512)) {
                    $r .= $str;
                    if (substr($str, 3, 1) == " ")
                        break;
                }
                return $r;
            };

            $getResponse($socket);

            fputs($socket, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n");
            $getResponse($socket);

            fputs($socket, "AUTH LOGIN\r\n");
            $getResponse($socket);

            fputs($socket, base64_encode($user) . "\r\n");
            $getResponse($socket);

            fputs($socket, base64_encode($pass) . "\r\n");
            $loginRes = $getResponse($socket);

            if (strpos($loginRes, '235') === false) {
                logError("SMTP Login Failed for $user. Response: $loginRes");
                return false;
            }

            fputs($socket, "MAIL FROM: <$from_email>\r\n");
            $getResponse($socket);

            fputs($socket, "RCPT TO: <$to>\r\n");
            $getResponse($socket);

            fputs($socket, "DATA\r\n");
            $getResponse($socket);

            $headers = "To: $to\r\n";
            $headers .= "From: $from_name <$from_email>\r\n";
            $headers .= "Subject: $subject\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n\r\n";

            fputs($socket, $headers . $message . "\r\n.\r\n");
            $dataRes = $getResponse($socket);

            fputs($socket, "QUIT\r\n");
            fclose($socket);

            return strpos($dataRes, '250') !== false;
        } catch (Exception $e) {
            logError("SMTP Critical Exception: " . $e->getMessage());
            return false;
        }
    }

    public static function sendPasswordReset($to, $name, $code, $is_admin = false)
    {
        $subject = ($is_admin ? "Admin " : "") . "Password Reset Code - " . SITE_NAME;

        $message = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; background-color: #ffffff;'>
            <div style='text-align: center; margin-bottom: 20px;'>
                <h2 style='color: #1a202c; margin-bottom: 10px;'>Password Reset Code</h2>
                <p style='color: #718096; font-size: 14px;'>Hello $name, use the code below to reset your password.</p>
            </div>
            
            <div style='background-color: #f7fafc; border: 2px dashed #e2e8f0; border-radius: 12px; padding: 20px; text-align: center; margin: 30px 0;'>
                <span style='font-family: monospace; font-size: 32px; font-weight: bold; color: #4f46e5; letter-spacing: 8px;'>$code</span>
            </div>
            
            <p style='color: #4a5568; font-size: 14px;'>Enter this code on the password reset page. This code will expire in 1 hour.</p>
            <p style='color: #a0aec0; font-size: 12px; margin-top: 30px;'>If you didn't request this, you can safely ignore this email.</p>
            
            <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;'>
            <p style='font-size: 12px; color: #cbd5e0; text-align: center;'>&copy; 2026 " . SITE_NAME . ". All rights reserved.</p>
        </div>
        ";

        return self::send($to, $subject, $message);
    }
}
