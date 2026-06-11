<?php
/**
 * API: kiem_tra_csdl.php - Kiem tra ket noi database (GET)
 */
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    traVeJson(false, null, 'Method not allowed', 405);
}

try {
    $pdo = ketNoiCSDL();
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    $productCount = (int) $pdo->query('SELECT COUNT(*) FROM product')->fetchColumn();

    traVeJson(true, [
        'database' => DB_NAME,
        'host'     => DB_HOST,
        'tables'   => $tables,
        'product_rows' => $productCount,
    ], 'Ket noi database thanh cong');
} catch (Throwable $e) {
    traVeJson(false, null, 'Loi ket noi: ' . $e->getMessage(), 500);
}
