<?php
/**
 * API: san_pham.php
 */
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../controller/san_pham_controller.php';
require_once __DIR__ . '/../model/bien_the.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    traVeJson(false, null, 'Method not allowed', 405);
}

if (!empty($_GET['mau_sac_list'])) {
    $model = new BienThe();
    traVeJson(true, $model->layTatCaMauSac());
}

$controller = new SanPhamController();

if (!empty($_GET['id'])) {
    $result = $controller->chiTiet((int) $_GET['id']);
} else {
    $filters = [];
    if (!empty($_GET['category_id'])) {
        $filters['category_id'] = (int) $_GET['category_id'];
    }
    if (!empty($_GET['tim'])) {
        $filters['tim'] = $_GET['tim'];
    }
    if (!empty($_GET['noi_bat'])) {
        $filters['noi_bat'] = 1;
    }
    if (!empty($_GET['is_sale'])) {
        $filters['is_sale'] = 1;
    }
    if (!empty($_GET['is_trend'])) {
        $filters['is_trend'] = 1;
    }
    if (!empty($_GET['mau_sac'])) {
        $filters['mau_sac'] = $_GET['mau_sac'];
    }
    $result = $controller->danhSach($filters);
}

traVeJson($result['success'], $result['data'] ?? null, $result['message'] ?? '');
