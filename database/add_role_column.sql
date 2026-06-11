-- Add role column to users table if it doesn't exist
ALTER TABLE users
ADD COLUMN IF NOT EXISTS role VARCHAR(20) NOT NULL DEFAULT 'customer';

-- Update existing users to have role = 'customer'
UPDATE users SET role = 'customer' WHERE role IS NULL OR role = '';

-- Show all users to verify
SELECT user_id, email, fullname, role FROM users;
