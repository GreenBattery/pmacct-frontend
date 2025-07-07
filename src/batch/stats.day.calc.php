#!/usr/bin/php
<?php
/**
 * Date: 02/07/2019
 * Time: 19:26
 */

date_default_timezone_set("utc");
require __DIR__ . '/../vendor/autoload.php';

use nucc1\Hostnames;
use \RedBeanPHP\R as R;

/*
 * we wish to calculate the monthly statistics and update the main_summary table periodically so that the
 * UI display of the stats is faster.
*/

/*
 * configure this script to run on a schedule of your choice with cron. I like:
 *         3,17,28,44,59 * * * *  --- 5 times per hour
 */

/*
 * for the long options:
 * all means calculate everything you find in the database, and aggregate by IP per month.
 *
 * current means calculate for the current month only, as determined by the time on execution machine
 *
 * month allows you to specify a required month to calculate.
 */
$oa = [
    'all',
    'current',
    'day:',
    'help'
];
$options = getopt("", $oa);

$dbUser = getenv("DB_USER");
$dbPassword = getenv("DB_PASSWORD");
$dbHost = getenv("DB_HOST");
$dbName = getenv("DB_NAME");

try{
    $db = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
} catch (PDOException $e) {
    echo $e->getMessage();
    print_r($e->getTraceAsString());
}

R::setup( "mysql:host=$dbHost;dbname=$dbName",
    $dbUser, $dbPassword ); //for both mysql or mariaDB

echo "Starting to run?";

if (empty($options) || array_key_exists('help', $options)) {
    echo "
    My job is to precompute the bandwidth stats and aggregate them by month.
    
    Usage: Execute me as `php /path/to/me.php` with any of the following options:
        --all to calculate everything 
        --current for day
        --day=2018-05-22 to calculate a specific month, in this case, 22nd May 2018.\n";
}

print_r($options);
$inputDate = $options['day'] ?? date('Y-m');
if (isset($options['current'])) {
    
}

if (isset($options['all'])) {
    $requestedDay = null; //ignore month and current if all is requested.
    echo "This is not yet supported";
    exit(1);

} elseif (isset($options['current'])) {
    $requestedDay = new DateTimeImmutable(); //overwrite if current month requested.
} else {
    $requestedDay = DateTimeImmutable::createFromFormat("Y-m-d", $inputDate);
}
//calculate for requested date
    
if ($requestedDay === false) {
    echo "Input date could not be parsed. Expect Y-m-d";
    exit(1);
}
    
$tableSuffix = $requestedDay->format('mY');
$requestedDayString = $requestedDay->format('Y-m-d');

$tableIn = "inbound_" . $tableSuffix;
$tableOut = "outbound_" . $tableSuffix;

$sql ="
    SELECT 
        ip_src AS ip,
        mac_src as mac,
        SUM(bytes) AS bytes
    FROM $tableOut
    WHERE
        stamp_inserted BETWEEN '$requestedDayString 00:00:00' AND '$requestedDayString 23:59:59'
    GROUP BY
        ip, mac
";



$b_out = R::getAll($sql, []);
echo "Calculating $requestedDayString: " . count($b_out) . " entries in outbound table.\n";

//the where clause is hard-coded, can be made a user-configurable value.
$sql ="
    SELECT 
        IF(post_nat_ip_dst = 0,ip_dst, post_nat_ip_dst) AS ip,
        SUM(bytes) AS bytes
    FROM $tableIn
    WHERE
        (post_nat_ip_dst = 0 
        OR post_nat_ip_dst LIKE '192.168.1.%')
        AND stamp_inserted BETWEEN :start AND :end
        
    GROUP BY
        ip
";

$b_in = R::getAll($sql, [':start' => $requestedDayString . ' 00:00:00', ':end' => $requestedDayString. ' 23:59:59']); //fetch inbound aggregates for month.
echo "Calculating $requestedDayString: " . count($b_in) . " entries in inbound table.\n";


$data = [];
foreach($b_out as $b) {
    $ip = $b['ip'];
    $temp = $data[$ip] ?? ['bytes_in' => 0, 'bytes_out' => 0, 'mac' => '']; //extract or initialize
    $temp['bytes_out'] = $b['bytes'];
    $temp['mac'] = $b['mac'];
    $data[$ip] = $temp;
}

foreach($b_in as $b) {
    $ip = $b['ip'];
    $temp = $data[$ip] ?? ['bytes_in' => 0, 'bytes_out' => 0, 'mac' => '']; //extract or initialize
    $temp['bytes_in'] = $b['bytes'];
    $data[$ip] = $temp;
}

echo "Pushing updated stats to DB\n";

//first delete all stats for this month before we insert the newly calculated stats
$query = "DELETE FROM 
            main_summary 
        WHERE
            duration_type = 'day' AND 
            duration = :duration
        ";
$res = R::exec($query, ['duration' => $requestedDayString]);

foreach($data as $ip => $datum) {
    $sq2 = "
                INSERT into main_summary
                (id, ip, mac, duration_type, duration, bytes_in, bytes_out, stamp_inserted)
                values (
                    null,
                    :ip_addr,
                    :mac,
                    'day',
                    :duration,
                    :bytes_in,
                    :bytes_out,
                    NOW()
                )
            ";

    $res2 = R::exec($sq2, [
        ':ip_addr'=> $ip,
        ':duration' => $requestedDayString,
        ':bytes_in' => $datum['bytes_in'],
        ':bytes_out'=> $datum['bytes_out'],
        ':mac' => $datum['mac']
    ]);
    //check result of query.
    if (!$res2) {
        //write a message to syslog.
        syslog(LOG_NOTICE,"failed to insert SUMMARY 'day' data for $ip, dur: $requestedDayString ");
    }
}
echo "all done. \n";
