<?php
/**
 * API: them_san_pham_mau.php - Thêm sản phẩm mẫu
 */
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';

header('Content-Type: text/plain; charset=utf-8');

$pdo = ketNoiCSDL();

$sanPham = [
    [1, 5, 'Áo vest Tailored Laine', 28500000, 12, 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&w=1200&q=80', 'Silhouette structured, vai tự nhiên.', 1, 1, 0, null],
    [2, 1, 'Bộ suit Navy Double', 32000000, 8, 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=1200&q=80', 'Bộ suit hai cúc, vải wool cao cấp.', 1, 0, 1, 22400000],
    [3, 2, 'Áo khoác Cashmere Long', 24500000, 10, 'https://images.unsplash.com/photo-1539533018447-63fcce2678e3?auto=format&fit=crop&w=1200&q=80', 'Dáng dài minimalist, cashmere blend.', 1, 1, 0, null],
    [4, 3, 'Váy lụa Midi Triomphe', 19800000, 15, 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=1200&q=80', 'Váy midi phom thẳng, lụa satin.', 1, 0, 1, 13860000],
    [5, 4, 'Túi Ava Leather', 42000000, 6, 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=1200&q=80', 'Túi da bê cao cấp, hardware vàng.', 1, 1, 0, null],
    [6, 4, 'Túi Belt Triomphe', 38500000, 5, 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=1200&q=80', 'Túi đeo chéo có thắt lưng, da grain.', 0, 0, 1, 26950000],
    [7, 6, 'Quần Wide-leg Wool', 12500000, 20, 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=1200&q=80', 'Quần ống rộng, wool blend.', 0, 1, 0, null],
    [8, 2, 'Blazer Structured Silk', 21500000, 9, 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=1200&q=80', 'Blazer một hàng khuy, lụa pha wool.', 1, 1, 0, null],
    [9, 3, 'Váy Cocktail Satin', 17500000, 11, 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?auto=format&fit=crop&w=1200&q=80', 'Váy cocktail satin, cổ boat neck.', 0, 0, 1, 12250000],
    [10, 7, 'Loafers Leather Classic', 9800000, 14, 'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=1200&q=80', 'Giày da bóng, đế leather.', 0, 1, 0, null],
];

try {
    // Xóa sản phẩm cũ
    $pdo->exec('DELETE FROM order_detail');
    $pdo->exec('DELETE FROM product_variant');
    $pdo->exec('DELETE FROM product');
    $pdo->exec('ALTER TABLE product AUTO_INCREMENT = 1');
    
    $sql = 'INSERT INTO product (product_id, category_id, product_name, price, stock_quantity, image, description, is_bestseller, is_trend, is_sale, sale_price) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
    $stmt = $pdo->prepare($sql);
    
    foreach ($sanPham as $sp) {
        $stmt->execute($sp);
    }
    
    // Thêm variants cho các sản phẩm
    $variants = [
    [1, 'Đen', '#1a1a1a', 1],
    [1, 'Kem', '#f5f0e8', 1],
    [2, 'Navy', '#1e3a5f', 2],
    [2, 'Charcoal', '#36454f', 2],
    [3, 'Camel', '#c19a6b', 3],
    [3, 'Đen', '#1a1a1a', 3],
    [4, 'Ivory', '#fffff0', 4],
    [4, 'Noir', '#0d0d0d', 4],
    [5, 'Tan', '#d2b48c', 5],
    [5, 'Đen', '#1a1a1a', 5],
    [6, 'Đen', '#1a1a1a', 6],
    [6, 'Trắng', '#fafafa', 6],
    [7, 'Be', '#e8dcc8', 7],
    [7, 'Đen', '#1a1a1a', 7],
    [8, 'Stone', '#b8b2a8', 8],
    [8, 'Đen', '#1a1a1a', 8],
    [9, 'Champagne', '#f7e7ce', 9],
    [9, 'Emerald', '#046307', 9],
    [10, 'Nâu', '#5c4033', 10],
    [10, 'Đen', '#1a1a1a', 10],
];
    
    $sqlVariant = 'INSERT INTO product_variant (base_product_id, color_name, color_hex, linked_product_id) 
                   VALUES (?, ?, ?, ?)';
    $stmtVariant = $pdo->prepare($sqlVariant);
    
    foreach ($variants as $var) {
        $stmtVariant->execute($var);
    }
    
    echo "✓ Đã thêm 10 sản phẩm thành công!\n";
    echo "✓ Đã thêm 20 variants!\n";
    echo "\nSản phẩm Xu hướng: 1, 3, 5, 7, 8, 10\n";
    echo "Sản phẩm Sale: 2, 4, 6, 9\n";
    
} catch (Exception $e) {
    echo "LỖI: " . $e->getMessage() . "\n";
}
