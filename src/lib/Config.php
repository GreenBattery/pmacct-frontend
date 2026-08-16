<?php
declare(strict_types=1);
namespace nucc1;

use Monolog\Level;
use Monolog\Logger;

class Config
{
    private static $logger;
    // static $database = array(
    //     'host' => getenv('DB_HOST'),
    //     'dbname' => getenv('DB_NAME'),
    //     'username' => getenv('DB_USER'),
    //     'password' => getenv('DB_PASSWORD'),
    //     'prefix' => 'inbound_',
    //     'engine' => 'mysql'
    // );
    public static function getDBConfig(): array
    {
        $log = self::getLogger();

        $host = getenv('DB_HOST') ?: 'db';
        $dbname = getenv('DB_NAME') ?: 'router';
        $username = getenv('DB_USER') ?: 'router';
        $password = getenv('DB_PASSWORD') ?: 'secret';

        $log->debug("Getting DB config");
        $log->debug('db host: ' . $host);
        $log->debug('db name: ' . $dbname);
        $log->debug('db user: ' . $username);
        return [
            'host' => $host,
            'dbname' => $dbname,
            'username' => $username,
            'password' => $password,
            'prefix' => 'inbound_',
            'engine' => 'mysql'
        ];

    }

    static $tz = "Europe/London";
    // IPs to include in the statistics
    // Set this to a blank array to show all IPs
    static $include_ips = array(
        // Only show 10.0.0.1 and 10.0.0.2
        // '10.0.0.1', '10.0.0.2'
    );

    public static function getLogger(): Logger
    {
        if (self::$logger === null) {
            self::$logger = new Logger('stats');
            self::$logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stderr', Level::Debug));
        }
        return self::$logger;
    }

    static $localSubnet = "192.168.1.0/24"; //d
}
