<?php
date_default_timezone_set("utc");
require __DIR__ . '/vendor/autoload.php';

use nucc1\Hostnames;
use \RedBeanPHP\R as R;

$hostnames = nucc1\Hostnames::read_leases();

R::setup( 'mysql:host=localhost;dbname=bandwidth',
    'router', 'router' ); //for both mysql or mariaDB
$s = new Smarty();
$s->setTemplateDir($_SERVER['DOCUMENT_ROOT'] . '/views/');

$s->setCompileDir($_SERVER['DOCUMENT_ROOT'] . '/cache/');
$s->setConfigDir($_SERVER['DOCUMENT_ROOT'] . '/configs/');
$s->setCacheDir($_SERVER['DOCUMENT_ROOT'] .'/cache/');

$endpoints = [
    'default'=> 'index'
];

$action = urldecode($_REQUEST['action']?? "default");

if (strlen(trim($action)) === 0) {
    $action = "default";
}


call_user_func($endpoints[$action]);

function index() {
    global $s;
    $output = [];
    $r = exec("/var/www/html/bin/fw",$output);

    $rules = json_decode($output[0], true);

    $s->assign("rules", $rules);
    $s->display('firewall.tpl');
}