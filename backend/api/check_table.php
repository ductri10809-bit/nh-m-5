<?php
/**
 * Check contact_messages table structure
 */
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';

try {
    $db = ketNoiCSDL();
    
    $stmt = $db->prepare("DESCRIBE contact_messages");
    $stmt->execute();
    $columns = $stmt->fetchAll();
    
    if (empty($columns)) {
        echo "Table contact_messages does not exist\n";
    } else {
        echo "Columns in contact_messages table:\n";
        foreach ($columns as $col) {
            echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
