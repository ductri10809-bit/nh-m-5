<?php
/**
 * API: dang_ky_admin.php
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../middleware/khach.php';
require_once __DIR__ . '/../controller/nguoi_dung_controller.php';
require_once __DIR__ . '/../helpers/validate.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    traVeJson(false, null, 'Method not allowed', 405);
}

chiChoKhach();
$data = layDuLieuJson();

// Admin registration code - set this to a secret code
define('ADMIN_REGISTRATION_CODE', 'admin2024');

// Verify admin code
$adminCode = $data['admin_code'] ?? '';
if ($adminCode !== ADMIN_REGISTRATION_CODE) {
    traVeJson(false, null, 'Mã code quản trị không chính xác', 400);
}

// Validate required fields
$loi = validateRequired($data, ['ho_ten', 'email', 'password']);
if ($loi) {
    traVeJson(false, null, $loi, 400);
}

// Validate email format
if (!validateEmail($data['email'])) {
    traVeJson(false, null, 'Email không hợp lệ', 400);
}

// Validate password match
if ($data['password'] !== ($data['confirm_password'] ?? '')) {
    traVeJson(false, null, 'Mật khẩu xác nhận không khớp', 400);
}

// Call registration controller but with admin role
$data['role'] = 'admin';
$result = (new NguoiDungController())->dangKy($data);

if ($result['success']) {
    traVeJson(true, $result['data'], 'Đăng ký admin thành công', 200);
} else {
    traVeJson(false, null, $result['message'] ?? 'Đăng ký thất bại', 400);
}
