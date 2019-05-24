<?php
$this->page_id = 'summary-day';
$data = $this->data;
$yest = $this->date - 86400;
$tomorrow = $this->date + 86400;

?>

<div class="page-header">
    <h1>Statistics for <?php echo date('Y-m-d', $this->date); ?></h1>
</div>
<div id="summary_container container">
<div class="row">
    <div class="pull-left"><a class="btn btn-primary" href="day.php?date=<?=$yest?>">Previous Day</a></div>
    <div class="pull-right"><a class="btn btn-primary pull-right" href="day.php?date=<?=$tomorrow?>">Following Day</a></div>

</div>
    <div class="row">
    <div class="col-lg-8 center-block" style="float: none;">

    <table id="day-summary" class="datatable">
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
            <td><?php echo $this->data['totals']['bytes_in']; ?></td>
            <td><?php echo $this->data['totals']['bytes_out']; ?></td>
            <td><?php echo $this->data['totals']['bytes_total']; ?></td>
        </tr>
        </tfoot>
        <tbody>
        <?php
        foreach ($this->data['data'] as $ip=>$row)
        {
            //var_dump($row);
            $b_in = $row['bytes_in'] ?? 0 ;
            $b_out = $row['bytes_out'] ?? 0;
            $b_t = $b_in + $b_out;
            $hname = array_key_exists($ip, $data['hostnames']) ? $data['hostnames'][$ip] : $ip;
            echo '
			<tr data-in="', $b_in, '" data-out="', $b_out, '" data-total="', $b_t, '">
				<td><a href="host.php?date=', date('Y-m-d', mktime()), '&ip=', urlencode($ip) , '">', $ip, '</a></td>
				<td>', $hname, '</td>
				<td> ', $b_in, '</td>
				<td> ', $b_out, '</td>
				<td> ', $b_t, '</td>
			</tr>';
        }
        ?>

        </tbody>
    </table>

    </div>
</div>

</div>
<script>

</script>