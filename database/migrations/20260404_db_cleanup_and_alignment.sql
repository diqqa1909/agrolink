-- Database cleanup and schema alignment for agrolink (latest)
-- Date: 2026-04-04
-- Safe to run multiple times (idempotent where possible)

START TRANSACTION;

-- 1) Schema alignment required by current code paths
ALTER TABLE buyer_profiles
  ADD COLUMN IF NOT EXISTS additional_address_details VARCHAR(100) NULL AFTER postal_code;

ALTER TABLE orders
  ADD COLUMN IF NOT EXISTS total_weight_kg DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total weight of all items in kg' AFTER order_total;

ALTER TABLE order_items
  ADD COLUMN IF NOT EXISTS item_weight_kg DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total weight of items in kg' AFTER quantity;

-- Keep password column compatible with PHP password_hash/password_verify
ALTER TABLE users
  MODIFY COLUMN password VARCHAR(255) NOT NULL;

-- Track farmer email-change policy (max changes handled in app logic)
CREATE TABLE IF NOT EXISTS user_email_changes (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  old_email VARCHAR(255) NOT NULL,
  new_email VARCHAR(255) NOT NULL,
  changed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_user_email_changes_user_id (user_id),
  CONSTRAINT user_email_changes_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) Create missing delivery_requests table used by checkout/transporter flow
CREATE TABLE IF NOT EXISTS delivery_requests (
  id INT(11) NOT NULL AUTO_INCREMENT,
  order_id INT(11) NOT NULL,
  buyer_id INT(11) NOT NULL,
  buyer_name VARCHAR(255) NOT NULL,
  buyer_phone VARCHAR(20) NOT NULL,
  buyer_address TEXT NOT NULL,
  buyer_city VARCHAR(100) NOT NULL,
  buyer_district_id INT(11) DEFAULT NULL,
  farmer_id INT(11) NOT NULL,
  farmer_name VARCHAR(255) NOT NULL,
  farmer_phone VARCHAR(20) DEFAULT NULL,
  farmer_address TEXT DEFAULT NULL,
  farmer_city VARCHAR(100) DEFAULT NULL,
  farmer_district_id INT(11) DEFAULT NULL,
  total_weight_kg DECIMAL(10,2) NOT NULL,
  shipping_fee DECIMAL(10,2) NOT NULL,
  distance_km DECIMAL(10,2) DEFAULT NULL,
  required_vehicle_type_id INT(11) DEFAULT NULL COMMENT 'Minimum vehicle type needed based on weight',
  status ENUM('pending','accepted','in_transit','delivered','cancelled') DEFAULT 'pending',
  transporter_id INT(11) DEFAULT NULL COMMENT 'Assigned transporter',
  accepted_at TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_order (order_id),
  KEY idx_buyer (buyer_id),
  KEY idx_farmer (farmer_id),
  KEY idx_transporter (transporter_id),
  KEY idx_status (status),
  KEY idx_weight (total_weight_kg),
  KEY idx_vehicle_type (required_vehicle_type_id),
  CONSTRAINT delivery_requests_ibfk_1 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
  CONSTRAINT delivery_requests_ibfk_2 FOREIGN KEY (buyer_id) REFERENCES users (id),
  CONSTRAINT delivery_requests_ibfk_3 FOREIGN KEY (farmer_id) REFERENCES users (id),
  CONSTRAINT delivery_requests_ibfk_4 FOREIGN KEY (transporter_id) REFERENCES users (id) ON DELETE SET NULL,
  CONSTRAINT delivery_requests_ibfk_5 FOREIGN KEY (required_vehicle_type_id) REFERENCES vehicle_types (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3) Clean orphan cart rows that refer to deleted products
DELETE c
FROM cart c
LEFT JOIN products p ON p.id = c.product_id
WHERE p.id IS NULL;

-- 4) Backfill cart farmer details from current product/user records
UPDATE cart c
JOIN products p ON p.id = c.product_id
LEFT JOIN users u ON u.id = p.farmer_id
SET c.farmer_name = COALESCE(NULLIF(c.farmer_name, ''), u.name, ''),
    c.farmer_location = COALESCE(NULLIF(c.farmer_location, ''), p.location, '');

-- 5) Normalize vehicle_type_id from existing vehicles.type values
UPDATE vehicles v
JOIN vehicle_types vt ON LOWER(REPLACE(vt.vehicle_name, ' ', '')) = LOWER(REPLACE(v.type, ' ', ''))
SET v.vehicle_type_id = vt.id
WHERE v.vehicle_type_id IS NULL;

COMMIT;
