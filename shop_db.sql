-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2026 at 04:22 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(1, 'Suit'),
(2, 'Áo khoác'),
(3, 'Váy đầm'),
(4, 'Túi xách'),
(5, 'Áo vest'),
(6, 'Quần'),
(7, 'Giày');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `message_id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_email` varchar(150) DEFAULT NULL,
  `total_amount` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `order_status` varchar(50) DEFAULT 'Pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `customer_name`, `customer_email`, `total_amount`, `address`, `phone`, `order_status`, `order_date`) VALUES
(1, NULL, NULL, NULL, 899, 'đcsdvsư', '092402384', 'huy', '2026-05-26 13:50:06'),
(2, 2, NULL, NULL, 1698, 'đcsdvsư', '23424243', 'cho_xu_ly', '2026-05-26 14:08:32'),
(6, 16, 'tri', 'a@gmail.com', 126000000, '233414231\nGhi chú: 131312', '23u4234u92', 'da_xu_ly', '2026-06-01 07:33:39'),
(7, 17, 'tri', 't@gmail.com', 43000000, '3342434', '123122123131', 'huy', '2026-06-01 08:37:52'),
(11, 20, 'trí', 'hungtic21@gmail.com', 12500000, '21qwd1\nGhi chú: qưed12qweds', '21312313', 'cho_xu_ly', '2026-06-03 07:26:50'),
(12, 20, 'trí', 'hungtic21@gmail.com', 63500000, '2weds23we\nGhi chú: 12qwesd', '1239102312', 'cho_xu_ly', '2026-06-03 07:32:16'),
(13, 20, 'Trí Phan', 'ductri10809@gmail.com', 24500000, '43wes3w\nGhi chú: ưesf', '1239102312', 'cho_xu_ly', '2026-06-04 00:33:36'),
(14, 20, 'triz', 'd@gmail.com', 83300000, 'lâm thị hố\nGhi chú: 23wesđv', '1239102312', 'cho_xu_ly', '2026-06-04 01:41:51'),
(15, 20, 'triz', 'd@gmail.com', 63500000, 'lâm thị hố\nGhi chú: qwsa', '1239102312', 'cho_xu_ly', '2026-06-04 08:38:43'),
(16, 20, 'triz', 'd@gmail.com', 21500000, 'lâm thị hố\nGhi chú: wqsaa', '1239102312', 'cho_xu_ly', '2026-06-04 08:40:03'),
(18, 34, 'Trí Phan', 'tri12h@gmail.com', 10500000, 'lâm thị hố\nGhi chú: qứa', '123131231', 'cho_xu_ly', '2026-06-08 07:11:58'),
(19, 34, 'Trí Phan', 'tri12h@gmail.com', 10500000, 'lâm thị hố\nGhi chú: hi', '123131231', 'cho_xu_ly', '2026-06-08 07:17:14'),
(20, 34, 'Trí Phan', 'tri12h@gmail.com', 21500000, 'lâm thị hố\nGhi chú: hi', '123131231', 'da_xu_ly', '2026-06-08 07:17:52'),
(21, 34, 'Trí Phan', 'tri12h@gmail.com', 24500000, 'lam thi ho\nGhi chú: hi', '123131231', 'da_xu_ly', '2026-06-08 07:40:47'),
(22, NULL, 'Trí Phan', 'ductri10809@gmail.com', 78000000, 'ws\nGhi chú: 4wesd', '21312313', 'cho_xu_ly', '2026-06-10 01:12:31');

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`order_detail_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(29, 18, 11, 1, 10500000),
(30, 19, 11, 1, 10500000),
(31, 20, 8, 1, 21500000),
(32, 21, 3, 1, 24500000),
(33, 22, 1, 2, 28500000),
(34, 22, 11, 1, 10500000),
(35, 22, 11, 1, 10500000);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` varchar(50) DEFAULT 'Unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `payment_method`, `payment_status`) VALUES
(1, 1, 'Bank Transfer', 'Unpaid'),
(2, 2, 'COD', 'Unpaid');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `product_name` varchar(150) NOT NULL,
  `price` int(11) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `original_price` int(11) DEFAULT NULL,
  `is_bestseller` tinyint(1) NOT NULL DEFAULT 0,
  `is_trend` tinyint(4) DEFAULT 0,
  `is_sale` tinyint(1) NOT NULL DEFAULT 0,
  `sale_price` decimal(15,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `category_id`, `product_name`, `price`, `stock_quantity`, `image`, `description`, `original_price`, `is_bestseller`, `is_trend`, `is_sale`, `sale_price`) VALUES
