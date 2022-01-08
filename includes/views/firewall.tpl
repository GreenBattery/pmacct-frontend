{extends file='main.tpl'}
{block name=title}Router - Firewall{/block}

{block name=cssFiles}
    <link rel="stylesheet" href="css/datatables.min.css" />
{/block}

{block name=scriptFiles}
    <script src="lib/DataTables/datatables.js" type="text/javascript"></script>
    <script src="js/firewall.js" type="text/javascript"></script>

{/block}

{block name=body}
    <h1>Firewall</h1>
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link active" id="rulesTab" data-toggle="tab" href="#rules">Rules</a></li>
        <li class="nav-item"><a class="nav-link" id="eventsTab" data-toggle="tab" href="#events">Events</a></li>
    </ul>

    <div class="tab-content h-100">
        <div class="tab-pane active h-100" id="rules">
            <p>Let's use nftables (nft) to retrieve and display all the rules.</p>
            <pre class="pre-scrollable">{$rules|print_r:true}</pre>

        </div>
        <div class="tab-pane h-100" id="events">
            <h3>Events</h3>
            <p>We will read some logs and present any notable events here.</p>
        </div>
    </div>
{/block}