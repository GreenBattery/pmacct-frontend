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
    'month:',
    'help'
];
$options = getopt("", $oa);

R::setup( 'mysql:host=localhost;dbname=router',
    'router', 'router' ); //for both mysql or mariaDB

if (empty($options) || array_key_exists('help', $options)) {
    echo "
    My job is to precompute the bandwidth stats and aggregate them by month.
    
    Usage: Execute me as `php /path/to/me.php` with any of the following options:
        --all to calculate everything 
        --current for current month
        --month=2018-05 to calculate a specific month, in this case, May 2018.\n";
}

print_r($options);
$month = $options['month'] ?? date('Y-m');
if (isset($options['current'])) {
    $month = date('Y-m'); //overwrite if current month requested.
}

if (isset($options['all'])) {
    $month = null; //ignore month and current if all is requested.
}

if ($month === null) {
    //calculate all data.

} else {
    //calculate requested month
    $d = date_parse_from_format("Y-m", $month);
    $mm = (string) $d['month'];
    if (strlen($mm) === 1) {
        $mm = "0" . $mm;
    }
    $table_in = "inbound_" . $mm. $d['year'];
    $table_out = "outbound_" . $mm . (string) $d['year'];

    $duration = $mm . (string) $d['year'];

    $sql ="
        SELECT 
            ip_src AS ip,
            mac_src as mac,
            SUM(bytes) AS bytes
        FROM $table_out
        GROUP BY
            ip, mac
	";


    $b_out = R::getAll($sql, []);
    echo "Calculating $month: " . count($b_out) . " entries in outbound table.\n";

    //the where clause is hard-coded, can be made a user-configurable value.
    $sql ="
        SELECT 
            IF(post_nat_ip_dst = 0,ip_dst, post_nat_ip_dst) AS ip,
            SUM(bytes) AS bytes
        FROM $table_in
        WHERE
            post_nat_ip_dst = 0 
            OR post_nat_ip_dst LIKE '192.168.1.%'
        GROUP BY
            ip
	";

    $b_in = R::getAll($sql, []); //fetch inbound aggregates for month.
    echo "Calculating $month: " . count($b_in) . " entries in inbound table.\n";

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
                duration_type = 'month' AND 
                duration = :duration
           ";
    $res = R::exec($query, ['duration' => $duration]);

    foreach($data as $ip => $datum) {
        $sq2 = "
                    INSERT into main_summary
                    (id, ip, mac, duration_type, duration, bytes_in, bytes_out, stamp_inserted)
                    values (
                      null,
                      :ip_addr,
                      :mac,
                      'month',
                      :duration,
                      :bytes_in,
                      :bytes_out,
                      NOW()
                    )
                ";

        $res2 = R::exec($sq2, [
            ':ip_addr'=> $ip,
            ':duration' => $duration,
            ':bytes_in' => $datum['bytes_in'],
            ':bytes_out'=> $datum['bytes_out'],
            ':mac' => $datum['mac']
        ]);

        //check result of query.
        if (!$res2) {
            //write a message to syslog.
            syslog(LOG_NOTICE,"failed to insert SUMMARY 'month' data for $ip, dur: $duration ");
        }
    }

}

