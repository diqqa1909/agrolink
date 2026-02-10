-- =====================================================
-- AgroLink Shipping Cost Calculation - MINIMAL TABLES
-- Only tables needed for calculating shipping cost
-- =====================================================

-- 1. VEHICLE TYPES TABLE (Reference for pricing)
CREATE TABLE `vehicle_types` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `vehicle_name` VARCHAR(50) NOT NULL,
    `max_weight_kg` INT(11) NOT NULL,
    `base_fee_lkr` DECIMAL(10,2) NOT NULL,
    `cost_per_km_lkr` DECIMAL(10,2) NOT NULL,
    `cost_per_kg_lkr` DECIMAL(10,2) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `vehicle_name` (`vehicle_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert Vehicle Types with Reasonable Pricing
INSERT INTO `vehicle_types` (`vehicle_name`, `max_weight_kg`, `base_fee_lkr`, `cost_per_km_lkr`, `cost_per_kg_lkr`) VALUES
('Bike', 50, 150.00, 20.00, 5.00),
('Threewheel', 150, 300.00, 30.00, 3.00),
('Small Van', 300, 500.00, 40.00, 2.50),
('Van', 500, 800.00, 50.00, 2.00),
('Small Lorry', 750, 1200.00, 65.00, 1.50),
('Lorry', 1000, 1800.00, 80.00, 1.00);

-- =====================================================
-- 2. UPDATE EXISTING VEHICLES TABLE
-- Add vehicle_type_id to link with vehicle_types
-- =====================================================
ALTER TABLE `vehicles`
ADD COLUMN `vehicle_type_id` INT(11) DEFAULT NULL AFTER `type`,
ADD KEY `idx_vehicle_type` (`vehicle_type_id`),
ADD CONSTRAINT `vehicles_ibfk_2` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_types` (`id`);

-- Note: You'll need to update existing vehicles to set their vehicle_type_id
-- Example: UPDATE vehicles SET vehicle_type_id = 2 WHERE type = 'Threewheel';

-- =====================================================
-- 3. CROP VOLUME FACTORS TABLE (Only 3 Categories)
-- =====================================================
CREATE TABLE `crop_volume_factors` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `crop_name` VARCHAR(100) NOT NULL,
    `volume_factor` DECIMAL(3,2) NOT NULL,
    `category` ENUM('Vegetables', 'Fruits', 'Spices') NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `crop_name` (`crop_name`),
    KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert Crop Volume Factors (Organized by 3 Categories)
INSERT INTO `crop_volume_factors` (`crop_name`, `volume_factor`, `category`) VALUES
-- VEGETABLES
('Carrot', 1.00, 'Vegetables'),
('Potato', 1.00, 'Vegetables'),
('Beetroot', 1.10, 'Vegetables'),
('Radish', 1.00, 'Vegetables'),
('Onion', 1.20, 'Vegetables'),
('Garlic', 1.10, 'Vegetables'),
('Tomato', 1.30, 'Vegetables'),
('Capsicum', 1.50, 'Vegetables'),
('Brinjal', 1.40, 'Vegetables'),
('Beans', 1.20, 'Vegetables'),
('Peas', 1.10, 'Vegetables'),
('Cabbage', 1.60, 'Vegetables'),
('Lettuce', 1.80, 'Vegetables'),
('Spinach', 1.50, 'Vegetables'),
('Leeks', 1.40, 'Vegetables'),
('Pumpkin', 2.20, 'Vegetables'),
('Butternut', 2.00, 'Vegetables'),
('Cucumber', 1.40, 'Vegetables'),
('Bitter Gourd', 1.30, 'Vegetables'),
('Ridge Gourd', 1.50, 'Vegetables'),
('Snake Gourd', 1.60, 'Vegetables'),
('Okra', 1.30, 'Vegetables'),
('Ash Plantain', 1.70, 'Vegetables'),

-- FRUITS
('Banana', 1.60, 'Fruits'),
('Papaya', 1.90, 'Fruits'),
('Mango', 1.50, 'Fruits'),
('Pineapple', 1.80, 'Fruits'),
('Watermelon', 2.30, 'Fruits'),
('Coconut', 1.90, 'Fruits'),
('Avocado', 1.40, 'Fruits'),
('Passion Fruit', 1.30, 'Fruits'),
('Guava', 1.40, 'Fruits'),
('Orange', 1.50, 'Fruits'),
('Lime', 1.20, 'Fruits'),
('Pomegranate', 1.50, 'Fruits'),
('Dragon Fruit', 1.70, 'Fruits'),

-- SPICES
('Chili', 1.10, 'Spices'),
('Ginger', 1.20, 'Spices'),
('Turmeric', 1.10, 'Spices'),
('Pepper', 0.90, 'Spices'),
('Cinnamon', 0.80, 'Spices'),
('Cardamom', 0.70, 'Spices'),
('Cloves', 0.70, 'Spices'),
('Nutmeg', 0.80, 'Spices');

-- =====================================================
-- 4. DISTRICTS TABLE
-- =====================================================
CREATE TABLE `districts` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `district_name` VARCHAR(100) NOT NULL,
    `district_code` VARCHAR(10) NOT NULL,
    `province` VARCHAR(50) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `district_name` (`district_name`),
    UNIQUE KEY `district_code` (`district_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert All 25 Sri Lankan Districts
INSERT INTO `districts` (`district_name`, `district_code`, `province`) VALUES
('Colombo', 'COL', 'Western'),
('Gampaha', 'GAM', 'Western'),
('Kalutara', 'KAL', 'Western'),
('Kandy', 'KAN', 'Central'),
('Matale', 'MAT', 'Central'),
('Nuwara Eliya', 'NUW', 'Central'),
('Galle', 'GAL', 'Southern'),
('Matara', 'MATA', 'Southern'),
('Hambantota', 'HAM', 'Southern'),
('Jaffna', 'JAF', 'Northern'),
('Kilinochchi', 'KIL', 'Northern'),
('Mannar', 'MAN', 'Northern'),
('Vavuniya', 'VAV', 'Northern'),
('Mullaitivu', 'MUL', 'Northern'),
('Batticaloa', 'BAT', 'Eastern'),
('Ampara', 'AMP', 'Eastern'),
('Trincomalee', 'TRI', 'Eastern'),
('Kurunegala', 'KUR', 'North Western'),
('Puttalam', 'PUT', 'North Western'),
('Anuradhapura', 'ANU', 'North Central'),
('Polonnaruwa', 'POL', 'North Central'),
('Badulla', 'BAD', 'Uva'),
('Monaragala', 'MON', 'Uva'),
('Ratnapura', 'RAT', 'Sabaragamuwa'),
('Kegalle', 'KEG', 'Sabaragamuwa');

-- =====================================================
-- 5. DISTRICT DISTANCES TABLE
-- =====================================================
CREATE TABLE `district_distances` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `from_district_id` INT(11) NOT NULL,
    `to_district_id` INT(11) NOT NULL,
    `distance_km` INT(11) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_route` (`from_district_id`, `to_district_id`),
    KEY `idx_from` (`from_district_id`),
    KEY `idx_to` (`to_district_id`),
    CONSTRAINT `district_distances_ibfk_1` FOREIGN KEY (`from_district_id`) REFERENCES `districts` (`id`),
    CONSTRAINT `district_distances_ibfk_2` FOREIGN KEY (`to_district_id`) REFERENCES `districts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert District to District Distances (Bidirectional for major routes)
INSERT INTO `district_distances` (`from_district_id`, `to_district_id`, `distance_km`) VALUES
-- From Colombo (id=1)
(1, 1, 0), (1, 2, 29), (1, 3, 43), (1, 4, 115), (1, 5, 142), (1, 6, 180), 
(1, 7, 119), (1, 8, 160), (1, 9, 237), (1, 18, 94), (1, 20, 206), (1, 24, 101), (1, 25, 80),
-- From Gampaha (id=2)
(2, 1, 29), (2, 2, 0), (2, 4, 144), (2, 18, 123),
-- From Kalutara (id=3)
(3, 1, 43), (3, 3, 0), (3, 7, 76), (3, 24, 122),
-- From Kandy (id=4)
(4, 1, 115), (4, 2, 144), (4, 4, 0), (4, 5, 42), (4, 6, 77), (4, 18, 59), 
(4, 20, 106), (4, 21, 101), (4, 22, 95),
-- From Matale (id=5)
(5, 1, 142), (5, 4, 42), (5, 5, 0), (5, 18, 73), (5, 20, 76),
-- From Nuwara Eliya (id=6)
(6, 1, 180), (6, 4, 77), (6, 6, 0), (6, 22, 78),
-- From Galle (id=7)
(7, 1, 119), (7, 3, 76), (7, 7, 0), (7, 8, 45), (7, 9, 118),
-- From Matara (id=8)
(8, 1, 160), (8, 7, 45), (8, 8, 0), (8, 9, 78),
-- From Hambantota (id=9)
(9, 1, 237), (9, 7, 118), (9, 8, 78), (9, 9, 0),
-- From Kurunegala (id=18)
(18, 1, 94), (18, 2, 123), (18, 4, 59), (18, 5, 73), (18, 18, 0), (18, 19, 72), (18, 20, 85),
-- From Anuradhapura (id=20)
(20, 1, 206), (20, 4, 106), (20, 5, 76), (20, 18, 85), (20, 20, 0), (20, 21, 104),
-- From Polonnaruwa (id=21)
(21, 4, 101), (21, 20, 104), (21, 21, 0),
-- From Badulla (id=22)
(22, 1, 230), (22, 4, 95), (22, 6, 78), (22, 22, 0), (22, 23, 85),
-- From Monaragala (id=23)
(23, 22, 85), (23, 23, 0),
-- From Ratnapura (id=24)
(24, 1, 101), (24, 3, 122), (24, 24, 0), (24, 25, 48),
-- From Kegalle (id=25)
(25, 1, 80), (25, 4, 43), (25, 24, 48), (25, 25, 0);

-- =====================================================
-- 6. TOWNS TABLE
-- =====================================================
CREATE TABLE `towns` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `town_name` VARCHAR(100) NOT NULL,
    `district_id` INT(11) NOT NULL,
    `extra_distance_km` INT(11) NOT NULL DEFAULT 0,
    `postal_code` VARCHAR(10) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_district` (`district_id`),
    KEY `idx_town_name` (`town_name`),
    CONSTRAINT `towns_ibfk_1` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert Sample Towns for Major Districts
INSERT INTO `towns` (`town_name`, `district_id`, `extra_distance_km`, `postal_code`) VALUES
-- Colombo District (id=1)
('Colombo Fort', 1, 0, '00100'),
('Dehiwala', 1, 8, '10350'),
('Maharagama', 1, 14, '10280'),
('Kotte', 1, 7, '10100'),
('Homagama', 1, 22, '10200'),
('Kaduwela', 1, 18, '10640'),
('Moratuwa', 1, 19, '10400'),
('Nugegoda', 1, 10, '10250'),
('Battaramulla', 1, 12, '10120'),
-- Kandy District (id=4)
('Kandy Town', 4, 0, '20000'),
('Peradeniya', 4, 8, '20400'),
('Katugastota', 4, 6, '20800'),
('Gampola', 4, 27, '20500'),
('Nawalapitiya', 4, 42, '20650'),
('Teldeniya', 4, 20, '20900'),
('Kundasale', 4, 10, '20168'),
('Akurana', 4, 14, '20850'),
-- Matale District (id=5)
('Matale Town', 5, 0, '21000'),
('Dambulla', 5, 28, '21100'),
('Galewela', 5, 20, '21200'),
('Ukuwela', 5, 12, '21300'),
('Naula', 5, 28, '21600'),
('Rattota', 5, 18, '21400'),
-- Galle District (id=7)
('Galle Fort', 7, 0, '80000'),
('Hikkaduwa', 7, 19, '80240'),
('Ambalangoda', 7, 28, '80300'),
('Elpitiya', 7, 24, '80400'),
('Bentota', 7, 35, '80500'),
-- Gampaha District (id=2)
('Gampaha', 2, 0, '11000'),
('Negombo', 2, 12, '11500'),
('Ja-Ela', 2, 16, '11350'),
('Kadawatha', 2, 10, '11850'),
('Ragama', 2, 6, '11010'),
-- Kurunegala District (id=18)
('Kurunegala', 18, 0, '60000'),
('Kuliyapitiya', 18, 28, '60200'),
('Polgahawela', 18, 16, '60300'),
('Mawathagama', 18, 22, '60060');

-- =====================================================
-- 7. PLATFORM CONFIGURATION TABLE
-- =====================================================
CREATE TABLE `platform_config` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `config_key` VARCHAR(100) NOT NULL,
    `config_value` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `config_key` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert Platform Configuration
INSERT INTO `platform_config` (`config_key`, `config_value`, `description`) VALUES
('platform_fee_percentage', '5', 'Platform service fee as percentage of shipping cost'),
('platform_fee_min_lkr', '20', 'Minimum platform fee in LKR'),
('platform_fee_max_lkr', '150', 'Maximum platform fee in LKR'),
('transporter_earning_percentage', '85', 'Percentage of shipping cost paid to transporter'),
('vehicle_size_multiplier_max', '2', 'Maximum vehicle capacity vs effective weight ratio'),
('default_crop_volume_factor', '1.0', 'Default volume factor if crop not found');

-- =====================================================
-- DONE! These are the ONLY tables needed for calculating shipping cost
-- =====================================================

/*
SUMMARY OF TABLES:
1. vehicle_types - Pricing reference for different vehicle types
2. vehicles (existing) - Just add vehicle_type_id column
3. crop_volume_factors - Only 3 categories: Vegetables, Fruits, Spices
4. districts - 25 Sri Lankan districts
5. district_distances - Distance between districts
6. towns - Towns within districts with extra distance
7. platform_config - Configuration for fees and calculations

NO delivery_requests, orders, earnings tables - just pure calculation!
*/