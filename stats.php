<?php
require __DIR__ . '/vendor/autoload.php';
use \RedBeanPHP\R as R;

R::setup( 'mysql:host=localhost;dbname=bandwidth',
    'router', 'router' ); //for both mysql or mariaDB
$s = new Smarty();
$s->setTemplateDir($_SERVER['DOCUMENT_ROOT'] . '/views/');

$s->setCompileDir($_SERVER['DOCUMENT_ROOT'] . '/cache/');
$s->setConfigDir($_SERVER['DOCUMENT_ROOT'] . '/configs/');
$s->setCacheDir($_SERVER['DOCUMENT_ROOT'] .'/cache/');

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
    global $s, $table_in, $table_out;

    $date = !empty($_GET['date']) ? (int) $_GET['date'] : strtotime("today");
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
    $hostnames = nucc1\Hostnames::read_leases();

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
        $totals['bytes_in'] += $stat['bytes_in'];
    }

    $totals['total'] = $totals['bytes_in'] + $totals['bytes_out'];



    $data = ['stats' => $b_t, 'totals' => $totals];
    $s->assign('data',$data);
    $s->display('stats.day.tpl');
}

/*
 * get month stats
 */
function getMonth() {
    echo "<p>Month stats!</p>";

}