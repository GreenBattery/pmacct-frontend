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


<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">Details</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="daySummary" class="table table-striped dt-responsive nowrap" style="width:100%">
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
                                <td>
                                    <span class="badge text-bg-info">{$stat.hostname|default:"-"}</span>
                                    <span>{$stat.ip}</span>
                                </td>
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
                            <td>{$data.totals.aggregate_formatted}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
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
