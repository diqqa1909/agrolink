START TRANSACTION;

SET @now_ts = NOW();

-- 1) Remove rows that reference deleted users/products/orders
DELETE c FROM cart c
LEFT JOIN users u ON u.id = c.user_id
WHERE u.id IS NULL;

DELETE c FROM cart c
LEFT JOIN products p ON p.id = c.product_id
WHERE p.id IS NULL;

DELETE w FROM wishlist w
LEFT JOIN users u ON u.id = w.user_id
WHERE u.id IS NULL;

DELETE w FROM wishlist w
LEFT JOIN products p ON p.id = w.product_id
WHERE p.id IS NULL;

DELETE bp FROM buyer_profiles bp
LEFT JOIN users u ON u.id = bp.user_id
WHERE u.id IS NULL;

DELETE fp FROM farmer_profiles fp
LEFT JOIN users u ON u.id = fp.user_id
WHERE u.id IS NULL;

DELETE tp FROM transporter_profiles tp
LEFT JOIN users u ON u.id = tp.user_id
WHERE u.id IS NULL;

DELETE v FROM vehicles v
LEFT JOIN users u ON u.id = v.transporter_id
WHERE u.id IS NULL;

DELETE r FROM reviews r
LEFT JOIN users ub ON ub.id = r.buyer_id
LEFT JOIN users uf ON uf.id = r.farmer_id
LEFT JOIN products p ON p.id = r.product_id
LEFT JOIN orders o ON o.id = r.order_id
WHERE ub.id IS NULL OR uf.id IS NULL OR p.id IS NULL OR o.id IS NULL;

DELETE cr FROM crop_requests cr
LEFT JOIN users u ON u.id = cr.buyer_id
WHERE u.id IS NULL;

DELETE oi FROM order_items oi
LEFT JOIN orders o ON o.id = oi.order_id
LEFT JOIN products p ON p.id = oi.product_id
LEFT JOIN users uf ON uf.id = oi.farmer_id
WHERE o.id IS NULL OR p.id IS NULL OR uf.id IS NULL;

DELETE dr FROM delivery_requests dr
LEFT JOIN orders o ON o.id = dr.order_id
LEFT JOIN users ub ON ub.id = dr.buyer_id
LEFT JOIN users uf ON uf.id = dr.farmer_id
LEFT JOIN users ut ON ut.id = dr.transporter_id
WHERE o.id IS NULL OR ub.id IS NULL OR uf.id IS NULL OR (dr.transporter_id IS NOT NULL AND ut.id IS NULL);

-- 2) Remove old orders and all related rows (older than today)
DROP TEMPORARY TABLE IF EXISTS tmp_old_orders;
CREATE TEMPORARY TABLE tmp_old_orders (id INT PRIMARY KEY);
INSERT INTO tmp_old_orders (id)
SELECT id FROM orders WHERE created_at < CURDATE();

DELETE FROM reviews WHERE order_id IN (SELECT id FROM tmp_old_orders);
DELETE FROM delivery_requests WHERE order_id IN (SELECT id FROM tmp_old_orders);
DELETE FROM order_items WHERE order_id IN (SELECT id FROM tmp_old_orders);
DELETE FROM orders WHERE id IN (SELECT id FROM tmp_old_orders);

-- 3) Remove products without valid farmer user and products missing district/town
DELETE p FROM products p
LEFT JOIN users u ON u.id = p.farmer_id
WHERE u.id IS NULL OR p.district_id IS NULL OR p.town_id IS NULL;

-- 4) Ensure transporter kalmith (user_id=28) has profile and active low-weight vehicle
INSERT INTO transporter_profiles
(user_id, company_name, phone, vehicle_type, license_number, availability, profile_photo, created_at, updated_at)
VALUES
(28, 'Kalmith Logistics', '0771234567', 'bike', 'LIC-28-AL', 'available', NULL, @now_ts, @now_ts)
ON DUPLICATE KEY UPDATE
company_name = VALUES(company_name),
phone = VALUES(phone),
vehicle_type = VALUES(vehicle_type),
license_number = VALUES(license_number),
availability = VALUES(availability),
updated_at = @now_ts;

