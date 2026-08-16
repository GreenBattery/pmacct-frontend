-- Database initialization for pmacct frontend

CREATE TABLE IF NOT EXISTS `main_summary` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(50) NOT NULL DEFAULT '0',
  `mac` CHAR(17) DEFAULT NULL,
  `duration_type` varchar(10) NOT NULL DEFAULT '0',
  `duration` varchar(12) NOT NULL DEFAULT '0',
  `bytes_in` bigint(20) unsigned DEFAULT '0',
  `bytes_out` bigint(20) unsigned DEFAULT '0',
  `stamp_inserted` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_duration_type_duration` (`ip`,`duration_type`,`duration`),
  INDEX (duration),
  INDEX (ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Main summary table for aggregated bandwidth statistics';
