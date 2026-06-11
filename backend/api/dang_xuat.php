<?php
/**
 * API: dang_xuat.php
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    traVeJson(false, null, 'Method not allowed', 405);
}

// Clear session data
$_SESSION = [];

// Delete cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy session
session_destroy();

traVeJson(true, null, 'Đã đăng xuất thành công');
