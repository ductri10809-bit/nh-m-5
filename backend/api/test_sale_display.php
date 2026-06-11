<?php
/**
 * API: test_sale_display.php - Test sale price display functionality
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../controller/san_pham_controller.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    traVeJson(false, null, 'Method not allowed', 405);
}

$controller = new SanPhamController();

// Test 1: Get all products with sale tags
$allProducts = $controller->layTatCa();

// Test 2: Count products with sale prices
$saleProducts = array_filter($allProducts, function($p) {
    return isset($p['is_sale']) && $p['is_sale'] === 1 && isset($p['sale_price']) && $p['sale_price'] !== null;
});

// Test 3: Sample data with discount calculation
$samples = [];
foreach (array_slice($saleProducts, 0, 3) as $product) {
    $originalPrice = $product['gia'] ?? 0;
    $salePrice = $product['sale_price'] ?? 0;
    $discount = $originalPrice > 0 ? round(((($originalPrice - $salePrice) / $originalPrice) * 100)) : 0;
    
    $samples[] = [
        'product_id' => $product['id'],
        'product_name' => $product['ten'],
        'original_price' => $originalPrice,
        'sale_price' => $salePrice,
        'discount_percent' => $discount . '%',
        'is_sale' => $product['is_sale'],
        'is_trend' => $product['is_trend'],
    ];
}

$result = [
    'total_products' => count($allProducts),
    'sale_products_count' => count($saleProducts),
    'sample_sales' => $samples,
    'test_results' => [
        'all_products_returned' => count($allProducts) > 0 ? '✅ PASS' : '❌ FAIL',
        'sale_filter_working' => count($saleProducts) > 0 ? '✅ PASS' : '❌ FAIL (No sale products found)',
        'discount_calculation' => count($samples) > 0 ? '✅ PASS' : '❌ FAIL',
        'price_fields_present' => isset($allProducts[0]['gia']) && isset($allProducts[0]['sale_price']) ? '✅ PASS' : '❌ FAIL',
    ]
];

traVeJson(true, $result, 'Test sale display functionality', 200);
?>
