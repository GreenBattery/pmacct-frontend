<div class="row">
    <div class="col-8"><h3 class="mt-4">{$date}</h3></div>
    <div class="col-4">
        <div class="btn-group btn-group-sm w-100">
            <button data-date="{$links.prev}" class="btn btn-outline-primary dayNav"
                    role='button'>Prev Day</button>
            <button data-date="{$links.next}" class="btn btn-outline-primary dayNav"
                    role="button">Next Day</button>
        </div>
    </div>
</div>
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
            <td><span>{$ip}</span> <span class="badge badge-info">{$stat.hostname}</span></td>
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
<script id="dayScript">
    $(function() {ldelim}
        console.log('day JS');
        var sc2 = document.createElement('script');
        sc2.src = "/js/stats.day.js";

        console.log(sc2);
        $("#dayScript").before(sc2);
    {rdelim})
</script>
