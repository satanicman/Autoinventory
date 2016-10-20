<ul class="leads-list">
    {foreach from=$leads item=lead}
        {if isset($lead) && $lead && isset($lead.product) && $lead.product}
            <li class="leads-lead clearfix">

                {if isset($lead.product.id) && isset($lead.product.link_rewrite) && $lead.product.id
                && $lead.product.link_rewrite}
                    <div class="left-block col-lg-3 col-md-4">
                        <a class="product_img_link" href="{$lead.product.link|escape:'html':'UTF-8'}"
                           title="{$lead.product.name|escape:'html':'UTF-8'}" itemprop="url">
                            <img class="replace-2x img-responsive"
                                 src="{$link->getImageLink($lead.product.link_rewrite, $lead.product.image.id_image, 'home_default')|escape:'html':'UTF-8'}"
                                 alt="{$lead.product.name|escape:'html':'UTF-8'}"
                                 title="{$lead.product.name|escape:'html':'UTF-8'}" itemprop="image"/>
                        </a>
                    </div>
                {/if}
                <div class="center-block col-lg-9 col-md-8">
                    {if isset($lead.product.name) && $lead.product.name}
                        <h5 itemprop="name" class="title-wrap">
                            <a class="myacc-title medium" href="{$lead.product.link|escape:'html':'UTF-8'}"
                               title="{$lead.product.name|escape:'html':'UTF-8'}" itemprop="url">
                                {$lead.product.name|truncate:45:'...'|escape:'html':'UTF-8'}
                            </a>
                        </h5>
                    {/if}
                    <table class="auto-table">
                        {if isset($lead.name) && $lead.name}
                        <tr>
                            <td class="auto-table-name">{l s="Name"}</td>
                            <td class="auto-table-value leads-lead-text leads-lead-name">{$lead.name}</td>
                        </tr>
                        {/if}
                        {if isset($lead.phone) && $lead.phone}
                            <tr>
                                <td class="auto-table-name">{l s="Phone"}</td>
                                <td class="auto-table-value leads-lead-text leads-lead-phone">{$lead.phone}</td>
                            </tr>
                        {/if}
                        {if isset($lead.mail) && $lead.mail}
                            <tr>
                                <td class="auto-table-name">{l s="Email"}</td>
                                <td class="auto-table-value leads-lead-text leads-lead-email">{$lead.mail}</td>
                            </tr>
                        {/if}
                        {if isset($lead.text) && $lead.text}
                            <tr>
                                <td class="auto-table-name">{l s="Message"}</td>
                                <td class="auto-table-value leads-lead-text leads-lead-message">{$lead.text}</td>
                            </tr>
                        {/if}
                    </table>
                    <div class="button-container">
                        {if isset($type) && $type != 'checked'}
                            <a href="{$link->getPageLink('my-account', true, null, 'tab=leads&id_question='|cat:$lead.id_question)|escape:'html':'UTF-8'}" class="myacc-btn">{l s="Check"}</a>
                        {/if}
                        <a href="{$link->getPageLink('my-account', true, null, 'tab=leads&delete=1&id_question='|cat:$lead.id_question)|escape:'html':'UTF-8'}" class="myacc-btn red">{l s="Delete"}</a>
                    </div>
                </div>
            </li>
        {/if}
    {/foreach}
</ul>