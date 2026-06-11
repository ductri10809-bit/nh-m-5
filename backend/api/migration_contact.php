<?php
/**
 * Migration API - Run database schema updates
 * Access: /backend/api/migration_contact.php
 */
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';

// CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

try {
    $db = ketNoiCSDL();
    $messages = [];

    // Step 1: Add columns to contact_messages
    $columns = $db->query("SHOW COLUMNS FROM `contact_messages`")->fetchAll();
    $existingCols = array_column($columns, 'Field');

    if (!in_array('user_id', $existingCols)) {
        $db->exec("ALTER TABLE `contact_messages` ADD COLUMN `user_id` INT NULL AFTER `message_id`");
        $messages[] = "✓ user_id column added";
    } else {
        $messages[] = "✓ user_id column already exists";
    }

    if (!in_array('status', $existingCols)) {
        $db->exec("ALTER TABLE `contact_messages` ADD COLUMN `status` VARCHAR(50) DEFAULT 'pending' AFTER `message`");
        $messages[] = "✓ status column added";
    } else {
        $messages[] = "✓ status column already exists";
    }

    if (!in_array('updated_at', $existingCols)) {
        $db->exec("ALTER TABLE `contact_messages` ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        $messages[] = "✓ updated_at column added";
    } else {
        $messages[] = "✓ updated_at column already exists";
    }

    // Step 2: Drop feedback table
    $tables = $db->query("SHOW TABLES LIKE 'feedback'")->fetchAll();
    if (count($tables) > 0) {
        $db->exec("DROP TABLE `feedback`");
        $messages[] = "✓ feedback table dropped";
    } else {
        $messages[] = "✓ feedback table already dropped";
    }

    // Step 3: Add indexes
    try {
        $db->exec("ALTER TABLE `contact_messages` ADD INDEX `idx_user_id` (`user_id`)");
        $messages[] = "✓ idx_user_id index added";
    } catch (Exception $e) {
        $messages[] = "✓ idx_user_id index already exists";
    }

    try {
        $db->exec("ALTER TABLE `contact_messages` ADD INDEX `idx_status` (`status`)");
        $messages[] = "✓ idx_status index added";
    } catch (Exception $e) {
        $messages[] = "✓ idx_status index already exists";
    }

    try {
        $db->exec("ALTER TABLE `contact_messages` ADD INDEX `idx_created_at` (`created_at`)");
        $messages[] = "✓ idx_created_at index added";
    } catch (Exception $e) {
        $messages[] = "✓ idx_created_at index already exists";
    }

    traVeJson(true, ['messages' => $messages], 'Migration completed successfully!');

} catch (Exception $e) {
    traVeJson(false, null, 'Migration failed: ' . $e->getMessage(), 500);
}
?>
