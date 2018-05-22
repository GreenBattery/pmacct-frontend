<?php
// Configuration

class Config
{
	static $database = array(
		'host' => 'localhost',
		'dbname' => 'bandwidth',
		'username' => 'router',
		'password' => 'router',
		'prefix' => 'inbound_',
        'engine' => 'mysql'
	);
	
	// IPs to include in the statistics
	// Set this to a blank array to show all IPs
	static $include_ips = array(
		// Only show 10.0.0.1 and 10.0.0.2
		// '10.0.0.1', '10.0.0.2'
	);

	static $localSubnet = "192.168.1.0/24"; //default subnet to treat as LAN.
}

?>