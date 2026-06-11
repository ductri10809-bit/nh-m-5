<?php
/**
 * API: doi_mat_khau.php
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../helpers/bao_mat.php';
require_once __DIR__ . '/../middleware/xac_thuc.php';
require_once __DIR__ . '/../model/nguoi_dung.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    traVeJson(false, null, 'Method not allowed', 405);
}

yeuCauDangNhap();
$data = layDuLieuJson();

$old = trim($data['old_password'] ?? '');
$new = trim($data['new_password'] ?? '');

if ($old === '' || $new === '') {
    traVeJson(false, null, 'Thiếu mật khẩu cũ hoặc mật khẩu mới', 400);
}

if (strlen($new) < 6) {
    traVeJson(false, null, 'Mật khẩu mới phải ít nhất 6 ký tự', 400);
}

$userId = layUserId();
$model = new NguoiDung();
$user = $model->timTheoId($userId);
if (!$user) {
    traVeJson(false, null, 'Không tìm thấy người dùng', 404);
}

if (!kiemTraMatKhau($old, $user['password'])) {
    traVeJson(false, null, 'Mật khẩu cũ không đúng', 400);
}

$hash = hashMatKhau($new);
$ok = $model->capNhatMatKhau($userId, $hash);

traVeJson($ok, null, $ok ? 'Đổi mật khẩu thành công' : 'Cập nhật mật khẩu thất bại', $ok ? 200 : 500);
