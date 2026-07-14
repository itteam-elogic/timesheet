-- Update script for existing employee_feedback table
-- Run this to update an existing table to match the current form structure

-- Remove rating and priority columns if they exist (optional - can be kept for historical data)
-- ALTER TABLE `employee_feedback` DROP COLUMN IF EXISTS `rating`;
-- ALTER TABLE `employee_feedback` DROP COLUMN IF EXISTS `priority`;

-- Change team_members from text to int(11) for single employee ID
ALTER TABLE `employee_feedback` 
MODIFY COLUMN `team_members` int(11) DEFAULT NULL COMMENT 'Team Member Employee ID';

-- Add indexes for better query performance
ALTER TABLE `employee_feedback` 
ADD INDEX IF NOT EXISTS `idx_reporting_manager` (`reporting_manager`),
ADD INDEX IF NOT EXISTS `idx_project_coordinator` (`project_coordinator`),
ADD INDEX IF NOT EXISTS `idx_feedback_month` (`feedback_month`);

