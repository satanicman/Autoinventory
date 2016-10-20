<ul class="nav nav-tabs">
    {if isset($messages[0]) && $messages[0]}
        <li><a data-toggle="tab" href="#new" class="myac-link">{l s="New"} <span class="myac-link-num">{$messages[0]|count}</span></a></li>
    {/if}
    {if isset($messages[1]) && $messages[1]}
        <li class="last"><a data-toggle="tab" href="#checked" class="myac-link">{l s="Checked"} <span class="myac-link-num">{$messages[1]|count}</span></a></li>
    {/if}
    <li class="button-wrap"><a href="{$link->getPageLink('my-account', true, null, 'tab=list-a-car')|escape:'html':'UTF-8'}" class="btn btn-default">{l s="+ List a Car"}</a></li>
</ul>
<div class="tab-content">
    {if isset($messages[0]) && $messages[0]}
        <div id="new" class="tab-pane fade">
            {include file="./leads-list.tpl" leads=$messages[0] type='new'}
        </div>
    {/if}
    {if isset($messages[1]) && $messages[1]}
        <div id="checked" class="tab-pane fade clearfix">
            {include file="./leads-list.tpl" leads=$messages[1] type='checked'}
        </div>
    {/if}
</div>