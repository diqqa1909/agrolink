-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 25, 2026 at 05:52 AM
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
-- Database: `agrolink`
--

-- --------------------------------------------------------

--
-- Table structure for table `buyer_profiles`
--

CREATE TABLE `buyer_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `apartment_code` varchar(50) DEFAULT NULL,
  `street_name` varchar(150) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `additional_address_details` varchar(100) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buyer_profiles`
--

INSERT INTO `buyer_profiles` (`id`, `user_id`, `phone`, `apartment_code`, `street_name`, `city`, `district`, `postal_code`, `profile_photo`, `created_at`, `updated_at`) VALUES
(1, 11, '0702242499', '35/4', 'Lake Rd', 'Matale', 'Matale', '21000', 'profile_photo_11_1769005209_8874b3e1.jpg', '2025-12-25 16:04:09', '2026-01-21 14:20:09'),
(2, 35, '0712356987', '34/2', 'Fixed lane', 'Gall', 'Galle', '34000', NULL, '2026-01-13 21:33:52', '2026-01-21 14:17:49'),
(3, 36, '0241563287', '56/4', 'Flower Rd', 'Matale', 'Matale', '21000', 'profile_photo_36_1769005500_3f0e02f6.jpg', '2026-01-21 14:07:52', '2026-01-21 14:25:00');

-- --------------------------------------------------------

--
-- Table structure for table `buyer_profiles_backup`
--

CREATE TABLE `buyer_profiles_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buyer_profiles_backup`
--

INSERT INTO `buyer_profiles_backup` (`id`, `user_id`, `phone`, `city`, `delivery_address`, `created_at`, `updated_at`) VALUES
(1, 11, '789546123', 'matara', '56/9,kl rd,matara', '2025-12-25 16:04:09', '2025-12-25 16:04:09'),
(2, 35, NULL, NULL, NULL, '2026-01-13 21:33:52', '2026-01-13 21:33:52');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `farmer_name` varchar(255) NOT NULL,
  `farmer_location` varchar(100) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `product_name`, `product_price`, `quantity`, `farmer_name`, `farmer_location`, `product_image`, `created_at`, `updated_at`) VALUES
(40, 30, '32', 'leaks', 70.00, 2, '', '', 'l', '2025-10-22 20:22:24', '2025-10-22 20:22:48'),
(41, 30, '31', 'tomatoes', 50.00, 3, '', '', 't', '2025-10-22 20:22:26', '2025-10-22 20:22:45'),
(42, 31, '35', 'pumpkin', 59.00, 1, '', '', 'product_68f92bfcf3d31.jpeg', '2025-10-23 06:45:23', '2025-10-23 06:46:00'),
(43, 31, '32', 'leaks', 70.00, 2, '', '', 'product_68f9262659a90.jpg', '2025-10-23 06:45:24', '2025-10-23 06:47:38'),
(44, 31, '31', 'tomatoes', 50.00, 8, '', '', 'product_68f924d735c46.jpg', '2025-10-23 06:45:27', '2025-10-23 06:46:09'),
(48, 34, '35', 'pumpkin', 59.00, 2, '', '', 'product_68f92bfcf3d31.jpeg', '2025-11-04 09:37:48', '2025-11-04 09:37:58'),
(49, 34, '32', 'leaks', 70.00, 3, '', '', 'product_68f9262659a90.jpg', '2025-11-04 09:37:49', '2025-11-04 09:37:56'),
(142, 35, '37', 'Banana', 100.00, 2, '', '', 'product_68f9436a272fb.webp', '2026-01-21 09:14:15', '2026-01-21 14:06:24'),
(144, 35, '32', 'leaks', 70.00, 1, '', '', 'product_68f9262659a90.jpg', '2026-01-21 14:06:22', '2026-01-21 14:06:22'),
(152, 36, '36', 'Papaya', 120.00, 1, '', '', 'product_68f93e9f14c81.jpeg', '2026-01-21 14:41:57', '2026-01-21 14:41:57'),
(166, 11, '38', 'onion', 80.00, 10, '', '', 'product_69751058cae1f.jpg', '2026-01-24 18:33:18', '2026-01-24 18:34:19'),
(167, 11, '37', 'Banana', 100.00, 4, '', '', 'product_68f9436a272fb.webp', '2026-01-24 18:34:07', '2026-01-24 18:35:57');

-- --------------------------------------------------------

--
-- Table structure for table `crop_requests`
--

