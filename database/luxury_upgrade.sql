-- Luxury Fashion Store upgrade (shop_db)
-- Chay file nay trong phpMyAdmin hoac: mysql -u root shop_db < luxury_upgrade.sql

SET NAMES utf8mb4;

-- Bien the: anh rieng cho tung mau
ALTER TABLE product_variant
  ADD COLUMN IF NOT EXISTS image_url VARCHAR(500) NULL AFTER color_hex;

-- Them cot is_trend va is_sale
ALTER TABLE product
  ADD COLUMN IF NOT EXISTS is_trend TINYINT DEFAULT 0 AFTER is_bestseller;

-- Them cot sale_price (gia da giam)
ALTER TABLE product
  ADD COLUMN IF NOT EXISTS sale_price DECIMAL(15,0) NULL AFTER is_sale;

-- Don hang khach (khong can dang nhap)
ALTER TABLE orders
  MODIFY user_id INT(11) NULL DEFAULT NULL;

ALTER TABLE orders
  ADD COLUMN IF NOT EXISTS customer_name VARCHAR(100) NULL AFTER user_id,
  ADD COLUMN IF NOT EXISTS customer_email VARCHAR(150) NULL AFTER customer_name;

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS role VARCHAR(20) NOT NULL DEFAULT 'customer';

-- Xoa san pham trung (moi mau tung la 1 product rieng)
DELETE FROM order_detail WHERE product_id IN (6,7,8,9,10,11,12,13,14,15);
DELETE FROM product_variant WHERE base_product_id IN (6,7,8,9,10,11,12,13,14,15)
   OR linked_product_id IN (6,7,8,9,10,11,12,13,14,15);
DELETE FROM product WHERE product_id IN (6,7,8,9,10,11,12,13,14,15);

-- Cap nhat danh muc
UPDATE category SET category_name = 'Áo khoác' WHERE category_id = 2;
UPDATE category SET category_name = 'Váy đầm' WHERE category_id = 3;
INSERT IGNORE INTO category (category_id, category_name) VALUES
  (4, 'Túi xách'),
  (5, 'Áo vest'),
  (6, 'Quần'),
  (7, 'Giày');

-- Cap nhat anh variant tu linked product
UPDATE product_variant pv
JOIN product p ON p.product_id = pv.linked_product_id
SET pv.image_url = p.image
WHERE pv.image_url IS NULL OR pv.image_url = '';

-- Xoa va them lai san pham luxury
DELETE FROM order_detail;
DELETE FROM product_variant;
DELETE FROM product;
ALTER TABLE product AUTO_INCREMENT = 1;

INSERT INTO product (product_id, category_id, product_name, price, stock_quantity, image, description, is_bestseller, is_trend, is_sale) VALUES
(1, 5, 'Áo vest Tailored Laine', 28500000, 12,
 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&w=1200&q=80',
 'Silhouette structured, vai tự nhiên, phom Céline-inspired cho tủ đồ executive.', 1, 1, 0),
(2, 1, 'Bộ suit Navy Double', 32000000, 8,
 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=1200&q=80',
 'Bộ suit hai cúc, vải wool cao cấp, hoàn thiện thủ công.', 1, 0, 1),
(3, 2, 'Áo khoác Cashmere Long', 24500000, 10,
 'https://images.unsplash.com/photo-1539533018447-63fcce2678e3?auto=format&fit=crop&w=1200&q=80',
 'Dáng dài minimalist, cashmere blend, phù hợp layer mùa lạnh.', 1, 1, 0),
(4, 3, 'Váy lụa Midi Triomphe', 19800000, 15,
 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=1200&q=80',
 'Váy midi phom thẳng, lụa satin, đường cắt thanh lịch.', 1, 0, 1),
(5, 4, 'Túi Ava Leather', 42000000, 6,
 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=1200&q=80',
 'Túi da bê cao cấp, hardware vàng champagne, form structured.', 1, 1, 0),