(1, 5, 'Áo vest Tailored Laine', 28500000, 12, 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&w=1200&q=80', 'Silhouette structured, vai tự nhiên, phom Céline-inspired cho tủ đồ executive.', NULL, 1, 1, 0, NULL),
(2, 1, 'Bộ suit Navy Double', 32000000, 8, 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=1200&q=80', 'Bộ suit hai cúc, vải wool cao cấp, hoàn thiện thủ công.', NULL, 1, 0, 1, 15000000),
(3, 2, 'Áo khoác Cashmere Long', 24500000, 10, 'https://images.unsplash.com/photo-1539533018447-63fcce2678e3?auto=format&fit=crop&w=1200&q=80', 'Dáng dài minimalist, cashmere blend, phù hợp layer mùa lạnh.', NULL, 1, 0, 1, 10000000),
(4, 3, 'Váy lụa Midi Triomphe', 19800000, 15, 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=1200&q=80', 'Váy midi phom thẳng, lụa satin, đường cắt thanh lịch.', NULL, 1, 0, 1, 10000000),
(5, 4, 'Túi Ava Leather', 42000000, 6, 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=1200&q=80', 'Túi da bê cao cấp, hardware vàng champagne, form structured.', 0, 1, 0, 1, 10000000),
(6, 4, 'Túi Belt Triomphe', 38500000, 5, 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=1200&q=80', 'Túi đeo chéo có thắt lưng, da grain, chi tiết logo tinh tế.', NULL, 0, 1, 0, NULL),
(7, 6, 'Quần Wide-leg Wool', 12500000, 20, 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=1200&q=80', 'Quần ống rộng, wool blend, tone neutral luxury.', NULL, 0, 0, 0, NULL),
(8, 2, 'Blazer Structured Silk', 21500000, 9, 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=1200&q=80', 'Blazer một hàng khuy, lụa pha wool, phom relaxed tailored.', NULL, 1, 1, 0, NULL),
(9, 3, 'Váy Cocktail Satin', 17500000, 11, 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?auto=format&fit=crop&w=1200&q=80', 'Váy cocktail satin, cổ boat neck, dự tiệc tối giản.', NULL, 0, 0, 0, NULL),
(10, 7, 'Loafers Leather Classic', 9800000, 14, 'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=1200&q=80', 'Giày da bóng, đế leather, phong cách Parisian chic.', NULL, 0, 0, 0, NULL),
(11, 3, 'Black Woven Flared Skirt', 10500000, 15, 'https://sixdo.vn/images/products/2026/original/172a2156-copy-1778906499.jpg', 'nothing', NULL, 1, 0, 1, 5500000);

-- --------------------------------------------------------

--
-- Table structure for table `product_variant`
--

CREATE TABLE `product_variant` (
  `variant_id` int(11) NOT NULL,
  `base_product_id` int(11) NOT NULL,
  `color_name` varchar(50) NOT NULL DEFAULT 'Trß║»ng',
  `color_hex` varchar(7) NOT NULL DEFAULT '#ffffff',
  `image_url` varchar(500) DEFAULT NULL,
  `linked_product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variant`
--

INSERT INTO `product_variant` (`variant_id`, `base_product_id`, `color_name`, `color_hex`, `image_url`, `linked_product_id`) VALUES
(378, 1, 'Đen', '#1a1a1a', 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&w=1200&q=80', 1),
(379, 1, 'Kem', '#f5f0e8', 'https://images.unsplash.com/photo-1617137968427-85924c800a22?auto=format&fit=crop&w=1200&q=80', 1),
(380, 1, 'Xám than', '#4a4a4a', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=1200&q=80', 1),
(381, 2, 'Navy', '#1e3a5f', 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=1200&q=80', 2),
(382, 2, 'Charcoal', '#36454f', 'https://images.unsplash.com/photo-1617127365859-fb317aefccb2?auto=format&fit=crop&w=1200&q=80', 2),
(383, 3, 'Camel', '#c19a6b', 'https://images.unsplash.com/photo-1539533018447-63fcce2678e3?auto=format&fit=crop&w=1200&q=80', 3),
(384, 3, 'Đen', '#1a1a1a', 'https://images.unsplash.com/photo-1548883354-762dc8974552?auto=format&fit=crop&w=1200&q=80', 3),
(385, 4, 'Ivory', '#fffff0', 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=1200&q=80', 4),
(386, 4, 'Noir', '#0d0d0d', 'https://images.unsplash.com/photo-1572804013309-59a23b1c4eeb?auto=format&fit=crop&w=1200&q=80', 4),
(387, 4, 'Burgundy', '#722f37', 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?auto=format&fit=crop&w=1200&q=80', 4),
(388, 5, 'Tan', '#d2b48c', 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=1200&q=80', 5),
(389, 5, 'Đen', '#1a1a1a', 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=1200&q=80', 5),
(390, 6, 'Đen', '#1a1a1a', 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=1200&q=80', 6),
(391, 6, 'Trắng', '#fafafa', 'https://images.unsplash.com/photo-1594223274512-ad4803739db8?auto=format&fit=crop&w=1200&q=80', 6),
(392, 7, 'Be', '#e8dcc8', 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=1200&q=80', 7),
(393, 7, 'Đen', '#1a1a1a', 'https://images.unsplash.com/photo-1624378515194-6db824fd94f7?auto=format&fit=crop&w=1200&q=80', 7),
(394, 8, 'Stone', '#b8b2a8', 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=1200&q=80', 8),
(395, 8, 'Đen', '#1a1a1a', 'https://images.unsplash.com/photo-1591369822096-ffd7deabaad?auto=format&fit=crop&w=1200&q=80', 8),
(396, 9, 'Champagne', '#f7e7ce', 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?auto=format&fit=crop&w=1200&q=80', 9),
(397, 9, 'Emerald', '#046307', 'https://images.unsplash.com/photo-1572804013309-59a23b1c4eeb?auto=format&fit=crop&w=1200&q=80', 9),
(398, 10, 'Nâu', '#5c4033', 'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=1200&q=80', 10),
(399, 10, 'Đen', '#1a1a1a', 'https://images.unsplash.com/photo-1614252239476-952789159a06?auto=format&fit=crop&w=1200&q=80', 10),
(400, 11, 'đen', '#1A1A1A', NULL, 11),
(401, 11, 'màu kem sửa', '#FFF8DC', 'https://sixdo.vn/images/products/2026/original/172a1854-1774860908.jpg', 11);

-- --------------------------------------------------------

--
-- Table structure for table `qr_payments`
--

CREATE TABLE `qr_payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `qr_code_data` longtext NOT NULL COMMENT 'JSON data encoded in QR',
  `qr_image_url` varchar(255) DEFAULT NULL COMMENT 'Path to QR image file',
  `admin_email` varchar(255) NOT NULL DEFAULT 'ductri10809@gmail.com',
  `customer_email` varchar(255) NOT NULL,
  `bank_account` varchar(50) DEFAULT NULL COMMENT 'Ngân hàng sử dụng để chuyển khoản',
  `transaction_status` varchar(50) DEFAULT 'pending' COMMENT 'pending, confirmed, rejected',
  `admin_confirmed_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian admin duyệt',
  `customer_notified_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian gửi thông báo cho khách',
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `qr_payments`
--

INSERT INTO `qr_payments` (`id`, `order_id`, `qr_code_data`, `qr_image_url`, `admin_email`, `customer_email`, `bank_account`, `transaction_status`, `admin_confirmed_at`, `customer_notified_at`, `amount`, `created_at`, `updated_at`) VALUES
(1, 12, '{\"admin_email\":\"ductri10809@gmail.com\",\"customer_email\":\"hungtic21@gmail.com\",\"order_id\":12,\"amount\":63500000,\"timestamp\":1780471936,\"app\":\"Luxurious Fashion Store\"}', '/luxurious-fashion-store/uploads/qr_codes/qr_12_1780471937.png', 'ductri10809@gmail.com', 'hungtic21@gmail.com', NULL, 'pending', NULL, NULL, 63500000.00, '2026-06-03 07:32:17', '2026-06-03 07:32:17'),
(2, 13, '{\"admin_email\":\"ductri10809@gmail.com\",\"customer_email\":\"ductri10809@gmail.com\",\"order_id\":13,\"amount\":24500000,\"timestamp\":1780533216,\"app\":\"Luxurious Fashion Store\"}', '/luxurious-fashion-store/uploads/qr_codes/qr_13_1780533220.png', 'ductri10809@gmail.com', 'ductri10809@gmail.com', NULL, 'pending', NULL, NULL, 24500000.00, '2026-06-04 00:33:40', '2026-06-04 00:33:40'),
(3, 14, '{\"admin_email\":\"ductri10809@gmail.com\",\"customer_email\":\"d@gmail.com\",\"order_id\":14,\"amount\":83300000,\"timestamp\":1780537311,\"app\":\"Luxurious Fashion Store\"}', '/luxurious-fashion-store/uploads/qr_codes/qr_14_1780537313.png', 'ductri10809@gmail.com', 'd@gmail.com', NULL, 'pending', NULL, NULL, 83300000.00, '2026-06-04 01:41:53', '2026-06-04 01:41:53'),
(4, 16, '{\"admin_email\":\"ductri10809@gmail.com\",\"customer_email\":\"d@gmail.com\",\"order_id\":16,\"amount\":21500000,\"timestamp\":1780562403,\"app\":\"Luxurious Fashion Store\"}', '/luxurious-fashion-store/uploads/qr_codes/qr_16_1780562405.png', 'ductri10809@gmail.com', 'd@gmail.com', NULL, 'pending', NULL, NULL, 21500000.00, '2026-06-04 08:40:05', '2026-06-04 08:40:05'),
(6, 21, '{\"admin_email\":\"ductri10809@gmail.com\",\"customer_email\":\"tri12h@gmail.com\",\"order_id\":21,\"amount\":24500000,\"timestamp\":1780904447,\"app\":\"Luxurious Fashion Store\"}', '/luxurious-fashion-store/uploads/qr_codes/qr_21_1780904449.png', 'ductri10809@gmail.com', 'tri12h@gmail.com', NULL, 'pending', NULL, NULL, 24500000.00, '2026-06-08 07:40:49', '2026-06-08 07:40:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'customer',
  `dia_chi` text DEFAULT NULL,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expires_at` datetime DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `fullname`, `email`, `phone`, `address`, `role`, `dia_chi`, `otp_code`, `otp_expires_at`, `is_verified`) VALUES
(2, 'trisz', 'tri12h555', 'ẻge', 'ductri108090@gmail.com', '028404', NULL, 'customer', NULL, NULL, NULL, 0),
(9, '', '$2y$10$bHb0UOnfaT1BRtqfZhzU7ex11xQTPhvZldkNWaCA6Y3R3SzKts2Wu', 'Tri Test', 'tri.test+1@example.com', '', NULL, 'customer', NULL, NULL, NULL, 0),
(16, 'a', '$2y$10$ZLje3VF027qYgBE1UXbuHO.FJIOCeLCAzJ5JmrVCZJdqvEApZKluS', 'tri', 'a@gmail.com', '', NULL, 'customer', NULL, NULL, NULL, 0),
(17, 't', '$2y$10$043syKUTuMpuzx.2mYN5Q.Bu4Kr4oPKSpjB.ALYgwVdTQs6hnp8c2', 'trisz', 't@gmail.com', '', NULL, 'customer', NULL, NULL, NULL, 0),
(20, 'd', '$2y$10$GERI5BB2TfMoc11cKxOHS.w1W/SkXlONUZvv4M0NaYZStsks4HVo.', 'triz', 'd@gmail.com', '1239102312', NULL, 'customer', 'lâm thị hố', NULL, NULL, 0),
(33, 'tp', '$2y$10$cTjQ./ZU/SslKCXd7sAh/eUwGvwv9xfsACkBXTnDnWgo3SH2q/B9m', 'trisz12', 'tp@gmail.com', '0359921160', NULL, 'customer', NULL, NULL, NULL, 1),
(34, 'tri12h', '$2y$10$0SuluSJw2qWxX1D9/EmnAunJRDZXbZrMoBPs1vnNnQwcQM21vQv/y', 'Trí Phan', 'tri12h@gmail.com', '123131231', NULL, 'customer', 'lam thi ho', NULL, NULL, 1),
(35, 'r', '$2y$10$BMMJeygRyvBQvTNlzbSZ8OKi6XrwrVcHkh1hpIOxz34YKGUQCdmGe', 'Trí Phan', 'r@gmail.com', '123122123131', NULL, 'admin', NULL, NULL, NULL, 1),
(36, 'p', '$2y$10$kcJrXCnZO9hZu7AUtVbXIeATSf1fQkdzARmfu9sPxX8aA4bbzMJri', 'tri', 'p@gmail.com', '123456789', NULL, 'customer', NULL, NULL, NULL, 1),
(37, 'v', '$2y$10$/L1uLJx1dovGq5Lf.G9Zhua90Ic1FkUjO1B7MtdAZjYIBsfS79gK.', 'tri1234', 'v@gmail.com', '', NULL, 'admin', NULL, '025540', '2026-06-10 09:16:08', 0),
(40, 'k', '$2y$10$jAEIJGh1pUPehLme.9iswuYNC/Wo5rY8bk0JylxaYP2Z/udY9Cp.e', 'wsd', 'k@gmail.com', '', NULL, 'customer', NULL, '322622', '2026-06-11 02:46:14', 0),
(41, 'tri123hjk', '$2y$10$2lZv98mvvaFEdUpkoAg.4.cqR0lSZPLQx.9qDRaT9P3S1sV1nrWdG', 'triz', 'tri123hjk@gmail.com', '', NULL, 'admin', NULL, '614436', '2026-06-11 02:46:59', 0);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(64) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_variant`
--
ALTER TABLE `product_variant`
  ADD PRIMARY KEY (`variant_id`),
  ADD KEY `base_product_id` (`base_product_id`),
  ADD KEY `linked_product_id` (`linked_product_id`);

--
-- Indexes for table `qr_payments`
--
ALTER TABLE `qr_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_status` (`transaction_status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD UNIQUE KEY `unique_guest` (`session_id`,`product_id`),
  ADD UNIQUE KEY `unique_user` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `product_variant`
--
ALTER TABLE `product_variant`
  MODIFY `variant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=402;

--
-- AUTO_INCREMENT for table `qr_payments`
--
ALTER TABLE `qr_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_detail_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_detail_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);

--
-- Constraints for table `product_variant`
--
ALTER TABLE `product_variant`
  ADD CONSTRAINT `product_variant_ibfk_1` FOREIGN KEY (`base_product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `product_variant_ibfk_2` FOREIGN KEY (`linked_product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `qr_payments`
--
ALTER TABLE `qr_payments`
  ADD CONSTRAINT `qr_payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
