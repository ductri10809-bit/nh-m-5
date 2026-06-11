<?php
/**
 * Direct test of feedback submission
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

try {
    require_once __DIR__ . '/../model/phan_hoi.php';
    require_once __DIR__ . '/../helpers/validate.php';
    require_once __DIR__ . '/../helpers/bao_mat.php';
    
    $model = new PhanHoi();
    
    $data = [
        'ho_ten' => 'Test User',
        'email' => 'test@gmail.com',
        'noi_dung' => 'Test message'
    ];
    
    // Validate
    $error = validateRequired($data, ['ho_ten', 'email', 'noi_dung']);
    if ($error) {
        echo "Validation Error: " . $error . "\n";
        exit(1);
    }
    
    if (!validateEmail($data['email'])) {
        echo "Email validation error\n";
        exit(1);
    }
    
    // Insert
    $id = $model->tao([
        'ho_ten'   => sanitize($data['ho_ten']),
        'email'    => sanitize($data['email']),
        'noi_dung' => sanitize($data['noi_dung']),
    ]);
    
    echo "Success! ID: " . $id . "\n";
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
