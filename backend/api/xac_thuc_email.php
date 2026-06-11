<?php
/**
 * API: xac_thuc_email.php
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../controller/nguoi_dung_controller.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    traVeJson(false, null, 'Method not allowed', 405);
}

$data = layDuLieuJson();
$code = $data['code'] ?? '';

if (empty($code)) {
    traVeJson(false, null, 'Ma xac thuc khong hop le', 400);
}

$controller = new NguoiDungController();
$result = $controller->xacThucEmail($code);

traVeJson($result['success'], $result['data'] ?? null, $result['message'], $result['success'] ? 200 : 400);
