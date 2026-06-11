<?php
/**
 * Setup contact_messages table
 * Access: http://localhost/luxurious-fashion-store/backend/api/setup_contact_table.php
 */
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';

try {
    $db = ketNoiCSDL();
    
    $sql = "CREATE TABLE IF NOT EXISTS contact_messages (
      message_id INT PRIMARY KEY AUTO_INCREMENT,
      ho_ten VARCHAR(100) NOT NULL,
      email VARCHAR(150) NOT NULL,
      noi_dung LONGTEXT NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX idx_email (email),
      INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->exec($sql);
    traVeJson(true, null, 'Contact messages table created successfully');
} catch (Exception $e) {
    traVeJson(false, null, 'Error: ' . $e->getMessage(), 500);
}