UPDATE vehicles
SET status = 'active', updated_at = @now_ts
WHERE transporter_id = 28 AND vehicle_type_id = 1;

INSERT INTO vehicles
(transporter_id, type, vehicle_type_id, registration, capacity, fuel_type, model, status, created_at, updated_at)
SELECT 28, 'Bike', 1, 'CAL-281-BK', 20, 'petrol', 'Honda Dio', 'active', @now_ts, @now_ts
FROM dual
WHERE NOT EXISTS (
    SELECT 1 FROM vehicles WHERE transporter_id = 28 AND vehicle_type_id = 1
);

-- 5) Seed heavy products (>25kg) for requested user IDs 9,11,23,24
INSERT INTO products
(farmer_id, name, product_master_id, price, quantity, description, district_id, town_id, image, location, category, listing_date, created_at, updated_at)
VALUES
(9,  'Seed Big Pumpkin (UID9)',  NULL, 120.00, 80,  'Bulk pumpkins for wholesale', 5, 18, NULL, 'Matale Town, Matale', 'vegetables', CURDATE(), @now_ts, @now_ts),
(11, 'Seed Dry Chili (UID11)',   NULL, 220.00, 40,  'Dry chili stock for large orders', 5, 18, NULL, 'Matale Town, Matale', 'spices', CURDATE(), @now_ts, @now_ts),
(23, 'Seed Carrot Bulk (UID23)', NULL, 95.00, 120, 'Fresh carrots in bulk quantities', 5, 18, NULL, 'Matale Town, Matale', 'vegetables', CURDATE(), @now_ts, @now_ts),
(24, 'Seed Banana Box (UID24)',  NULL, 70.00, 60,  'Banana bulk stock for retailers', 5, 18, NULL, 'Matale Town, Matale', 'fruits', CURDATE(), @now_ts, @now_ts);

SET @p9  = (SELECT id FROM products WHERE farmer_id = 9  AND name = 'Seed Big Pumpkin (UID9)'  ORDER BY id DESC LIMIT 1);
SET @p23 = (SELECT id FROM products WHERE farmer_id = 23 AND name = 'Seed Carrot Bulk (UID23)' ORDER BY id DESC LIMIT 1);

-- 6) Seed fresh orders and delivery tracking flow
INSERT INTO orders
(buyer_id, total_amount, shipping_cost, order_total, total_weight_kg, payment_method, delivery_address, delivery_city, delivery_district_id, delivery_town_id, delivery_phone, status, created_at, updated_at)
VALUES
(11, 3600.00, 480.00, 4080.00, 30.00, 'cash_on_delivery', 'No 12, Matale Road', 'Matale', 5, 18, '0711111111', 'processing', @now_ts, @now_ts);
SET @order_a = LAST_INSERT_ID();

INSERT INTO order_items
(order_id, product_id, product_name, product_price, quantity, item_weight_kg, farmer_id, created_at)
VALUES
(@order_a, @p9, 'Seed Big Pumpkin (UID9)', 120.00, 30, 30.00, 9, @now_ts);

INSERT INTO delivery_requests
(order_id, buyer_id, buyer_name, buyer_phone, buyer_address, buyer_city, buyer_district_id, farmer_id, farmer_name, farmer_phone, farmer_address, farmer_city, farmer_district_id, total_weight_kg, shipping_fee, distance_km, required_vehicle_type_id, status, transporter_id, accepted_at, created_at, updated_at)
VALUES
(
    @order_a,
    11,
    COALESCE((SELECT name FROM users WHERE id = 11), 'Buyer 11'),
    COALESCE((SELECT phone FROM buyer_profiles WHERE user_id = 11), '0711111111'),
    COALESCE((SELECT CONCAT_WS(', ', apartment_code, street_name, city) FROM buyer_profiles WHERE user_id = 11), 'No 12, Matale Road'),
    COALESCE((SELECT city FROM buyer_profiles WHERE user_id = 11), 'Matale'),
    5,
    9,
    COALESCE((SELECT name FROM users WHERE id = 9), 'Farmer 9'),
    (SELECT phone FROM farmer_profiles WHERE user_id = 9),
    (SELECT full_address FROM farmer_profiles WHERE user_id = 9),
    'Matale',
    5,
    30.00,
    480.00,
    24.00,
    2,
    'accepted',
    28,
    @now_ts,
    @now_ts,
    @now_ts
);

