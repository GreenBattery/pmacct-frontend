
<div class="row">

    <div class="btn-group btn-group-sm w-100">
        <button data-month="{$links.lm}" class="btn btn-outline-primary monthNav" role='button'
                data-year="{$links.py}">Prev Month</button>
        <button data-month="{$links.nm}" class="btn btn-outline-primary monthNav" role="button"
                data-year="{$links.ny}">Next Month</button>
    </div>

<h2>{$date}</h2>
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
        <div id="monthcontainer" class="card">
            <div class="card-header">Details</div>
            <div class="card-body">
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
                            <td class="w-75"><span class="badge badge-info">{$stat.hostname}</span> <span>{$stat.ip}</span></td>
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
<script id="monthScript">
    $(function() {ldelim}
        var script=document.createElement('script');
        script.src="/js/stats.month.js";

        $("#monthScript").before(script);
    {rdelim})

</script>
