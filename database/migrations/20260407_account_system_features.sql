-- AgroLink account/system feature schema updates
-- Date: 2026-04-07

START TRANSACTION;

-- Users table additions for password/account lifecycle tracking
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS password_updated_at TIMESTAMP NULL DEFAULT NULL AFTER password,
  ADD COLUMN IF NOT EXISTS deactivated_at TIMESTAMP NULL DEFAULT NULL AFTER status,
  ADD COLUMN IF NOT EXISTS deactivation_reason VARCHAR(500) NULL AFTER deactivated_at;

SET @has_users_status_index := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'users'
    AND INDEX_NAME = 'idx_users_status'
);
SET @users_status_index_sql := IF(
  @has_users_status_index > 0,
  'SELECT 1',
  'CREATE INDEX idx_users_status ON users (status)'
);
PREPARE stmt_users_status_idx FROM @users_status_index_sql;
EXECUTE stmt_users_status_idx;
DEALLOCATE PREPARE stmt_users_status_idx;

SET @has_user_email_changes_fk := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'user_email_changes'
    AND CONSTRAINT_NAME = 'user_email_changes_ibfk_1'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @user_email_changes_fk_sql := IF(
  @has_user_email_changes_fk > 0,
  'SELECT 1',
  'ALTER TABLE user_email_changes
   ADD CONSTRAINT user_email_changes_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE'
);
PREPARE stmt_user_email_changes_fk FROM @user_email_changes_fk_sql;
EXECUTE stmt_user_email_changes_fk;
DEALLOCATE PREPARE stmt_user_email_changes_fk;

-- Notifications storage (read/unread + history)
CREATE TABLE IF NOT EXISTS notifications (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  event_key VARCHAR(120) NOT NULL,
  title VARCHAR(180) NOT NULL,
  message TEXT NOT NULL,
  type VARCHAR(50) NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  related_id INT(11) DEFAULT NULL,
  link VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  read_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_notifications_user_event (user_id, event_key),
  KEY idx_notifications_user_read (user_id, is_read),
  KEY idx_notifications_type (type),
  KEY idx_notifications_created_at (created_at),
  CONSTRAINT notifications_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Buyer saved cards storage (masked data only)
CREATE TABLE IF NOT EXISTS buyer_saved_cards (
  id INT(11) NOT NULL AUTO_INCREMENT,
  buyer_id INT(11) NOT NULL,
  card_holder_name VARCHAR(80) NOT NULL,
  card_last_four CHAR(4) NOT NULL,
  card_brand VARCHAR(40) NOT NULL DEFAULT 'Card',
  expiry_month TINYINT(3) UNSIGNED NOT NULL,
  expiry_year SMALLINT(5) UNSIGNED NOT NULL,
  is_default TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_buyer_saved_cards_buyer (buyer_id),
  KEY idx_buyer_saved_cards_default (buyer_id, is_default),
  CONSTRAINT buyer_saved_cards_ibfk_1 FOREIGN KEY (buyer_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Compatibility migration from older buyer_saved_cards schema (if previously created at runtime)
ALTER TABLE buyer_saved_cards
  ADD COLUMN IF NOT EXISTS card_last_four CHAR(4) NULL AFTER card_holder_name,
  ADD COLUMN IF NOT EXISTS card_brand VARCHAR(40) NOT NULL DEFAULT 'Card' AFTER card_last_four,
  ADD COLUMN IF NOT EXISTS created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

SET @has_old_card_last4 := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'buyer_saved_cards'
    AND COLUMN_NAME = 'card_last4'
);
SET @card_last4_backfill_sql := IF(
  @has_old_card_last4 > 0,
  'UPDATE buyer_saved_cards
   SET card_last_four = card_last4
   WHERE (card_last_four IS NULL OR card_last_four = \"\")
     AND card_last4 IS NOT NULL',
  'SELECT 1'
);
PREPARE stmt_card_last4 FROM @card_last4_backfill_sql;
EXECUTE stmt_card_last4;
DEALLOCATE PREPARE stmt_card_last4;

UPDATE buyer_saved_cards
SET expiry_year = CASE
    WHEN CAST(expiry_year AS UNSIGNED) < 100 THEN CAST(expiry_year AS UNSIGNED) + 2000
    ELSE CAST(expiry_year AS UNSIGNED)
END;

ALTER TABLE buyer_saved_cards
  MODIFY COLUMN card_last_four CHAR(4) NOT NULL,
  MODIFY COLUMN card_brand VARCHAR(40) NOT NULL DEFAULT 'Card',
  MODIFY COLUMN expiry_month TINYINT(3) UNSIGNED NOT NULL,
  MODIFY COLUMN expiry_year SMALLINT(5) UNSIGNED NOT NULL,
  MODIFY COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MODIFY COLUMN updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE buyer_saved_cards
  DROP COLUMN IF EXISTS card_last4;

SET @has_buyer_cards_buyer_idx := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'buyer_saved_cards'
    AND COLUMN_NAME = 'buyer_id'
);
SET @buyer_cards_buyer_idx_sql := IF(
  @has_buyer_cards_buyer_idx > 0,
  'SELECT 1',
  'CREATE INDEX idx_buyer_saved_cards_buyer ON buyer_saved_cards (buyer_id)'
);
PREPARE stmt_buyer_cards_buyer_idx FROM @buyer_cards_buyer_idx_sql;
EXECUTE stmt_buyer_cards_buyer_idx;
DEALLOCATE PREPARE stmt_buyer_cards_buyer_idx;

-- Remove orphan card rows before adding FK.
DELETE bsc
FROM buyer_saved_cards bsc
LEFT JOIN users u ON u.id = bsc.buyer_id
WHERE u.id IS NULL;

SET @has_buyer_cards_fk := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'buyer_saved_cards'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @buyer_cards_fk_sql := IF(
  @has_buyer_cards_fk > 0,
  'SELECT 1',
  'ALTER TABLE buyer_saved_cards
   ADD CONSTRAINT buyer_saved_cards_ibfk_1 FOREIGN KEY (buyer_id) REFERENCES users (id) ON DELETE CASCADE'
);
PREPARE stmt_buyer_cards_fk FROM @buyer_cards_fk_sql;
EXECUTE stmt_buyer_cards_fk;
DEALLOCATE PREPARE stmt_buyer_cards_fk;

-- Payout accounts for farmer/transporter earnings
CREATE TABLE IF NOT EXISTS payout_accounts (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  account_holder_name VARCHAR(120) NOT NULL,
  bank_name VARCHAR(120) NOT NULL,
  branch_name VARCHAR(120) DEFAULT NULL,
  account_number VARCHAR(30) NOT NULL,
  account_type VARCHAR(40) DEFAULT NULL,
  is_default TINYINT(1) NOT NULL DEFAULT 0,
  is_verified TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_payout_accounts_user (user_id),
  KEY idx_payout_accounts_default (user_id, is_default),
  KEY idx_payout_accounts_verified (is_verified),
  CONSTRAINT payout_accounts_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
