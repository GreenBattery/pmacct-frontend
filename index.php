<?php
// Redirect to this month's report
//header('Location: ' . date('Y/m/'));

require __DIR__ . '/includes/core.php';

//unix time for midnight on first day of the month (in UTC)
$start_date = mktime(0,0,0, date('n'), 1, date('Y'));

var_dump($start_date);

$data = Data_Summary::month($start_date);

var_dump("dates|: " . $start_date . "__++++___:" . $data);

View::factory('summary')
    ->set('date', $start_date)
    ->set('data', $data)
    ->render();