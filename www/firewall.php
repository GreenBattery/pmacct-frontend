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

    $fwData = json_decode($output[0], true);

    $adds = $fwData['nftables'];

    $tables = [];
    $chains = [];
    foreach ($adds as $add) {
        if (isset($add['table'])) {
            $tkey = $add['table']['name'] . "-" . $add['table']['family'];
            $tables[$tkey] = $add['table'];
        }elseif (isset($add['chain'])) {
            $chainName = $add['chain']['name'];
            $chainTable = $add['chain']['table'];
            $chainFamily = $add['chain']['family'];
            $chains[$add['chain']['name']] = $add['chain'];
        } elseif (isset($add['rule'])) { //add it under the right table.
            $tkey = $add['rule']['table'] . "-" . $add['rule']['family'];
            $ruleData = $add['rule'];
            //simplify the expression value.
            $expression = $add['rule']['expr'];

            var_dump($expression);
            $commands = [];
            $actions = [];
            foreach ($expression as $expr) {
                $key = array_key_first($expr);
                switch ($key) {
                    case "match":
                        $commands[] = $expr;
                        break;
                    case "accept":
                        $actions[] = ['name' => "accept", 'data' => []];
                        break;
                    case "counter":
                        $actions[] = ['name' => "counter", 'data' => $expr['counter']];
                        break;
                }
            }
            $tables[$tkey]['rules']["rule-" . $add['rule']['handle']] = [
                'family' => $add['rule']['family'],
                'table' => $add['rule']['table'],
                'chain' => $add['rule']['chain'],
                'handle' => $add['rule']['handle'],
                'commands' => $commands,
                'actions' => $actions
            ];


        }

    }

    var_dump($tables['filter-ip']['rules']);

    $s->assign("rules", $fwData);
    $s->display('firewall.tpl');
}