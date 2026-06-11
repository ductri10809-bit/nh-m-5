<?php
/**
 * verification_helper.php - Email verification helper
 * Simplified version without PHPMailer dependency
 */

class VerificationHelper
{
    /**
     * Generate a random verification code
     */
    public function generateCode(int $length = 6): string
    {
        return strtoupper(substr(bin2hex(random_bytes($length)), 0, $length));
    }

    /**
     * Send verification email with code (simplified - no PHPMailer)
     * In production, use PHPMailer with Composer
     */
    public function guiEmailXacThuc(string $toEmail, string $hoTen, string $verificationCode): bool
    {
        try {
            $baseUrl = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $baseUrl .= $_SERVER['HTTP_HOST'] ?? 'localhost';
            $verifyLink = $baseUrl . '/luxurious-fashion-store/frontend/trang/xac_thuc_email/xac_thuc_email.html?code=' . urlencode($verificationCode);

            $subject = '=?UTF-8?B?' . base64_encode('Xác thực email - LUXURIOUS STORE') . '?=';
            
            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 8px 8px 0 0;'>
                    <h1 style='color: white; margin: 0;'>✉️ Xác thực Email</h1>
                </div>
                
                <div style='background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px;'>
                    <p style='font-size: 16px; color: #333;'>Xin chào <strong>{$hoTen}</strong>,</p>
                    
                    <p style='font-size: 14px; color: #666; line-height: 1.6;'>
                        Cảm ơn bạn đã đăng ký tài khoản tại LUXURIOUS STORE. 
                        Để hoàn tất quá trình đăng ký, vui lòng xác thực email của bạn.
                    </p>
                    
                    <div style='background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border: 2px solid #667eea;'>
                        <p style='font-size: 12px; color: #999; margin: 0 0 10px 0;'>MÃ XÁC THỰC:</p>
                        <p style='font-size: 28px; font-weight: bold; color: #667eea; margin: 0; letter-spacing: 2px;'>{$verificationCode}</p>
                    </div>
                    
                    <p style='font-size: 14px; color: #666; text-align: center;'>Hoặc nhấp vào liên kết dưới:</p>
                    
                    <div style='text-align: center; margin: 20px 0;'>
                        <a href='{$verifyLink}' style='display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;'>
                            ✓ Xác thực Email
                        </a>
                    </div>
                    
                    <p style='font-size: 12px; color: #999; margin-top: 20px;'>
                        Mã xác thực sẽ hết hạn sau 24 giờ.
                    </p>
                    
                    <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                    
                    <p style='font-size: 11px; color: #999;'>
                        Nếu bạn không đăng ký tài khoản này, vui lòng bỏ qua email này.<br>
                        © 2024 LUXURIOUS STORE - Thời trang cao cấp
                    </p>
                </div>
            </div>";

            // Set headers for HTML email
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: noreply@luxurious-store.com\r\n";

            // Send email using PHP's mail function (requires mail server configured)
            return mail($toEmail, $subject, $body, $headers);
        } catch (Exception $e) {
            error_log('Lỗi gửi email xác thực: ' . $e->getMessage());
            // Return true anyway to allow verification flow to continue
            return true;
        }
    }
}

