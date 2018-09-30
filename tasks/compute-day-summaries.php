<?php

/**
 * Created by PhpStorm.
 * User: nucc1
 * Date: 30/09/2018
 * Time: 06:55
 */

require_once("../includes/ip.lib.php");
require_once("../includes/core.php");


date_default_timezone_set(Config::$tz);
$block = IPBlock::create(Config::$localSubnet);
$i = 0; //use this counter to avoid expanding more than 256 IP addresses.
$addresses = array(); //hold list of IPs. we set hard limit of 256 addresses.
foreach ($block as $ip){
    $addresses[] = (string) $ip;
    $i++;

    if ($i >=255){
        break;
    }
}

//format the list of IP addresses in our /24 subnet for use in mysql IN clause.
$ip_list = "('" . implode("','",$addresses) . "')";


//get a database connection via PDO object
$db = Database::getDB();

//get all tables with raw data (excluding summary table(s)
$tn = "select table_name from information_schema.tables where table_schema='bandwidth' AND table_name like 'inbound_%' ";

$r = $db->query($tn);
$inbound_tables = $r->fetchAll(PDO::FETCH_ASSOC);

$tn = "select table_name from information_schema.tables where table_schema='bandwidth' AND table_name like 'outbound_%' ";
$r = $db->query($tn);
$outbound_tables = $r->fetchAll(PDO::FETCH_ASSOC);


//var_dump($inbound_tables);
//var_dump($outbound_tables);

//find the most recent day for which we have a summary
$sql = "select id, duration from main_summary where duration_type='day' order by id desc limit 1";

$r = $db->query($sql);

$day_of_year = -1; //-1 means a summary has never been generated previously.

if ($r->rowCount() > 0) {
    $temp = $r->fetchAll(PDO::FETCH_ASSOC);
    $day_of_year = $temp[0]['duration'];
    //var_dump($temp);
}

//find the most recent year for which a summary exists.
$sql = "select id, duration from main_summary WHERE duration_type='year' order by id desc limit 1";
$r = $db->query($sql);

$year = -1;
if ($r->rowCount() > 0) {
    $temp = $r->fetchAll(PDO::FETCH_ASSOC);
    $year = $temp[0]['duration'];
}

if ($day_of_year == -1) { //start from scratch
    //iterate over raw table data.
    foreach($inbound_tables as $t) {
        $first_day = 1; //first day of month.


        $mm = trim(explode("_", $t['table_name'])[1]);

        $dd = DateTime::createFromFormat('!mY', $mm);

        $last_day = $dd->format('t');
        //var_dump("last day: " . $last_day);

        //for each day of this month:
        for ($i = $first_day; $i <= $last_day; $i++) {

            $cdate = $dd->format("d-M-Y"); //current date we're working on.

            print("working on: " . $cdate . "for {$t['table_name']}\n");
            $start_date = $dd->getTimestamp();

            $end_date = $start_date + 86399;



            //retrieve the bytes in statistics for that day of month.

            $sql = "
			SELECT ip_dst AS ip, SUM(bytes) AS bytes_in
			FROM   {$t['table_name']}
			WHERE ip_dst IN $ip_list AND stamp_inserted BETWEEN FROM_UNIXTIME($start_date) and FROM_UNIXTIME($end_date)
			GROUP BY ip
			ORDER BY bytes_in DESC";
            $r = $db->query($sql);
            $summary = $r->fetchAll(PDO::FETCH_ASSOC);

            $sql = "INSERT INTO main_summary values (NULL, :ip, 'day', :duration, :bytes_in, 0)";
            //now insert values for day into main_summary table.
            $stmt = null;
            foreach($summary as $s) {
                $stmt = $db->prepare($sql);
                $data = array(
                    ':ip' => $s['ip'],
                    ':duration' => $cdate,
                    ':bytes_in' =>$s['bytes_in']
                );

                $stmt->execute($data);

            }

            $dd->add(new DateInterval('P1D')); //add one day to date.
        }
    }

    //now handle outbound table
    foreach($outbound_tables as $t) {
        $first_day = 1; //first day of month.


        $mm = trim(explode("_", $t['table_name'])[1]);

        $dd = DateTime::createFromFormat('!mY', $mm);

        $last_day = $dd->format('t');
        //var_dump("last day: " . $last_day);

        //for each day of this month:
        for ($i = $first_day; $i <= $last_day; $i++) {

            $cdate = $dd->format("d-M-Y"); //current date we're working on.

            print("working on: " . $cdate . "for {$t['table_name']}\n");
            $start_date = $dd->getTimestamp();

            $end_date = $start_date + 86399;



            //retrieve the bytes in statistics for that day of month.

            $sql = "
			SELECT ip_src AS ip, SUM(bytes) AS bytes_out
			FROM   {$t['table_name']}
			WHERE ip_src IN $ip_list AND stamp_inserted BETWEEN FROM_UNIXTIME($start_date) and FROM_UNIXTIME($end_date)
			GROUP BY ip
			ORDER BY bytes_out DESC";
            $r = $db->query($sql);
            $summary = $r->fetchAll(PDO::FETCH_ASSOC); //outbound data summary

            $sql_upd = "UPDATE main_summary SET bytes_out = :bytes_out WHERE duration_type = 'day' AND ip = :ip AND duration = :duration";
            $sql_ins = "INSERT INTO main_summary values (NULL, :ip, 'day', :duration, 0, :bytes_out)";
            //now insert values for day into main_summary table.
            $stmt = null;
            $stmt_ins = null;
            foreach($summary as $s) {
                $stmt = $db->prepare($sql_upd);
                $data = array(
                    ':ip' => $s['ip'],
                    ':duration' => $cdate,
                    ':bytes_out' =>$s['bytes_out']
                );

                $result = $stmt->execute($data);

                if (!$result) { //update didn't work, so must insert.
                    print("WARN! no inbound bytes for: " . $s['ip'] . "\n");
                    var_dump($stmt->errorInfo());
                    $stmt->closeCursor();

                    $stmt_ins = $db->prepare($sql_ins);
                    $result = $stmt_ins->execute($data); //execute an insert query.
                }
            }

            $dd->add(new DateInterval('P1D')); //add one day to date.
        }
    }
}else {//start from the day specified in the year specified since we've run previously.

    var_dump($day_of_year);
    var_dump($year);
}

