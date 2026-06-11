<?php
/**
 * API: lien_he_cap_nhat.php - Update contact status (admin only)
 */
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../controller/lien_he_controller.php';
require_once __DIR__ . '/../middleware/quan_tri.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    traVeJson(false, null, 'Method not allowed', 405);
}

try {
    yeuCauAdmin();
    
    $data = layDuLieuJson();
    
    if (empty($data['message_id']) || empty($data['status'])) {
        traVeJson(false, null, 'Missing required fields', 400);
    }

    $result = (new LienHeController())->updateStatus($data['message_id'], $data['status']);
    traVeJson($result['success'], null, $result['message'], $result['success'] ? 200 : 400);
} catch (Exception $e) {
    traVeJson(false, null, $e->getMessage(), 403);
}
