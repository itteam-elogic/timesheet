-- Migration Script: Update Feedback Status
-- This script updates the status ENUM values in the employee_feedback table
-- Run this script to update your existing database

-- Step 1: Update existing records to map old values to new ones
-- Map old status values to new status values
UPDATE `employee_feedback` 
SET `status` = 'Sent' 
WHERE `status` IN ('New', 'In Progress');

UPDATE `employee_feedback` 
SET `status` = 'Acknowledge' 
WHERE `status` IN ('Resolved', 'Closed');

-- Step 2: Modify the ENUM column to the new values
ALTER TABLE `employee_feedback` 
MODIFY COLUMN `status` 
ENUM('Sent', 'Acknowledge') 
DEFAULT 'Sent' 
COMMENT 'Feedback status';

