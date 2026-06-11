<?php
/**
 * Simple contact test
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

header('Content-Type: application/json; charset=utf-8');

try {
    // Test 1: Check database connection
    require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';
    $db = ketNoiCSDL();
    echo json_encode(['test1' => 'DB Connected OK']);
    die();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    die();
}
