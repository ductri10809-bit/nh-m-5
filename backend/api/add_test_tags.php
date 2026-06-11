<?php
/**
 * API: add_test_tags.php
 * Adds sale and trend tags to ~45% of products for testing
 */
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    traVeJson(false, null, 'Only POST allowed', 405);
}

try {
    $db = ketNoiCSDL();
    
    // Get all products
    $stmt = $db->query('SELECT product_id FROM product ORDER BY product_id');
    $products = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    
    if (empty($products)) {
        traVeJson(false, null, 'Không có sản phẩm nào', 400);
    }
    
    $count = count($products);
    $targetCount = (int) ($count * 0.45); // 45% of products
    
    // Randomly shuffle and pick 45%
    shuffle($products);
    $selectedProducts = array_slice($products, 0, $targetCount);
    
    // Update selected products
    $updated = 0;
    $db->beginTransaction();
    
    foreach ($selectedProducts as $productId) {
        // Randomly assign either sale, trend, or both
        $rand = rand(1, 3);
        $isSale = ($rand === 1 || $rand === 3) ? 1 : 0;
        $isTrend = ($rand === 2 || $rand === 3) ? 1 : 0;
        
        $stmt = $db->prepare('UPDATE product SET is_sale = ?, is_trend = ? WHERE product_id = ?');
        $stmt->execute([$isSale, $isTrend, $productId]);
        $updated++;
    }
    
    $db->commit();
    
    traVeJson(true, [
        'products_total' => $count,
        'products_tagged' => $updated,
        'percentage' => round(($updated / $count) * 100, 1) . '%',
    ], "Đã gắn thẻ cho $updated sản phẩm (~45% của $count sản phẩm)", 200);
    
} catch (Exception $e) {
    traVeJson(false, null, 'Lỗi: ' . $e->getMessage(), 500);
}
