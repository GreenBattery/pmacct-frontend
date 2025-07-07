<div class="row">
    <div class="btn-group btn-group-sm w-100">
        <button data-date="{$links.prev}" class="btn btn-outline-primary dayNav"
                role='button'>Prev Day</button>
        <button data-date="{$links.next}" class="btn btn-outline-primary dayNav"
                role="button">Next Day</button>
    </div>

    <div class="col"><h3 class="mt-4">{$date}</h3></div>
</div>

<div class="row">
    <div class="col">
        <div class="card-group card-header-tabs">
            <div class="card">
                <div class="card-header">Downld</div>
                <div class="card-body">{$data.totals.bytes_in_formatted}</div>
            </div>
            <div class="card">
                <div class="card-header">Upload</div>
                <div class="card-body">{$data.totals.bytes_out_formatted}</div>
            </div>
            <div class="card">
                <div class="card-header">Aggreg</div>
                <div class="card-body">{$data.totals.aggregate_formatted}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">Details</div>
            <div class="card-body">
                <table id="daySummary" class="table table-striped">
                    <thead>
                    <tr>
                        <th>Hostname</th>
                        <th>In</th>
                        <th>Out</th>
                        <th>Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$data.stats key=ip item=stat}
                        <tr>
                            <td class="w-75">
                                <span class="badge badge-info">{$stat.hostname}</span>
                                <span>{$stat.ip}</span></td>
                            <td>{$stat.bytes_in}</td>
                            <td>{$stat.bytes_out}</td>
                            <td>{$stat.total}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="1">Totals</td>
                        <td>{$data.totals.bytes_in}</td>
                        <td>{$data.totals.bytes_out}</td>
                        <td>{$data.totals.total}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script id="dayScript">
    $(function() {ldelim}
        console.log('day JS');
        var sc2 = document.createElement('script');
        sc2.src = "js/stats.day.js";

        console.log(sc2);
        $("#dayScript").before(sc2);
    {rdelim})
</script>
