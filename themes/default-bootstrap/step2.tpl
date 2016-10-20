<div id="dealer_info">
    <form action="{$link->getPageLink('authentication', true)|escape:'html':'UTF-8'}" method="post"
           id="account-creation_form" enctype="multipart/form-data">
        <div class="registration-content-top clearfix">
            <h3 class="myac-title num col-lg-5 active"><span
                        class="num-number"><span>1</span></span><span>{l s="Billing Info"}</span></h3>
            <h3 class="myac-title num col-lg-7 active"><span
                        class="num-number"><span>2</span></span><span>{l s="Account Details"}</span></h3>
        </div>
        <div class="form-group half odd even">
            <label for="business_name" class="label">{l s="Business Name"}</label>
            <input type="text" name="business_name" id="business_name" class="form-control"
                   placeholder="Toyota of Staten Island"{if isset($smarty.post.business_name)} value="{$smarty.post.business_name}"{/if}>
        </div>
        <div class="clearfix"></div>
        <div class="form-group half odd even">
            <label for="address" class="label">{l s="Address"}</label>
            <input type="text" name="address" id="address" class="form-control" placeholder="14 Ocean Drive"{if isset($smarty.post.address)} value="{$smarty.post.address}"{/if}>
        </div>
        <div class="clearfix"></div>
        <div class="form-group half parts">
            <div class="part large">
                <label for="city" class="label">{l s="City"}</label>
                <input type="text" name="city" id="city" class="form-control" placeholder="Staten Island"{if isset($smarty.post.city)} value="{$smarty.post.city}"{/if}>
            </div>
            <div class="part large">
                <label for="id_state" class="label">{l s="State"}</label>
                <select name="id_state" id="state" class="form-control not_uniform">
                    <option value="">-</option>
                    {foreach from=$states item=state}
                        <option value="{$state.id_state}"{if isset($smarty.post.id_state) && $smarty.post.id_state == $state.id_state} selected="selected"{/if}>{$state.name}</option>
                    {/foreach}
                </select>
            </div>
            <div class="part medium">
                <label for="zip" class="label">{l s="ZipCode"}</label>
                <input type="text" name="zip" id="zip" class="form-control" placeholder="10305"{if isset($smarty.post.zip)} value="{$smarty.post.zip}"{/if}>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="form-group parts col-lg-9">
            <div class="part large">
                <label for="phone" class="label">{l s="Phone New"}</label>
                <input type="text" name="phones[main][phone]" id="phone" class="form-control" placeholder="718-789-9090"{if isset($smarty.post.phones['main']['phone'])} value="{$smarty.post.phones['main']['phone']}"{/if}>
            </div>
            <div class="part small">
                <label for="ext_1" class="label">{l s="Ext"}</label>
                <input type="text" name="phones[main][ext]" id="ext_1" class="ext form-control" placeholder="258"{if isset($smarty.post.phones['main']['ext'])} value="{$smarty.post.phones['main']['ext']}"{/if}>
            </div>
            <div class="part large">
                <label for="used_1" class="label">{l s="Used"}</label>
                <input type="text" name="phones[used][0][phone]" id="used_1" class="used form-control" placeholder="718-798-8987"{if isset($smarty.post.phones[used][0]['phone'])} value="{$smarty.post.phones[used][0]['phone']}"{/if}>
            </div>
            <div class="part small">
                <label for="ext_2" class="label">{l s="Ext"}</label>
                <input type="text" name="phones[used][0][ext]" id="ext_2" class="ext form-control" placeholder="258"{if isset($smarty.post.phones[used][0]['ext'])} value="{$smarty.post.phones[used][0]['ext']}"{/if}>
            </div>
            <div class="part large">
                <label for="used_2" class="label">{l s="Used"}</label>
                <input type="text" name="phones[used][2][phone]" id="used_2" class="used form-control" placeholder="718-798-8987"{if isset($smarty.post.phones[used][2]['phone'])} value="{$smarty.post.phones[used][2]['phone']}"{/if}>
            </div>
            <div class="part small">
                <label for="ext_3" class="label">{l s="Ext"}</label>
                <input type="text" name="phones[used][2][ext]" id="ext_3" class="ext form-control" placeholder="258"{if isset($smarty.post.phones[used][2]['ext'])} value="{$smarty.post.phones[used][2]['ext']}"{/if}>
            </div>
        </div>
        <div class="clearfix"></div>
        <hr/>
        <div class="form-group">
            <label for="business_description" class="label">{l s="Business Description"}</label>
            <textarea name="business_description" id="business_description" class="form-control">{if isset($smarty.post.business_description)}{$smarty.post.business_description}{/if}</textarea>
        </div>
        <div class="form-group">
            <label for="product_description" class="label">{l s="Product Description"}</label>
            <textarea name="product_description" id="product_description" class="form-control">{if isset($smarty.post.product_description)}{$smarty.post.product_description}{/if}</textarea>
        </div>
        <hr/>
        <div class="form-group radio-wrap clearfix">
            <p class="label clearfix">{l s="Offers"}</p>
            {if isset($offers) && $offers}
                {foreach from=$offers item=offer}
                    <label for="offer_{$offer.id_offers}" class="label">
                        <input type="checkbox" name="offers[{$offer.id_offers}]" id="offer_{$offer.id_offers}" value="{$offer.id_offers}"{if isset($smarty.post.offers[$offer.id_offers])} checked="checked"{/if}>
                        <span>{$offer.name}</span>
                    </label>
                {/foreach}
            {/if}
        </div>
        <hr/>
        <div class="dealer-cols clearfix">
            <div class="dealer-col col-left col-lg-3 col-md-12">
                <img src="{$img_dir}/no-photo.jpg" alt="{l s="No photo"}" id="image_preview">
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
                                {if isset($smarty.post.time.showroom[$day|lower])}
                                    {include file="$tpl_dir./time_line.tpl" day=$day type='showroom' selected=$smarty.post.time.showroom[$day|lower]}
                                {else}
                                    {include file="$tpl_dir./time_line.tpl" day=$day type='showroom'}
                                {/if}
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
                                {if isset($smarty.post.time.service[$day|lower])}
                                    {include file="$tpl_dir./time_line.tpl" day=$day type='service' selected=$smarty.post.time.service[$day|lower]}
                                {else}
                                    {include file="$tpl_dir./time_line.tpl" day=$day type='service'}
                                {/if}
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
                   placeholder="{l s="Paste your link here"}"{if isset($smarty.post.facebook)} value="{$smarty.post.facebook}"{/if}>
        </div>
        <div class="form-group social-group">
            <i class="social-link instagram"></i>
            <input type="text" name="instagram" id="instagram" class="form-control"
                   placeholder="{l s="Paste your link here"}"{if isset($smarty.post.instagram)} value="{$smarty.post.instagram}"{/if}>
        </div>
        <div class="form-group social-group">
            <i class="social-link twitter"></i>
            <input type="text" name="twitter" id="twitter" class="form-control"
                   placeholder="{l s="Paste your link here"}"{if isset($smarty.post.twitter)} value="{$smarty.post.twitter}"{/if}>
        </div>
        <div class="form-group social-group">
            <i class="social-link website"></i>
            <input type="text" name="website" id="website" class="form-control"
                   placeholder="{l s="Paste your link here"}"{if isset($smarty.post.website)} value="{$smarty.post.website}"{/if}>
        </div>
        <div class="form-group social-group">
            <i class="social-link email"></i>
            <input type="text" name="email" id="email" class="form-control"
                   placeholder="{l s="Paste your email here"}" value="{if isset($smarty.post.email) && $smarty.post.email}{$smarty.post.email}{elseif isset($email) && $email}{$email}{/if}">
        </div>
        <div class="form-group social-group">
            <i class="social-link password"></i>
            <input type="password" name="passwd" id="password_main" class="form-control"
                   placeholder="{l s="Enter your password hear"}">
        </div>
        <div class="form-group social-group">
            <i class="social-link password"></i>
            <input type="password" name="passwd_confirm" id="password_confirm" class="form-control"
                   placeholder="{l s="Confirm your password"}">
        </div>
        <input type="hidden" name="id" id="id" value="{if isset($smarty.post.id)}{$smarty.post.id}{elseif isset($id_billing) && $id_billing}{$id_billing}{/if}">
        <div class="clearfix"></div>
        <div class="registration-content-footer">
            <button class="btn btn-default" name="submitAccount">{l s="Continue"}</button>
            <p class="registration-content-footer-text">{l s="After your free 60 day trial, you’ll be charged $499/month. You can cancel anytime on your setting page
    By clicking “Continue you start free free trail & agreeing to autorize this recurring charge"}</p>
        </div>
    </form>
</div>