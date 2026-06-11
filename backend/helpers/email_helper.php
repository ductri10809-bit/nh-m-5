<?php
/**
 * email_helper.php - Helper để gửi email
 */
require_once __DIR__ . '/../cau_hinh/hang_so.php';

// Check if composer autoload exists
if (!file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    throw new Exception('Composer dependencies not installed. Run: composer install');
}

require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailHelper
{
    private PHPMailer $mail;

    public function __construct()
    {
        // Kiểm tra nếu PHPMailer đã được cài đặt
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            throw new Exception('PHPMailer chưa được cài đặt. Chạy: composer require phpmailer/phpmailer');
        }

        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = SMTP_HOST;
        $this->mail->SMTPAuth = true;
        $this->mail->Username = SMTP_USER;
        $this->mail->Password = SMTP_PASS;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = SMTP_PORT;
        $this->mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $this->mail->CharSet = 'UTF-8';
    }

    /**
     * Gửi email thông báo tới admin về thanh toán QR
     */
    public function guiThongBaoAdminQR(array $qrData, array $orderData, array $paymentData): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress(ADMIN_EMAIL);
            $this->mail->isHTML(true);
            $this->mail->Subject = '[' . APP_NAME . '] Thông báo thanh toán mới - Đơn #' . $qrData['order_id'];

            $html = $this->taoEmailAdminQR($qrData, $orderData, $paymentData);
            $this->mail->Body = $html;

            return $this->mail->send();
        } catch (Exception $e) {
            error_log('Lỗi gửi email admin: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Gửi email xác nhận thanh toán cho khách hàng
     */
    public function guiThongBaoKhachThanhToanThanhCong(array $qrData, array $orderData): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($qrData['customer_email']);
            $this->mail->isHTML(true);
            $this->mail->Subject = '[' . APP_NAME . '] Thanh toán thành công - Đơn #' . $qrData['order_id'];

            $html = $this->taoEmailKhachThanhToanThanhCong($qrData, $orderData);
            $this->mail->Body = $html;

            return $this->mail->send();
        } catch (Exception $e) {
            error_log('Lỗi gửi email khách: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo HTML email cho admin
     */
    private function taoEmailAdminQR(array $qrData, array $orderData, array $paymentData): string
    {
        $orderId = $qrData['order_id'];
        $customerName = $orderData['customer_name'] ?? 'N/A';
        $customerEmail = $qrData['customer_email'];
        $amount = number_format($qrData['amount'], 0, ',', '.') . ' VND';
        $approveLink = BASE_URL . '/backend/api/qr_payment_api.php?action=approve&id=' . $qrData['id'];

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: 'Arial', sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { padding: 30px; }
                .info-box { background: #f9f9f9; border-left: 4px solid #667eea; padding: 15px; margin: 15px 0; border-radius: 4px; }
                .label { color: #666; font-size: 12px; text-transform: uppercase; font-weight: bold; }
                .value { color: #333; font-size: 16px; margin-top: 5px; }
                .amount { font-size: 24px; color: #667eea; font-weight: bold; }
                .action-btn { display: inline-block; background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; margin-top: 20px; font-weight: bold; }
                .action-btn:hover { background: #218838; }
                .footer { background: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #e0e0e0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎉 Thông báo thanh toán mới</h1>
                    <p>Khách hàng vừa quét QR code thanh toán</p>
                </div>
                <div class="content">
                    <h2 style="color: #333; margin-top: 0;">Chi tiết thanh toán</h2>

                    <div class="info-box">
                        <div class="label">Đơn hàng</div>
                        <div class="value">#$orderId</div>
                    </div>

                    <div class="info-box">
                        <div class="label">Khách hàng</div>
                        <div class="value">$customerName</div>
                    </div>

                    <div class="info-box">
                        <div class="label">Email khách</div>
                        <div class="value">$customerEmail</div>
                    </div>

                    <div class="info-box">
                        <div class="label">Số tiền</div>
                        <div class="value amount">$amount</div>
                    </div>

                    <div class="info-box">
                        <div class="label">Thời gian quét</div>
                        <div class="value">{$qrData['created_at']}</div>
                    </div>

                    <center>
                        <a href="$approveLink" class="action-btn">✓ Xác nhận và duyệt thanh toán</a>
                    </center>

                    <p style="margin-top: 30px; color: #666; font-size: 12px;">
                        Hệ thống sẽ tự động gửi email xác nhận thanh toán cho khách hàng sau khi bạn duyệt.
                    </p>
                </div>
                <div class="footer">
                    <p>© 2024 ${ APP_NAME }. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    /**
     * Tạo HTML email cho khách hàng
     */
    private function taoEmailKhachThanhToanThanhCong(array $qrData, array $orderData): string
    {
        $orderId = $qrData['order_id'];
        $customerName = $orderData['customer_name'] ?? 'Khách hàng';
        $amount = number_format($qrData['amount'], 0, ',', '.') . ' VND';
        $orderDate = $orderData['order_date'] ?? date('d/m/Y H:i');

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: 'Arial', sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; }
                .header { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; }
                .header .checkmark { font-size: 48px; }
                .content { padding: 30px; }
                .info-box { background: #f9f9f9; border-left: 4px solid #28a745; padding: 15px; margin: 15px 0; border-radius: 4px; }
                .label { color: #666; font-size: 12px; text-transform: uppercase; font-weight: bold; }
                .value { color: #333; font-size: 16px; margin-top: 5px; font-weight: 500; }
                .amount { font-size: 28px; color: #28a745; font-weight: bold; }
                .order-details { background: #e8f5e9; padding: 15px; border-radius: 4px; margin: 20px 0; }
                .thank-you { color: #28a745; font-size: 16px; margin: 20px 0; font-weight: bold; }
                .footer { background: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #e0e0e0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <div class="checkmark">✓</div>
                    <h1>Thanh toán thành công!</h1>
                </div>
                <div class="content">
                    <p style="color: #333; font-size: 16px;">Xin chào <strong>$customerName</strong>,</p>

                    <p style="color: #666; margin-bottom: 20px;">Cảm ơn bạn đã thanh toán. Chúng tôi đã nhận được khoản tiền của bạn.</p>

                    <div class="order-details">
                        <h3 style="margin-top: 0; color: #333;">📦 Chi tiết đơn hàng</h3>

                        <div class="info-box" style="background: white; border-left-color: #28a745; margin: 10px 0;">
                            <div class="label">Mã đơn</div>
                            <div class="value">#$orderId</div>
                        </div>

                        <div class="info-box" style="background: white; border-left-color: #28a745; margin: 10px 0;">
                            <div class="label">Thời gian</div>
                            <div class="value">$orderDate</div>
                        </div>

                        <div class="info-box" style="background: white; border-left-color: #28a745; margin: 10px 0;">
                            <div class="label">Số tiền thanh toán</div>
                            <div class="value amount">$amount</div>
                        </div>
                    </div>

                    <div class="thank-you">
                        ❤️ Cảm ơn bạn đã mua sắm tại { APP_NAME }!
                    </div>

                    <p style="color: #666; font-size: 14px; margin-top: 30px;">
                        Đơn hàng của bạn sẽ được xử lý ngay. Bạn sẽ nhận được thông báo về tình trạng giao hàng sớm.
                    </p>

                    <p style="color: #999; font-size: 12px; margin-top: 20px;">
                        Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua email hoặc điện thoại.
                    </p>
                </div>
                <div class="footer">
                    <p>© 2024 ${ APP_NAME }. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }
}
