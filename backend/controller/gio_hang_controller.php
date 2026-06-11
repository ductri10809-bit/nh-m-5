<?php
/**
 * gio_hang_controller.php - Quan ly gio hang qua session
 */
require_once __DIR__ . '/../cau_hinh/session.php';

class GioHangController
{
    private function layGioHang(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    private function luuGioHang(array $cart): void
    {
        $_SESSION['cart'] = $cart;
    }

    public function lay(): array
    {
        return ['success' => true, 'data' => $this->layGioHang()];
    }

    public function them(array $item): array
    {
        $cart = $this->layGioHang();
        $id = (int) ($item['product_id'] ?? 0);
        $soLuong = (int) ($item['so_luong'] ?? 1);

        if (isset($cart[$id])) {
            $cart[$id]['so_luong'] += $soLuong;
        } else {
            $cart[$id] = [
                'product_id' => $id,
                'so_luong'   => $soLuong,
                'gia'        => $item['gia'] ?? 0,
                'ten'        => $item['ten'] ?? '',
            ];
        }

        $this->luuGioHang($cart);
        return ['success' => true, 'message' => 'Da them vao gio hang', 'data' => $cart];
    }

    public function xoa(int $productId): array
    {
        $cart = $this->layGioHang();
        unset($cart[$productId]);
        $this->luuGioHang($cart);
        return ['success' => true, 'message' => 'Da xoa khoi gio hang', 'data' => $cart];
    }
}