CREATE TABLE `crop_requests` (
  `id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `crop_name` varchar(150) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `target_price` decimal(10,2) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `status` enum('active','accepted','declined','completed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crop_requests`
--

INSERT INTO `crop_requests` (`id`, `buyer_id`, `crop_name`, `quantity`, `target_price`, `delivery_date`, `location`, `status`, `created_at`) VALUES
(8, 11, 'mango', 100.00, 20.00, '2025-12-20', 'Kandy, Central', '', '2025-12-16 13:40:28'),
(9, 11, 'BeetRoot', 50.00, 35.00, '2026-01-20', 'Ragama, Western', 'completed', '2025-12-16 13:42:40'),
(10, 11, 'papaya', 45.00, 55.00, '2026-01-14', 'Badulla, Uva', 'accepted', '2025-12-16 14:27:44'),
(11, 11, 'mango', 56.00, 23.00, '2026-01-13', 'colombo', 'accepted', '2025-12-17 04:38:21'),
(12, 35, 'tomato', 50.00, 40.00, '2026-01-20', 'Rabukkana,kegall', '', '2026-01-09 04:04:10'),
(13, 11, 'banana', 56.00, 20.00, '2026-01-24', 'colombo,western', '', '2026-01-21 09:31:05');

-- --------------------------------------------------------

--
-- Table structure for table `crop_volume_factors`
--

CREATE TABLE `crop_volume_factors` (
  `id` int(11) NOT NULL,
  `crop_name` varchar(100) NOT NULL,
  `volume_factor` decimal(3,2) NOT NULL,
  `category` enum('Vegetables','Fruits','Spices') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crop_volume_factors`
--

INSERT INTO `crop_volume_factors` (`id`, `crop_name`, `volume_factor`, `category`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Carrot', 1.00, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(2, 'Potato', 1.00, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(3, 'Beetroot', 1.10, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(4, 'Radish', 1.00, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(5, 'Onion', 1.20, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(6, 'Garlic', 1.10, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(7, 'Tomato', 1.30, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(8, 'Capsicum', 1.50, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(9, 'Brinjal', 1.40, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(10, 'Beans', 1.20, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(11, 'Peas', 1.10, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(12, 'Cabbage', 1.60, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(13, 'Lettuce', 1.80, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(14, 'Spinach', 1.50, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(15, 'Leeks', 1.40, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(16, 'Pumpkin', 2.20, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(17, 'Butternut', 2.00, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(18, 'Cucumber', 1.40, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(19, 'Bitter Gourd', 1.30, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(20, 'Ridge Gourd', 1.50, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(21, 'Snake Gourd', 1.60, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(22, 'Okra', 1.30, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(23, 'Ash Plantain', 1.70, 'Vegetables', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(24, 'Banana', 1.60, 'Fruits', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(25, 'Papaya', 1.90, 'Fruits', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(26, 'Mango', 1.50, 'Fruits', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(27, 'Pineapple', 1.80, 'Fruits', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(28, 'Watermelon', 2.30, 'Fruits', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(29, 'Coconut', 1.90, 'Fruits', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(30, 'Avocado', 1.40, 'Fruits', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(31, 'Passion Fruit', 1.30, 'Fruits', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(32, 'Guava', 1.40, 'Fruits', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(33, 'Orange', 1.50, 'Fruits', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(34, 'Lime', 1.20, 'Fruits', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(35, 'Pomegranate', 1.50, 'Fruits', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(36, 'Dragon Fruit', 1.70, 'Fruits', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(37, 'Chili', 1.10, 'Spices', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(38, 'Ginger', 1.20, 'Spices', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(39, 'Turmeric', 1.10, 'Spices', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(40, 'Pepper', 0.90, 'Spices', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(41, 'Cinnamon', 0.80, 'Spices', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(42, 'Cardamom', 0.70, 'Spices', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(43, 'Cloves', 0.70, 'Spices', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37'),
(44, 'Nutmeg', 0.80, 'Spices', 1, '2026-01-24 14:32:37', '2026-01-24 14:32:37');

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `id` int(11) NOT NULL,
  `district_name` varchar(100) NOT NULL,
  `district_code` varchar(10) NOT NULL,
  `province` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`id`, `district_name`, `district_code`, `province`, `is_active`, `created_at`) VALUES
(1, 'Colombo', 'COL', 'Western', 1, '2026-01-24 14:33:58'),
(2, 'Gampaha', 'GAM', 'Western', 1, '2026-01-24 14:33:58'),
(3, 'Kalutara', 'KAL', 'Western', 1, '2026-01-24 14:33:58'),
(4, 'Kandy', 'KAN', 'Central', 1, '2026-01-24 14:33:58'),
(5, 'Matale', 'MAT', 'Central', 1, '2026-01-24 14:33:58'),
(6, 'Nuwara Eliya', 'NUW', 'Central', 1, '2026-01-24 14:33:58'),
(7, 'Galle', 'GAL', 'Southern', 1, '2026-01-24 14:33:58'),
(8, 'Matara', 'MATA', 'Southern', 1, '2026-01-24 14:33:58'),
(9, 'Hambantota', 'HAM', 'Southern', 1, '2026-01-24 14:33:58'),
(10, 'Jaffna', 'JAF', 'Northern', 1, '2026-01-24 14:33:58'),
(11, 'Kilinochchi', 'KIL', 'Northern', 1, '2026-01-24 14:33:58'),
(12, 'Mannar', 'MAN', 'Northern', 1, '2026-01-24 14:33:58'),
(13, 'Vavuniya', 'VAV', 'Northern', 1, '2026-01-24 14:33:58'),
(14, 'Mullaitivu', 'MUL', 'Northern', 1, '2026-01-24 14:33:58'),
(15, 'Batticaloa', 'BAT', 'Eastern', 1, '2026-01-24 14:33:58'),
(16, 'Ampara', 'AMP', 'Eastern', 1, '2026-01-24 14:33:58'),
(17, 'Trincomalee', 'TRI', 'Eastern', 1, '2026-01-24 14:33:58'),
(18, 'Kurunegala', 'KUR', 'North Western', 1, '2026-01-24 14:33:58'),
(19, 'Puttalam', 'PUT', 'North Western', 1, '2026-01-24 14:33:58'),
(20, 'Anuradhapura', 'ANU', 'North Central', 1, '2026-01-24 14:33:58'),
(21, 'Polonnaruwa', 'POL', 'North Central', 1, '2026-01-24 14:33:58'),
(22, 'Badulla', 'BAD', 'Uva', 1, '2026-01-24 14:33:58'),
(23, 'Monaragala', 'MON', 'Uva', 1, '2026-01-24 14:33:58'),
(24, 'Ratnapura', 'RAT', 'Sabaragamuwa', 1, '2026-01-24 14:33:58'),
(25, 'Kegalle', 'KEG', 'Sabaragamuwa', 1, '2026-01-24 14:33:58');

-- --------------------------------------------------------

--
-- Table structure for table `district_distances`
--

CREATE TABLE `district_distances` (
  `id` int(11) NOT NULL,
  `from_district_id` int(11) NOT NULL,
  `to_district_id` int(11) NOT NULL,
  `distance_km` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `district_distances`
--

INSERT INTO `district_distances` (`id`, `from_district_id`, `to_district_id`, `distance_km`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(2, 1, 2, 29, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(3, 1, 3, 43, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(4, 1, 4, 115, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(5, 1, 5, 142, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(6, 1, 6, 180, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(7, 1, 7, 119, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(8, 1, 8, 160, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(9, 1, 9, 237, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(10, 1, 18, 94, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(11, 1, 20, 206, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(12, 1, 24, 101, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(13, 1, 25, 80, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(14, 2, 1, 29, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(15, 2, 2, 0, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(16, 2, 4, 144, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(17, 2, 18, 123, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(18, 3, 1, 43, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(19, 3, 3, 0, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(20, 3, 7, 76, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(21, 3, 24, 122, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(22, 4, 1, 115, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(23, 4, 2, 144, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(24, 4, 4, 0, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(25, 4, 5, 42, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(26, 4, 6, 77, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(27, 4, 18, 59, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(28, 4, 20, 106, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(29, 4, 21, 101, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(30, 4, 22, 95, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(31, 5, 1, 142, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(32, 5, 4, 42, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(33, 5, 5, 0, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(34, 5, 18, 73, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(35, 5, 20, 76, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(36, 6, 1, 180, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(37, 6, 4, 77, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(38, 6, 6, 0, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(39, 6, 22, 78, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(40, 7, 1, 119, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(41, 7, 3, 76, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(42, 7, 7, 0, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(43, 7, 8, 45, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(44, 7, 9, 118, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(45, 8, 1, 160, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(46, 8, 7, 45, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(47, 8, 8, 0, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(48, 8, 9, 78, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(49, 9, 1, 237, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(50, 9, 7, 118, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(51, 9, 8, 78, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(52, 9, 9, 0, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(53, 18, 1, 94, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(54, 18, 2, 123, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(55, 18, 4, 59, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(56, 18, 5, 73, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(57, 18, 18, 0, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(58, 18, 19, 72, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(59, 18, 20, 85, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(60, 20, 1, 206, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(61, 20, 4, 106, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(62, 20, 5, 76, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(63, 20, 18, 85, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(64, 20, 20, 0, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(65, 20, 21, 104, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(66, 21, 4, 101, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(67, 21, 20, 104, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(68, 21, 21, 0, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(69, 22, 1, 230, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(70, 22, 4, 95, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(71, 22, 6, 78, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(72, 22, 22, 0, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(73, 22, 23, 85, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(74, 23, 22, 85, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(75, 23, 23, 0, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(76, 24, 1, 101, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(77, 24, 3, 122, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(78, 24, 24, 0, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(79, 24, 25, 48, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(80, 25, 1, 80, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(81, 25, 4, 43, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(82, 25, 24, 48, '2026-01-24 14:35:24', '2026-01-24 14:35:24'),
(83, 25, 25, 0, '2026-01-24 14:35:24', '2026-01-24 14:35:24');

-- --------------------------------------------------------

--
-- Table structure for table `farmer_profiles`
--

CREATE TABLE `farmer_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `crops_selling` text DEFAULT NULL,
  `full_address` text DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmer_profiles`
--

INSERT INTO `farmer_profiles` (`id`, `user_id`, `phone`, `district`, `crops_selling`, `full_address`, `profile_photo`, `created_at`, `updated_at`) VALUES
(1, 9, NULL, NULL, NULL, NULL, NULL, '2026-01-08 06:06:28', '2026-01-08 06:06:28'),
(2, 5, NULL, NULL, NULL, NULL, NULL, '2026-01-21 18:32:13', '2026-01-21 18:32:13');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL COMMENT 'Product total before shipping',
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `order_total` decimal(10,2) NOT NULL COMMENT 'Total including shipping',
  `total_weight_kg` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total weight of all items in kg',
  `payment_method` varchar(50) DEFAULT 'cash_on_delivery',
  `delivery_address` text NOT NULL,
  `delivery_city` varchar(100) NOT NULL,
  `delivery_district_id` int(11) DEFAULT NULL,
  `delivery_town_id` int(11) DEFAULT NULL,
  `delivery_phone` varchar(20) NOT NULL,
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `buyer_id`, `total_amount`, `shipping_cost`, `order_total`, `total_weight_kg`, `payment_method`, `delivery_address`, `delivery_city`, `delivery_district_id`, `delivery_town_id`, `delivery_phone`, `status`, `created_at`, `updated_at`) VALUES
(1, 11, 120.00, 150.00, 270.00, 5.00, 'bank_transfer', '35/4, Lake Rd', 'Matale', 5, 18, '0702242499', 'pending', '2026-01-24 15:56:46', '2026-01-24 15:56:46'),
(2, 11, 59.00, 150.00, 209.00, 2.50, 'cash_on_delivery', '35/4, Lake Rd', 'Matale', 5, 18, '0702242499', 'pending', '2026-01-24 15:57:24', '2026-01-24 15:57:24'),
(3, 11, 65.00, 150.00, 215.00, 1.00, 'bank_transfer', '35/4, Lake Rd', 'Matale', 5, 18, '0702242499', 'pending', '2026-01-24 16:01:17', '2026-01-24 16:01:17'),
(4, 11, 3600.00, 1538.25, 5138.25, 150.00, 'bank_transfer', '35/4, Lake Rd', 'Matale', 5, 18, '0702242499', 'pending', '2026-01-24 17:32:45', '2026-01-24 17:32:45'),
(5, 11, 3600.00, 1538.25, 5138.25, 150.00, 'bank_transfer', '35/4, Lake Rd', 'Matale', 5, 18, '0702242499', 'pending', '2026-01-24 17:32:57', '2026-01-24 17:32:57'),
(6, 11, 3600.00, 1538.25, 5138.25, 150.00, 'cash_on_delivery', '35/4, Lake Rd', 'Matale', 5, 18, '0702242499', 'pending', '2026-01-24 17:33:32', '2026-01-24 17:33:32'),
(7, 11, 700.00, 955.50, 1655.50, 75.00, 'cash_on_delivery', '35/4, Lake Rd', 'Matale', 5, 18, '0702242499', 'pending', '2026-01-24 17:33:50', '2026-01-24 17:33:50');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `weight_kg` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total weight of items in kg',
  `farmer_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_price`, `quantity`, `weight_kg`, `farmer_id`, `created_at`) VALUES
(1, 2, 35, 'pumpkin', 59.00, 1, 2.50, 23, '2026-01-24 15:57:24'),
(2, 3, 30, 'carrots', 65.00, 1, 1.00, 23, '2026-01-24 16:01:17');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_requests`
--

CREATE TABLE `delivery_requests` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `buyer_name` varchar(255) NOT NULL,
  `buyer_phone` varchar(20) NOT NULL,
  `buyer_address` text NOT NULL,
  `buyer_city` varchar(100) NOT NULL,
  `buyer_district_id` int(11) DEFAULT NULL,
  `farmer_id` int(11) NOT NULL,
  `farmer_name` varchar(255) NOT NULL,
  `farmer_phone` varchar(20) DEFAULT NULL,
  `farmer_address` text DEFAULT NULL,
  `farmer_city` varchar(100) DEFAULT NULL,
  `farmer_district_id` int(11) DEFAULT NULL,
  `total_weight_kg` decimal(10,2) NOT NULL,
  `shipping_fee` decimal(10,2) NOT NULL,
  `distance_km` decimal(10,2) DEFAULT NULL,
  `required_vehicle_type_id` int(11) DEFAULT NULL COMMENT 'Minimum vehicle type needed based on weight',
  `status` enum('pending','accepted','in_transit','delivered','cancelled') DEFAULT 'pending',
  `transporter_id` int(11) DEFAULT NULL COMMENT 'Assigned transporter',
  `accepted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `platform_config`
--

CREATE TABLE `platform_config` (
  `id` int(11) NOT NULL,
  `config_key` varchar(100) NOT NULL,
  `config_value` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `platform_config`
--

INSERT INTO `platform_config` (`id`, `config_key`, `config_value`, `description`, `updated_at`) VALUES
(1, 'platform_fee_percentage', '5', 'Platform service fee as percentage of shipping cost', '2026-01-24 14:37:37'),
(2, 'platform_fee_min_lkr', '20', 'Minimum platform fee in LKR', '2026-01-24 14:37:37'),
(3, 'platform_fee_max_lkr', '150', 'Maximum platform fee in LKR', '2026-01-24 14:37:37'),
(4, 'transporter_earning_percentage', '85', 'Percentage of shipping cost paid to transporter', '2026-01-24 14:37:37'),
(5, 'vehicle_size_multiplier_max', '2', 'Maximum vehicle capacity vs effective weight ratio', '2026-01-24 14:37:37'),
(6, 'default_crop_volume_factor', '1.0', 'Default volume factor if crop not found', '2026-01-24 14:37:37');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `farmer_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `product_master_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `town_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'other',
  `listing_date` date NOT NULL DEFAULT curdate(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `farmer_id`, `name`, `product_master_id`, `price`, `quantity`, `description`, `district_id`, `town_id`, `image`, `location`, `category`, `listing_date`, `created_at`, `updated_at`) VALUES
(30, 23, 'carrots', NULL, 65.00, 14, '', NULL, NULL, 'product_68f924a2f2789.webp', 'Kandy', 'vegetables', '2025-10-22', '2025-10-22 13:08:27', '2026-01-24 16:01:17'),
(31, 23, 'tomatoes', NULL, 50.00, 20, '', NULL, NULL, 'product_68f924d735c46.jpg', 'Kandy', 'vegetables', '2025-10-22', '2025-10-22 13:09:19', '2025-10-22 13:09:19'),
(32, 23, 'leaks', NULL, 70.00, 40, '', NULL, NULL, 'product_68f9262659a90.jpg', 'mathale', 'vegetables', '2025-10-22', '2025-10-22 13:14:54', '2025-10-22 13:14:54'),
(35, 23, 'pumpkin', NULL, 59.00, 9, '', NULL, NULL, 'product_68f92bfcf3d31.jpeg', 'galle', 'vegetables', '2025-10-22', '2025-10-22 13:39:49', '2026-01-24 15:57:24'),
(36, 29, 'Papaya', NULL, 120.00, 60, '', NULL, NULL, 'product_68f93e9f14c81.jpeg', 'Dabulla', 'fruits', '2025-10-24', '2025-10-22 20:29:19', '2025-10-22 20:29:19'),
(37, 29, 'Banana', NULL, 100.00, 80, '', NULL, NULL, 'product_68f9436a272fb.webp', 'monaragala', 'fruits', '2025-10-23', '2025-10-22 20:49:46', '2025-10-22 20:49:46'),
(38, 9, 'onion', NULL, 80.00, 100, '', NULL, NULL, 'product_69751058cae1f.jpg', 'Jaffna', 'vegetables', '2026-01-24', '2026-01-24 18:32:56', '2026-01-24 18:32:56');

-- --------------------------------------------------------

--
-- Table structure for table `towns`
--

CREATE TABLE `towns` (
  `id` int(11) NOT NULL,
  `town_name` varchar(100) NOT NULL,
  `district_id` int(11) NOT NULL,
  `extra_distance_km` int(11) NOT NULL DEFAULT 0,
  `postal_code` varchar(10) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `towns`
--

INSERT INTO `towns` (`id`, `town_name`, `district_id`, `extra_distance_km`, `postal_code`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Colombo Fort', 1, 0, '00100', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(2, 'Dehiwala', 1, 8, '10350', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(3, 'Maharagama', 1, 14, '10280', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(4, 'Kotte', 1, 7, '10100', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(5, 'Homagama', 1, 22, '10200', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(6, 'Kaduwela', 1, 18, '10640', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(7, 'Moratuwa', 1, 19, '10400', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(8, 'Nugegoda', 1, 10, '10250', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(9, 'Battaramulla', 1, 12, '10120', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(10, 'Kandy Town', 4, 0, '20000', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(11, 'Peradeniya', 4, 8, '20400', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(12, 'Katugastota', 4, 6, '20800', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(13, 'Gampola', 4, 27, '20500', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(14, 'Nawalapitiya', 4, 42, '20650', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(15, 'Teldeniya', 4, 20, '20900', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(16, 'Kundasale', 4, 10, '20168', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(17, 'Akurana', 4, 14, '20850', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(18, 'Matale Town', 5, 0, '21000', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(19, 'Dambulla', 5, 28, '21100', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(20, 'Galewela', 5, 20, '21200', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(21, 'Ukuwela', 5, 12, '21300', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(22, 'Naula', 5, 28, '21600', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(23, 'Rattota', 5, 18, '21400', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(24, 'Galle Fort', 7, 0, '80000', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(25, 'Hikkaduwa', 7, 19, '80240', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(26, 'Ambalangoda', 7, 28, '80300', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(27, 'Elpitiya', 7, 24, '80400', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(28, 'Bentota', 7, 35, '80500', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(29, 'Gampaha', 2, 0, '11000', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(30, 'Negombo', 2, 12, '11500', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(31, 'Ja-Ela', 2, 16, '11350', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(32, 'Kadawatha', 2, 10, '11850', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(33, 'Ragama', 2, 6, '11010', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(34, 'Kurunegala', 18, 0, '60000', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(35, 'Kuliyapitiya', 18, 28, '60200', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(36, 'Polgahawela', 18, 16, '60300', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43'),
(37, 'Mawathagama', 18, 22, '60060', 1, '2026-01-24 14:36:43', '2026-01-24 14:36:43');

-- --------------------------------------------------------

--
-- Table structure for table `transporter_profiles`
--

CREATE TABLE `transporter_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `apartment_code` varchar(50) DEFAULT NULL,
  `street_name` varchar(150) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `full_address` text DEFAULT NULL,
  `vehicle_type` varchar(100) DEFAULT NULL,
  `license_number` varchar(100) DEFAULT NULL,
  `availability` varchar(255) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(15) NOT NULL,
  `role` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
(4, 'diqqa', 'diqqa1909@gmail.com', '123456789', 'farmer', 'active', '2025-10-19 12:14:27', '2025-10-19 12:14:27'),
(5, 'kalmith', 'kalmith@gmail.com', '123456789', 'farmer', 'active', '2025-10-19 12:14:27', '2025-10-19 12:14:27'),
(6, 'nimo', 'nimo@gmail.com', '123456789', 'transporter', 'active', '2025-10-19 12:14:27', '2025-10-19 12:14:27'),
(7, 'diqqa', 'me@gmail.com', '123456789', 'buyer', 'active', '2025-10-19 12:14:27', '2025-10-19 12:14:27'),
(8, 'yomal', 'yomal@gmail.com', '123456789', 'transporter', 'active', '2025-10-19 12:14:27', '2025-10-19 12:14:27'),
(9, 'sewni', 'sewni@gmail.com', '123456789', 'farmer', 'active', '2025-10-19 12:14:27', '2025-10-19 12:14:27'),
(10, 'sadun', 'sadun@gmail.com', '12345678', 'buyer', 'active', '2025-10-19 12:14:27', '2025-10-19 12:14:27'),
(11, 'Yomal Chandima', 'yc@gmail.com', '12345678', 'buyer', 'active', '2025-10-19 12:14:27', '2026-01-21 14:19:54'),
(23, 'vonara', 'vonara@gmail.com', '123456789', 'farmer', 'active', '2025-10-22 07:22:02', '2025-10-22 07:22:02'),
(24, 'yomal', 'yomal@gmail.com', '123456789', 'buyer', 'active', '2025-10-22 07:37:23', '2025-10-22 07:37:23'),
(25, 'sadhiq', 'sa@gmail.com', '123456789', 'farmer', 'active', '2025-10-22 08:32:07', '2025-10-22 08:32:07'),
(26, 'sdq', 'sdq@gmail.com', '123456789', 'admin', 'active', '2025-10-22 08:33:43', '2025-10-22 08:33:43'),
(27, 'sdq', 'sdq@gmail.com', '123456789', 'admin', 'active', '2025-10-22 08:33:48', '2025-10-22 08:33:48'),
(28, 'kalmith', 'kal@gmail.com', '123456789', 'transporter', 'active', '2025-10-22 08:38:58', '2025-10-22 08:38:58'),
(29, 'faramer1', 'F1@gmail.com', '123456abc', 'farmer', 'active', '2025-10-22 20:19:07', '2025-10-22 20:19:07'),
(30, 'B1', 'b1@gmail.com', '12345678', 'buyer', 'active', '2025-10-22 20:21:39', '2025-10-22 20:21:39'),
(31, 'ch', 'ch@gmail.com', '12345678', 'buyer', 'active', '2025-10-23 06:44:15', '2025-10-23 06:44:15'),
(32, 'T1', 't1@gmail.com', '12345678', 'transporter', 'active', '2025-10-23 06:58:26', '2025-10-23 06:58:26'),
(33, 'fgg', 'fg2@gmail.com', '12345678', 'farmer', 'active', '2025-10-23 07:55:03', '2025-10-23 07:55:03'),
(34, 'b3', 'b3@gmail.com', '12345678', 'buyer', 'active', '2025-11-04 09:37:09', '2025-11-04 09:37:09'),
(35, 'W.P.Perera', 'wp@gmail.com', '12345678', 'buyer', 'active', '2026-01-05 18:05:34', '2026-01-21 14:17:49'),
(36, 'TJ', 'TJ@gmail.com', '12345678', 'buyer', 'active', '2026-01-21 14:07:52', '2026-01-21 14:07:52');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `transporter_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `vehicle_type_id` int(11) DEFAULT NULL,
  `registration` varchar(20) NOT NULL,
  `capacity` decimal(10,2) NOT NULL,
  `fuel_type` varchar(20) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive','maintenance') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_types`
--

CREATE TABLE `vehicle_types` (
  `id` int(11) NOT NULL,
  `vehicle_name` varchar(50) NOT NULL,
  `min_weight_kg` int(11) NOT NULL DEFAULT 0,
  `max_weight_kg` int(11) NOT NULL,
  `base_fee_lkr` decimal(10,2) NOT NULL,
  `cost_per_km_lkr` decimal(10,2) NOT NULL,
  `cost_per_kg_lkr` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_types`
--

INSERT INTO `vehicle_types` (`id`, `vehicle_name`, `min_weight_kg`, `max_weight_kg`, `base_fee_lkr`, `cost_per_km_lkr`, `cost_per_kg_lkr`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Bike', 0, 50, 150.00, 5.00, 5.00, 1, '2026-01-24 14:29:09', '2026-01-24 14:29:09'),
(2, 'Threewheel', 51, 150, 300.00, 7.00, 3.00, 1, '2026-01-24 14:29:09', '2026-01-24 14:29:09'),
(3, 'Small Van', 151, 300, 500.00, 10.00, 2.50, 1, '2026-01-24 14:29:09', '2026-01-24 14:29:09'),
(4, 'Van', 301, 500, 800.00, 15.00, 2.00, 1, '2026-01-24 14:29:09', '2026-01-24 14:29:09'),
(5, 'Small Lorry', 501, 750, 1200.00, 20.00, 1.50, 1, '2026-01-24 14:29:09', '2026-01-24 14:29:09'),
(6, 'Lorry', 751, 1000, 1800.00, 25.00, 1.00, 1, '2026-01-24 14:29:09', '2026-01-24 14:29:09');

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
(16, 35, 37, '2026-01-08 09:41:54'),
(17, 35, 32, '2026-01-08 09:42:03'),
(20, 11, 37, '2026-01-13 19:55:43'),
(21, 11, 36, '2026-01-13 20:12:51'),
(22, 11, 35, '2026-01-21 14:21:35'),
(23, 36, 37, '2026-01-21 14:25:12'),
(24, 11, 38, '2026-01-24 18:33:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buyer_profiles`
--
ALTER TABLE `buyer_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `crop_requests`
--
ALTER TABLE `crop_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buyer_id` (`buyer_id`);

--
-- Indexes for table `crop_volume_factors`
--
ALTER TABLE `crop_volume_factors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crop_name` (`crop_name`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `district_name` (`district_name`),
  ADD UNIQUE KEY `district_code` (`district_code`);

--
-- Indexes for table `district_distances`
--
ALTER TABLE `district_distances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_route` (`from_district_id`,`to_district_id`),
  ADD KEY `idx_from` (`from_district_id`),
  ADD KEY `idx_to` (`to_district_id`);

--
-- Indexes for table `farmer_profiles`
--
ALTER TABLE `farmer_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_buyer` (`buyer_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_farmer` (`farmer_id`);

--
-- Indexes for table `delivery_requests`
--
ALTER TABLE `delivery_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_buyer` (`buyer_id`),
  ADD KEY `idx_farmer` (`farmer_id`),
  ADD KEY `idx_transporter` (`transporter_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_weight` (`total_weight_kg`),
  ADD KEY `idx_vehicle_type` (`required_vehicle_type_id`);

--
-- Indexes for table `platform_config`
--
ALTER TABLE `platform_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `config_key` (`config_key`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `towns`
--
ALTER TABLE `towns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_district` (`district_id`),
  ADD KEY `idx_town_name` (`town_name`);

--
-- Indexes for table `transporter_profiles`
--
ALTER TABLE `transporter_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transporter_id` (`transporter_id`),
  ADD KEY `idx_vehicle_type` (`vehicle_type_id`);

--
-- Indexes for table `vehicle_types`
--
ALTER TABLE `vehicle_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_name` (`vehicle_name`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buyer_profiles`
--
ALTER TABLE `buyer_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT for table `crop_requests`
--
ALTER TABLE `crop_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `crop_volume_factors`
--
ALTER TABLE `crop_volume_factors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `district_distances`
--
ALTER TABLE `district_distances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `farmer_profiles`
--
ALTER TABLE `farmer_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `delivery_requests`
--
ALTER TABLE `delivery_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `platform_config`
--
ALTER TABLE `platform_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `towns`
--
ALTER TABLE `towns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `transporter_profiles`
--
ALTER TABLE `transporter_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `vehicle_types`
--
ALTER TABLE `vehicle_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `buyer_profiles`
--
ALTER TABLE `buyer_profiles`
  ADD CONSTRAINT `buyer_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `crop_requests`
--
ALTER TABLE `crop_requests`
  ADD CONSTRAINT `crop_requests_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `district_distances`
--
ALTER TABLE `district_distances`
  ADD CONSTRAINT `district_distances_ibfk_1` FOREIGN KEY (`from_district_id`) REFERENCES `districts` (`id`),
  ADD CONSTRAINT `district_distances_ibfk_2` FOREIGN KEY (`to_district_id`) REFERENCES `districts` (`id`);

--
-- Constraints for table `farmer_profiles`
--
ALTER TABLE `farmer_profiles`
  ADD CONSTRAINT `farmer_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`farmer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `delivery_requests`
--
ALTER TABLE `delivery_requests`
  ADD CONSTRAINT `delivery_requests_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `delivery_requests_ibfk_2` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `delivery_requests_ibfk_3` FOREIGN KEY (`farmer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `delivery_requests_ibfk_4` FOREIGN KEY (`transporter_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `delivery_requests_ibfk_5` FOREIGN KEY (`required_vehicle_type_id`) REFERENCES `vehicle_types` (`id`);

--
-- Constraints for table `towns`
--
ALTER TABLE `towns`
  ADD CONSTRAINT `towns_ibfk_1` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`);

--
-- Constraints for table `transporter_profiles`
--
ALTER TABLE `transporter_profiles`
  ADD CONSTRAINT `transporter_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`transporter_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `vehicles_ibfk_2` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_types` (`id`);

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
