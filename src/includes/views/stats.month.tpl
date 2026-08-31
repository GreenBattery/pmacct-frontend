
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
                <div class="card-header">Data Usage</div>
                <div class="card-body">
                    <div class="w-full text-end text-md-start">
                        <span class="font-monospace fs-3">{$data.totals.bytes_in_formatted}</span> &nbsp; <i
                            class="bi bi-download" tooltip="Download"></i>
                    </div>


                        <div class="w-full text-end text-md-start border-bottom">
                            <span class="font-monospace">{$data.totals.bytes_out_formatted}</span> &nbsp; <i
                                tooltip="Upload"
                                class="bi bi-upload"></i>
                        </div>
                        <div class="border-bottom w-full text-end text-md-start fs-2">
                            <span class="font-monospace font-bold">{$data.totals.aggregate_formatted}</span> &nbsp; <i tooltip="Total" class="bi bi-calendar3"></i>
                        </div>
                </div>

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
                            <td class="w-75">
                            <span class="badge text-bg-info">{$stat.hostname}</span> <span>{$stat.ip}</span></td>
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
