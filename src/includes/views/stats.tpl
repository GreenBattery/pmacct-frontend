{extends file='main.tpl'}
{block name="body"}
    <h1>The Stats</h1>
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item" role="presentation"><a class="nav-link active" id="dayTab" data-bs-toggle="tab" data-bs-target="#day" href="#day" role="tab" aria-controls="day" aria-selected="true">Day</a></li>
        <li class="nav-item" role="presentation"><a class="nav-link" id="monthTab" data-bs-toggle="tab" data-bs-target="#month" href="#month" role="tab" aria-controls="month" aria-selected="false">Month</a></li>
    </ul>

    <div class="tab-content h-100">
        <div class="tab-pane show active h-100" id="day" role="tabpanel" aria-labelledby="dayTab">
            <p>Day stats here.</p>

        </div>
        <div class="tab-pane h-100" id="month" role="tabpanel" aria-labelledby="monthTab">
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