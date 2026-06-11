<?php
/**
 * bien_the.php - Model bang product_variant
 */
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';
require_once __DIR__ . '/../helpers/dinh_dang_san_pham.php';

class BienThe
{
    private PDO $db;

    public function __construct()
    {
        $this->db = ketNoiCSDL();
    }

    public function layTheoSanPham(int $productId): array
    {
        $sql = 'SELECT pv.variant_id, pv.base_product_id, pv.color_name, pv.color_hex,
                       COALESCE(NULLIF(pv.image_url, \'\'), p.image) AS image_url
                FROM product_variant pv
                LEFT JOIN product p ON p.product_id = pv.linked_product_id
                WHERE pv.base_product_id = ?
                ORDER BY pv.variant_id ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId]);
        return array_map('dinhDangBienThe', $stmt->fetchAll());
    }

    public function layTatCaMauSac(): array
    {
        $stmt = $this->db->query(
            'SELECT DISTINCT color_name, color_hex FROM product_variant ORDER BY color_name ASC'
        );
        return $stmt->fetchAll();
    }

    public function laySanPhamIdTheoMau(string $colorName): array
    {
        $stmt = $this->db->prepare(
            'SELECT DISTINCT base_product_id FROM product_variant WHERE color_name = ?'
        );
        $stmt->execute([$colorName]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }
}
