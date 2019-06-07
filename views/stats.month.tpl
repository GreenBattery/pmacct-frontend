
<div class="row">
    <div class="col-8"><h3 class="mt-4">{$date}</h3></div>
    <div class="col-4">
        <div class="btn-group btn-group-sm w-100">
            <button data-month="{$links.lm}" class="btn btn-outline-primary monthNav" role='button'
                data-year="{$links.py}">Prev Month</button>
            <button data-month="{$links.nm}" class="btn btn-outline-primary monthNav" role="button"
                data-year="{$links.ny}">Next Month</button>
        </div>
    </div>
</div>
<div id="monthcontainer">
<table id="monthSummary" class="table table-striped">
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
</div>
<script id="monthScript">
    $(function() {ldelim}
        var script=document.createElement('script');
        script.src="/js/stats.month.js";

        $("#monthScript").before(script);
    {rdelim})

</script>
