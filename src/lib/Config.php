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

        $log->debug("Getting DB config");
        $log->debug('db host: ' . getenv('DB_HOST'));
        $log->debug('db name: ' . getenv('DB_NAME'));
        $log->debug('db user: ' . getenv('DB_USER'));
        return [
            'host' => getenv('DB_HOST'),
            'dbname' => getenv('DB_NAME'),
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
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
