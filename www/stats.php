<?php
date_default_timezone_set("utc");
require '../vendor/autoload.php';

use nucc1\Hostnames;
use \RedBeanPHP\R as R;

$hostnames = nucc1\Hostnames::read_leases();

R::setup( 'mysql:host=localhost;dbname=bandwidth',
    'router', 'router' ); //for both mysql or mariaDB
$s = new Smarty();
$s->setTemplateDir($_SERVER['DOCUMENT_ROOT'] . '/views/');

$s->setCompileDir($_SERVER['DOCUMENT_ROOT'] . '/../template-cache/');
$s->setConfigDir($_SERVER['DOCUMENT_ROOT'] . '/../template-config/');
$s->setCacheDir($_SERVER['DOCUMENT_ROOT'] .'/../template-cache/');

$endpoints = [
    'default'=> 'index',
    'day' => 'getDay',
    'month' => 'getMonth'
];

$action = urldecode($_REQUEST['action']?? "default");

if (strlen(trim($action)) === 0) {
    $action = "default";
}


call_user_func($endpoints[$action]);

function index() {
    global $s;
    $s->display('stats.tpl');
}

/*
 * stats for specified day
 */
function getDay() {
    global $s, $table_in, $table_out, $hostnames;
    $s->caching = 0;

    $date = !empty($_GET['date']) ? (int) $_GET['date'] : strtotime("today");

    $cd = date_create_immutable("@$date");
    $d1 = new DateInterval("P1D");
    $yest = $cd->sub($d1)->getTimestamp(); //yesterday.
    $tomm = $cd->add($d1)->getTimestamp(); //tomorrow

    $end_date = mktime(23, 59, 59, date('m', $date),
        date('d', $date), date('Y', $date));

    //make the table name in _mmYY format. for inbound table
    $table_in = "inbound_" . date("mY", $date);
    $table_out = "outbound_" . date("mY", $date);

    //fetch inbound byte stats
    $sql = "
			SELECT ip_dst AS ip, SUM(bytes) AS bytes_in
			FROM   $table_in
			WHERE stamp_inserted BETWEEN FROM_UNIXTIME(:start_date) AND FROM_UNIXTIME(:end_date)
			GROUP BY ip
			ORDER BY bytes_in DESC";
    $b_in = R::getAll($sql, [
            ':start_date' => $date,
            ':end_date' => $end_date
        ]
    );

//fetch outbound byte stats
//sql for fetching outbound/upload stats.
    $sql = "
			SELECT ip_src AS ip, SUM(bytes) AS bytes_out
			FROM   $table_out
			WHERE stamp_inserted BETWEEN FROM_UNIXTIME($date) AND FROM_UNIXTIME($end_date)
			GROUP BY ip";

    $b_out = R::getAll($sql);

    $b_t = [];
    $totals['bytes_out'] = 0;
    $totals['bytes_in'] = 0;

    foreach($b_out as $stat) {
        $ip = $stat['ip'];
        $b_t[$ip]['bytes_out'] = $stat['bytes_out'];
        $b_t[$ip]['bytes_in'] = 0; //ensure this key exists.
        $b_t[$ip]['total'] = $stat['bytes_out']; //this is first time, so set this value.
        $b_t[$ip]['hostname'] = $hostnames[$stat['ip']] ?? $ip; //set the hostname if available.

        $totals['bytes_out'] += $stat['bytes_out'];
    }
    foreach ($b_in as $stat) {
        $ip = $stat['ip'];
        $b_t[$ip]['bytes_in'] = $stat['bytes_in'];
        if (isset($b_t[$ip]['total'])) { //if there is a value, add to it.
            $b_t[$ip]['total'] += $stat['bytes_in'];
        }else {
            $b_t[$ip]['total'] = $stat['bytes_in'];
        }

        if (!isset($b_t[$ip]['bytes_out'])) {
            $b_t[$ip]['bytes_out'] = 0; //ensure that this key exists.
        }
        $b_t[$ip]['hostname'] = $hostnames[$stat['ip']] ?? $ip; //set the hostname if available.
        $totals['bytes_in'] += $stat['bytes_in'];
    }

    $totals['total'] = $totals['bytes_in'] + $totals['bytes_out'];
    $totals['bytes_out_formatted'] = formatBytes($totals['bytes_out']);
    $totals['bytes_in_formatted'] = formatBytes($totals['bytes_in']);
    $totals['aggregate_formatted'] = formatBytes(($totals['bytes_in'] + $totals['bytes_out']));



    $do = new DateTime("@$date");
    $data = ['stats' => $b_t, 'totals' => $totals];
    $s->assign('data',$data);
    $s->assign('date', $do->format('Y-m-d'));
    $s->assign('links', ['prev' => $yest, 'next'=> $tomm]);
    $s->display('stats.day.tpl');
}

