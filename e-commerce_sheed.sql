-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2025 at 02:40 PM
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
-- Database: `e-commerce_sheed`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `total`, `created_at`, `updated_at`) VALUES
(22, 2, 23400.00, '2025-05-23 22:47:56', '2025-05-23 22:47:56'),
(23, 2, 300.00, '2025-05-23 22:59:03', '2025-05-23 22:59:03'),
(24, 2, 1250.00, '2025-05-23 23:13:13', '2025-05-23 23:13:13'),
(25, 2, 500.00, '2025-05-23 23:13:25', '2025-05-23 23:13:25'),
(26, 2, 500.00, '2025-05-23 23:21:56', '2025-05-23 23:21:56'),
(27, 2, 1000.00, '2025-05-23 23:36:31', '2025-05-23 23:36:31'),
(28, 2, 1250.00, '2025-05-23 23:37:09', '2025-05-23 23:37:09'),
(29, 3, 500.00, '2025-05-24 01:22:09', '2025-05-24 01:22:09'),
(30, 2, 500.00, '2025-05-24 01:23:58', '2025-05-24 01:23:58');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`, `subtotal`) VALUES
(20, 22, 3, 59, 350.00, 20650.00),
(21, 22, 4, 5, 250.00, 1250.00),
(22, 22, 6, 6, 250.00, 1500.00),
(23, 23, 10, 1, 300.00, 300.00),
(24, 24, 5, 1, 500.00, 500.00),
(25, 24, 8, 1, 750.00, 750.00),
(26, 25, 4, 2, 250.00, 500.00),
(27, 26, 4, 2, 250.00, 500.00),
(28, 27, 5, 2, 500.00, 1000.00),
(29, 28, 4, 5, 250.00, 1250.00),
(30, 29, 5, 1, 500.00, 500.00),
(31, 30, 4, 1, 250.00, 250.00),
(32, 30, 6, 1, 250.00, 250.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `slug`, `image_path`, `category_id`, `created_at`, `updated_at`) VALUES
(3, 'Petal Dew Hydrating Toner', 'A refreshing burst of hydration inspired by morning cherry blossom dew. This alcohol-free toner soothes and preps your skin with botanical extracts and a radiant glow.', 350.00, 'petal-dew-hydrating-toner', 'uploads/ChatGPT Image May 23, 2025, 10_21_14 PM.png', 1, '2025-05-23 22:22:26', '2025-05-23 16:22:26'),
(4, 'Silken Bloom Facial Cleanser', 'A gentle, creamy cleanser that lifts away impurities while wrapping your skin in the delicate softness of sakura petals. Perfect for sensitive or dry skin', 250.00, 'silken-bloom-facial-cleanser', 'uploads/ChatGPT Image May 23, 2025, 10_21_52 PM.png', 1, '2025-05-23 22:26:12', '2025-05-23 16:26:12'),
(5, 'Radiant Veil Moisture Cream', 'A rich yet breathable moisturizer that melts into your skin, delivering deep hydration and sealing in a natural, petal-soft luminosity.', 500.00, 'radiant-veil-moisture-cream', 'uploads/ChatGPT Image May 23, 2025, 10_24_14 PM.png', 1, '2025-05-23 22:30:30', '2025-05-23 16:30:30'),
(6, 'Cherry Whisper Lip Tint', 'A sheer, buildable lip tint with a natural cherry blossom flush. Lightweight and moisturizing with a satin finish that lasts all day.', 250.00, 'cherry-whisper-lip-tint', 'uploads/ChatGPT Image May 23, 2025, 10_28_38 PM.png', 2, '2025-05-23 22:31:23', '2025-05-23 16:31:23'),
(7, 'Blossom Glow Cheek Stain', 'A silky, blendable cheek stain that mimics a fresh blush from spring air. Infused with floral essences for a skin-loving glow.', 600.00, 'blossom-glow-cheek-stain', 'uploads/ChatGPT Image May 23, 2025, 10_33_20 PM.png', 2, '2025-05-23 22:33:47', '2025-05-23 16:33:47'),
(8, 'Floral Veil BB Cushion', 'A brightening BB cushion with medium coverage and a luminous, dewy finish. Enriched with sakura extract for calming and radiance.', 750.00, 'floral-veil-bb-cushion', 'uploads/ChatGPT Image May 23, 2025, 10_38_10 PM.png', 2, '2025-05-23 22:39:22', '2025-05-23 16:39:22'),
(9, 'Sakura Mist Eau de Parfum', 'A timeless scent capturing the first bloom of cherry blossoms in spring—light, floral, and gracefully feminine.', 325.00, 'sakura-mist-eau-de-parfum', 'uploads/ChatGPT Image May 23, 2025, 10_49_06 PM.png', 3, '2025-05-23 22:49:38', '2025-05-23 16:49:38'),
(10, 'Bloomheart Roll-On Perfume', 'A travel-sized roll-on with a sweet, romantic cherry blossom heart note and soft musk base—perfect for quick, elegant touch-ups.', 300.00, 'bloomheart-roll-on-perfume', 'uploads/8f77a83c-52c9-4db5-9835-fc55edf7d95d.png', 3, '2025-05-23 22:52:35', '2025-05-23 16:52:35'),
(11, 'Cherry Rain Body Spray', 'A refreshing body mist inspired by a gentle spring shower in a sakura garden—invigorating, clean, and subtly sweet.', 380.00, 'cherry-rain-body-spray', 'uploads/ChatGPT Image May 23, 2025, 10_54_59 PM.png', 3, '2025-05-23 22:55:35', '2025-05-23 16:55:35');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `category_name`, `created_at`, `updated_at`) VALUES
(1, 'Skincare', NULL, NULL),
(2, 'Cosmetics', NULL, NULL),
(3, 'Fragrance', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `address`, `phone`, `birthdate`, `created_at`, `updated_at`, `is_admin`) VALUES
(2, 'Rasheed', 'tapalesrasheed123@gmail.com', '$2y$10$bKQgXCerf7mN4o780HJWUeJnTtriSZ8CKvISpjig/JsPPXjyqEVhe', 'San Fernando, Cebu', '09060425341', '2025-05-02', '2025-05-21 00:11:39', '2025-05-21 00:11:39', 0),
(3, 'admin', 'admin@yourstore.com', '$2y$10$6xC.//poEnofsO8UnQEhruU9HyTfvgHypKgjBUOAnPzhIJbo0/LfK', 'San Fernando, Cebu', '09456989966', '2006-06-05', '2025-05-23 14:37:41', '2025-05-23 14:37:41', 1),
(9, 'Rodeliza La Rosa', 'tapalesrasheed@gmail.com', '$2y$10$9i2f3R6oQuQFuI289xkWKO9HTR1z9Qe/WSU2TMAQ5Sf6kRp4M3C2S', NULL, NULL, NULL, '2025-05-24 01:21:18', '2025-05-24 01:21:18', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