(6, 4, 'Túi Belt Triomphe', 38500000, 5,
 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=1200&q=80',
 'Túi đeo chéo có thắt lưng, da grain, chi tiết logo tinh tế.', 0, 0, 1),
(7, 6, 'Quần Wide-leg Wool', 12500000, 20,
 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=1200&q=80',
 'Quần ống rộng, wool blend, tone neutral luxury.', 0, 1, 0),
(8, 2, 'Blazer Structured Silk', 21500000, 9,
 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=1200&q=80',
 'Blazer một hàng khuy, lụa pha wool, phom relaxed tailored.', 1, 1, 0),
(9, 3, 'Váy Cocktail Satin', 17500000, 11,
 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?auto=format&fit=crop&w=1200&q=80',
 'Váy cocktail satin, cổ boat neck, dự tiệc tối giản.', 0, 0, 1),
(10, 7, 'Loafers Leather Classic', 9800000, 14,
 'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=1200&q=80',
 'Giày da bóng, đế leather, phong cách Parisian chic.', 0, 1, 0);

INSERT INTO product_variant (base_product_id, color_name, color_hex, linked_product_id, image_url) VALUES
(1, 'Đen', '#1a1a1a', 1, 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&w=1200&q=80'),
(1, 'Kem', '#f5f0e8', 1, 'https://images.unsplash.com/photo-1617137968427-85924c800a22?auto=format&fit=crop&w=1200&q=80'),
(1, 'Xám than', '#4a4a4a', 1, 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=1200&q=80'),
(2, 'Navy', '#1e3a5f', 2, 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=1200&q=80'),
(2, 'Charcoal', '#36454f', 2, 'https://images.unsplash.com/photo-1617127365859-fb317aefccb2?auto=format&fit=crop&w=1200&q=80'),
(3, 'Camel', '#c19a6b', 3, 'https://images.unsplash.com/photo-1539533018447-63fcce2678e3?auto=format&fit=crop&w=1200&q=80'),
(3, 'Đen', '#1a1a1a', 3, 'https://images.unsplash.com/photo-1548883354-762dc8974552?auto=format&fit=crop&w=1200&q=80'),
(4, 'Ivory', '#fffff0', 4, 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=1200&q=80'),
(4, 'Noir', '#0d0d0d', 4, 'https://images.unsplash.com/photo-1572804013309-59a23b1c4eeb?auto=format&fit=crop&w=1200&q=80'),
(4, 'Burgundy', '#722f37', 4, 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?auto=format&fit=crop&w=1200&q=80'),
(5, 'Tan', '#d2b48c', 5, 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=1200&q=80'),
(5, 'Đen', '#1a1a1a', 5, 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=1200&q=80'),
(6, 'Đen', '#1a1a1a', 6, 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=1200&q=80'),
(6, 'Trắng', '#fafafa', 6, 'https://images.unsplash.com/photo-1594223274512-ad4803739db8?auto=format&fit=crop&w=1200&q=80'),
(7, 'Be', '#e8dcc8', 7, 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=1200&q=80'),
(7, 'Đen', '#1a1a1a', 7, 'https://images.unsplash.com/photo-1624378515194-6db824fd94f7?auto=format&fit=crop&w=1200&q=80'),
(8, 'Stone', '#b8b2a8', 8, 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=1200&q=80'),
(8, 'Đen', '#1a1a1a', 8, 'https://images.unsplash.com/photo-1591369822096-ffd7deabaad?auto=format&fit=crop&w=1200&q=80'),
(9, 'Champagne', '#f7e7ce', 9, 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?auto=format&fit=crop&w=1200&q=80'),
(9, 'Emerald', '#046307', 9, 'https://images.unsplash.com/photo-1572804013309-59a23b1c4eeb?auto=format&fit=crop&w=1200&q=80'),
(10, 'Nâu', '#5c4033', 10, 'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=1200&q=80'),
(10, 'Đen', '#1a1a1a', 10, 'https://images.unsplash.com/photo-1614252239476-952789159a06?auto=format&fit=crop&w=1200&q=80');
