<?php
/**
 * setup_qr_table.php - Create QR payments table
 */
require_once __DIR__ . '/../cau_hinh/hang_so.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Connect to database
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($mysqli->connect_error) {
        throw new Exception('Database connection failed: ' . $mysqli->connect_error);
    }
    
    $mysqli->set_charset("utf8mb4");
    
    // SQL statement for creating QR payments table
    $createTableSQL = "
    CREATE TABLE IF NOT EXISTS qr_payments (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_id INT NOT NULL,
        qr_code_data LONGTEXT NOT NULL COMMENT 'JSON data encoded in QR',
        qr_image_url VARCHAR(255) COMMENT 'Path to QR image file',
        admin_email VARCHAR(255) NOT NULL DEFAULT 'ductri10809@gmail.com',
        customer_email VARCHAR(255) NOT NULL,
        bank_account VARCHAR(50) COMMENT 'Ngân hàng sử dụng để chuyển khoản',
        transaction_status VARCHAR(50) DEFAULT 'pending' COMMENT 'pending, confirmed, rejected',
        admin_confirmed_at TIMESTAMP NULL COMMENT 'Thời gian admin duyệt',
        customer_notified_at TIMESTAMP NULL COMMENT 'Thời gian gửi thông báo cho khách',
        amount DECIMAL(15, 2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
        INDEX idx_order_id (order_id),
        INDEX idx_status (transaction_status),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    // Execute create table
    if (!$mysqli->query($createTableSQL)) {
        throw new Exception('Failed to create table: ' . $mysqli->error);
    }
    
    // Verify table exists
    $result = $mysqli->query("SHOW TABLES LIKE 'qr_payments'");
    if (!$result || $result->num_rows === 0) {
        throw new Exception('Table verification failed');
    }
    
    // Get table info
    $info = $mysqli->query("DESCRIBE qr_payments");
    $columns = [];
    while ($row = $info->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    
    $mysqli->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'QR payments table created successfully!',
        'table_info' => [
            'table_name' => 'qr_payments',
            'column_count' => count($columns),
            'columns' => $columns
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
