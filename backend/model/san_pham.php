<?php
/**
 * san_pham.php - Model bang product
 */
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';
require_once __DIR__ . '/../helpers/dinh_dang_san_pham.php';
require_once __DIR__ . '/bien_the.php';

class SanPham
{
    private PDO $db;
    private BienThe $bienThe;

    public function __construct()
    {
        $this->db = ketNoiCSDL();
        $this->bienThe = new BienThe();
    }

    public function layTatCa(array $filters = []): array
    {
        $sql = 'SELECT p.*, c.category_name
                FROM product p
                LEFT JOIN category c ON c.category_id = p.category_id';
        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= ' WHERE p.category_id = ?';
            $params[] = $filters['category_id'];
        }

        if (!empty($filters['tim'])) {
            if (strpos($sql, 'WHERE') !== false) {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql .= 'p.product_name LIKE ?';
            $params[] = '%' . $filters['tim'] . '%';
        }

        if (!empty($filters['noi_bat'])) {
            if (strpos($sql, 'WHERE') !== false) {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql .= 'p.is_bestseller = 1';
        }

        if (!empty($filters['is_trend']) || !empty($filters['is_sale'])) {
            if (strpos($sql, 'WHERE') !== false) {
                $sql .= ' AND (';
            } else {
                $sql .= ' WHERE (';
            }
            
            $conditions = [];
            if (!empty($filters['is_trend'])) {
                $conditions[] = 'p.is_trend = 1';
            }
            if (!empty($filters['is_sale'])) {
                $conditions[] = 'p.is_sale = 1';
            }
            
            $sql .= implode(' OR ', $conditions) . ')';
        }

        if (!empty($filters['mau_sac'])) {
            $ids = $this->bienThe->laySanPhamIdTheoMau($filters['mau_sac']);
            if (empty($ids)) {
                return [];
            }
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            if (strpos($sql, 'WHERE') !== false) {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql .= "p.product_id IN ($placeholders)";
            $params = array_merge($params, $ids);
        }

        $sql .= ' ORDER BY p.is_bestseller DESC, p.product_id DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        return array_map(function ($row) {
            $sp = dinhDangSanPham($row);
            $sp['bien_the'] = $this->bienThe->layTheoSanPham($sp['id']);
            if (!empty($sp['bien_the'][0]['hinh_anh'])) {
                $sp['hinh_anh'] = $sp['bien_the'][0]['hinh_anh'];
            }
            return $sp;
        }, $rows);
    }

    public function timTheoId(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.category_name
             FROM product p
             LEFT JOIN category c ON c.category_id = p.category_id
             WHERE p.product_id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        $sp = dinhDangSanPham($row);
        $sp['bien_the'] = $this->bienThe->layTheoSanPham($id);
        return $sp;
    }

    public function tao(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO product (category_id, product_name, price, stock_quantity, image, description, is_bestseller, is_trend, is_sale, sale_price)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['category_id'] ?? null,
            $data['product_name'] ?? '',
            $data['price'] ?? 0,
            $data['stock_quantity'] ?? 0,
            $data['image'] ?? '',
            $data['description'] ?? '',
            $data['is_bestseller'] ? 1 : 0,
            $data['is_trend'] ? 1 : 0,
            $data['is_sale'] ? 1 : 0,
            isset($data['sale_price']) && $data['sale_price'] ? $data['sale_price'] : null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function capNhat(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE product SET category_id = ?, product_name = ?, price = ?, stock_quantity = ?, image = ?, description = ?, is_bestseller = ?, is_trend = ?, is_sale = ?, sale_price = ?
             WHERE product_id = ?'
        );
        return $stmt->execute([
            $data['category_id'] ?? null,
            $data['product_name'] ?? '',
            $data['price'] ?? 0,
            $data['stock_quantity'] ?? 0,
            $data['image'] ?? '',
            $data['description'] ?? '',
            $data['is_bestseller'] ? 1 : 0,
            $data['is_trend'] ? 1 : 0,
            $data['is_sale'] ? 1 : 0,
            isset($data['sale_price']) && $data['sale_price'] ? $data['sale_price'] : null,
            $id,
        ]);
    }

    public function xoa(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM product WHERE product_id = ?');
        return $stmt->execute([$id]);
    }
}
