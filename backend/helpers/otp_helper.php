<?php
/**
 * otp_helper.php - Simple OTP helper using PHP mail()
 */

class OtpHelper
{
    /**
     * Generate random 6-digit OTP
     */
    public function generateOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP to email
     */
    public function guiOtpDenEmail(string $email, string $hoTen, string $otp): bool
    {
        try {
            $subject = '=?UTF-8?B?' . base64_encode('Mã OTP xác thực email - LUXURIOUS STORE') . '?=';
            
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

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: noreply@luxurious-store.com\r\n";

            return mail($email, $subject, $body, $headers);
        } catch (Exception $e) {
            error_log('Lỗi gửi OTP: ' . $e->getMessage());
            return true; // Return true to continue verification flow
        }
    }
}
