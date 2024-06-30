
--
-- Table structure for table inbound/Download statistics
--

create table inbound_%m%Y (
        agent_id INT(4) UNSIGNED,
        mac_src CHAR(17) NOT NULL,
        mac_dst CHAR(17) NOT NULL,
        vlan INT(2) UNSIGNED NOT NULL,
        ip_src CHAR(50) NOT NULL,
        ip_dst CHAR(50) NOT NULL,
        src_port INT(2) UNSIGNED NOT NULL,
        dst_port INT(2) UNSIGNED NOT NULL,
        ip_proto CHAR(20) NOT NULL,
        post_nat_ip_dst VARCHAR(50) DEFAULT NULL,
        post_nat_port_src INT(2) DEFAULT NULL,
        post_nat_ip_src VARCHAR(50) DEFAULT NULL,
        post_nat_port_dst INT(2) DEFAULT NULL,
        packets INT UNSIGNED NOT NULL,
        bytes BIGINT UNSIGNED NOT NULL,
        stamp_inserted DATETIME NOT NULL,
        stamp_updated DATETIME,
        PRIMARY KEY (agent_id, mac_src, mac_dst, vlan, ip_src, ip_dst, src_port, dst_port, ip_proto, stamp_inserted, post_nat_ip_dst, post_nat_port_dst)
);
-- --------------------------------------------------------

--
-- Table structure for outbound/upload statistics
--

create table outbound_%m%Y (
        agent_id INT(4) UNSIGNED,
        mac_src CHAR(17) NOT NULL,
        mac_dst CHAR(17) NOT NULL,
        vlan INT(2) UNSIGNED NOT NULL,
        ip_src CHAR(50) NOT NULL,
        ip_dst CHAR(50) NOT NULL,
        src_port INT(50) UNSIGNED NOT NULL,
        dst_port INT(2) UNSIGNED NOT NULL,
        ip_proto CHAR(20) NOT NULL,
        packets INT UNSIGNED NOT NULL,
        post_nat_ip_dst VARCHAR(50) DEFAULT NULL,
        post_nat_port_src INT(2) DEFAULT NULL,
        post_nat_ip_src VARCHAR(50) DEFAULT NULL,
        post_nat_port_dst INT(2) DEFAULT NULL,
        bytes BIGINT UNSIGNED NOT NULL,
        stamp_inserted DATETIME NOT NULL,
        stamp_updated DATETIME,
        PRIMARY KEY (agent_id, mac_src, mac_dst, vlan, ip_src, ip_dst, src_port, dst_port, ip_proto, stamp_inserted)
);

-----------------------------------------

-- Table structure for main statistics cache/summary.
CREATE TABLE `main_summary` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(50) NOT NULL DEFAULT '0',
  `duration_type` varchar(10) NOT NULL DEFAULT '0',
  `duration` varchar(12) NOT NULL DEFAULT '0',
  `bytes_in` bigint(20) unsigned DEFAULT '0',
  `bytes_out` bigint(20) unsigned DEFAULT '0',
  `stamp_inserted` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_duration_type_duration` (`ip`,`duration_type`,`duration`),
  INDEX (duration),
  INDEX (ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='duration type specifies whether this is a state for day, month or year. \r\n\r\nthe duration field specifies which unit of the duration the stat covers. Example, for a duration_type = day, then the duration field will contain "2018-10-15" meaning the stats are for the 15th of October 2018. For duration_type = month, you should expect "102018" meaning october 2018.'

