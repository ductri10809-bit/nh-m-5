<?php
/**
 * don_hang.php - Model bang orders
 */
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';

class DonHang
{
    private PDO $db;

    public function __construct()
    {
        $this->db = ketNoiCSDL();
    }

    public function tao(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO orders (user_id, customer_name, customer_email, total_amount, order_status, address, phone)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['user_id'] ?? null,
            $data['customer_name'] ?? null,
            $data['customer_email'] ?? null,
            $data['tong_tien'],
            $data['trang_thai'] ?? 'cho_xu_ly',
            $data['dia_chi'] ?? '',
            $data['phone'] ?? '',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function layTheoUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, COALESCE(SUM(od.quantity), 0) AS item_count
             FROM orders o
             LEFT JOIN order_detail od ON od.order_id = o.order_id
             WHERE o.user_id = ?
             GROUP BY o.order_id
             ORDER BY o.order_date DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function timTheoId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE order_id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function layTatCa(): array
    {
        $stmt = $this->db->query('SELECT * FROM orders ORDER BY order_date DESC');
        return $stmt->fetchAll();
    }

    public function capNhatTrangThai(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE orders SET order_status = ? WHERE order_id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function xoa(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM orders WHERE order_id = ?');
        return $stmt->execute([$id]);
    }
}
