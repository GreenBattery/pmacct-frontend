<?php
// Redirect to this month's report
//header('Location: ' . date('Y/m/'));

require __DIR__ . '/includes/core.php';

$start_date = mktime(0, 0, 0, $month, 1, $year);
var_dump($start_date);

$data = Data_Summary::month($start_date);

var_dump("dates|: " . $start_date . "__++++___:" . $data);

View::factory('month')
    ->set('date', $start_date)
    ->set('data', $data)
    ->render();