<?php
/**
 * API: dat_hang.php - Dat hang khach (khong bat buoc dang nhap)
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../controller/don_hang_controller.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    traVeJson(false, null, 'Method not allowed', 405);
}

$controller = new DonHangController();
$userId = layUserId() ?: null;
$result = $controller->taoDon($userId, layDuLieuJson());

traVeJson(
    $result['success'],
    $result['data'] ?? null,
    $result['message'] ?? '',
    $result['success'] ? 200 : 400
);
