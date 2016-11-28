{if isset($massage) && $massage}
        <p class="warning">{$massage}</p>
{else}
        <ul class="nav nav-tabs">
            {if isset($products[0]) && $products[0]['cat_products']}
                <li><a data-toggle="tab" href="#active" class="myac-link">{l s="Active"} <span class="myac-link-num">{$products[0]['cat_products']|count}</span></a></li>
            {/if}
            {if isset($products[1]) && $products[1]['cat_products']}
                <li><a data-toggle="tab" href="#expired" class="myac-link">{l s="Expired"} <span class="myac-link-num">{$products[1]['cat_products']|count}</span></a></li>
            {/if}
            {if isset($products[2]) && $products[2]['cat_products']}
                <li><a data-toggle="tab" href="#sold" class="myac-link">{l s="Sold"} <span class="myac-link-num">{$products[2]['cat_products']|count}</span></a></li>
            {/if}
            {if isset($products[3]) && $products[3]['cat_products']}
                <li ><a data-toggle="tab" href="#deleted" class="myac-link">{l s="Deleted"} <span class="myac-link-num">{$products[3]['cat_products']|count}</span></a></li>
            {/if}
            {if isset($products['pending']) && $products['pending']['cat_products']}
                <li class="last"><a data-toggle="tab" href="#pending" class="myac-link">{l s="Pending"} <span class="myac-link-num">{$products['pending']['cat_products']|count}</span></a></li>
            {/if}
            <li class="button-wrap"><a href="{$link->getPageLink('my-account', true, null, 'tab=list-a-car')|escape:'html':'UTF-8'}" class="btn btn-default">{l s="+ List a Car"}</a></li>
        </ul>
        <div class="tab-content">
            {if isset($products[0]) && $products[0]['cat_products']}
                <div id="active" class="tab-pane fade">
                    {include file="./product-list-myacc.tpl" products=$products[0]['cat_products'] status=0}
                </div>
            {/if}
            {if isset($products[1]) && $products[1]['cat_products']}
                <div id="expired" class="tab-pane fade clearfix">
                    {include file="./product-list-myacc.tpl" products=$products[1]['cat_products'] status=1}
                </div>
            {/if}
            {if isset($products[2]) && $products[2]['cat_products']}
                <div id="sold" class="tab-pane fade">
                    {include file="./product-list-myacc.tpl" products=$products[2]['cat_products'] status=2}
                </div>
            {/if}
            {if isset($products[3]) && $products[3]['cat_products']}
                <div id="deleted" class="tab-pane fade">
                    {include file="./product-list-myacc.tpl" products=$products[3]['cat_products'] status=3}
                </div>
            {/if}
            {if isset($products['pending']) && $products['pending']['cat_products']}
                <div id="pending" class="tab-pane fade">
                    {include file="./product-list-myacc.tpl" products=$products['pending']['cat_products'] status='pending'}
                </div>
            {/if}
        </div>
{/if}