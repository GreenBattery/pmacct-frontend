<?php
$this->page_id = 'host-day';
?>
<h1>Statistics for <?= $this->ip; ?>  on <?= date('M d, Y', $this->date); ?></h1>

<table id="host">
	<thead>
		<tr>
			<th>Time</th>
			<th>In</th>
			<th>Out</th>
			<th>Total</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Totals</th>
			<td><?php echo Format::decimal_size($this->data['totals']['bytes_in']); ?></td>
			<td><?php echo Format::decimal_size($this->data['totals']['bytes_out']); ?></td>
			<td><?php echo Format::decimal_size($this->data['totals']['bytes_total']); ?></td>
		</tr>
	</tfoot>
	<tbody>
<?php
foreach ($this->data['data'] as $hour=>$row)
{
	echo '
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
		<tr data-in="', $row['bytes_in'], '" data-out="', $row['bytes_out'], '" data-total="', $row['bytes_in'] + $row['bytes_out'], '">
			<td>' . $hour . '</td>
			<td>', Format::decimal_size($row['bytes_in']), '</td>
			<td>', Format::decimal_size($row['bytes_out']), '</td>
			<td>', Format::decimal_size($row['bytes_total']), '</td>
=======
=======
>>>>>>> dev
=======
>>>>>>> dev
		<tr data-in="', $row['bytes_in'] ?? 0 , '" data-out="', $row['bytes_out'] ?? 0, '" data-total="', $row['bytes_in'] ?? 0  + $row['bytes_out'] ?? 0, '">
			<td>' . $hour . '</td>
			<td>', Format::decimal_size($row['bytes_in'] ?? 0), '</td>
			<td>', Format::decimal_size($row['bytes_out'] ?? 0 ), '</td>
			<td>', Format::decimal_size($row['bytes_total'] ?? 0), '</td>
<<<<<<< HEAD
<<<<<<< HEAD
>>>>>>> dev
=======
>>>>>>> dev
=======
>>>>>>> dev
		</tr>';
}
?>
	</tbody>
</table>