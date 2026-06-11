<?php
/**
 * qr_helper.php - Helper để tạo QR code
 */
require_once __DIR__ . '/../cau_hinh/hang_so.php';

// Check if composer autoload exists
if (!file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    throw new Exception('Composer dependencies not installed. Run: composer install');
}

require_once __DIR__ . '/../../vendor/autoload.php';

class QRHelper
{
    /**
     * Tạo QR code từ dữ liệu
     * Trả về base64 encoded image hoặc đường dẫn file
     */
    public static function taoQRCode(array $data, bool $returnBase64 = true): string
    {
        // Cần cài đặt: composer require chillerlan/php-qrcode
        if (!class_exists('chillerlan\QRCode\QRCode')) {
            throw new Exception('PHP QRCode library chưa được cài đặt. Chạy: composer require chillerlan/php-qrcode');
        }

        $qrContent = json_encode($data);
        $qr = new \chillerlan\QRCode\QRCode();
        $qrImage = $qr->render($qrContent);

        if ($returnBase64) {
            // Trả về base64
            return 'data:image/png;base64,' . base64_encode($qrImage);
        } else {
            // Lưu file và trả về đường dẫn
            $filename = 'qr_' . time() . '_' . uniqid() . '.png';
            $uploadDir = __DIR__ . '/../../uploads/qr_codes/';

            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }

            $filepath = $uploadDir . $filename;
            file_put_contents($filepath, $qrImage);

            return '/luxurious-fashion-store/uploads/qr_codes/' . $filename;
        }
    }

    /**
     * Tạo dữ liệu QR code (JSON) từ thông tin thanh toán
     */
    public static function taoDataQR(array $paymentInfo): array
    {
        return [
            'admin_email' => $paymentInfo['admin_email'],
            'customer_email' => $paymentInfo['customer_email'],
            'order_id' => $paymentInfo['order_id'],
            'amount' => $paymentInfo['amount'],
            'timestamp' => time(),
            'app' => APP_NAME,
        ];
    }

    /**
     * Xác minh dữ liệu QR code
     */
    public static function xacMinhDataQR(string $qrContent): ?array
    {
        try {
            $data = json_decode($qrContent, true);
            if (!is_array($data)) {
                return null;
            }

            // Kiểm tra các trường bắt buộc
            $required = ['admin_email', 'customer_email', 'order_id', 'amount'];
            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    return null;
                }
            }

            return $data;
        } catch (Exception $e) {
            return null;
        }
    }
}
