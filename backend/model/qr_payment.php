<?php
/**
 * qr_payment.php - Model bang qr_payments
 */
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';

class QRPayment
{
    private PDO $db;

    public function __construct()
    {
        $this->db = ketNoiCSDL();
    }

    /**
     * Tạo QR payment record
     */
    public function tao(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO qr_payments (order_id, qr_code_data, admin_email, customer_email, amount, transaction_status)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['order_id'],
            $data['qr_code_data'],
            $data['admin_email'] ?? ADMIN_EMAIL,
            $data['customer_email'],
            $data['amount'],
            $data['transaction_status'] ?? 'pending',
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Lấy QR payment theo order_id
     */
    public function layTheoOrder(int $orderId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM qr_payments WHERE order_id = ? LIMIT 1'
        );
        $stmt->execute([$orderId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Lấy QR payment theo ID
     */
    public function layTheoId(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM qr_payments WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Cập nhật trạng thái QR payment
     */
    public function capNhatTrangThai(int $id, string $status): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE qr_payments SET transaction_status = ? WHERE id = ?'
        );
        return $stmt->execute([$status, $id]);
    }

    /**
     * Xác nhận thanh toán (duyệt bởi admin)
     */
    public function xacNhanThanhToan(int $id): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE qr_payments 
             SET transaction_status = "confirmed", admin_confirmed_at = NOW() 
             WHERE id = ?'
        );
        return $stmt->execute([$id]);
    }

    /**
     * Đánh dấu đã gửi thông báo cho khách
     */
    public function danhDauDaThongBao(int $id): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE qr_payments SET customer_notified_at = NOW() WHERE id = ?'
        );
        return $stmt->execute([$id]);
    }

    /**
     * Lấy danh sách QR payment pending (chờ duyệt)
     */
    public function layDanhSachCho(int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            'SELECT qr.*, o.customer_name, o.customer_email, o.total_amount, o.order_date
             FROM qr_payments qr
             JOIN orders o ON qr.order_id = o.order_id
             WHERE qr.transaction_status = "pending"
             ORDER BY qr.created_at DESC
             LIMIT ? OFFSET ?'
        );
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách QR payment đã confirmed
     */
    public function layDanhSachDaDuyet(int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            'SELECT qr.*, o.customer_name, o.customer_email, o.total_amount, o.order_date
             FROM qr_payments qr
             JOIN orders o ON qr.order_id = o.order_id
             WHERE qr.transaction_status = "confirmed"
             ORDER BY qr.admin_confirmed_at DESC
             LIMIT ? OFFSET ?'
        );
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Đếm số QR payment pending
     */
    public function demSoCho(): int
    {
        $stmt = $this->db->query(
            'SELECT COUNT(*) as count FROM qr_payments WHERE transaction_status = "pending"'
        );
        $result = $stmt->fetch();
        return (int)$result['count'];
    }

    /**
     * Cập nhật đường dẫn ảnh QR
     */
    public function capNhatQRImage(int $id, string $imagePath): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE qr_payments SET qr_image_path = ? WHERE id = ?'
        );
        return $stmt->execute([$imagePath, $id]);
    }
}
