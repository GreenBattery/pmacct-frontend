<?php
require '../vendor/autoload.php';

$S = new Smarty();
$S->setTemplateDir(__DIR__ . '/../includes/views/');


$S->setCompileDir($_SERVER['DOCUMENT_ROOT'] . '/../template-cache/');
$S->setConfigDir($_SERVER['DOCUMENT_ROOT'] . '/../template-config/');
$S->setCacheDir($_SERVER['DOCUMENT_ROOT'] .'/../template-cache/');

$endpoints = [
    'default'=> 'index',
    'day' => 'getDay',
    'month' => 'getMonth'
];

$action = urldecode($_REQUEST['action'] ?? "default");

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
            if ($device['operstate'] === "UP") { //set a marker to highlight this in bootstrap using backgrounds.
                $device['marker'] = "bg";
            }
            $d[] = $device;
        }
    }

        $S->assign("devices", $d);
    $S->display('index.tpl');

}