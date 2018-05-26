<?php
/**
 * Viewing traffic to a specific host for a specific day
 * @author Daniel15 <daniel at dan.cx>
 */

require __DIR__ . '/includes/core.php';

// get date and IP from query string if available. 
$date = strtotime($_GET['date']) ?? strtotime("today");
//if no IP supplied, should show all hosts
$ip = $_GET['ip'];

var_dump($date);

$date = gmmktime(0, 0, 0, $month, $day, $year);

var_dump("gmmktim: " . $date);

$data = Data_Host::day($ip, $date);

View::factory('host/day')
	->set('ip', $ip)
	->set('date', $date)
	->set('data', $data)
	->render();
?>