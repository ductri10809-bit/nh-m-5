<?php
/**
 * API: verify_otp.php
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
$otp = $data['otp'] ?? '';

if (empty($otp)) {
    traVeJson(false, null, 'Mã OTP không hợp lệ', 400);
}

$controller = new NguoiDungController();
$result = $controller->verifyOtp($otp);

traVeJson($result['success'], $result['data'] ?? null, $result['message'], $result['success'] ? 200 : 400);
