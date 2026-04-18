-- Add dispute tracking columns to orders table
-- Date: 2026-04-18
-- Adds columns to track payment disputes and revisions for cancelled orders

START TRANSACTION;

-- Add columns to orders table for dispute tracking
ALTER TABLE `orders`
  ADD COLUMN IF NOT EXISTS `dispute_status` ENUM('none', 'open', 'in_progress', 'resolved') DEFAULT 'none' AFTER `status`,
  ADD COLUMN IF NOT EXISTS `dispute_reason` VARCHAR(500) DEFAULT NULL AFTER `dispute_status`,
  ADD COLUMN IF NOT EXISTS `dispute_priority` ENUM('low', 'medium', 'high') DEFAULT 'medium' AFTER `dispute_reason`;

-- Add columns to track payment revisions
ALTER TABLE `orders`
  ADD COLUMN IF NOT EXISTS `original_total_amount` DECIMAL(10,2) DEFAULT NULL AFTER `dispute_priority`,
  ADD COLUMN IF NOT EXISTS `original_shipping_cost` DECIMAL(10,2) DEFAULT NULL AFTER `original_total_amount`,
  ADD COLUMN IF NOT EXISTS `original_order_total` DECIMAL(10,2) DEFAULT NULL AFTER `original_shipping_cost`,
  ADD COLUMN IF NOT EXISTS `revised_at` TIMESTAMP NULL DEFAULT NULL AFTER `original_order_total`,
  ADD COLUMN IF NOT EXISTS `revised_by_admin_id` INT(11) DEFAULT NULL AFTER `revised_at`,
  ADD COLUMN IF NOT EXISTS `revision_reason` VARCHAR(500) DEFAULT NULL AFTER `revised_by_admin_id`;

-- Add index for dispute lookups
CREATE INDEX IF NOT EXISTS idx_orders_dispute_status ON `orders` (`dispute_status`);
CREATE INDEX IF NOT EXISTS idx_orders_cancel_status ON `orders` (`status`, `dispute_status`);
CREATE INDEX IF NOT EXISTS idx_orders_revised_at ON `orders` (`revised_at`);
CREATE INDEX IF NOT EXISTS idx_orders_revised_by_admin ON `orders` (`revised_by_admin_id`);

-- Add foreign key for revised_by_admin_id
ALTER TABLE `orders`
  ADD CONSTRAINT orders_revised_by_admin_fk FOREIGN KEY IF NOT EXISTS (revised_by_admin_id) REFERENCES users (id) ON DELETE SET NULL;

COMMIT;
