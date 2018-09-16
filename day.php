<?php
/**
 * Viewing a summary of statistics by day
 * @author Daniel15 <daniel at dan.cx>
 */

require __DIR__ . '/includes/core.php';

// Querystring parameters
$date = !empty($_GET['date']) ? (int) $_GET['date'] : strtotime("today");

$data = Data_Summary::day($date);

View::factory('day')
	->set('date', $date)
	->set('data', $data)
	->render();
?>