INSERT INTO orders
(buyer_id, total_amount, shipping_cost, order_total, total_weight_kg, payment_method, delivery_address, delivery_city, delivery_district_id, delivery_town_id, delivery_phone, status, created_at, updated_at)
VALUES
(24, 3800.00, 620.00, 4420.00, 40.00, 'cash_on_delivery', 'No 05, Market Street', 'Matale', 5, 18, '0722222222', 'shipped', @now_ts, @now_ts);
SET @order_b = LAST_INSERT_ID();

INSERT INTO order_items
(order_id, product_id, product_name, product_price, quantity, item_weight_kg, farmer_id, created_at)
VALUES
(@order_b, @p23, 'Seed Carrot Bulk (UID23)', 95.00, 40, 40.00, 23, @now_ts);

INSERT INTO delivery_requests
(order_id, buyer_id, buyer_name, buyer_phone, buyer_address, buyer_city, buyer_district_id, farmer_id, farmer_name, farmer_phone, farmer_address, farmer_city, farmer_district_id, total_weight_kg, shipping_fee, distance_km, required_vehicle_type_id, status, transporter_id, accepted_at, created_at, updated_at)
VALUES
(
    @order_b,
    24,
    COALESCE((SELECT name FROM users WHERE id = 24), 'Buyer 24'),
    COALESCE((SELECT phone FROM buyer_profiles WHERE user_id = 24), '0722222222'),
    COALESCE((SELECT CONCAT_WS(', ', apartment_code, street_name, city) FROM buyer_profiles WHERE user_id = 24), 'No 05, Market Street'),
    COALESCE((SELECT city FROM buyer_profiles WHERE user_id = 24), 'Matale'),
    5,
    23,
    COALESCE((SELECT name FROM users WHERE id = 23), 'Farmer 23'),
    (SELECT phone FROM farmer_profiles WHERE user_id = 23),
    (SELECT full_address FROM farmer_profiles WHERE user_id = 23),
    'Matale',
    5,
    40.00,
    620.00,
    31.00,
    2,
    'in_transit',
    28,
    @now_ts,
    @now_ts,
    @now_ts
);

INSERT INTO orders
(buyer_id, total_amount, shipping_cost, order_total, total_weight_kg, payment_method, delivery_address, delivery_city, delivery_district_id, delivery_town_id, delivery_phone, status, created_at, updated_at)
VALUES
(11, 2375.00, 350.00, 2725.00, 25.00, 'cash_on_delivery', 'No 12, Matale Road', 'Matale', 5, 18, '0711111111', 'confirmed', @now_ts, @now_ts);
SET @order_c = LAST_INSERT_ID();

INSERT INTO order_items
(order_id, product_id, product_name, product_price, quantity, item_weight_kg, farmer_id, created_at)
VALUES
(@order_c, @p23, 'Seed Carrot Bulk (UID23)', 95.00, 25, 25.00, 23, @now_ts);

-- 7) Seed feedback records for farmer review/complaint replies
INSERT INTO reviews
(order_id, product_id, buyer_id, farmer_id, rating, comment, status, created_at, updated_at)
VALUES
(@order_a, @p9, 11, 9, 2, 'Some produce arrived bruised. Need better packing.', 'active', @now_ts, @now_ts),
(@order_b, @p23, 24, 23, 4, 'Good quality and fair pricing. Delivery in progress.', 'active', @now_ts, @now_ts);

-- 8) Update seeded product stock quantities after seeded orders
UPDATE products SET quantity = GREATEST(quantity - 30, 0) WHERE id = @p9;
UPDATE products SET quantity = GREATEST(quantity - 65, 0) WHERE id = @p23;

COMMIT;
