-- AgroLink Phase 1 critical fixes (notifications settings persistence)
-- Date: 2026-04-12

START TRANSACTION;

CREATE TABLE IF NOT EXISTS notification_settings (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  settings_json LONGTEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_notification_settings_user (user_id),
  CONSTRAINT notification_settings_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cleanup legacy mock/debug notification rows so users only see valid system notifications.
DELETE FROM notifications
WHERE event_key LIKE 'mock\_%'
   OR event_key LIKE 'test\_%'
   OR event_key LIKE 'debug\_%'
   OR LOWER(COALESCE(title, '')) LIKE '%localhost%'
   OR LOWER(COALESCE(message, '')) LIKE '%localhost%';

COMMIT;
