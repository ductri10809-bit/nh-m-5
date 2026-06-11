<?php
/**
 * Test contact form submission
 */
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../model/phan_hoi.php';

try {
    $model = new PhanHoi();
    $id = $model->tao([
        'ho_ten' => 'Test User',
        'email' => 'test@gmail.com',
        'noi_dung' => 'Test message'
    ]);
    traVeJson(true, ['id' => $id], 'Test successful');
} catch (Exception $e) {
    traVeJson(false, null, 'Error: ' . $e->getMessage(), 500);
}
