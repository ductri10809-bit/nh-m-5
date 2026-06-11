<?php
/**
 * API: dang_ky.php
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../middleware/khach.php';
require_once __DIR__ . '/../controller/nguoi_dung_controller.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    traVeJson(false, null, 'Method not allowed', 405);
}

chiChoKhach();
$data = layDuLieuJson();
$result = (new NguoiDungController())->dangKy($data);
traVeJson($result['success'], $result['data'] ?? null, $result['message'], $result['success'] ? 201 : 400);
