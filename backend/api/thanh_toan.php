<?php
/**
 * API: thanh_toan.php
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../middleware/xac_thuc.php';
require_once __DIR__ . '/../controller/thanh_toan_controller.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    traVeJson(false, null, 'Method not allowed', 405);
}

yeuCauDangNhap();
$data = layDuLieuJson();
$orderId = (int) ($data['order_id'] ?? 0);

if (!$orderId) {
    traVeJson(false, null, 'Thieu order_id', 400);
}

$result = (new ThanhToanController())->xuLy($orderId, $data);
traVeJson($result['success'], $result['data'] ?? null, $result['message'] ?? '', $result['success'] ? 200 : 400);
