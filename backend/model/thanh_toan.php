<?php
/**
 * thanh_toan.php - Model bang payments
 */
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';

class ThanhToan
{
    private PDO $db;

    public function __construct()
    {
        $this->db = ketNoiCSDL();
    }

    public function tao(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO payments (order_id, phuong_thuc, so_tien, trang_thai) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['order_id'],
            $data['phuong_thuc'],
            $data['so_tien'],
            $data['trang_thai'] ?? 'cho_xu_ly',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function layTheoDonHang(int $orderId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE order_id = ? LIMIT 1');
        $stmt->execute([$orderId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
