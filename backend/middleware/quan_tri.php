<?php
/**
 * quan_tri.php - Middleware yêu cầu quyền admin
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../model/nguoi_dung.php';

function yeuCauAdmin(): void
{
    $userId = layUserId();
    if (!$userId) {
        traVeJson(false, null, 'Vui lòng đăng nhập', 401);
    }

    $user = (new NguoiDung())->timTheoId($userId);
    if (!$user || ($user['role'] ?? 'customer') !== 'admin') {
        traVeJson(false, null, 'Bạn không có quyền truy cập', 403);
    }
}
