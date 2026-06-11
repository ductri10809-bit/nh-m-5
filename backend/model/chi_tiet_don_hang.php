<?php
/**
 * chi_tiet_don_hang.php - Model bang order_detail
 */
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';

class ChiTietDonHang
{
    private PDO $db;

    public function __construct()
    {
        $this->db = ketNoiCSDL();
    }

    public function tao(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO order_detail (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['order_id'],
            $data['product_id'],
            $data['so_luong'],
            $data['gia'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function layTheoDonHang(int $orderId): array
    {
        $stmt = $this->db->prepare(
            'SELECT od.*, p.product_name AS ten, p.image AS hinh_anh
             FROM order_detail od
             JOIN product p ON p.product_id = od.product_id
             WHERE od.order_id = ?'
        );
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public function xoaTheoDonHang(int $orderId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM order_detail WHERE order_id = ?');
        return $stmt->execute([$orderId]);
    }
}
