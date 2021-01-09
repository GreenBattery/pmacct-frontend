<?php
date_default_timezone_set("utc");
require '../vendor/autoload.php';

use nucc1\Hostnames;
use \RedBeanPHP\R as R;

$hostnames = nucc1\Hostnames::read_leases();

R::setup( 'mysql:host=localhost;dbname=bandwidth',
    'router', 'router' ); //for both mysql or mariaDB
$s = new Smarty();
$s->setTemplateDir($_SERVER['DOCUMENT_ROOT'] . '/views/');

$s->setCompileDir($_SERVER['DOCUMENT_ROOT'] . '/../template-cache/');
$s->setConfigDir($_SERVER['DOCUMENT_ROOT'] . '/../template-config/');
$s->setCacheDir($_SERVER['DOCUMENT_ROOT'] .'/../template-cache/');

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
    $r = exec("../bin/fw",$output);

    $rules = json_decode($output[0], true);

    $adds = $rules['nftables'][0]['add'];

    $tables = [];
    $chains = [];
    foreach ($adds as $add) {
        if (isset($add['table'])) {
            $tkey = $add['table']['name'] . $add['table']['family'];
            $tables[$tkey] = $add['table'];
        }elseif (isset($add['chain'])) {
            $chainName = $add['chain']['name'];
            $chainTable = $add['chain']['table'];
            $chainFamily = $add['chain']['family'];
            $chains[$add['chain']][$add['name']] = $add['chain'];
        }
    }

    $s->assign("rules", $rules);
    $s->display('firewall.tpl');
}