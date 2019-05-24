<h3>The daily stats will go here.</h3>
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
            <td><span title="{$ip}" data-toggle="tooltip">{$stat.hostname}</span></td>
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

<script src="/js/stats.day.js"></script>