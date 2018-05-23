<div id="summary_container">
    <?php var_dump($this->data); ?>
	<table id="summary">
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
				<td><?php echo Format::decimal_size($this->data['totals']['bytes_in']); ?></td>
				<td><?php echo Format::decimal_size($this->data['totals']['bytes_out']); ?></td>
				<td><?php echo Format::decimal_size($this->data['totals']['bytes_total']); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
foreach ($this->data['data'] as $ip=>$row)
{

    var_dump($row);
    $row['bytes_total'] = (int) $row['bytes_in'] + $row['bytes_out'];
	echo '
			<tr data-in="', $row['bytes_in'], '" data-out="', $row['bytes_out'], '" data-total="', $row['bytes_total'], '">
				<td><a href="', date('Y-m-d', $this->date), '/', $ip , '/">', $ip, '</a></td>
				<td><a href="', date('Y-m-d', $this->date), '/', $ip , '/">', gethostbyaddr($ip), '</a></td>
				<td>', Format::decimal_size($row['bytes_in']), '</td>
				<td>', Format::decimal_size($row['bytes_out']), '</td>
				<td>', Format::decimal_size($row['bytes_total']), '</td>
			</tr>';
}
?>

		</tbody>
	</table>

	<div id="pie"></div>
</div>