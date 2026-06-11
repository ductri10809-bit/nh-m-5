<?php
/**
 * API: lien_he_danh_sach.php - Get contact list (admin only)
 */
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../controller/lien_he_controller.php';
require_once __DIR__ . '/../middleware/quan_tri.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    traVeJson(false, null, 'Method not allowed', 405);
}

try {
    yeuCauAdmin();
    
    $filters = [
        'status' => $_GET['status'] ?? null,
        'user_id' => $_GET['user_id'] ?? null,
        'date_from' => $_GET['date_from'] ?? null,
        'date_to' => $_GET['date_to'] ?? null,
    ];

    $result = (new LienHeController())->getDanhSach($filters);
    traVeJson($result['success'], $result['data'] ?? null, $result['message'] ?? '', $result['success'] ? 200 : 400);
} catch (Exception $e) {
    traVeJson(false, null, $e->getMessage(), 403);
}
