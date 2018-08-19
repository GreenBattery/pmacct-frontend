<?php
date_default_timezone_set(Config::$tz);
$this->page_id = 'summary-month';
var_dump($this->date);
$dd = date_create("@{$this->date}");
date_sub($dd, date_interval_create_from_date_string("1 month"));
$lm = date_format($dd, "m");
$yy = date_format($dd, "Y");


$dd = date_create("@{$this->date}"); //reset date for use again in next month's calcs.
date_add($dd, date_interval_create_from_date_string("1 month"));
$nm = date_format($dd, "m");
$ny = date_format($dd, "Y");
var_dump($lm);
?>
<h1>Statistics for <?php echo date('F Y', $this->date); ?></h1>
<div>
    <a href="<?= "month.php?month=$lm&year=$yy" ?>">Previous Month</a>
    <a href="<?= "month.php?month=$nm&year=$ny" ?>">Next Month</a>
</div>

<div id="summary_container" >
    <table id="month-summary">
        <thead>
        <tr>
            <th>IP</th>
            <th>Hostname</th>
            <th>In</th>
            <th>Out</th>
            <th>Total</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th colspan="2">Totals</th>
            <td><?php echo $this->data['totals']['in']; ?></td>
            <td><?php echo $this->data['totals']['out']; ?></td>
            <td><?php echo $this->data['totals']['in'] + $this->data['totals']['out'] ; ?></td>
        </tr>
        </tfoot>
        <tbody>
        <?php
        foreach ($this->data['data'] as $ip=>$row)
        {
            $b_in = $row['udp']['bytes_in'] + $row['tcp']['bytes_in'] + $row['icmp']['bytes_in'];
            $b_out = $row['udp']['bytes_out'] + $row['tcp']['bytes_out'] + $row['icmp']['bytes_out'];
            $b_t = $b_in + $b_out;
            echo '
			<tr data-in="', $b_in, '" data-out="', $b_out, '" data-total="', $b_t, '">
				<td><a href="day_host.php?date=', date('Y-m-d', mktime()), '&ip=', urlencode($ip) , '">', $ip, '</a></td>
				<td><a href="', date('Y-m-d', $this->date), '/', $ip , '/">', gethostbyaddr($ip), '</a></td>
				<td>', $b_in, '</td>
				<td>', $b_out, '</td>
				<td>', $b_t, '</td>
			</tr>';
        }
        ?>

        </tbody>
    </table>
</div>


<div id="byday">stats by day for this month</div>
<script>
    $(function() {
        $('#month-summary').DataTable();
    })
</script>