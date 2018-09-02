<?php
$this->page_id = 'host-day';
//var_dump($this->data);
?>
<h1>Statistics for <?= $this->ip; ?>  on <?= date('M d, Y', $this->date); ?></h1>

<table  id="host-summary">
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
			<td><?php echo $this->data['totals']['bytes_in']; ?></td>
			<td><?php echo $this->data['totals']['bytes_out']; ?></td>
			<td><?php echo $this->data['totals']['bytes_total']; ?></td>
		</tr>
	</tfoot>
	<tbody>
<?php
foreach ($this->data['data'] as $hour=>$row)
{
	echo '<tr data-in="', $row['bytes_in'] ?? 0 , '" data-out="', $row['bytes_out'] ?? 0 , '" data-total="', ($row['bytes_in'] ?? 0 )  + ($row['bytes_out'] ?? 0), '">
			<td>' . $hour . '</td>
			<td>', $row['bytes_in'] ?? 0 , '</td>
			<td>', $row['bytes_out'] ?? 0, '</td>
			<td>', $row['bytes_total'] ?? 0, '</td>
		</tr>';
}
?>
	</tbody>
</table>

<script>
    $(function() {
        var opts = {
            lengthMenu: [[-1], ["All"]],
            columns: [
                {data: 'Hour'},
                {
                    data: 'in',
                    render: function(data, type, row) {
                        if (type === "display") {
                            return formatBytes(data);
                        }else {
                            return data;
                        }

                    }
                },
                {
                    data: 'out',
                    render: function(data, type, row) {
                        if (type === "display") {
                            return formatBytes(data);
                        }else {
                            return data;
                        }

                    }
                },
                {
                    data: 'total',
                    render: function(data, type, row) {
                        if (type === "display") {
                            return formatBytes(data);
                        }else {
                            return data;
                        }

                    }
                }
            ],
            'footerCallback': function(row, data, start, end, display) {
                var api = this.api();
                var inTotal = api.column(1).footer().innerText;
                if ($.isNumeric(inTotal)) {
                    $(api.column(1).footer()).html(formatBytes(inTotal));
                }


                var outTotal = api.column(2).footer().innerText;
                if ($.isNumeric(outTotal)) {
                    $(api.column(2).footer()).html(formatBytes(outTotal));
                }


                var sumTotal = api.column(3).footer().innerText;
                if ($.isNumeric(sumTotal)) {
                    $(api.column(3).footer()).html(formatBytes(sumTotal));
                }
            }
        }
        $('#host-summary').DataTable(opts);
    })
</script>