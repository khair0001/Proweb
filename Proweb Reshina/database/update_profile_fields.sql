-- Add additional fields to users table for profile information
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS phone VARCHAR(20) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS address TEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS city VARCHAR(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS bio TEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS profile_pic VARCHAR(255) DEFAULT '../assets/image/user.png';

-- Rename profile_image to profile_pic if it exists (to maintain consistency)
-- This will only execute if profile_image exists and profile_pic doesn't
-- You may need to run this manually if your MySQL version doesn't support IF EXISTS in ALTER TABLE
-- ALTER TABLE users CHANGE COLUMN profile_image profile_pic VARCHAR(255) DEFAULT '../assets/image/adit.jpg';
