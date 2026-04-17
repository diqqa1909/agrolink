-- Introduce fake payment gateway order flow and remove saved-card storage.
-- Creates payment_status on orders, removes payment_method, and drops buyer_saved_cards.
ALTER TABLE orders
ADD COLUMN IF NOT EXISTS payment_status ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending'
AFTER order_total;
-- Backfill existing records conservatively.
UPDATE orders
SET payment_status = 'paid'
WHERE payment_status = 'pending'
    AND status IN (
        'confirmed',
        'processing',
        'shipped',
        'delivered'
    );
UPDATE orders
SET payment_status = 'failed'
WHERE payment_status = 'pending'
    AND status = 'cancelled';
ALTER TABLE orders DROP COLUMN IF EXISTS payment_method;
ALTER TABLE orders
MODIFY COLUMN status ENUM(
        'pending_payment',
        'pending',
        'confirmed',
        'processing',
        'shipped',
        'delivered',
        'cancelled'
    ) DEFAULT 'pending_payment';
DROP TABLE IF EXISTS buyer_saved_cards;