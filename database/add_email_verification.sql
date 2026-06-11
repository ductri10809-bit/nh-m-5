-- Add email verification support to users table
-- This script adds the necessary columns for email verification

ALTER TABLE users ADD COLUMN IF NOT EXISTS is_verified TINYINT(1) DEFAULT 0;
ALTER TABLE users ADD COLUMN IF NOT EXISTS verification_code VARCHAR(64) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS verification_code_expires_at DATETIME NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS verified_at DATETIME NULL;

-- Create index for faster verification code lookup
CREATE INDEX IF NOT EXISTS idx_verification_code ON users(verification_code);
