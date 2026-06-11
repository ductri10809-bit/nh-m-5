-- Add OTP verification columns to users table
-- Run this in phpMyAdmin or: mysql -u root -p shop_db < database/add_otp_verification.sql

ALTER TABLE users ADD COLUMN IF NOT EXISTS otp_code VARCHAR(6) NULL DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS otp_expires_at DATETIME NULL DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS is_verified TINYINT(1) NOT NULL DEFAULT 0;

-- Create index for OTP lookup
CREATE INDEX IF NOT EXISTS idx_otp_code ON users(otp_code);
