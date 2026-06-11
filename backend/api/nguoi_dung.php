<?php
/**
 * API: nguoi_dung.php
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../middleware/xac_thuc.php';
require_once __DIR__ . '/../controller/nguoi_dung_controller.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

yeuCauDangNhap();
$controller = new NguoiDungController();
$userId = layUserId();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $result = $controller->layHoSo($userId);
        break;
    case 'PUT':
        $result = $controller->capNhatHoSo($userId, layDuLieuJson());
        break;
    default:
        traVeJson(false, null, 'Method not allowed', 405);
}

traVeJson($result['success'], $result['data'] ?? null, $result['message'] ?? '', $result['success'] ? 200 : 400);
