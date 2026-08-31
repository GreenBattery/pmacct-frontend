<?php
date_default_timezone_set("utc");
require "./vendor/autoload.php";

use nucc1\Config;
use RedBeanPHP\R as R;

$dbConfig = Config::getDBConfig();
$dbname = $dbConfig["dbname"];
$dbhost = $dbConfig["host"];

R::setup(
    "mysql:host=$dbhost;dbname=$dbname",
    $dbConfig["username"],
    $dbConfig["password"],
); //for both mysql or mariaDB
$s = new Smarty();
$s->setTemplateDir(__DIR__ . "/../includes/views/");

$s->setCompileDir($_SERVER["DOCUMENT_ROOT"] . "/../template-cache/");
$s->setConfigDir($_SERVER["DOCUMENT_ROOT"] . "/../template-config/");
$s->setCacheDir($_SERVER["DOCUMENT_ROOT"] . "/../template-cache/");

$summaryQuery = "
    SELECT
        GROUP_CONCAT(DISTINCT ip ORDER BY ip SEPARATOR ', ') AS ip,
        mac as hostname,
        sum(bytes_in) as bytes_in,
        sum(bytes_out) as bytes_out,
        sum(bytes_in) + sum(bytes_out) as total
    FROM   router.main_summary
    WHERE
        duration_type = :duration_type
        AND duration = :selected_date
    GROUP BY mac
";

$endpoints = [
    "default" => "index",
    "day" => "getDay",
    "month" => "getMonth",
];

$action = urldecode($_REQUEST["action"] ?? "default");

if (strlen(trim($action)) === 0) {
    $action = "default";
}

call_user_func($endpoints[$action]);

function index()
{
    global $s;
    $s->display("stats.tpl");
}

/*
 * stats for specified day
 */
function getDay()
{
    global $s, $tableIn, $table_out, $summaryQuery;
    $s->caching = 0;

    $date = !empty($_GET["date"]) ? (int) $_GET["date"] : strtotime("today");

    $cd = date_create_immutable("@$date");
    $d1 = new DateInterval("P1D");
    $yest = $cd->sub($d1)->getTimestamp(); //yesterday.
    $tomm = $cd->add($d1)->getTimestamp(); //tomorrow

    //fetch inbound byte stats
    $bwStats = R::getAll($summaryQuery, [
        ":selected_date" => $cd->format("Y-m-d"),
        ":duration_type" => "day",
    ]);

    $totals = ["bytes_in" => 0, "bytes_out" => 0, "aggregate" => 0];
    foreach ($bwStats as $k => $v) {
        $bwStats[$k]["bytes_in_formatted"] = formatBytes($v["bytes_in"]);
        $bwStats[$k]["bytes_out_formatted"] = formatBytes($v["bytes_out"]);
        $bwStats[$k]["aggregate_formatted"] = formatBytes($v["total"]);

        $totals["bytes_in"] += $v["bytes_in"];
        $totals["bytes_out"] += $v["bytes_out"];
        $totals["aggregate"] += $v["total"];
    }

    $formatted_in = formatBytes($totals["bytes_in"]);
    $totals["bytes_in_formatted"] = $formatted_in['value'] . ' ' . $formatted_in['unit_string'];

    $formatted_out = formatBytes($totals["bytes_out"], $formatted_in['unit_index']);
    $totals["bytes_out_formatted"] = $formatted_out['value'] . ' ' . $formatted_out['unit_string'];

    $formatted_total = formatBytes($totals["aggregate"], $formatted_in['unit_index']);
    $totals["aggregate_formatted"] = $formatted_total['value'] . ' ' . $formatted_total['unit_string'];


    $do = new DateTime("@$date");
    $data = ["stats" => $bwStats, "totals" => $totals];
    $s->assign("data", $data);
    $s->assign("date", $do->format("Y-m-d"));
    $s->assign("links", ["prev" => $yest, "next" => $tomm]);
    $s->display("stats.day.tpl");
}

/*
 * TODO: month stats stamp_inserted should always be midnight of the day calculated, and should
 * only be updated once per day, so as to not miss short-lived IP addresses in the monthly summary.
 *
 * get month stats
 */
function getMonth()
{
    global $s, $summaryQuery;
    // Querystring parameters
    $year = !empty($_GET["year"]) ? (int) $_GET["year"] : date("Y");
    $month = !empty($_GET["month"]) ? (int) $_GET["month"] : date("m");

    $start_date = mktime(0, 0, 0, $month, 1, $year);

    //we want the date information needed for creating prev and next links:
    //current date to display
    $cd = date_create_immutable("@$start_date");

    //previous month date object.
    $prevD = $cd->sub(new DateInterval("P1M"));

    $lm = $prevD->format("m"); //prev month.
    $py = $prevD->format("Y"); //year for prev month

    //next month date object.
    $nextD = $cd->add(new DateInterval("P1M"));
    $nm = $nextD->format("m"); //next month
    $ny = $nextD->format("Y"); //year for next month

    $last_day = $cd->format("t"); //get the last day of this month from timestamp.

    $cMonth = $cd->format("mY");

    $bwStats = R::getAll($summaryQuery, [
        ":duration_type" => "month",
        "selected_date" => $cMonth,
    ]);

    //initialise the totals array.
    $totals = [
        "bytes_in" => 0,
        "bytes_out" => 0,
    ];

    $totals = ["bytes_in" => 0, "bytes_out" => 0, "aggregate" => 0];
    foreach ($bwStats as $k => $pstat) {
        $bwStats[$k]["bytes_in"] = $pstat["bytes_in"];
        $bwStats[$k]["bytes_out"] = $pstat["bytes_out"];
        $bwStats[$k]["total"] = $pstat["bytes_in"] + $pstat["bytes_out"];

        $totals["bytes_in"] += $pstat["bytes_in"];
        $totals["bytes_out"] += $pstat["bytes_out"];
        $totals["aggregate"] += $pstat["total"];
    }

    $formatted_in = formatBytes($totals["bytes_in"]);
    $totals["bytes_in_formatted"] = $formatted_in['value'] . ' ' . $formatted_in['unit_string'];

    $formatted_out = formatBytes($totals["bytes_out"], $formatted_in['unit_index']);
    $totals["bytes_out_formatted"] = $formatted_out['value'] . ' ' . $formatted_out['unit_string'];

    $formatted_total = formatBytes($totals["aggregate"], $formatted_in['unit_index']);
    $totals["aggregate_formatted"] = $formatted_total['value'] . ' ' . $formatted_total['unit_string'];

    $s->assign("data", ["stats" => $bwStats, "totals" => $totals]);
    $s->assign("date", $cd->format("F Y"));
    $s->assign("links", ["lm" => $lm, "py" => $py, "nm" => $nm, "ny" => $ny]);
    $s->display("stats.month.tpl");
}

/**
 * @param $bytes
 * @param int | null $unit
 * @return array{
 *  value: string,
 *  unit_string: string,
 *  unit_index: int
 * }
 */
 function formatBytes($bytes, int | null $unit = null)
 {
     $units = ["B", "KiB", "MiB", "GiB", "TiB"];

     $bytes = max($bytes, 0);
     if ($unit !== null && $unit < count($units) && $unit >= 0) {
         $power = $unit;
     } else {
         $power = floor(($bytes ? log($bytes) : 0) / log(1024));
         $power = min($power, count($units) - 1);
     }

     $bytes /= pow(1024, $power);

     return [
         'value' => number_format($bytes, 2, '.', ','),
         'unit_string' => $units[$power],
         'unit_index' => $power,
     ];
 }
