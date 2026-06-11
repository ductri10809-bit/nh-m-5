<?php
/**
 * test_filters_api.php - Test filter logic
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../model/san_pham.php';

$model = new SanPham();

// Test 1: Get all products
$allProducts = $model->layTatCa([]);
$total = count($allProducts);

// Test 2: Filter by is_sale=1 only
$saleOnly = $model->layTatCa(['is_sale' => 1]);
$saleCount = count($saleOnly);

// Test 3: Filter by is_trend=1 only
$trendOnly = $model->layTatCa(['is_trend' => 1]);
$trendCount = count($trendOnly);

// Test 4: Filter by both is_sale=1 AND is_trend=1 (OR logic)
$bothFilters = $model->layTatCa(['is_sale' => 1, 'is_trend' => 1]);
$bothCount = count($bothFilters);

// Analyze results
$hasSaleProducts = $saleCount > 0;
$hasTrendProducts = $trendCount > 0;
$hasBothFilter = $bothCount > 0;

// Check if OR logic is working
// When both filters are applied, we should get products that are SALE OR TREND
// So bothCount should be >= max(saleCount, trendCount)
$orLogicWorking = $bothCount >= max($saleCount, $trendCount);

$response = [
    'summary' => [
        'total_products' => $total,
        'sale_products_count' => $saleCount,
        'trend_products_count' => $trendCount,
        'sale_or_trend_products_count' => $bothCount,
    ],
    'test_results' => [
        'sale_filter_works' => $hasSaleProducts ? '✅ YES' : '❌ NO',
        'trend_filter_works' => $hasTrendProducts ? '✅ YES' : '❌ NO',
        'or_logic_works' => $orLogicWorking ? '✅ YES' : '❌ NO (Expected: ' . max($saleCount, $trendCount) . ' or more, Got: ' . $bothCount . ')',
    ],
    'sample_data' => [
        'sale_products' => array_slice($saleOnly, 0, 2),
        'trend_products' => array_slice($trendOnly, 0, 2),
        'both_filters' => array_slice($bothFilters, 0, 2),
    ]
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
