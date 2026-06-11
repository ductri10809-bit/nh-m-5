<?php
/**
 * verify_product_prices.php - Check database for duplicate price columns
 */
require_once __DIR__ . '/cau_hinh/ket_noi_csdl.php';

try {
    \ = ketNoiCSDL();
    
    // Get table structure
    \ = \->query("DESCRIBE product");
    \ = \->fetchAll(PDO::FETCH_COLUMN, 0);
    
    echo "=== PRODUCT TABLE COLUMNS ===\n";
    echo json_encode(\, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "\n\n";
    
    // Check for problematic columns
    \ = in_array('original_price', \);
    \ = in_array('sale_price', \);
    \ = in_array('gia', \);
    
    echo "Column Check:\n";
    echo "- original_price: " . (\ ? "YES (REMOVE)" : "NO") . "\n";
    echo "- sale_price: " . (\ ? "YES (KEEP)" : "NO (CREATE)") . "\n";
    echo "- gia (original): " . (\ ? "YES (KEEP)" : "NO") . "\n";
    
    // Sample products with prices
    echo "\n=== SAMPLE PRODUCTS ===\n";
    \ = \->query("SELECT product_id, product_name, gia, sale_price, is_sale FROM product LIMIT 3");
    \ = \->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(\, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception \) {
    echo "Error: " . \->getMessage();
}
?>
