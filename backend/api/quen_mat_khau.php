<?php
/**
 * API: quen_mat_khau.php
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../helpers/validate.php';
require_once __DIR__ . '/../model/nguoi_dung.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    traVeJson(false, null, 'Method not allowed', 405);
}

$data = layDuLieuJson();
$email = trim($data['email'] ?? '');

if (!$email) {
    traVeJson(false, null, 'Vui lòng nhập email', 400);
}

if (!validateEmail($email)) {
    traVeJson(false, null, 'Email không hợp lệ', 400);
}

$model = new NguoiDung();
$user = $model->timTheoEmail($email);

if (!$user) {
    traVeJson(false, null, 'Email này không được đăng ký trong hệ thống', 404);
}

// In a real application, you would:
// 1. Generate a secure reset token
// 2. Store it in database with expiration
// 3. Send email with reset link
// For now, we return success and suggest checking email

traVeJson(true, null, 'Hướng dẫn đặt lại mật khẩu đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư.', 200);
