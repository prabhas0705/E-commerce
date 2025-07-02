-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2025 at 11:57 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(5, 3, 28, 1, '2025-04-10 17:46:26', '2025-04-10 17:46:26');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--             
INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `category`, `stock_quantity`, `created_at`, `updated_at`) VALUES
(6, 'Samsung Galaxy M05', 'Samsung Galaxy M05 (Mint Green, 4GB RAM, 64 GB Storage) | 50MP Dual Camera | Bigger 6.7\" HD+ Display | 5000mAh Battery | 25W Fast Charging', 6000.00, 'https://m.media-amazon.com/images/I/81T3olLXpUL._SL1500_.jpg', 'Electronics', 4, '2025-04-10 13:39:18', '2025-04-10 13:39:18'),
(7, 'IQOO', 'iQOO Neo 10R 5G (Raging Blue, 8GB RAM, 256GB Storage) | Snapdragon 8s Gen 3 Processor | India\'s Slimmest 6400mAh Battery Smartphone | Segment\'s Most Stable 90FPS for 5 Hours', 28998.00, 'https://m.media-amazon.com/images/I/61wL8Qbo0HL._SL1200_.jpg', 'Electronics', 10, '2025-04-10 15:35:49', '2025-04-10 15:35:49'),
(8, 'Redmi buds', 'Redmi Buds 6, Dual Driver TWS in Ear Earbuds, 49dB Hybrid Active Noise Cancellation, Spatial Audio, Dual Device Connection, Quad Mic AI ENC, 42 Hrs Playtime, Custom EQ, Wireless Earbuds (Black)', 2639.00, 'https://m.media-amazon.com/images/I/61FUkmZwb8L._SL1500_.jpg', 'Electronics', 16, '2025-04-10 15:37:14', '2025-04-10 15:37:14'),
(9, 'Honor laptop', 'HONOR MagicBook X16 (2024), 12th Gen Intel Core i5-12450H, 16-inch (40.64 cm) FHD IPS Anti-Glare Thin and Light Laptop (16GB/512GB PCIe SSD/Windows 11', 46490.00, 'https://m.media-amazon.com/images/I/51dfURQWjOL._SL1000_.jpg', 'Electronics', 5, '2025-04-10 15:38:17', '2025-04-10 15:38:17'),
(10, 'Projectors', 'WZATCO Yuva Go Android 13.0 Smart Projector, 2X Brighter, 1080P & 4K Support, Rotatable Design, Auto & 4D Keystone with Netflix, Prime etc, WiFi 6 & BT, Screen Mirroring, ARC, 720P Native, White', 6990.00, 'https://m.media-amazon.com/images/I/61yREZRul6L._SL1500_.jpg', 'Electronics', 24, '2025-04-10 15:39:16', '2025-04-10 15:39:16'),
(11, 'Projector screen', 'Inlight Map Type Projector Screen, 6 W x 4 H(in Imported HIGH GAIN Fabric A+++++ Grade)', 1563.00, 'https://m.media-amazon.com/images/I/71jruiaS-PL._SL1267_.jpg', 'Electronics', 3, '2025-04-10 15:40:24', '2025-04-10 15:40:24'),
(12, 'Nion camera', 'Nikon D7500 20.9MP Digital SLR Camera (Black) with AF-S DX NIKKOR 18-140mm f/3.5-5.6G ED VR Lens', 79999.00, 'https://m.media-amazon.com/images/I/71iKNJ6rVIL._SL1000_.jpg', 'Electronics', 2, '2025-04-10 15:41:02', '2025-04-10 15:41:02'),
(13, 'SanDisk', 'SanDisk Extreme SD UHS I 128GB Card for 4K Video for DSLR and Mirrorless Cameras 180MB/s Read & 90MB/s Write', 1600.00, 'https://m.media-amazon.com/images/I/81wyNOrsHFL._SY606_.jpg', 'Electronics', 6, '2025-04-10 15:42:02', '2025-04-10 15:42:02'),
(14, 'T-shirt', 'Tommy Hilfiger Mens White Color T-Shirt (S)', 1199.00, 'https://m.media-amazon.com/images/I/513uYLfMyJL._SY679_.jpg', 'Clothing', 10, '2025-04-10 15:42:49', '2025-04-10 15:42:49'),
(15, 'T-shirt', 'Symbol Premium Men\'s Cotton Stretch T-Shirt (Regular Fit)', 699.00, 'https://m.media-amazon.com/images/I/71inRZvpkjL._SY550_.jpg', 'Clothing', 7, '2025-04-10 15:47:24', '2025-04-10 15:47:24'),
(16, 'shirt', 'Tommy Hilfiger Men\'s Regular Fit Shirt', 2799.00, 'https://m.media-amazon.com/images/I/71uXSDk2SAL._SX425_.jpg', 'Clothing', 3, '2025-04-10 15:48:33', '2025-04-10 15:48:33'),
(17, 'Watch', 'Tommy Hilfiger Quartz Analog Black Dial Leather Strap Watch for Men-NETH1791461', 10625.00, 'https://m.media-amazon.com/images/I/51Q4y5KSZyL._SX522_.jpg', 'Clothing', 2, '2025-04-10 15:49:18', '2025-04-10 15:49:18'),
(18, 'Shorts', 'United Colors of Benetton Men\'s Bermuda Shorts', 839.00, 'https://m.media-amazon.com/images/I/513PidVbSYL._SX679_.jpg', 'Clothing', 4, '2025-04-10 15:49:56', '2025-04-10 15:49:56'),
(19, 'Watch', 'GUESS Men\'s 42mm Watch - Silver Tone Strap Black Dial Silver Case', 12246.00, 'https://m.media-amazon.com/images/I/61U4KsfCMyL._SY500_.jpg', 'Clothing', 1, '2025-04-10 15:50:27', '2025-04-10 15:50:27'),
(20, 'Shirt', 'MARK & ALBERT Men\'s Slim Fit Cotton Formal Shirt', 999.00, 'https://m.media-amazon.com/images/I/51rSD8O4IkL._SY500_.jpg', 'Clothing', 3, '2025-04-10 15:50:55', '2025-04-10 15:50:55'),
(21, 'Kurtha', 'GoSriKi Women\'s Rayon Viscose Anarkali Printed Kurta with Palazzo & Dupatta', 779.00, 'https://m.media-amazon.com/images/I/71mX4WATh-L._SX522_.jpg', 'Clothing', 4, '2025-04-10 15:51:46', '2025-04-10 15:51:46'),
(22, 'Kurtha', 'GoSriKi Women\'s Cotton Blend Embroidered Straight Kurta with Pant & Dupatta', 669.00, 'https://m.media-amazon.com/images/I/618alDKLTHL._SY550_.jpg', 'Clothing', 2, '2025-04-10 15:52:46', '2025-04-10 15:52:46'),
(23, 'Dresses', 'PURVAJA Women’s Bodycon Knee Length Dress (Wini-021-024)', 399.00, 'https://m.media-amazon.com/images/I/71gnMQXonpL._SY679_.jpg', 'Clothing', 23, '2025-04-10 15:53:43', '2025-04-10 15:53:43'),
(24, 'Dresses', 'PURVAJA Women’s Cut Out Above Knee Length Dress (Floe-016-018)', 959.00, 'https://m.media-amazon.com/images/I/713NBcuRISL._SY550_.jpg', 'Clothing', 3, '2025-04-10 15:54:17', '2025-04-10 15:54:17'),
(25, 'Dresses', 'PURVAJA Women’s Front Silt Midi Length Dress (Wini-040-042)', 987.00, 'https://m.media-amazon.com/images/I/81-ZmBwWQ4L._SY550_.jpg', 'Clothing', 4, '2025-04-10 15:54:55', '2025-04-10 15:54:55'),
(26, 'Bodysuits', 'Knitroot Monthly Birthday Teddy Special, Newborn Baby Half Sleeve Unisex Romper, Onesies, Sleepsuit, Body Suit, Envelope Neck, 1 to 12 Months Print, 0-12 Months, Infant Cloth for Boys & Girls', 423.00, 'https://m.media-amazon.com/images/I/41kf8bkIsEL._SX522_.jpg', 'Clothing', 3, '2025-04-10 15:55:55', '2025-04-10 15:55:55'),
(27, 'Clothing sets', 'EIO New Born Baby Clothing Gift Set -13 Pieces', 648.00, 'https://m.media-amazon.com/images/I/61X3IMp9dvL._SX679_.jpg', 'Clothing', 2, '2025-04-10 15:56:33', '2025-04-10 15:56:33'),
(28, 'Clothing sets', 'Hopscotch Boys Overall set', 657.00, 'https://m.media-amazon.com/images/I/61b5h0LZjSL._SX522_.jpg', 'Clothing', 3, '2025-04-10 15:57:45', '2025-04-10 15:57:45'),
(29, 'Jars & containers', 'Satpurush Fridge Storage Boxes (Pack of 6) Freezer & Refrigerator Organizer Containers Kitchen Storage Container Set Kitchen', 299.00, 'https://m.media-amazon.com/images/I/71ouJpAkamL._SL1500_.jpg', 'Home & Kitchen', 5, '2025-04-10 16:01:44', '2025-04-10 16:01:44'),
(30, 'Jars & containers', 'CELLO Checkers Pet Plastic Airtight Canister Set | Food grade and BPA free canisters | Air tight seal & Stackable Transparent', 579.00, 'https://m.media-amazon.com/images/I/71PjqF8xEFL._SL1500_.jpg', 'Home & Kitchen', 3, '2025-04-10 16:02:22', '2025-04-10 16:02:22'),
(31, 'Water purifier', 'HUL Pureit Eco Water Saver RO+UV+MF+Mineral | INR 2000 Off on Exchange | 7 stage | 10L | Upto 60% Water Savings |', 11999.00, 'https://m.media-amazon.com/images/I/51En2EB1aHL._SL1000_.jpg', 'Home & Kitchen', 2, '2025-04-10 16:04:28', '2025-04-10 16:04:28'),
(32, 'Water filters & purifiers', 'Aquaguard Delight NXT Aquasaver 9-Stage Water Purifier | Upto 60% Water Savings | RO+UV+UF+MC Tech | Taste Adjuster', 9999.00, 'https://m.media-amazon.com/images/I/51azW1nqt6L._SL1100_.jpg', 'Home & Kitchen', 3, '2025-04-10 16:05:21', '2025-04-10 16:05:21'),
(33, 'Wall shelves', 'Decazone ® Macramé Wall Hanging Shelf Pine Wood Floating Shelve with Wooden Dowel Modern Chic Woven Décor for Dorm Living Room Nursery Beige', 264.00, 'https://m.media-amazon.com/images/I/51H2pAkicTL._SY679_.jpg', 'Home & Kitchen', 2, '2025-04-10 16:06:06', '2025-04-10 16:06:06'),
(34, 'Tables', 'UHUD Crafts Beautiful Antique Wooden Fold-able Side Table/End Table/Plant Stand/Stool Living Room Kids Play Furniture Table', 349.00, 'https://m.media-amazon.com/images/I/31NJkTq39ZL._SY300_SX300_QL70_FMwebp_.jpg', 'Home & Kitchen', 20, '2025-04-10 16:06:39', '2025-04-10 16:06:39'),
(35, 'Shoe rack', 'AYSIS DIY Shoe Rack Organizer/Multi-Purpose Plastic 5 Layers Portable and Folding Shoe Rack', 1595.00, 'https://m.media-amazon.com/images/I/61lCAifKeJS._SL1500_.jpg', 'Home & Kitchen', 4, '2025-04-10 16:07:19', '2025-04-10 16:07:19'),
(36, 'Tables', 'Deion Engineered Wood 2 Layer Bed Side Table Wooden Organizer Stand/Home Decor Table/Coffee Table/End Table/Side', 999.00, 'https://m.media-amazon.com/images/I/41rzfeSGXbL._SX300_SY300_QL70_FMwebp_.jpg', 'Home & Kitchen', 1, '2025-04-10 16:07:54', '2025-04-10 16:07:54'),
(37, 'Table lamps', 'The Light Shadow Cotton Fabric Table CFL Lamp For Bedroom Desk Lamp For Living Room, Bedroom, Office Nightstand Dimmable Table Lamp Reading Desk', 499.00, 'https://m.media-amazon.com/images/I/51QhDA-teDL._SY445_SX342_QL70_FMwebp_.jpg', 'Home & Kitchen', 30, '2025-04-10 16:08:29', '2025-04-10 16:08:29'),
(38, 'Gas stoves', 'Prestige Iris Plus LP Gas Stove Gti-02+AI (With Powder Coated Body, Auto Ignition System, Glass Top & 2 Unit Brass Burners)', 3145.00, 'https://m.media-amazon.com/images/I/417ALMPci7L._SX300_SY300_QL70_FMwebp_.jpg', 'Home & Kitchen', 1, '2025-04-10 16:09:09', '2025-04-10 16:09:09'),
(39, 'Mixer Grinders', 'Butterfly Smart Mixer Grinder, 750W, 4 Jars', 3099.00, 'https://m.media-amazon.com/images/I/41opVWa6H1L._SX300_SY300_QL70_FMwebp_.jpg', 'Home & Kitchen', 4, '2025-04-10 16:09:40', '2025-04-10 16:09:40'),
(40, 'Pan sets', 'Amazon Brand - Solimo Aluminium 4 Piece Non-Stick Cookware Set | Granite Finish | Induction Base', 1199.00, 'https://m.media-amazon.com/images/I/51HaYVy6TnL._SX300_SY300_QL70_FMwebp_.jpg', 'Home & Kitchen', 8, '2025-04-10 16:10:37', '2025-04-10 16:10:37'),
(41, 'Idli makers', 'PANCA Premium Steamer For Vegatables, Momo, Rice, Multipurpose 2 Tier Stainless Steel Steamer With Glass Lid', 799.00, 'https://m.media-amazon.com/images/I/61hhhTgVPDL._SX425_.jpg', 'Home & Kitchen', 3, '2025-04-10 16:11:58', '2025-04-10 16:11:58'),
(42, 'Dopamine detox', 'Dopamine Detox : A Short Guide to Remove Distractions and Get Your Brain to Do Hard Things', 199.00, 'https://m.media-amazon.com/images/I/41ZeaEn3V4L._SY445_SX342_.jpg', 'Books', 10, '2025-04-10 16:12:55', '2025-04-10 16:12:55'),
(43, 'Can we be strangers Again?', 'Can We Be Strangers Again?', 599.00, 'https://m.media-amazon.com/images/I/71zpck45b2L._SY342_.jpg', 'Books', 5, '2025-04-10 16:14:09', '2025-04-10 16:14:09'),
(44, 'The anxious Generation', 'The Anxious Generation: How the Great Rewiring of Childhood Is Causing an Epidemic of Mental Illness\r\nby Jonathan Haidt', 799.00, 'https://m.media-amazon.com/images/I/719w6Tq5+VL._SY342_.jpg', 'Books', 4, '2025-04-10 16:15:06', '2025-04-10 16:15:06'),
(45, 'Stephen Hawking', 'The Theory Of Everything', 225.00, 'https://m.media-amazon.com/images/I/61fR6OnVBUL._SY342_.jpg', 'Books', 3, '2025-04-10 16:15:38', '2025-04-10 16:15:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `is_admin`) VALUES
(1, 'prabhas', 'praabhaschirala02@gmail.com', '$2y$10$wjLOfgPfzs3/GFJhAM7JIu6ezCI4lI/zi8TYT/OnlwWxcvFnoD5Py', '2025-04-10 12:16:33', 0),
(2, 'prabhas', 'prabhaschirala0705@gmail.com', '$2y$10$eU6oPQDjJJSo63BldM8X9OoNdFNCyQ7/q36NVfSehCWHxgDjZ3MiO', '2025-04-10 12:27:05', 1),
(3, 'prabhas', 'abcdef@gmail.com', '$2y$10$D4gHBfVnsmRQOkmyfdlnSuU03NVTVDnFG2ELib5TEt82X7WzNsoDi', '2025-04-10 12:39:18', 1),
(4, 'saranya', 'saranya@gmail.com', '$2y$10$GF0p3LYIzIuKo8uDRfH8wO8MFUCsKcRi.SAwts2VaoUkxcwJN2DUi', '2025-04-10 12:47:20', 1),
(5, 'prabhas', 'zaqxsw@gmail.com', '$2y$10$.bnyMvRMLQTPt1Kv2E69u.Vphq8gLjiS8mKs0cgKSivu5HF46Yytm', '2025-04-10 15:45:26', 0),
(6, 'prabhas', 'qwer@gmail.com', '$2y$10$u.uuc0G.rgQ1tIxUfcFpwOO4F6yV6gWgbb4SBmiwZ.ZvkQz59K09G', '2025-04-10 17:47:50', 0);

-- --------------------------------------------------------

--   
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(1, 3, 28, '2025-04-10 15:58:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist_item` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
