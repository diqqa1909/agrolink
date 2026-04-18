-- Payment revision history for cancelled-order disputes
-- Date: 2026-04-18
-- Safe to run multiple times (idempotent where possible)

START TRANSACTION;

CREATE TABLE IF NOT EXISTS order_payment_revisions (
  id INT(11) NOT NULL AUTO_INCREMENT,
  order_id INT(11) NOT NULL,
  original_total_amount DECIMAL(10,2) NOT NULL,
  original_shipping_cost DECIMAL(10,2) NOT NULL,
  original_order_total DECIMAL(10,2) NOT NULL,
  revised_total_amount DECIMAL(10,2) NOT NULL,
  revised_shipping_cost DECIMAL(10,2) NOT NULL,
  revised_order_total DECIMAL(10,2) NOT NULL,
  reason VARCHAR(500) DEFAULT NULL,
  revised_by_admin_id INT(11) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  applied_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_order_payment_revisions_order (order_id),
  KEY idx_order_payment_revisions_created (created_at),
  CONSTRAINT order_payment_revisions_ibfk_1 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
  CONSTRAINT order_payment_revisions_ibfk_2 FOREIGN KEY (revised_by_admin_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

