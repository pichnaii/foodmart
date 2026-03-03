-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 03, 2026 at 04:54 PM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `foodmart`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `code` varchar(100) NOT NULL,
  `name` varchar(200) NOT NULL,
  `created_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `code`, `name`, `created_date`) VALUES
(10, 'CA002', 'Fruits', '2025-02-20'),
(12, 'CA003', 'Food & Beverage', '2025-03-28'),
(13, 'CA004', 'ស្រោមជើង', '2025-11-01'),
(14, 'CA005', 'Football Boots', '2025-10-31'),
(15, 'CA006', 'Electronic', '2025-10-31');

-- --------------------------------------------------------

--
-- Table structure for table `configurations`
--

CREATE TABLE `configurations` (
  `id` int(11) NOT NULL,
  `slideshow` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `page` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `report` int(11) NOT NULL,
  `setting` int(11) NOT NULL,
  `user_delete` int(11) NOT NULL,
  `user_edit` int(11) NOT NULL,
  `theme` tinyint(1) NOT NULL,
  `theme_color` int(11) NOT NULL,
  `productprefix` varchar(11) NOT NULL,
  `sidebar_color` varchar(11) NOT NULL,
  `body_color` varchar(11) NOT NULL,
  `bg_color` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `configurations`
--

INSERT INTO `configurations` (`id`, `slideshow`, `product`, `category`, `page`, `user`, `report`, `setting`, `user_delete`, `user_edit`, `theme`, `theme_color`, `productprefix`, `sidebar_color`, `body_color`, `bg_color`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 1, 'PT', '#059941', '#d93636', '#f3f6f9');

-- --------------------------------------------------------

--
-- Table structure for table `pos_sale`
--

CREATE TABLE `pos_sale` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `qty` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pos_sale`
--

