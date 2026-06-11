<?php
/**
 * khach.php - Middleware chi cho phep khach (chua dang nhap)
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';

function chiChoKhach(): void
{
    if (layUserId()) {
        traVeJson(false, null, 'Ban da dang nhap roi', 400);
    }
}
