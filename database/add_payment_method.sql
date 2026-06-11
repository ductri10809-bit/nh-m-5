-- Add payment_method column to orders table
ALTER TABLE orders
ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) DEFAULT 'cash' AFTER customer_email;

-- Add index for payment_method for faster queries
ALTER TABLE orders
ADD INDEX idx_payment_method (payment_method);
