<?php
/**
 * Test database connection
 */
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';

try {
    $db = ketNoiCSDL();
    echo "Database connected successfully\n";
    
    // Test CREATE TABLE
    $sql = "CREATE TABLE IF NOT EXISTS test_feedback (
      id INT PRIMARY KEY AUTO_INCREMENT,
      name VARCHAR(100),
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $db->exec($sql);
    echo "Test table created successfully\n";
    
    // Try to insert
    $stmt = $db->prepare('INSERT INTO test_feedback (name) VALUES (?)');
    $stmt->execute(['Test']);
    echo "Insert successful\n";
    
    // Clean up
    $db->exec('DROP TABLE IF EXISTS test_feedback');
    echo "Test completed - all OK\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
