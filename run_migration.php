<?php
/**
 * Migration: Add contact management system
 * This script runs all necessary database alterations
 */
require_once __DIR__ . '/backend/cau_hinh/ket_noi_csdl.php';

try {
    $db = ketNoiCSDL();
    echo "Starting migration...\n";

    // Step 1: Add columns to contact_messages
    $columns = $db->query("SHOW COLUMNS FROM `contact_messages`")->fetchAll();
    $existingCols = array_column($columns, 'Field');

    if (!in_array('user_id', $existingCols)) {
        echo "Adding user_id column...\n";
        $db->exec("ALTER TABLE `contact_messages` ADD COLUMN `user_id` INT NULL AFTER `message_id`");
        echo "✓ user_id added\n";
    }

    if (!in_array('status', $existingCols)) {
        echo "Adding status column...\n";
        $db->exec("ALTER TABLE `contact_messages` ADD COLUMN `status` VARCHAR(50) DEFAULT 'pending' AFTER `message`");
        echo "✓ status added\n";
    }

    if (!in_array('updated_at', $existingCols)) {
        echo "Adding updated_at column...\n";
        $db->exec("ALTER TABLE `contact_messages` ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        echo "✓ updated_at added\n";
    }

    // Step 2: Drop feedback table
    $tables = $db->query("SHOW TABLES LIKE 'feedback'")->fetchAll();
    if (count($tables) > 0) {
        echo "Dropping feedback table...\n";
        $db->exec("DROP TABLE `feedback`");
        echo "✓ feedback table dropped\n";
    } else {
        echo "✓ feedback table already dropped\n";
    }

    // Step 3: Add indexes
    echo "Adding indexes...\n";
    try {
        $db->exec("ALTER TABLE `contact_messages` ADD INDEX `idx_user_id` (`user_id`)");
    } catch (Exception $e) {
        echo "  (idx_user_id already exists)\n";
    }

    try {
        $db->exec("ALTER TABLE `contact_messages` ADD INDEX `idx_status` (`status`)");
    } catch (Exception $e) {
        echo "  (idx_status already exists)\n";
    }

    try {
        $db->exec("ALTER TABLE `contact_messages` ADD INDEX `idx_created_at` (`created_at`)");
    } catch (Exception $e) {
        echo "  (idx_created_at already exists)\n";
    }

    echo "\n✅ Migration completed successfully!\n";

    // Show final table structure
    echo "\nFinal table structure:\n";
    $stmt = $db->query("SHOW COLUMNS FROM `contact_messages`");
    $columns = $stmt->fetchAll();
    foreach ($columns as $col) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }

} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    die(1);
}
?>
