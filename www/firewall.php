<?php
date_default_timezone_set("utc");
require '../vendor/autoload.php';

use nucc1\Hostnames;
use \RedBeanPHP\R as R;

$hostnames = nucc1\Hostnames::read_leases();

R::setup( 'mysql:host=localhost;dbname=bandwidth',
    'router', 'router' ); //for both mysql or mariaDB
$s = new Smarty();
$s->setTemplateDir(__DIR__  . '/../includes/views/');

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
    $output = "";
    
    $fp = popen("../bin/fw", "rb");
   
    stream_set_blocking($fp, true);
    
    while (!feof($fp)) {
        $output .= fread($fp, 2048);
        fseek($fp, strlen($output));
    }
    
   
    $rules = json_decode($output, true);
    //var_dump($rules['nftables'][3]);
    

    $s->assign("rules", $rules);
    $s->display('firewall.tpl');
}