<?php
/**
 * danh_muc.php - Model bang category
 */
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';
require_once __DIR__ . '/../helpers/dinh_dang_san_pham.php';

class DanhMuc
{
    private PDO $db;

    public function __construct()
    {
        $this->db = ketNoiCSDL();
    }

    public function layTatCa(): array
    {
        $stmt = $this->db->query('SELECT * FROM category ORDER BY category_name ASC');
        return array_map('dinhDangDanhMuc', $stmt->fetchAll());
    }

    public function timTheoId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM category WHERE category_id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? dinhDangDanhMuc($row) : null;
    }

    public function tao(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO category (category_name) VALUES (?)');
        $stmt->execute([ $data['category_name'] ?? '' ]);
        return (int) $this->db->lastInsertId();
    }

    public function capNhat(int $id, array $data): bool
    {
        $stmt = $this->db->prepare('UPDATE category SET category_name = ? WHERE category_id = ?');
        return $stmt->execute([ $data['category_name'] ?? '', $id ]);
    }

    public function xoa(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM category WHERE category_id = ?');
        return $stmt->execute([$id]);
    }
}
