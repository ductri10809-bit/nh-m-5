<?php
/**
 * san_pham_controller.php
 */
require_once __DIR__ . '/../model/san_pham.php';

class SanPhamController
{
    private SanPham $model;

    public function __construct()
    {
        $this->model = new SanPham();
    }

    public function danhSach(array $filters = []): array
    {
        return ['success' => true, 'data' => $this->model->layTatCa($filters)];
    }

    public function chiTiet(int $id): array
    {
        $sp = $this->model->timTheoId($id);
        if (!$sp) return ['success' => false, 'message' => 'Khong tim thay san pham'];
        return ['success' => true, 'data' => $sp];
    }
}
