-- Add address column to users table
ALTER TABLE users
ADD COLUMN IF NOT EXISTS dia_chi TEXT NULL AFTER phone;
