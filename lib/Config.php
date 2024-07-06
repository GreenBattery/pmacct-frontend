<?php
declare(strict_types=1);
namespace nucc1;

class Config
{
    static $database = array(
        'host' => 'localhost',
        'dbname' => 'router',
        'username' => 'router',
        'password' => 'router',
        'prefix' => 'inbound_',
        'engine' => 'mysql'
    );

    static $tz = "Europe/London";
    // IPs to include in the statistics
    // Set this to a blank array to show all IPs
    static $include_ips = array(
        // Only show 10.0.0.1 and 10.0.0.2
        // '10.0.0.1', '10.0.0.2'
    );

    static $localSubnet = "192.168.1.0/24"; //d
}
