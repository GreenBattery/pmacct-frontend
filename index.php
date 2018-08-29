<?php
// Redirect to this month's report
//header('Location: ' . date('Y/m/'));

require __DIR__ . '/includes/core.php';


$tz = date_default_timezone_get(); //get timezone.

//unix time for midnight today

$sd = strtotime("today");
//var_dump($sd);
$data = Data_Summary::day($sd);



View::factory('day')
    ->set('date', $sd)
    ->set('data', $data)
    ->render();