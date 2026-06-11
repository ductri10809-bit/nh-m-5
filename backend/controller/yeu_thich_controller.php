<?php
/**
 * yeu_thich_controller.php - Quan ly yeu thich qua session
 */
require_once __DIR__ . '/../cau_hinh/session.php';

class YeuThichController
{
    private function layDanhSach(): array
    {
        return $_SESSION['wishlist'] ?? [];
    }

    private function luu(array $list): void
    {
        $_SESSION['wishlist'] = $list;
    }

    public function lay(): array
    {
        return ['success' => true, 'data' => array_values($this->layDanhSach())];
    }

    public function them(array $item): array
    {
        $list = $this->layDanhSach();
        $id = (int) ($item['product_id'] ?? 0);
        $list[$id] = $item;
        $this->luu($list);
        return ['success' => true, 'message' => 'Da them vao yeu thich'];
    }

    public function xoa(int $productId): array
    {
        $list = $this->layDanhSach();
        unset($list[$productId]);
        $this->luu($list);
        return ['success' => true, 'message' => 'Da xoa khoi yeu thich'];
    }
}
