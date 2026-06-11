<?php
/**
 * xac_thuc.php - Middleware yeu cau dang nhap
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';

function yeuCauDangNhap(): void
{
    if (!layUserId()) {
        traVeJson(false, null, 'Vui long dang nhap', 401);
    }
}
