-- Remove unused legacy buyer profile backup tables
-- Date: 2026-04-16
-- Safe to run multiple times
START TRANSACTION;
DROP TABLE IF EXISTS buyer_profiles_backup;
DROP TABLE IF EXISTS buyer_backup_profiles;
COMMIT;