/*
 * TODO: month stats stamp_inserted should always be midnight of the day calculated, and should
 * only be updated once per day, so as to not miss short-lived IP addresses in the monthly summary.
 *
 * get month stats
 */
function getMonth() {
    global $s, $hostnames;
    // Querystring parameters
    $year = !empty($_GET['year']) ? (int) $_GET['year'] : date('Y');
    $month = !empty($_GET['month']) ? (int) $_GET['month'] : date('m');

    $start_date = mktime(0, 0, 0, $month, 1, $year);

    //we want the date information needed for creating prev and next links:
    //current date to display
    $cd = date_create_immutable("@$start_date");

//previous month date object.
    $prevD = $cd->sub(new DateInterval("P1M"));

    $lm = $prevD->format("m"); //prev month.
    $py = $prevD->format("Y"); //year for prev month

//next month date object.
    $nextD = $cd->add(new DateInterval("P1M"));
    $nm = $nextD->format("m"); //next month
    $ny = $nextD->format("Y"); //year for next month

    $last_day = date('t', $start_date); //get the last day of this month from timestamp.

    $ctime = mktime(); //current timesamp for use in main_summary.
    $cmonth = date("mY", $start_date); //get the MMYYYY to use as the duration key in main_summary table.

    //get the epoch in localtime?
    $end_date = mktime(23, 59, 59, date('n', $start_date), $last_day);
    $sql = "
          SELECT 
            * 
          FROM 
            main_summary 
          WHERE 
            duration_type = 'month' and 
            duration = :duration
          ";
    $prev_stats = R::getAll($sql, [':duration' => $cmonth]);

    //initialise the totals array.
    $totals = array(
        'bytes_in'=>0,
        'bytes_out'=>0
    );

    $data = []; // prepare results array.
    //prefill the data and totals arrays with previously cached stats.
    foreach ($prev_stats as $pstat) {
        $ip = $pstat['ip'];
        $data[$ip]['bytes_in'] = $pstat['bytes_in'];
        $data[$ip]['bytes_out'] = $pstat['bytes_out'];
        $data[$ip]['total'] = $pstat['bytes_in'] + $pstat['bytes_out'];
        $data[$ip]['hostname'] = $hostnames[$ip] ?? $ip; //set the hostname if available.

        $totals['bytes_in'] += $pstat['bytes_in'];
        $totals['bytes_out'] += $pstat['bytes_out'];
    }

    $month_data = [];//we'll compute the data for main_summary table here too.

    $totals['total'] = $totals['bytes_in'] + $totals['bytes_out'];
    $totals['bytes_out_formatted'] = formatBytes($totals['bytes_out']);
    $totals['bytes_in_formatted'] = formatBytes($totals['bytes_in']);
    $totals['aggregate_formatted'] = formatBytes(($totals['bytes_in'] + $totals['bytes_out']));


    $s->assign('data', ['stats' =>$data, 'totals' => $totals]);
    $s->assign('date', $cd->format("F Y"));
    $s->assign('links', ['lm'=>$lm, 'py' => $py, 'nm' => $nm, 'ny'=> $ny]);
    $s->display('stats.month.tpl');

}

/**
 * @param $bytes
 * @param int $precision
 * @return string
 */
function formatBytes($bytes) {
    $units = array('B', 'KiB', 'MiB', 'GiB', 'TiB');

    $bytes = max($bytes, 0);
    $power = floor(($bytes ? log($bytes) : 0) / log(1024));
    $power = min($power, count($units) - 1);

    $bytes /= pow(1024, $power);

    return round($bytes, 4) . ' ' . $units[$power];
}