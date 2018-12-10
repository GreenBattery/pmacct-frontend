<?php
/**
 * Viewing a summary of statistics by month
 * @author Daniel15 <daniel at dan.cx>
 */

require __DIR__ . '/includes/core.php';

// Querystring parameters
$year = !empty($_GET['year']) ? (int) $_GET['year'] : date('Y');
$month = !empty($_GET['month']) ? (int) $_GET['month'] : date('m');

$start_date = mktime(0, 0, 0, $month, 1, $year);

$curr_date = mktime();

$data = array(
    'totals' => array (),
    'data' => array()
);

if ($start_date <= $curr_date) {
    $data = Data_Summary::month($start_date);
}



//var_dump("dates|: " . $start_date . "__++++___:" );

View::factory('month')
	->set('date', $start_date)
	->set('data', $data)
	->render();
?>