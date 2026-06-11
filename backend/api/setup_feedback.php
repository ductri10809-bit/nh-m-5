<?php
/**
 * API: setup_feedback.php - Create feedback table if not exists
 */
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';

// Only allow POST from admin/localhost
$allowedIPs = ['127.0.0.1', 'localhost', '::1'];
$clientIP = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

if (!in_array($clientIP, $allowedIPs)) {
    traVeJson(false, null, 'Unauthorized', 403);
}

try {
    $db = ketNoiCSDL();
    
    $sql = "CREATE TABLE IF NOT EXISTS feedback (
      feedback_id INT PRIMARY KEY AUTO_INCREMENT,
      ho_ten VARCHAR(100) NOT NULL,
      email VARCHAR(150) NOT NULL,
      noi_dung LONGTEXT NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX idx_email (email),
      INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->exec($sql);
    traVeJson(true, null, 'Feedback table created/verified successfully');
} catch (Exception $e) {
    traVeJson(false, null, 'Error: ' . $e->getMessage(), 500);
}
