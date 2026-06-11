<?php
/**
 * tao_qr.php - QR code generation endpoint (NO COMPOSER REQUIRED)
 * Auto-creates qr_payments table if missing
 * Uses QRServer API for QR generation
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../cau_hinh/hang_so.php';
require_once __DIR__ . '/../model/don_hang.php';

$orderId = (int)($_POST['order_id'] ?? 0);
$customerEmail = $_POST['customer_email'] ?? '';
$customerName = $_POST['customer_name'] ?? '';
$amount = (int)($_POST['amount'] ?? 0);

if (!$orderId || !$customerEmail) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu order_id hoặc customer_email'
    ]);
    exit;
}

try {
    // Connect to database
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) {
        throw new Exception('Database connection failed: ' . $mysqli->connect_error);
    }
    $mysqli->set_charset("utf8mb4");
    
    // Create table if not exists
    $createTableSQL = "
    CREATE TABLE IF NOT EXISTS qr_payments (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_id INT NOT NULL,
        qr_code_data LONGTEXT NOT NULL COMMENT 'JSON data encoded in QR',
        qr_image_url VARCHAR(255) COMMENT 'Path to QR image file',
        admin_email VARCHAR(255) NOT NULL DEFAULT 'ductri10809@gmail.com',
        customer_email VARCHAR(255) NOT NULL,
        bank_account VARCHAR(50),
        transaction_status VARCHAR(50) DEFAULT 'pending',
        admin_confirmed_at TIMESTAMP NULL,
        customer_notified_at TIMESTAMP NULL,
        amount DECIMAL(15, 2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
        INDEX idx_order_id (order_id),
        INDEX idx_status (transaction_status),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    if (!$mysqli->query($createTableSQL)) {
        throw new Exception('Failed to create qr_payments table: ' . $mysqli->error);
    }
    
    // Check order exists
    $orderResult = $mysqli->query("SELECT * FROM orders WHERE order_id = $orderId LIMIT 1");
    if (!$orderResult || $orderResult->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy đơn hàng'
        ]);
        $mysqli->close();
        exit;
    }
    
    $order = $orderResult->fetch_assoc();
    
    // Check if QR already exists
    $existingResult = $mysqli->query("SELECT * FROM qr_payments WHERE order_id = $orderId LIMIT 1");
    if ($existingResult && $existingResult->num_rows > 0) {
        $existing = $existingResult->fetch_assoc();
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => [
                'qr_payment_id' => $existing['id'],
                'qr_image_url' => $existing['qr_image_url'] ?? ''
            ],
            'message' => 'QR payment đã tồn tại'
        ]);
        $mysqli->close();
        exit;
    }
    
    // Create QR data (JSON format)
    $dataQR = [
        'admin_email' => ADMIN_EMAIL,
        'customer_email' => $customerEmail,
        'order_id' => $orderId,
        'amount' => $amount ?: $order['total_amount'],
        'timestamp' => time(),
        'app' => APP_NAME,
    ];
    
    $qrContent = json_encode($dataQR);
    
    // Generate QR code using qrserver.com API
    $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/';
    $qrParams = http_build_query([
        'size' => '300x300',
        'data' => $qrContent,
        'format' => 'png',
        'error_correction' => 'H',
    ]);
    
    // Download QR image from API
    $qrImageUrl = $qrApiUrl . '?' . $qrParams;
    $qrImageData = @file_get_contents($qrImageUrl, false, stream_context_create([
        'http' => ['timeout' => 5]
    ]));
    
    if ($qrImageData === false) {
        throw new Exception('Không thể tạo QR code từ API');
    }
    
    // Save QR image locally
    $uploadDir = __DIR__ . '/../../uploads/qr_codes/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }
    
    $filename = 'qr_' . $orderId . '_' . time() . '.png';
    $filepath = $uploadDir . $filename;
    
    if (!file_put_contents($filepath, $qrImageData)) {
        throw new Exception('Không thể lưu QR code');
    }
    
    $qrImageRelativePath = '/luxurious-fashion-store/uploads/qr_codes/' . $filename;
    $qrImageBase64 = 'data:image/png;base64,' . base64_encode($qrImageData);
    
    // Save QR payment record
    $qrCodeDataEscaped = $mysqli->real_escape_string(json_encode($dataQR));
    $adminEmailEscaped = $mysqli->real_escape_string(ADMIN_EMAIL);
    $customerEmailEscaped = $mysqli->real_escape_string($customerEmail);
    $amountFinal = $amount ?: $order['total_amount'];
    
    $insertSQL = "
    INSERT INTO qr_payments (order_id, qr_code_data, qr_image_url, admin_email, customer_email, amount, transaction_status)
    VALUES ($orderId, '$qrCodeDataEscaped', '$qrImageRelativePath', '$adminEmailEscaped', '$customerEmailEscaped', $amountFinal, 'pending')
    ";
    
    if (!$mysqli->query($insertSQL)) {
        throw new Exception('Failed to save QR payment: ' . $mysqli->error);
    }
    
    $qrPaymentId = $mysqli->insert_id;
    $mysqli->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => [
            'qr_payment_id' => $qrPaymentId,
            'qr_image_url' => $qrImageBase64,
            'qr_image_base64' => $qrImageBase64,
            'order_id' => $orderId,
            'customer_email' => $customerEmail,
            'amount' => $amountFinal,
        ],
        'message' => 'Tạo QR code thành công'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}
