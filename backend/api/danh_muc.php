<?php
/**
 * API: danh_muc.php
 */
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../controller/danh_muc_controller.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    traVeJson(false, null, 'Method not allowed', 405);
}

$controller = new DanhMucController();

if (!empty($_GET['id'])) {
    $result = $controller->chiTiet((int) $_GET['id']);
} else {
    $result = $controller->layTatCa();
}

traVeJson($result['success'], $result['data'] ?? null, $result['message'] ?? '');
