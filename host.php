<?php
/**
 * Viewing traffic to a specific host for a specific day
 * @author Daniel15 <daniel at dan.cx>
 */

require __DIR__ . '/includes/core.php';
date_default_timezone_set(Config::$tz);

$date = strtotime("today");
//if date was supplied use it.
if (array_key_exists("date", $_GET)) {
    $date = strtotime($_GET["date"]);
}

//var_dump($date);

$ip = "";
//if no IP supplied, should show all hosts

if(array_key_exists("ip", $_GET)) {
    $ip = $_GET['ip']; //should validate this?
} else {
    header("Location: ./day.php?date=$date");
}

$data = Data_Host::day($ip, $date);

View::factory('host/day')
	->set('ip', $ip)
	->set('date', $date)
	->set('data', $data)
	->render();