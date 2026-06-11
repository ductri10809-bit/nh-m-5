<?php
/**
 * API: debug_filters.php - Debug filter functionality
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../model/san_pham.php';

$model = new SanPham();

// Get all products without filter
$all = $model->layTatCa([]);
$allCount = count($all);

// Get only sale products
$sale = $model->layTatCa(['is_sale' => 1]);
$saleCount = count($sale);

// Get only trend products
$trend = $model->layTatCa(['is_trend' => 1]);
$trendCount = count($trend);

// Sample data
$saleData = array_slice($sale, 0, 3);
$trendData = array_slice($trend, 0, 3);

echo json_encode([
    'test_results' => [
        'all_products' => $allCount,
        'sale_only' => $saleCount,
        'trend_only' => $trendCount,
        'sale_sample' => $saleData,
        'trend_sample' => $trendData,
        'query_test' => [
            'is_sale_filter_working' => $saleCount > 0 && $saleCount < $allCount ? 'YES' : 'NO - Issue detected',
            'is_trend_filter_working' => $trendCount > 0 && $trendCount < $allCount ? 'YES' : 'NO - Issue detected',
        ]
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
