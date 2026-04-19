-- Add product-level pickup address support and propagate to order items.
-- This keeps delivery requests aligned to the actual product pickup location.
ALTER TABLE products
ADD COLUMN IF NOT EXISTS full_address TEXT NULL
AFTER location;
UPDATE products
SET full_address = COALESCE(NULLIF(full_address, ''), NULLIF(location, ''))
WHERE full_address IS NULL
    OR TRIM(full_address) = '';
ALTER TABLE order_items
ADD COLUMN IF NOT EXISTS product_full_address TEXT NULL
AFTER farmer_id;
UPDATE order_items oi
    INNER JOIN products p ON p.id = oi.product_id
SET oi.product_full_address = COALESCE(
        NULLIF(oi.product_full_address, ''),
        NULLIF(p.full_address, ''),
        NULLIF(p.location, '')
    )
WHERE oi.product_full_address IS NULL
    OR TRIM(oi.product_full_address) = '';