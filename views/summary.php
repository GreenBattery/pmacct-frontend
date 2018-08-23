<?php var_dump ($this->data); ?>
<div class="page-header">
    <H1>Summary for the month of <?= date("M, Y", $this->date); ?></H1>
</div>
<div id="summary_container">

	<table id="summary" class="datatable">
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
				<td><?php echo Format::decimal_size($this->data['totals']['in']); ?></td>
				<td><?php echo Format::decimal_size($this->data['totals']['out']); ?></td>
				<td><?php echo Format::decimal_size($this->data['totals']['in'] + $this->data['totals']['out']); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
foreach ($this->data['data'] as $ip=>$row)
{
    //var_dump($row);
    $b_in = $row['udp']['bytes_in'] + $row['tcp']['bytes_in'] + $row['icmp']['bytes_in'];
    $b_out = $row['udp']['bytes_out'] + $row['tcp']['bytes_out'] + $row['icmp']['bytes_out'];
    $b_t = $b_in + $b_out;
	echo '
			<tr data-in="', $b_in, '" data-out="', $b_out, '" data-total="', $b_t, '">
				<td><a href="day_host.php?date=', date('Y-m-d', mktime()), '&ip=', urlencode($ip) , '">', $ip, '</a></td>
				<td><a href="', date('Y-m-d', $this->date), '/', $ip , '/">', gethostbyaddr($ip), '</a></td>
				<td>', Format::decimal_size($b_in), '</td>
				<td>', Format::decimal_size($b_out), '</td>
				<td>', Format::decimal_size($b_t), '</td>
			</tr>';
}
?>

		</tbody>
	</table>

	<div id="pie"></div>
</div>
