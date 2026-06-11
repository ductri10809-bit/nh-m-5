<?php
/**
 * API: yeu_thich.php
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../controller/yeu_thich_controller.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

$controller = new YeuThichController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $result = $controller->lay();
        break;
    case 'POST':
        $result = $controller->them(layDuLieuJson());
        break;
    case 'DELETE':
        $data = layDuLieuJson();
        $result = $controller->xoa((int) ($data['product_id'] ?? $_GET['product_id'] ?? 0));
        break;
    default:
        traVeJson(false, null, 'Method not allowed', 405);
}

traVeJson($result['success'], $result['data'] ?? null, $result['message'] ?? '');
