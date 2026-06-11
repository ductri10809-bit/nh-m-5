<?php
/**
 * phan_hoi.php - Model bang contact_messages
 */
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';

class PhanHoi
{
    private PDO $db;

    public function __construct()
    {
        $this->db = ketNoiCSDL();
    }

    public function tao(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO contact_messages (fullname, email, message, user_id, status) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['fullname'] ?? $data['ho_ten'] ?? '',
            $data['email'],
            $data['message'] ?? $data['noi_dung'] ?? '',
            $data['user_id'] ?? null,
            'pending'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function getDanhSach(array $filters = []): array
    {
        $query = 'SELECT * FROM contact_messages WHERE 1=1';
        $params = [];

        if (!empty($filters['status'])) {
            $query .= ' AND status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['user_id'])) {
            $query .= ' AND user_id = ?';
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['date_from'])) {
            $query .= ' AND DATE(created_at) >= ?';
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $query .= ' AND DATE(created_at) <= ?';
            $params[] = $filters['date_to'];
        }

        $query .= ' ORDER BY created_at DESC LIMIT 100';

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM contact_messages WHERE message_id = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE contact_messages SET status = ?, updated_at = NOW() WHERE message_id = ?'
        );
        return $stmt->execute([$status, $id]);
    }

    public function getLichSuUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM contact_messages WHERE user_id = ? ORDER BY created_at DESC LIMIT 50'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