INSERT INTO `pos_sale` (`id`, `product_name`, `unit_price`, `qty`, `total_price`, `created_at`) VALUES
(1, 'Adidas F50 Cherry', '225.00', 1, '225.00', '2025-05-07 15:19:07'),
(2, 'ផ្លែដូងខ្ទិះ កំពត', '1.00', 1, '1.00', '2025-05-07 15:19:07'),
(3, 'ទឹកផ្លែឈើខ្មែរស្រស់ ស្រស់', '200.00', 1, '200.00', '2025-05-26 02:15:01'),
(4, 'ផ្លែ​ប័រខ្មែរពិតពិត', '2.50', 1, '2.50', '2025-05-26 02:15:01'),
(5, 'ផ្លែដូងខ្ទិះ កំពត', '15.00', 1, '15.00', '2025-10-31 15:38:16'),
(6, 'ទឹកផ្លែឈើខ្មែរស្រស់ ស្រស់', '200.00', 1, '200.00', '2025-10-31 15:38:16'),
(7, 'ផ្លែ​ប័រខ្មែរពិតពិត', '2.50', 3, '7.50', '2025-10-31 15:38:16'),
(8, 'Summer Juice', '2.50', 1, '2.50', '2025-10-31 15:38:16'),
(9, 'បាយឆាគ្រឿងសមុទ្រ', '5.50', 1, '5.50', '2025-10-31 15:38:16'),
(10, 'ការ៉េម Nicer រសជាតិ​សូកូឡា', '3.50', 3, '10.50', '2025-10-31 15:38:16'),
(11, 'Nike Air Zoom 2023', '175.00', 1, '175.00', '2025-10-31 15:38:16');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `code` varchar(100) NOT NULL,
  `name` varchar(200) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `category_name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `quantity` decimal(10,0) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `unit` varchar(20) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `code`, `name`, `category_id`, `category_name`, `price`, `cost`, `quantity`, `unit_id`, `unit`, `image`, `image_path`, `status`) VALUES
(2, '96592816', 'Apple Juice 500ml', 10, 'Fruits', '3.50', '1.50', '1', 16, 'KG', NULL, 'da9db879941504fc321bfdfe586f3b97.png', 1),
(3, '86592817', 'Adidas F50 Cherry', 14, 'Football Boots', '225.00', '150.00', '15', 20, 'Pair', NULL, 'fce5e7e0bc45e1c0b19cfd87d66bec65.png', 1),
(4, '96492817', 'ផ្លែដូងខ្ទិះ កំពត', 10, 'Fruits', '15.00', '9.00', '2', 16, 'KG', NULL, '150fa55c8483f24f1d133311ef941112.png', 1),
(5, '96591817', 'ទឹកផ្លែឈើខ្មែរស្រស់ ស្រស់', 12, 'Food & Beverage', '20.00', '15.00', '50', 16, 'KG', NULL, '141c6fb204f874bcc1258ef09ea51894.png', 1),
(6, '96592819', 'Mizuno Morelia Neo V Beta', 14, 'Football Boots', '175.00', '155.00', '40', 20, 'Pair', '', '541b178bfdbe55219c515743674a840b.png', 1),
(8, '96592814', 'ការ៉េម Nicer រសជាតិ​សូកូឡា', 12, 'Food & Beverage', '3.50', '2.00', '1', 22, 'PCS', NULL, 'bdfa96db83139081e07f26eb8935f033.png', 1),
(18, '96592817', 'Summer Juice', 12, 'Food & Beverage', '2.50', '1.50', '1', 16, 'KG', NULL, 'd041e594e85e58b4850db5367008449b.png', 1),
(19, '96592813', 'ផ្លែ​ប័រខ្មែរពិតពិត', 10, 'Fruits', '2.50', '1.50', '1', 16, 'KG', NULL, 'e23c946b739d5eb5f071e6ebe2ecfb87.png', 1),
(21, '96592815', 'ផ្លែ​ចេកអំបូងលឿង', 10, 'Fruits', '3.50', '1.50', '2', 16, 'KG', NULL, 'e41ab04b66ffd07ca8987cd7f7153458.png', 1),
(22, '28476798', 'បាយឆាគ្រឿងសមុទ្រ', 12, 'Food & Beverage', '5.50', '2.00', '1', 21, 'ចាន', NULL, '2e5abc34cd7282fc38aa67c1d99f4ad8.jpg', 1),
(23, '18777552', 'Mizuno Morelia Neo IV Beta', 14, 'Football Boots', '35.00', '20.00', '10', 20, 'Pair', NULL, '6e5ac051048650b4a0821c47c7392772.jpg', 1),
(24, '54322328', 'Mizuno Morelia neo beta IV', 14, 'Football Boots', '35.00', '20.00', '10', 20, 'Pair', NULL, '934b8949bf5a5dcb2c0fa3281a4fe784.png', 1),
(25, '24097703', 'adidas F50 Elite Firm Ground', 14, 'Football Boots', '35.00', '25.00', '20', 20, 'Pair', NULL, '2e5bde252d33c193fd403c0179783d9e.png', 1),
(26, '35188317', 'ASICS Lethal Flash', 14, 'Football Boots', '35.00', '12.00', '20', 20, 'Pair', NULL, '3fc9f63530a2217dc771cf6d6732b13e.png', 1),
(27, '45376325', 'Nike Air Zoom 2023 FG', 14, 'Football Boots', '40.00', '20.00', '50', 20, 'Pair', NULL, 'ba1ea1488137de694e92e361180690c0.png', 1),
(28, '13728099', 'Nike sort socks', 13, 'ស្រោមជើង', '3.00', '0.50', '20', 20, 'Pair', NULL, '1f1c64b245cf01ee94ed7517ce16103f.png', 1),
(29, '05817797', 'Mizuno socks sort', 13, 'ស្រោមជើង', '2.50', '0.50', '10', 20, 'Pair', NULL, '5b1bc20e111933b1f534506f0fb34eb9.png', 1),
(30, '16168716', 'F50 Elite Rose Black', 14, 'Football Boots', '35.00', '20.00', '50', 20, 'Pair', NULL, '37e130c5f760c9970b7b3774ac1024a5.png', 1),
(31, '68753231', 'Nike Mercurial Superfly', 14, 'Football Boots', '55.00', '22.00', '50', 20, 'Pair', NULL, 'b57e7ff4cea2a4ef7c84cd61577c83db.png', 1),
(35, '06850234', 'Mizuno Alpha 3', 14, 'Football Boots', '195.00', '155.00', '25', 20, 'Pair', NULL, '1b1196de8eb79714d125cb8f75c9f961.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `reference` varchar(100) NOT NULL,
  `supply_id` int(11) DEFAULT NULL,
  `create_date` date NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `warehouse` varchar(255) DEFAULT NULL,
  `rate` varchar(50) DEFAULT NULL,
  `tax` varchar(50) DEFAULT NULL,
  `discount` varchar(50) DEFAULT NULL,
  `shipping` varchar(50) DEFAULT NULL,
  `note` longtext DEFAULT NULL,
  `grand_total` decimal(10,2) DEFAULT NULL,
  `paid` decimal(10,2) DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `product_id`, `product_name`, `reference`, `supply_id`, `create_date`, `price`, `cost`, `supplier`, `company_id`, `company`, `warehouse_id`, `warehouse`, `rate`, `tax`, `discount`, `shipping`, `note`, `grand_total`, `paid`, `balance`, `payment_status`) VALUES
(27, NULL, NULL, '2025/PU/0001', NULL, '2025-11-02', '0.00', NULL, 'china', NULL, 'KungFU Group', NULL, 'Phnom Penh', '4200', '10', '5', '5', 'the good was corrected', '350.00', '30.50', NULL, 'partial'),
(28, NULL, NULL, '2025/PU/0002', NULL, '2025-10-02', '0.00', NULL, 'china warehouse', NULL, 'KungFU Group', NULL, 'Phnom Penh', '4200', '', '', '', 'okay', '1500.00', '1500.00', NULL, NULL),
(29, NULL, NULL, '2025/PU/0003', NULL, '2025-11-02', '0.00', NULL, 'Shenzen', NULL, 'KungFU Group', NULL, 'Phnom Penh', '4200', '', '', '', 'okay', '950.00', NULL, NULL, NULL),
(31, NULL, NULL, '2025/PU/0004', NULL, '2025-11-03', '0.00', NULL, 'Shenzen', NULL, 'KungFU Group', NULL, 'Phnom Penh', '4200', '0', '0', '0', 'free shipping', '2250.00', NULL, NULL, NULL),
(32, NULL, NULL, '2025/PU/0005', NULL, '2026-03-02', '0.00', NULL, '', NULL, '', NULL, '', '0', '0', '0', '0', '', '3780.00', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_code` varchar(200) DEFAULT NULL,
  `product_name` varchar(200) NOT NULL,
  `unit` varchar(200) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `quantity` varchar(200) DEFAULT NULL,
  `created_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `purchase_items`
--

INSERT INTO `purchase_items` (`id`, `purchase_id`, `product_id`, `product_code`, `product_name`, `unit`, `cost`, `quantity`, `created_date`) VALUES
(16, 31, 25, '24097703', 'adidas F50 Elite Firm Ground', 'Pair', '25.00', '50', NULL),
(17, 31, 23, '18777552', 'Mizuno Morelia Neo IV Beta', 'Pair', '20.00', '45', NULL),
(18, 32, 35, '06850234', 'Mizuno Alpha 3', 'Pair', '155.00', '20', NULL),
(19, 32, 31, '68753231', 'Nike Mercurial Superfly', 'Pair', '22.00', '20', NULL),
(20, 32, 26, '35188317', 'ASICS Lethal Flash', 'Pair', '12.00', '20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `slideshows`
--

CREATE TABLE `slideshows` (
  `id` int(11) NOT NULL,
  `code` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `flavor` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `link_page` varchar(100) NOT NULL,
  `link_media` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL,
  `imagetype` int(11) NOT NULL,
  `image_path` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `slideshows`
--

INSERT INTO `slideshows` (`id`, `code`, `name`, `flavor`, `description`, `link_page`, `link_media`, `status`, `imagetype`, `image_path`) VALUES
(11, 'SS001', 'Fresh Smoothie & Summer Juice', '100% natural', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Dignissim massa diam elementum.', 'index', 'youtube', 'Active', 1, 'cd440b1881d53a4da83ff516a89e77d8.png'),
(12, 'SS002', 'Fresh Smoothie & Summer Juice', '100% natural', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Dignissim massa diam elementum.', '', 'youtube', 'Active', 1, '97fe4fbfbc084b7ce892cd7760c8cd50.png'),
(13, 'SS003', 'ទឹកផ្លែក្រូចច្របាច់ស្រស់ ស្រស់', 'Original Product 100%', 'ទឹកផ្លែក្រូចច្របាច់ស្រស់ ស្រស់ផ្អែមហើយមុត​ធ្វើឡើងដោយដៃ ១០០%​ ជ្រើសរើសផ្លែក្រូចខ្មែរដែលមានគុណភាពនិងជួលបំប៉នសុខភាពលោកអ្នក', '', 'youtube', 'Active', 1, '9cd057274e8b189b81d487bba1f7d90d.png'),
(14, 'SS005', 'Monster Energy & Marry Christmas', 'Sugar Free 1000500%', 'Monster Energy is a popular energy drink brand that was introduced in 2002 by Hansen Natural Company (now Monster Beverage Corporation).', '', 'youtube', 'Active', 1, '4296045e660f0059d416cea71cd2428a.png'),
(17, 'SS005', 'Nike Phantom GX Elite', 'Original Product 100% From Nike Store', 'Professional Boots for Professional player on Earth', '', '', 'Inactive', 1, '2b73f8b9781a258eca785cdb0b3263b1.png'),
(18, 'SS006', 'Xiaomi 17 Pro Max', '', '', '', '', 'Inactive', 1, '106a269386a9e20c32f90e520991a536.png');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `code` varchar(100) NOT NULL,
  `name` varchar(200) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `created_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `code`, `name`, `full_name`, `created_date`) VALUES
