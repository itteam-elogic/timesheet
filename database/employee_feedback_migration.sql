-- Migration script to add new columns to existing employee_feedback table
-- Run this if the table already exists

ALTER TABLE `employee_feedback` 
ADD COLUMN IF NOT EXISTS `reporting_manager` int(11) DEFAULT NULL COMMENT 'Reporting Manager ID' AFTER `response_date`,
ADD COLUMN IF NOT EXISTS `project_coordinator` int(11) DEFAULT NULL COMMENT 'Project Coordinator Employee ID' AFTER `reporting_manager`,
ADD COLUMN IF NOT EXISTS `team_members` int(11) DEFAULT NULL COMMENT 'Team Member Employee ID' AFTER `project_coordinator`,
ADD COLUMN IF NOT EXISTS `feedback_month` varchar(20) DEFAULT NULL COMMENT 'Feedback Month (YYYY-MM format)' AFTER `team_members`,
ADD COLUMN IF NOT EXISTS `attached_file` varchar(255) DEFAULT NULL COMMENT 'Attached file path' AFTER `feedback_month`;

