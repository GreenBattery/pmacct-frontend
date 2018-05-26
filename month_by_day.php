<?php
/**
 * Viewing a summary of statistics by month, grouped by day and host
 * @author Daniel15 <daniel at dan.cx>
 */

require __DIR__ . '/includes/core.php';

// Querystring parameters
$year = (int) $_GET['year'] ?? date('Y');
$month = (int) $_GET['month'] ?? date('m');

$start_date = gmmktime(0, 0, 0, $month, 1, $year);

$data = Data_Summary::month_by_day($start_date);

header('Content-Type: text/json');
echo json_encode((object)array(
	// Get day values
	'days' => array_keys(reset($data)),
	'data' => $data,
));
?>