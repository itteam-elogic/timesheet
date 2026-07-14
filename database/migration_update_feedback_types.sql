-- Migration Script: Update Feedback Types
-- This script updates the feedback_type ENUM values in the employee_feedback table
-- Run this script to update your existing database

-- Step 1: Modify the column to allow the new ENUM values
-- Note: This will change existing values, so we need to map old values to new ones first

-- First, update existing records to map old values to new ones
-- You may want to customize this mapping based on your business logic
UPDATE `employee_feedback` 
SET `feedback_type` = 'Productivity & Efficiency' 
WHERE `feedback_type` = 'Performance';

UPDATE `employee_feedback` 
SET `feedback_type` = 'Quality Improvement' 
WHERE `feedback_type` = 'General';

UPDATE `employee_feedback` 
SET `feedback_type` = 'Technical Knowledge & Skill Development' 
WHERE `feedback_type` = 'Training';

UPDATE `employee_feedback` 
SET `feedback_type` = 'Ownership & Accountability' 
WHERE `feedback_type` = 'Management';

UPDATE `employee_feedback` 
SET `feedback_type` = 'Communication & Coordination' 
WHERE `feedback_type` = 'Work Environment';

-- For 'Other' type, you may want to map it to a specific new type or leave it
-- This example maps 'Other' to 'Innovation', but you can change this
UPDATE `employee_feedback` 
SET `feedback_type` = 'Innovation' 
WHERE `feedback_type` = 'Other';

-- Step 2: Modify the ENUM column to the new values
ALTER TABLE `employee_feedback` 
MODIFY COLUMN `feedback_type` 
ENUM(
    'Productivity & Efficiency',
    'Quality Improvement',
    'Technical Knowledge & Skill Development',
    'Ownership & Accountability',
    'Innovation',
    'Communication & Coordination'
) 
DEFAULT 'Productivity & Efficiency' 
COMMENT 'Type of feedback';

