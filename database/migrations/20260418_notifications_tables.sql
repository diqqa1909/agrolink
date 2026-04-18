-- Notifications tables for user dashboards
-- Date: 2026-04-18
-- Safe to run multiple times (idempotent where possible)

START TRANSACTION;

CREATE TABLE IF NOT EXISTS notifications (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  event_key VARCHAR(120) NOT NULL,
  title VARCHAR(180) NOT NULL,
  message TEXT NOT NULL,
  type VARCHAR(50) NOT NULL DEFAULT 'system',
  related_id INT(11) DEFAULT NULL,
  link VARCHAR(255) DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  read_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_user_event (user_id, event_key),
  KEY idx_user_created (user_id, created_at),
  KEY idx_user_read (user_id, is_read),
  KEY idx_user_type (user_id, type),
  CONSTRAINT notifications_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notification_settings (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  settings_json JSON NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_notification_settings_user (user_id),
  CONSTRAINT notification_settings_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

