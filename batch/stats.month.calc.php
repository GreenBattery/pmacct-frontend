<?php
/**
 * User: nucc1
 * Date: 02/07/2019
 * Time: 19:26
 */

date_default_timezone_set("utc");
require __DIR__ . '/../vendor/autoload.php';

use nucc1\Hostnames;
use \RedBeanPHP\R as R;

/*
 * we wish to calculate the monthly statistics and update the main_summary table periodically so that the
 * UI display of the stats is faster.
*/

/*
 * for the long options:
 * all means calculate everything you find in the database, and aggregate by IP per month.
 *
 * current means calculate for the current month only, as determined by the time on execution machine
 *
 * month allows you to specify a required month to calculate.
 */
$oa = [
    'all',
    'current',
    'month:',
    'help'
];
$options = getopt("", $oa);

if (empty($options) || array_key_exists('help', $options)) {
    echo "
    My job is to precompute the bandwidth stats and aggregate them by month.
    
    Usage: Execute me as `php /path/to/me.php` with any of the following options:
        --all to calculate everything 
        --current for current month
        --month=2018-05 to calculate a specific month, in this case, May 2018.\n";
}