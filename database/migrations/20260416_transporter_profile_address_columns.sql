-- Align transporter profile schema with the current profile UI and vehicle management flow.
-- Safe to run multiple times on MariaDB/MySQL versions that support IF EXISTS.
ALTER TABLE transporter_profiles DROP COLUMN IF EXISTS vehicle_type,
    DROP COLUMN IF EXISTS license_number,
    DROP COLUMN IF EXISTS apartment_code,
    DROP COLUMN IF EXISTS city,
    DROP COLUMN IF EXISTS street_name,
    DROP COLUMN IF EXISTS postal_code;