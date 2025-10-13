-- Migration: Increase OTP code column size for hashed storage
-- Date: October 13, 2025
-- Purpose: Change otp_code column from char(6) to varchar(255) to store hashed OTPs securely

-- Before: otp_code char(6) - could only store plain 6-digit OTPs
-- After: otp_code varchar(255) - can store bcrypt hashed OTPs (~60 characters)

USE seblak_app;

-- Update the password_resets table
ALTER TABLE password_resets 
MODIFY COLUMN otp_code VARCHAR(255) NOT NULL 
COMMENT 'Stores bcrypt hashed OTP codes for security';

-- Verify the change
DESCRIBE password_resets;

-- Security benefit: OTPs are now hashed using bcrypt before storage
-- This prevents rainbow table attacks if the database is compromised

SELECT 'Migration completed: OTP code column updated to support hashed storage' as status;