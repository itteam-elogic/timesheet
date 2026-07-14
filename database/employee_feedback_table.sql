-- Employee Feedback Table
-- This table stores employee feedback submissions

CREATE TABLE IF NOT EXISTS `employee_feedback` (
  `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
  `empId` int(11) NOT NULL COMMENT 'Employee ID who submitted the feedback',
  `employee_name` varchar(255) NOT NULL COMMENT 'Employee name',
  `department` varchar(100) DEFAULT NULL COMMENT 'Employee department',
  `feedback_type` enum('Productivity & Efficiency','Quality Improvement','Technical Knowledge & Skill Development','Ownership & Accountability','Innovation','Communication & Coordination') DEFAULT 'Productivity & Efficiency' COMMENT 'Type of feedback',
  `subject` varchar(255) NOT NULL COMMENT 'Feedback subject/title',
  `feedback_message` text NOT NULL COMMENT 'Detailed feedback message',
  `status` enum('Sent','Acknowledge') DEFAULT 'Sent' COMMENT 'Feedback status',
  `assigned_to` int(11) DEFAULT NULL COMMENT 'Manager/Admin assigned to handle this feedback',
  `response` text DEFAULT NULL COMMENT 'Response from management',
  `response_date` datetime DEFAULT NULL COMMENT 'Date when response was given',
  `reporting_manager` int(11) DEFAULT NULL COMMENT 'Reporting Manager ID',
  `project_coordinator` int(11) DEFAULT NULL COMMENT 'Project Coordinator Employee ID',
  `team_members` int(11) DEFAULT NULL COMMENT 'Team Member Employee ID',
  `feedback_month` varchar(20) DEFAULT NULL COMMENT 'Feedback Month (YYYY-MM format)',
  `attached_file` varchar(255) DEFAULT NULL COMMENT 'Attached file path',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`feedback_id`),
  KEY `idx_empId` (`empId`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_department` (`department`),
  KEY `idx_reporting_manager` (`reporting_manager`),
  KEY `idx_project_coordinator` (`project_coordinator`),
  KEY `idx_feedback_month` (`feedback_month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Employee feedback submissions and management';

