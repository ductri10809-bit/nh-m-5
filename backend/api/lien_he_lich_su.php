<?php
/**
 * API: lien_he_lich_su.php - Get user's contact history
 */
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../controller/lien_he_controller.php';
require_once __DIR__ . '/../middleware/xac_thuc.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    traVeJson(false, null, 'Method not allowed', 405);
}

try {
    yeuCauDangNhap();
    
    $userId = layUserId();
    $result = (new LienHeController())->getLichSuUser($userId);
    traVeJson($result['success'], $result['data'] ?? null, $result['message'] ?? '', $result['success'] ? 200 : 400);
} catch (Exception $e) {
    traVeJson(false, null, $e->getMessage(), 500);
}
