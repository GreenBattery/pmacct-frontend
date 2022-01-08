{extends file='main.tpl'}
{block name="body"}
    <h1>The Stats</h1>
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link active" id="dayTab" data-toggle="tab" href="#day">Day</a></li>
        <li class="nav-item"><a class="nav-link" id="monthTab" data-toggle="tab" href="#month">Month</a></li>
    </ul>

    <div class="tab-content h-100">
        <div class="tab-pane active h-100" id="day">
            <p>Day stats here.</p>

        </div>
        <div class="tab-pane h-100" id="month">
            <h3>The monthly stats are here for your viewing pleasure</h3>
        </div>
    </div>
{/block}
{block name="title"}Router Bandwidth Statistics{/block}

{block name="cssFiles"}
    <link rel="stylesheet" href="css/datatables.min.css" />

{/block}
{block name="scriptFiles"}
    <script src="lib/DataTables/datatables.js" type="text/javascript"></script>
    <script src="js/stats.js" type="text/javascript"></script>
    <script src="js/popper.min.js" type="text/javascript"></script>
    <script src="js/tooltip.min.js" type="text/javascript"></script>
{/block}