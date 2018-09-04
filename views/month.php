<?php
date_default_timezone_set(Config::$tz);
$this->page_id = 'summary-month';

$cm = date("m", $this->date); //current month.

$lm = ((int) date("m", $this->date) -1 ) % 12; //get prev month.

$yy = date("Y", $this->date); //this year.

if ($lm > $cm) { // last month is greater than this month if we went into last year.
    $yy--;
}




$ny = date("Y", $this->date); //next year.
$nm = ((int) date("m", $this->date) + 1) % 12; //get next month.

//if the next month obtained is less than curent month, it means it's the next year, so increment year.
if ($nm < $cm) {
    $ny++;
}

?>
<h1>Statistics for <?php echo date('F Y', $this->date); ?></h1>
<div class="row">
    <div class="col-sm-5">
        <a class="btn btn-primary" href="<?= "month.php?month=$lm&year=$yy" ?>">Previous Month</a>
    </div>
    <div class="col-sm-5">
        <a class="btn btn-primary" href="<?= "month.php?month=$nm&year=$ny" ?>">Next Month</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8" >
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
				<td><a href="host.php?date=', date('Y-m-d', mktime()), '&ip=', urlencode($ip) , '">', $ip, '</a></td>
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

    <div class="col-lg-4" id="byday">

    </div>

</div>

<script>
    $(function() {
        var opts = {
            order: [[4, "desc"]],
            columns: [
                {data: 'IP'},
                {data: 'Hostname'},
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
                var inTotal = api.column(2).footer().innerText;
                if ($.isNumeric(inTotal)) {
                    $(api.column(2).footer()).html(formatBytes(inTotal));
                }


                var outTotal = api.column(3).footer().innerText;
                if ($.isNumeric(outTotal)) {
                    $(api.column(3).footer()).html(formatBytes(outTotal));
                }


                var sumTotal = api.column(4).footer().innerText;
                if ($.isNumeric(sumTotal)) {
                    $(api.column(4).footer()).html(formatBytes(sumTotal));
                }
            }
        }
        $('#month-summary').DataTable(opts);
    })
</script>