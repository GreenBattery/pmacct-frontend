<?php
require __DIR__ . '/vendor/autoload.php';

$S = new Smarty();
$S->setTemplateDir($_SERVER['DOCUMENT_ROOT'] . '/views/');

$S->setCompileDir($_SERVER['DOCUMENT_ROOT'] . '/cache/');
$S->setConfigDir($_SERVER['DOCUMENT_ROOT'] . '/configs/');
$S->setCacheDir($_SERVER['DOCUMENT_ROOT'] .'/cache/');

$S->display('main.tpl');

/*require __DIR__ . '/includes/core.php';


$tz = date_default_timezone_get(); //get timezone.

//unix time for midnight today

$sd = strtotime("today");
//var_dump($sd);
$data = Data_Summary::day($sd);



View::factory('day')
    ->set('date', $sd)
    ->set('data', $data)
    ->render();

*/