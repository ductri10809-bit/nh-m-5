-- Table để lưu QR code payments
CREATE TABLE IF NOT EXISTS qr_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    qr_code_data LONGTEXT NOT NULL COMMENT 'JSON data encoded in QR',
    qr_image_path VARCHAR(255) COMMENT 'Path to QR image file',
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

-- Add column to payments table if not exists
ALTER TABLE payments ADD COLUMN IF NOT EXISTS qr_payment_id INT;
ALTER TABLE payments ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50);
