<?php
/**
 * danh_muc_controller.php
 */
require_once __DIR__ . '/../model/danh_muc.php';

class DanhMucController
{
    private DanhMuc $model;

    public function __construct()
    {
        $this->model = new DanhMuc();
    }

    public function layTatCa(): array
    {
        return ['success' => true, 'data' => $this->model->layTatCa()];
    }

    public function chiTiet(int $id): array
    {
        $dm = $this->model->timTheoId($id);
        if (!$dm) return ['success' => false, 'message' => 'Khong tim thay danh muc'];
        return ['success' => true, 'data' => $dm];
    }
}