(16, '0002', 'KG', 'Kilogram', '2026-03-01'),
(18, 'U001', 'BOX', 'BOXES', '2026-03-02'),
(19, 'U003', 'កំប៉ុង', 'កំប៉ុង CAN', '2026-03-01'),
(20, 'U004', 'Pair', 'Pair of Shoes', '2026-03-02'),
(21, 'U005', 'ចាន', 'ចាន', '2026-03-02'),
(22, 'U006', 'PCS', 'PCS', '2026-03-02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_role` varchar(255) NOT NULL,
  `username` varchar(200) NOT NULL,
  `password` varchar(100) NOT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_role`, `username`, `password`, `lastname`, `firstname`, `age`, `gender`, `phone`) VALUES
(1, 'admin', 'super_admin', '81dc9bdb52d04dc20036dbd8313ed055', 'Pich', 'Choronaii', 500, 'male', '01234567811'),
(12, 'accounting', 'accounting', '202cb962ac59075b964b07152d234b70', 'Tai', 'Loun', 25, 'female', '0123456789'),
(13, 'cashier', 'cashier', 'e2b1bde9ccccab2c7ac41a04d8ede3b3', 'Master', 'OOgway', 600, 'male', '01234567812');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `configurations`
--
ALTER TABLE `configurations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pos_sale`
--
ALTER TABLE `pos_sale`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `slideshows`
--
ALTER TABLE `slideshows`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `configurations`
--
ALTER TABLE `configurations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pos_sale`
--
ALTER TABLE `pos_sale`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `slideshows`
--
ALTER TABLE `slideshows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
