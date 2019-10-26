<?php
require __DIR__ . '/vendor/autoload.php';

$S = new Smarty();
$S->setTemplateDir($_SERVER['DOCUMENT_ROOT'] . '/views/');

$S->setCompileDir($_SERVER['DOCUMENT_ROOT'] . '/cache/');
$S->setConfigDir($_SERVER['DOCUMENT_ROOT'] . '/configs/');
$S->setCacheDir($_SERVER['DOCUMENT_ROOT'] .'/cache/');

$endpoints = [
    'default'=> 'index',
    'day' => 'getDay',
    'month' => 'getMonth'
];

$action = urldecode($_REQUEST['action']?? "default");

if (strlen(trim($action)) === 0) {
    $action = "default";
}

//dispatch handler.
call_user_func($endpoints[$action]);

function index() {
    global $S;
    $data = shell_exec('ip -j addr show');
    $devices = json_decode($data, true);

    $d = []; //hold the ones of interest here.

    foreach($devices as $device) {
        //we want ethernet and ones that actually do something.
        if ($device['link_type'] === "ether" && $device['qdisc'] !== "noop") {
            $d[] = $device;
        }
    }

        $S->assign("devices", $d);
    $S->display('index.tpl');

}


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