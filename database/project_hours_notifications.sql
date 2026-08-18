

CREATE TABLE IF NOT EXISTS `project_hours_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_Id` int(11) NOT NULL,
  `milestone_hours` decimal(10,2) NOT NULL,
  `total_hours` decimal(10,2) NOT NULL,
  `notif_interval` decimal(10,2) NOT NULL,
  `sent_to` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_milestone` (`project_Id`,`milestone_hours`),
  KEY `idx_project_Id` (`project_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
