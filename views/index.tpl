{extends file='main.tpl'}
{block name="body"}
    <h1>The Router UI</h1>


    <h3>Network Interfaces</h3>
    <div class="card-group" aria-label="List of Interfaces">
    {foreach from=$devices item=d}
        <div class="card">
            <div class="card-header">
                <h3>{$d.ifname}</h3>
            </div>
            <div class="card-body">
                <h5>Addresses</h5>
                <ul class="list-group">
                    {foreach from=$d.addr_info item=addr}
                        <li class="list-group-item">{$addr.local}</li>
                    {/foreach}

                </ul>

                <h5>Other Data</h5>
                <ul class="list-group" aria-label="Interface Details">
                    <li class="list-group-item"><strong>Type:</strong> Ethernet</li>
                    <li class="list-group-item"><strong>MAC:</strong> {$d.address}</li>
                    <li class="list-group-item"><strong>Q Discipline:</strong> {$d.qdisc}</li>
                </ul>

            </div>
        </div>
    {/foreach}
    </div>


{/block}
{block name="title"}Router UI{/block}