
--
-- Table structure for table inbound/Download statistics
--

create table inbound_%m%Y (
        agent_id INT NOT NULL,
        mac_src CHAR(17) NOT NULL,
        mac_dst CHAR(17) NOT NULL,
        vlan INT UNSIGNED NOT NULL,
        ip_src CHAR(50) NOT NULL,
        ip_dst CHAR(50) NOT NULL,
        src_port INT UNSIGNED NOT NULL,
        dst_port INT UNSIGNED NOT NULL,
        ip_proto CHAR(6) NOT NULL,
        packets INT UNSIGNED NOT NULL,
        bytes BIGINT UNSIGNED NOT NULL,
        stamp_inserted DATETIME NOT NULL,
        stamp_updated DATETIME,
        PRIMARY KEY (agent_id, mac_src, mac_dst, vlan, ip_src, ip_dst, src_port, dst_port, ip_proto, stamp_inserted)
);
-- --------------------------------------------------------

--
-- Table structure for outbound/upload statistics
--

create table outbound_%m%Y (
        agent_id INT UNSIGNED NOT NULL,
        mac_src CHAR(17) NOT NULL,
        mac_dst CHAR(17) NOT NULL,
        vlan INT UNSIGNED NOT NULL,
        ip_src CHAR(50) NOT NULL,
        ip_dst CHAR(50) NOT NULL,
        src_port INT UNSIGNED NOT NULL,
        dst_port INT UNSIGNED NOT NULL,
        ip_proto CHAR(6) NOT NULL,
        packets INT UNSIGNED NOT NULL,
        bytes BIGINT UNSIGNED NOT NULL,
        stamp_inserted DATETIME NOT NULL,
        stamp_updated DATETIME,
        PRIMARY KEY (agent_id, mac_src, mac_dst, vlan, ip_src, ip_dst, src_port, dst_port, ip_proto, stamp_inserted)
);-----------------------------------------

-- Table structure for main statistics cache/summary.
CREATE TABLE `main_summary` (
  `id` INTEGER primary key,
  `ip` char(50) NOT NULL DEFAULT '0',
  `duration_type` char(10) NOT NULL DEFAULT '0',
  `duration` char(12) NOT NULL DEFAULT '0',
  `bytes_in` bigint unsigned DEFAULT '0',
  `bytes_out` bigint unsigned DEFAULT '0',
  `stamp_inserted` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE (`ip`,`duration_type`,`duration`),
  INDEX duration_type,
  INDEX ip
);
