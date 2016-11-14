<ul class="nav nav-tabs">
    <li><a data-toggle="tab" href="#dealer_info" class="myac-link">{l s="Dealer Info"}</a></li>
    <li><a data-toggle="tab" href="#settings" class="myac-link">{l s="Settings"}</a></li>
    <li class="last"><a data-toggle="tab" href="#billing_info" class="myac-link">{l s="Billing Info"}</a></li>
    <li class="button-wrap"><a
                href="{$link->getPageLink('my-account', true, null, 'tab=list-a-car')|escape:'html':'UTF-8'}"
                class="btn btn-default">{l s="+ List a Car"}</a></li>
</ul>
<div class="tab-content">
    <div id="dealer_info" class="tab-pane fade clearfix">
        <h2 class="myac-title">{l s="Update Dealers Info"}</h2>
        <form action="{$link->getPageLink('my-account', true, null, 'tab=dealer')|escape:'html':'UTF-8'}" method="POST" enctype="multipart/form-data">
            {*<pre>{$customer|var_dump}</pre>*}
            <div class="form-group half odd even">
                <label for="business_name" class="label">{l s="Business Name"}</label>
                <input type="text" name="business_name" id="business_name" class="form-control"
                       value="{if isset($customer->business_name) && $customer->business_name}{$customer->business_name}{/if}">
            </div>
            <div class="clearfix"></div>
            <div class="form-group half odd even">
                <label for="address" class="label">{l s="Address"}</label>
                <input type="text" name="address" id="address" class="form-control" value="{if isset($customer->address) && $customer->address}{$customer->address}{/if}">
            </div>
            <div class="clearfix"></div>
            <div class="form-group half parts">
                <div class="part large">
                    <label for="city" class="label">{l s="City"}</label>
                    <input type="text" name="city" id="city" class="form-control" value="{if isset($customer->city) && $customer->city}{$customer->city}{/if}">
                </div>
                {if isset($states) && $states}
                <div class="part large">
                    <label for="state" class="label">{l s="State"}</label>
                    <select name="state" id="state" class="form-control not_uniform">
                        {foreach from=$states item=state}
                            <option value="{$state.id_state}"{if $state.id_state == $customer->state} selected="selected"{/if}>{$state.name}</option>
                        {/foreach}
                    </select>
                </div>
                {/if}
                <div class="part medium">
                    <label for="zip" class="label">{l s="ZipCode"}</label>
                    <input type="text" name="zip" id="zip" class="form-control" value="{if isset($customer->zip) && $customer->zip}{$customer->zip}{/if}">
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group parts col-lg-9">
                <div class="part large">
                    <label for="phone" class="label">{l s="Phone New"}</label>
                    <input type="text" name="phones[main][phone]" id="phone" class="form-control" value="{if isset($phones.main) && count($phones.main)}{$phones.main[0].phone}{/if}">
                </div>
                <div class="part small">
                    <label for="ext_1" class="label">{l s="Ext"}</label>
                    <input type="text" name="phones[main][ext]" id="ext_1" class="ext form-control" value="{if isset($phones.main) && count($phones.main)}{$phones.main[0].ext}{/if}">
                </div>
                <div class="part large">
                    <label for="phone" class="label">{l s="Used"}</label>
                    <input type="text" name="phones[used][0][phone]" id="phone" class="form-control" value="{if isset($phones.used) && count($phones.used) && isset($phones.used[0])}{$phones.used[0].phone}{/if}">
                </div>
                <div class="part small">
                    <label for="ext_1" class="label">{l s="Ext"}</label>
                    <input type="text" name="phones[used][0][ext]" id="ext_1" class="ext form-control" value="{if isset($phones.used) && count($phones.used) && isset($phones.used[1])}{$phones.used[1].ext}{/if}">
                </div>
                <div class="part large">
                    <label for="phone" class="label">{l s="Used"}</label>
                    <input type="text" name="phones[used][2][phone]" id="phone" class="form-control" value="{if isset($phones.used) && count($phones.used) && isset($phones.used[1])}{$phones.used[1].phone}{/if}">
                </div>
                <div class="part small">
                    <label for="ext_1" class="label">{l s="Ext"}</label>
                    <input type="text" name="phones[used][2][ext]" id="ext_1" class="ext form-control" value="{if isset($phones.used) && count($phones.used) && isset($phones.used[0])}{$phones.used[0].ext}{/if}">
                </div>
            </div>
            <div class="clearfix"></div>
            <hr/>
            <div class="form-group">
                <label for="business_description" class="label">{l s="Business Description"}</label>
                <textarea name="business_description" id="business_description" class="form-control">{if isset($customer->business_description) && $customer->business_description}{$customer->business_description}{/if}</textarea>
            </div>
            <div class="form-group">
                <label for="product_description" class="label">{l s="Product Description"}</label>
                <textarea name="product_description" id="product_description" class="form-control">{if isset($customer->product_description) && $customer->product_description}{$customer->product_description}{/if}</textarea>
            </div>
            <hr/>
            {if isset($offers) && $offers}
                <div class="form-group radio-wrap clearfix">
                    <p class="label clearfix">{l s="Offers"}</p>
                    {foreach from=$offers item=offer}
                        <label for="offer_{$offer.id_offers}" class="label">
                            <input type="checkbox" name="offers[{$offer.id_offers}]" id="offer_{$offer.id_offers}" value="{$offer.id_offers}"{if in_array($offer.id_offers, $customer_offers)} checked="checked"{/if}>
                            <span>{$offer.name}</span>
                        </label>
                    {/foreach}
                </div>
            {/if}
            <hr/>
            <div class="dealer-cols clearfix">
                <div class="dealer-col col-left col-lg-3 col-md-12">
                    {if isset($category) && $category && $category->id_image}
                        <img src="{$link->getCatImageLink($category->link_rewrite, $category->id_image, 'category_default')|escape:'html':'UTF-8'}" alt="{$customer->business_name}" id="image_preview"/>
                    {else}
                        <img src="{$img_dir}/no-photo.jpg" alt="{l s="No photo"}" id="image_preview">
                    {/if}
                    <div class="button-container text-center">
                        <input type="file" name="images" id="files-customer" class="hidden">
                        <a href="#" class="myacc-btn grey" onclick="$('#files-customer').click();return false;">{l s="Upload"}</a>
                    </div>
                </div>
                <div class="dealer-col col-right col-lg-7 col-md-12">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#showroom">{l s="Showroom Hours"}</a></li>
                        <li><a data-toggle="tab" href="#service">{l s="Service Hours"}</a></li>
                    </ul>
                    {$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']}
                    <div class="tab-content">
                        <div id="showroom" class="tab-pane fade in active">
                            <table class="dealer-table">
                                <tr class="dealer-table-row">
                                    <td class="dealer-table-col text-center"></td>
                                    <td class="dealer-table-col text-center">{l s="Open time"}</td>
                                    <td class="dealer-table-col text-center">{l s="Close time"}</td>
                                    <td class="dealer-table-col text-center"></td>
                                </tr>
                                {foreach from=$days item=day}
                                    {include file="$tpl_dir./time_line.tpl" day=$day type='showroom' selected=$times['showroom'][$day|lower]}
                                {/foreach}
                            </table>
                        </div>
                        <div id="service" class="tab-pane fade in">
                            <table class="dealer-table">
                                <tr class="dealer-table-row">
                                    <td class="dealer-table-col text-center"></td>
                                    <td class="dealer-table-col text-center">{l s="Open time"}</td>
                                    <td class="dealer-table-col text-center">{l s="Close time"}</td>
                                    <td class="dealer-table-col text-center"></td>
                                </tr>
                                {foreach from=$days item=day}
                                    {include file="$tpl_dir./time_line.tpl" day=$day type='service' selected=$times['service'][$day|lower]}
                                {/foreach}
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2"></div>
            </div>
            <div class="form-group social-group">
                <i class="social-link facebook"></i>
                <input type="text" name="facebook" id="facebook" class="form-control"
                       placeholder="{l s="Paste your link here"}"{if isset($customer->facebook) && $customer->facebook} value="{$customer->facebook}" {/if}>
            </div>
            <div class="form-group social-group">
                <i class="social-link instagram"></i>
                <input type="text" name="instagram" id="instagram" class="form-control"
                       placeholder="{l s="Paste your link here"}"{if isset($customer->instagram) && $customer->instagram} value="{$customer->instagram}" {/if}>
            </div>
            <div class="form-group social-group">
                <i class="social-link twitter"></i>
                <input type="text" name="twitter" id="twitter" class="form-control"
                       placeholder="{l s="Paste your link here"}"{if isset($customer->twitter) && $customer->twitter} value="{$customer->twitter}" {/if}>
            </div>
            <div class="form-group social-group">
                <i class="social-link website"></i>
                <input type="text" name="website" id="website" class="form-control"
                       placeholder="{l s="Paste your link here"}"{if isset($customer->site) && $customer->site} value="{$customer->site}" {/if}>
            </div>
            <div class="clearfix"></div>
            <hr/>
            <button class="btn btn-default btn-update" name="updateCustomer">{l s="Update"}</button>
        </form>
    </div>
    <div id="settings" class="tab-pane fade clearfix">
        <h2 class="myac-title">{l s="Update Dealers Info"}</h2>
        <div class="settings-wrap">
            <h6 class="myac-title sub">{l s="Update your email address"}</h6>
            <form action="{$link->getPageLink('my-account', true, null, 'tab=dealer')|escape:'html':'UTF-8'}" method="POST" class="setting-block email">
                <table class="setting-table">
                    <tr class="setting-row">
                        <td class="setting-col setting-col-label required">
                            <label for="old" class="label">{l s="Current Email"} <span>*</span></label>
                        </td>
                        <td class="setting-col">
                            <input type="text" name="email[old]" id="old" class="form-control">
                        </td>
                    </tr>
                    <tr class="setting-row">
                        <td class="setting-col setting-col-label required">
                            <label for="new" class="label">{l s="New Email"} <span>*</span></label>
                        </td>
                        <td class="setting-col">
                            <input type="text" name="email[new]" id="new" class="form-control">
                        </td>
                    </tr>
                    <tr class="setting-row">
                        <td class="setting-col setting-col-label required">
                            <label for="confirm" class="label">{l s="Cofirm Email"} <span>*</span></label>
                        </td>
                        <td class="setting-col">
                            <input type="text" name="email[confirm]" id="confirm" class="form-control">
                        </td>
                    </tr>
                    <tr class="setting-row">
                        <td class="setting-col">
                        </td>
                        <td class="setting-col">
                            <button class="btn btn-default btn-update" name="updateEmail">{l s="Update"}</button>
                        </td>
                    </tr>
                </table>
            </form>
            <h6 class="myac-title sub">{l s="Update your password"}</h6>
            <form action="{$link->getPageLink('my-account', true, null, 'tab=dealer')|escape:'html':'UTF-8'}" method="POST" class="setting-block password">
                <table class="setting-table">
                    <tr class="setting-row">
                        <td class="setting-col setting-col-label required">
                            <label for="old" class="label">{l s="Current Password"} <span>*</span></label>
                        </td>
                        <td class="setting-col">
                            <input type="password" name="password[old]" id="old" class="form-control">
                        </td>
                    </tr>
                    <tr class="setting-row">
                        <td class="setting-col setting-col-label required">
                            <label for="new" class="label">{l s="New Password"} <span>*</span></label>
                        </td>
                        <td class="setting-col">
                            <input type="password" name="password[new]" id="new" class="form-control">
                        </td>
                    </tr>
                    <tr class="setting-row">
                        <td class="setting-col setting-col-label required">
                            <label for="confirm" class="label">{l s="Confirm Password"} <span>*</span></label>
                        </td>
                        <td class="setting-col">
                            <input type="password" name="password[confirm]" id="confirm" class="form-control">
                        </td>
                    </tr>
                    <tr class="setting-row">
                        <td class="setting-col">
                        </td>
                        <td class="setting-col">
                            <button class="btn btn-default btn-update" name="updatePassword">{l s="Update"}</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <div id="billing_info" class="tab-pane fade clearfix">
        <h2 class="myac-title">{l s="Update Dealers Info"}</h2>
        <form action="{$link->getPageLink('my-account', true, null, 'tab=dealer')|escape:'html':'UTF-8'}" method="POST">
            <div class="billing-info-wrap clearfix">
                <div class="col-lg-5 billing-info-col left">
                    <h6 class="myac-title num"><span
                                class="num-number"><span>1</span></span><span>{l s="Billing Adress"}</span></h6>
                    <div class="form-group">
                        <label for="business_name" class="label">{l s="Business Name"}</label>
                        <input type="text" class="form-control" id="business_name" name="business_name"
                               placeholder="John Doe"{if isset($billing->business_name) && $billing->business_name} value="{$billing->business_name}"{/if}>
                    </div>
                    <div class="form-group">
                        <label for="adress1" class="label">{l s="Adress 1"}</label>
                        <input type="text" class="form-control" id="adress1" name="adress_1"
                               placeholder="631 Inventory ave"{if isset($billing->adress_1) && $billing->adress_1} value="{$billing->adress_1}"{/if}>
                    </div>
                    <div class="form-group">
                        <label for="adress2" class="label">{l s="Adress 2"}</label>
                        <input type="text" class="form-control" id="adress2"
                               name="adress_2"{if isset($billing->adress_1) && $billing->adress_2} value="{$billing->adress_2}"{/if}
                        >
                    </div>
                    <div class="form-group">
                        <label for="city" class="label">{l s="City"}</label>
                        <input type="text" class="form-control" id="city" name="city"
                               placeholder="Auto city"{if isset($billing->city) && $billing->city} value="{$billing->city}"{/if}>
                    </div>
                    <div class="form-group parts">
                        <div class="part large">
                            <label for="state" class="label">{l s="State"}</label>
                            <select name="state" id="state" class="form-control not_uniform">
                                <option value="0"{if !$billing->id_state} selected="selected"{/if}>-</option>
                                {foreach from=$states item=state}
                                    <option value="{$state.id_state}"{if $state.id_state == $billing->id_state} selected="selected"{/if}>{$state.name}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="part medium">
                            <label for="zip" class="label">{l s="ZipCode"}</label>
                            <input type="text" class="form-control" id="zip" name="zip"
                                   placeholder="10305"{if isset($billing->zip_code) && $billing->zip_code} value="{$billing->zip_code}"{/if}>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 billing-info-col right">
                    <h6 class="myac-title num"><span
                                class="num-number"><span>2</span></span><span>{l s="Credit Card Info"}</span></h6>
                    <div class="form-group">
                        <label for="card_name" class="label">{l s="Name On Card"}</label>
                        <input type="text" class="form-control" name="card_name" id="card_name"
                               placeholder="John Doe"{if isset($billing->card_name) && $billing->card_name} value="{$billing->card_name}"{/if}>
                    </div>
                    <div class="form-group">
                        <label for="card_number" class="label payment-wrap">
                            <span>{l s="Card Number"}</span>
                            <span class="payment-logos">
                                    <span class="payment-logo visa"></span>
                                    <span class="payment-logo mc"></span>
                                    <span class="payment-logo ae"></span>
                                    <span class="payment-logo discovery"></span>
                                </span>
                        </label>
                        <input type="text" class="form-control" name="card_number" id="card_number"
                               placeholder="xxxx-xxx-xxx xxxx"{if isset($billing->card_number) && $billing->card_number} value="{$billing->card_number}"{/if}>
                    </div>
                    <div class="form-group parts large">
                        <div class="part medium">
                            <label for="cvv" class="label">{l s="CVV Number"}</label>
                            <div class="help_container">
                                <input type="text" class="form-control" name="cvv" id="cvv"
                                       placeholder="CVV"{if isset($billing->cvv) && $billing->cvv} value="{$billing->cvv}"{/if}>
                                <span class="help-wrap">
                                        <span class="help-icon"></span>
                                        <span class="help-text">{l s="some help text"}</span>
                                    </span>
                            </div>
                        </div>
                        <div class="part medium">
                            <label for="exp_month" class="label">{l s="Exp. Month"}</label>
                            <input type="text" class="form-control" name="month" id="cvv"
                                   placeholder="Exp. Month"{if isset($billing->month) && $billing->month} value="{$billing->month}"{/if}>
                        </div>
                        <div class="part medium">
                            <label for="exp_day" class="label">{l s="Exp. Day"}</label>
                            <input type="text" class="form-control" name="day" id="cvv"
                                   placeholder="Exp. Day"{if isset($billing->year) && $billing->year} value="{$billing->year}"{/if}>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <button class="btn btn-default btn-update" name="updateBilling">{l s="Update"}</button>
            {if isset($customer->subscription_id) && $customer->subscription_id}
                <button class="btn btn-default" name="unSubscribe" id="unSubscribe">{l s="Terminate the paid membership"}</button>
            {/if}
        </form>
    </div>
</div>