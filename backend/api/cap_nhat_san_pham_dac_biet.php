<?php
/**
 * API: cap_nhat_san_pham_dac_biet.php - Update is_trend, is_sale, sale_price
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    traVeJson(false, null, 'Method not allowed', 405);
}

if (!isset($_POST['product_id']) || !isset($_POST['field']) || !isset($_POST['value'])) {
    traVeJson(false, null, 'Missing parameters', 400);
}

$product_id = intval($_POST['product_id']);
$field = $_POST['field'];
$value = $_POST['value'];

// Validate field
if (!in_array($field, ['is_trend', 'is_sale', 'is_bestseller', 'sale_price'])) {
    traVeJson(false, null, 'Invalid field', 400);
}

try {
    $pdo = ketNoiCSDL();
    
    // If updating sale_price, also set is_sale = 1
    if ($field === 'sale_price') {
        $value = intval($value);
        $sql = "UPDATE product SET sale_price = ?, is_sale = 1 WHERE product_id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$value, $product_id]);
    } else {
        $sql = "UPDATE product SET $field = ? WHERE product_id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$value ? 1 : 0, $product_id]);
    }
    
    if ($success) {
        traVeJson(true, null, 'Update successful', 200);
    } else {
        traVeJson(false, null, 'Update failed', 500);
    }
} catch (Exception $e) {
    traVeJson(false, null, $e->getMessage(), 500);
}
