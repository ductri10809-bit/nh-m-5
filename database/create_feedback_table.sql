-- Create feedback/contact_messages table
-- Run this in phpMyAdmin or: mysql -u root -p shop_db < database/create_feedback_table.sql

CREATE TABLE IF NOT EXISTS feedback (
  feedback_id INT PRIMARY KEY AUTO_INCREMENT,
  ho_ten VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  noi_dung LONGTEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
