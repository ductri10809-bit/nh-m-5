-- Migration: Add contact management system
-- Date: 2026-06-10

-- Step 1: Alter contact_messages table to add new columns
ALTER TABLE `contact_messages` 
ADD COLUMN `user_id` INT NULL AFTER `message_id`,
ADD COLUMN `status` VARCHAR(50) DEFAULT 'pending' AFTER `message`,
ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`,
ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL;

-- Step 2: Drop feedback table
DROP TABLE IF EXISTS `feedback`;

-- Step 3: Add index for contact_messages for better query performance
ALTER TABLE `contact_messages`
ADD INDEX `idx_user_id` (`user_id`),
ADD INDEX `idx_status` (`status`),
ADD INDEX `idx_created_at` (`created_at`);
