<?php
/**
 * OtpHelperGmail - Gửi OTP qua Gmail SMTP (không dùng Composer)
 */
class OtpHelperGmail
{
    private $smtpConfig;

    public function __construct()
    {
        $this->smtpConfig = require __DIR__ . '/../cau_hinh/email.php';
    }

    /**
     * Generate random 6-digit OTP
     */
    public function generateOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP via Gmail SMTP
     */
    public function guiOtpDenEmail(string $email, string $hoTen, string $otp): bool
    {
        try {
            $subject = 'Mã OTP xác thực email - LUXURIOUS STORE';
            
            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 8px 8px 0 0;'>
                    <h1 style='color: white; margin: 0;'>🔐 Mã OTP Xác Thực</h1>
                </div>
                
                <div style='background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; border: 1px solid #e0e0e0;'>
                    <p style='font-size: 16px; color: #333; margin-bottom: 20px;'>Xin chào <strong>{$hoTen}</strong>,</p>
                    
                    <p style='font-size: 14px; color: #666; line-height: 1.6; margin-bottom: 20px;'>
                        Cảm ơn bạn đã đăng ký tài khoản tại <strong>LUXURIOUS STORE</strong>. 
                        Vui lòng nhập mã OTP dưới đây để xác thực email:
                    </p>
                    
                    <div style='background: white; padding: 25px; border-radius: 8px; text-align: center; border: 2px solid #667eea; margin: 20px 0;'>
                        <p style='font-size: 12px; color: #999; margin: 0 0 10px 0;'>MÃ OTP CỦA BẠN:</p>
                        <p style='font-size: 36px; font-weight: bold; color: #667eea; margin: 0; letter-spacing: 3px; font-family: monospace;'>{$otp}</p>
                    </div>
                    
                    <p style='font-size: 13px; color: #666; text-align: center; margin-bottom: 20px;'>
                        ⏰ Mã OTP này sẽ hết hạn sau <strong>10 phút</strong>
                    </p>
                    
                    <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                    
                    <p style='font-size: 11px; color: #999; line-height: 1.6;'>
                        <strong>⚠️ Lưu ý:</strong><br>
                        • Không chia sẻ mã OTP này cho bất kỳ ai<br>
                        • LUXURIOUS STORE sẽ không bao giờ yêu cầu OTP qua tin nhắn<br>
                        • Nếu bạn không đăng ký, vui lòng bỏ qua email này<br><br>
                        © 2024 LUXURIOUS STORE - Thời trang cao cấp
                    </p>
                </div>
            </div>";

            // Gửi qua Gmail SMTP
            return $this->sendViaSmtp($email, $subject, $body);
        } catch (Exception $e) {
            error_log('Lỗi gửi OTP: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email via SMTP (Gmail SSL)
     */
    private function sendViaSmtp(string $to, string $subject, string $body): bool
    {
        $config = $this->smtpConfig;
        
        // Mở kết nối SMTP với SSL
        $smtp = fsockopen('ssl://' . $config['smtp_host'], $config['smtp_port'], $errno, $errstr, 30);
        if (!$smtp) {
            error_log("SMTP Error: $errno - $errstr");
            return false;
        }

        // Đọc greeting
        $response = fgets($smtp, 1024);
        if (strpos($response, '220') === false) {
            fclose($smtp);
            return false;
        }

        // EHLO
        fwrite($smtp, "EHLO " . gethostname() . "\r\n");
        $response = fgets($smtp, 1024);
        
        // AUTH LOGIN
        fwrite($smtp, "AUTH LOGIN\r\n");
        $response = fgets($smtp, 1024);
        if (strpos($response, '334') === false) {
            fclose($smtp);
            return false;
        }

        // Send username (base64)
        fwrite($smtp, base64_encode($config['smtp_user']) . "\r\n");
        $response = fgets($smtp, 1024);
        if (strpos($response, '334') === false) {
            fclose($smtp);
            return false;
        }

        // Send password (base64)
        fwrite($smtp, base64_encode($config['smtp_pass']) . "\r\n");
        $response = fgets($smtp, 1024);
        if (strpos($response, '235') === false) {
            error_log("SMTP Auth Failed: " . $response);
            fclose($smtp);
            return false;
        }

        // MAIL FROM
        fwrite($smtp, "MAIL FROM: <" . $config['from_email'] . ">\r\n");
        $response = fgets($smtp, 1024);

        // RCPT TO
        fwrite($smtp, "RCPT TO: <$to>\r\n");
        $response = fgets($smtp, 1024);

        // DATA
        fwrite($smtp, "DATA\r\n");
        $response = fgets($smtp, 1024);

        // Build email
        $headers = "From: " . $config['from_name'] . " <" . $config['from_email'] . ">\r\n";
        $headers .= "To: $to\r\n";
        $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $body_to_send = $headers . "\r\n" . $body . "\r\n.\r\n";
        fwrite($smtp, $body_to_send);
        
        $response = fgets($smtp, 1024);

        // QUIT
        fwrite($smtp, "QUIT\r\n");
        fclose($smtp);

        return strpos($response, '250') !== false;
    }
}
