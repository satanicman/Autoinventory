{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    PrestaShop SA    <contact@prestashop.com>
* @copyright 2007-2015 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

{capture name=path}<a href="{$link->getPageLink('my-account.php', true)}">{l s='My account' mod='addprod'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='My products' mod='addprod'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<script type="text/javascript" src="{$modules_dir|escape:'htmlall':'UTF-8'}addprod/ui/js/jquery-ui-1.8.22.custom.min.js"></script>
<script type="text/javascript" src="{$modules_dir|escape:'htmlall':'UTF-8'}addprod/views/js/jquery-1.11.3.min.js"></script>
<script>
    var jq111 = jQuery.noConflict( true );
</script>
<link rel="stylesheet" type="text/css" href="{$modules_dir|escape:'htmlall':'UTF-8'}addprod/ui/css/ui-darkness/jquery-ui-1.8.22.custom.css" />

<script type="text/javascript">
        {if isset($ifGth1605) && ifGth1605}
            $(function() {
                $("#tabs").tabs();
                $("#tabs2").tabs();
            });
        {else}
            $n(function() {
                $n("#tabs").tabs();
                $n("#tabs2").tabs();
            });
        {/if}
        $(document).ready(function(){
            $('#is_virtual_product').click(function(){                
                if($(this).is(':checked')){
                    $('#virtualProductDiv').show();
                }else{
                    $('#virtualProductDiv').hide();
                }
            });
            $('[name="categories[]"]').change(function(){
                $("#defaultCategory option").remove();
                $("select[name='categories[]'] option").each(function(){
                    if($(this).is(':selected')){
                        $("#defaultCategory").append('<option value="' + $(this).val() + '">' + $(this).attr('attr-name') + '</option>');
                    }
                });
            });
            
        });
</script>

<style type="text/css">
	{literal}
	.ui-tabs-panel {padding: 4px !important;}
	{/literal}
        .idTabs {
            list-style-type: none !important;
            margin-top: 20px !important;
            padding: 0 5px !important;
            border-bottom: none !important;
        }
        .rte table{
            border: 1px #e5e6e7 solid;
            text-align: center;
            border-spacing: 1px;
            border-collapse: separate;
        }
        .rte td{
            border: 1px #e5e6e7 solid;
            border-spacing: 1px;
        }
</style>

<h1 class="block_header corners">{l s='Customer products' mod='addprod'}</h1>

{include file="$tpl_dir./errors.tpl"}
{if isset($eproduct->id) && !$upd_mod}
    <p class="warning alert alert-warning" style="padding:6px 12px;">{l s='After changing the product will be resent for moderation.' mod='addprod'}</p>
{/if}

{if $mess != ''}
    <p class="success alert alert-success">{l s=$mess mod='addprod'}{if $addInformation != ''} {l s=$addInformation mod='addprod'}{/if}</p>
{/if}


<ul class="idTabs ">
    {if $addprod_manufacturer_id}
    <li><a href="#idTab1" {if $activeTab eq 'adprod'}class="selected"{/if}>{l s='Add a product' mod='addprod'}</a></li>
    <li><a href="#idTab2" {if $activeTab eq 'product'}class="selected"{/if}>{l s='Your products' mod='addprod'}</a></li>
    <li><a href="#idTab3" {if $activeTab eq 'payment'}class="selected"{/if}>{l s='Payments' mod='addprod'}</a></li>
    <li><a href="#idTab4">{l s='Your information' mod='addprod'}</a></li>
    {else}
    <li><a href="#idTab0" class="selected">{l s='General  information' mod='addprod'}</a></li>
    {/if}
</ul>
{if $addprod_manufacturer_id}
    <div id="idTab1">
        
        {if isset($is16) && $is16}
            <script type="text/javascript" src="{$content_dir|escape:'htmlall':'UTF-8'}modules/addprod/views/js/tiny_mce/tiny_mce.js"></script>
        {else}
            <script type="text/javascript" src="{$content_dir|escape:'htmlall':'UTF-8'}js/tiny_mce/tiny_mce.js"></script>
        {/if}
	<script type="text/javascript">
		tinyMCE.init({ldelim}
			mode : "none",
			theme : "advanced",
			plugins : "safari,pagebreak,style,table,advimage,advlink,inlinepopups,media,contextmenu,paste,fullscreen",
			// Theme options
			theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,image,cleanup,code,,|,forecolor,backcolor",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,pagebreak,|,fullscreen,|,anchor",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : false,
			content_css : "{$css_dir|escape:'htmlall':'UTF-8'}tinymce.css",
			document_base_url : "{$content_dir|escape:'htmlall':'UTF-8'}",
			width: "525px",
			height: "auto",
			font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
			elements : "nourlconvert",
			convert_urls : false,
			language : "en"
						
		{rdelim});
		{foreach from=$lngs item=lang}
			tinyMCE.execCommand("mceAddControl", true, "description_{$lang.id_lang|intval}");
			tinyMCE.execCommand("mceAddControl", true, "man_description_{$lang.id_lang|intval}");
		{/foreach}

	</script>

        <div style='clear: both; width: 100%;'></div>
	<form method="post" action="{if isset($eproduct->id)}{$link->getModuleLink('addprod', 'user', ['edit' => $eproduct->id])}{else}{$link->getModuleLink('addprod', 'user')}{/if}" class="std" enctype="multipart/form-data">
	    <fieldset style="width:100%;" class="block corners">
		{if isset($eproduct->id)}<input name="product_id" type="hidden" value="{$eproduct->id}" />{/if}
                
		<div style="margin:12px;">
                    <div style="width: 18%; display: block; float: left;">
                        <p style="padding-bottom: 5px;" class="form-lbl">{l s='Categories' mod='addprod'}:</p>
                    </div>                    
                    <div>
                    {if isset($categories.children) && !empty($categories.children)}
                        <select multiple name='categories[]' title="{l s='Use the Ctrl key for multiple choose' mod='addprod'}">
                        {foreach from=$categories.children item=category}
                            <option class="categories_t" value="{$category.id}"  {if isset($post.categories) && in_array($category.id, $post.categories)} selected{else}{if isset($aProductCategoriesIds) && in_array($category.id, $aProductCategoriesIds)} selected{/if}{/if} attr-name='{$category.name|escape:'html':'UTF-8'}'>{$category.name|escape:'html':'UTF-8'}</option>                            
                            {if $category.children|@count > 0}{include file="$addprod_branch" categories=$category.children nsbp='&nbsp;&nbsp;&nbsp;&nbsp;'}{/if}
                        {/foreach}
                        </select>
                    {/if}
                    </div>
		</div>
                <div style="width: 100%; clear: both;"></div>
                <div style="margin:12px;">
		    <label for="defaultCategory" style="float: left; display: block; width: 150px;" class="form-lbl">{l s='Default category' mod='addprod'}:</label>
		    <select name="defaultCategory" id='defaultCategory'>
                        {if !empty($aProductCategories) && $aProductCategories}
                            {foreach from=$aProductCategories item=categoryT}
                                <option value="{$categoryT->id}" {if isset($post.defaultCategory) && $post.defaultCategory == $categoryT->id}selected{else}{if isset($eproduct->id) && $categoryT->id == $eproduct->id_category_default}selected{/if}{/if}>{$categoryT->name|escape:'html':'UTF-8'}</option>
                            {/foreach}
                        {/if}
		    </select>
		</div>
		<div style='clear: both; width: 100%;'></div>
            <div style="margin:12px; {if $vrt_mod} display: none; {/if}">
                <label for="is_virtual_product" style="float: left; width: 150px;" class="form-lbl">{l s='Virtual product' mod='addprod'}</label>
                <input type='checkbox' value='1' name="is_virtual_product" id='is_virtual_product' {if isset($isVirtualProduct) && $isVirtualProduct}checked="checked"{/if}>
            </div>
                {if isset($PS_TAX) && $PS_TAX}
                <div style="width: 100%; clear: both;"></div>
                <div style="margin:12px;">
		    <label for="tax_rule" style="float: left; display: block; width: 150px;" class="form-lbl">{l s='Tax Rule' mod='addprod'}:</label>
		    <select name="tax_rule" id='tax_rule'>
                        <option value="0">{l s='No Tax' mod='addprod'}</option>
                        {if !empty($aTaxRules) && $aTaxRules}
                            {foreach from=$aTaxRules item=a_tax}
                                <option value="{$a_tax.id_tax_rules_group}" {if isset($post.tax_rule) && $post.tax_rule == $a_tax.id_tax_rules_group}selected{else}{if isset($eproduct->id) && $a_tax.id_tax_rules_group == $eproduct->id_tax_rules_group}selected{/if}{/if}>{$a_tax.name|escape:'html':'UTF-8'}</option>
                            {/foreach}
                        {/if}
		    </select>
		</div>
                {/if}
		<div style="width: 100%; clear: both;"></div>
		<div style="margin:12px;">
		    <label for="price" style="float: left; display: block; width: 150px;" class="form-lbl">{l s='Price' mod='addprod'}:</label>
			<input type="text" name="price" class="form-txt form-small" value="{if isset($post.price) && $post.price}{$post.price}{else}{if isset($eproduct->price)}{$eproduct->price}{else}0{/if}{/if}" size="7" /> {$currency_sign}
		</div>
            <div style="clear: both; display: block;"></div>
            <div style="margin:12px; {if (isset($isVirtualProduct) && $isVirtualProduct) || !$PS_STOCK_MANAGEMENT}display: none;{/if}" class="field_quantity">
                <label for="quantity" style="float: left; display: block; width: 150px;" class="form-lbl" >{l s='Quantity' mod='addprod'}:</label>
                <input id="quantity" type="text" value="{if isset($smarty.post.quantity)}{$smarty.post.quantity}{elseif isset($eproduct->quantity)}{$eproduct->quantity}{else}1000{/if}" name="quantity" class="multi form-txt" size="7">
            </div>
           <div>
               <div style="clear: both; display: block;"></div>
               <div style="margin: 12px; {if isset($isVirtualProduct) && $isVirtualProduct}display: none;{/if}" class="field_carriers">
                   <label for="carriers" style="float: left; display: block; width: 150px;" class="form-lbl" >{l s='Carriers' mod='addprod'}:</label>
                   <select id="carriers" name="carriers[]" multiple title="{l s='Use the Ctrl key for multiple choose' mod='addprod'}">
                        {if isset($carriers_list) && $carriers_list}
                            {foreach from=$carriers_list item=carrier}
                                <option {if in_array($carrier['id_reference'], $carriers)}selected="selected"{/if} value="{$carrier.id_reference}">{$carrier.name}</option>
                            {/foreach}
                        {/if}
                   </select>
                   <div class="field_info">{l s='If no carrier selected, all carriers can be used to ship this product' mod='addprod'}</div>
               </div>
           </div>
		<div id="tabs">
                    
			<ul style="margin:12px;">
				{foreach from=$lngs item=lang}
				<li>
					<a href="#tabs-{$lang.id_lang|intval}"><img src="{$img_lang_dir|escape:'htmlall':'UTF-8'}{$lang.id_lang|intval}.jpg" alt="{$lang.iso_code|escape:'htmlall':'UTF-8'}" title="{$lang.name|escape:'htmlall':'UTF-8'}" /></a>
				</li>
				{/foreach}
                                
			</ul>
			{foreach from=$lngs item=lang}
                            <div id="tabs-{$lang.id_lang|intval}">
                                    <div style="margin:12px;">
                                        <label for="name_{$lang.id_lang|intval}" class="form-lbl" style="margin-left: -4px; width: auto;">{l s='Product name' mod='addprod'}:</label>
                                        <input type="text" class="form-txt" name="name_{$lang.id_lang|intval}" value="{if isset($lang.posted_name)}{$lang.posted_name}{else}{if isset($lang.product->name)}{$lang.product->name|escape:'htmlall':'UTF-8'|stripslashes}{/if}{/if}" placeholder="{l s='Product name' mod='addprod'}" size="48" />
                                    </div>
                                    <div>
                                        <p  style="margin:8px;">{l s='Short description' mod='addprod'}:</p>
                                        <textarea id="description_short_{$lang.id_lang|intval}" name="description_short_{$lang.id_lang}" rows="5" cols="65" placeholder="{l s='Short description' mod='addprod'}">{if isset($lang.posted_s_descr)}{$lang.posted_s_descr}{else}{if isset($lang.product->description_short)}{$lang.product->description_short|escape:'htmlall':'UTF-8'|stripslashes}{/if}{/if}</textarea>
                                    </div>
                                    <div style="width:100%;">
                                        <p>{l s='Full description' mod='addprod'}:</p>
                                        <textarea id="description_{$lang.id_lang|intval}" name="description_{$lang.id_lang|intval}" rows="10" cols="45">{if isset($lang.posted_description)}{$lang.posted_description}{else}{if isset($lang.product->description)}{$lang.product->description|escape:'htmlall':'UTF-8'|stripslashes}{/if}{/if}</textarea>
                                    </div>
                            </div>
			{/foreach}

			<div style="margin:12px;">
	{if isset($eimages)&&sizeof($eimages)}
		<!-- accessories -->
		<ul id="accessories">
					{foreach from=$eimages item=eimage}
						<li>
							{assign var=imageIds value="`$eproduct->id`-`$eimage.id_image`"}
							<img src="{$link->getImageLink($eproduct->link_rewrite, $imageIds, 'medium_default')}" width="{if isset($mediumSize.width) && $mediumSize.width}{$mediumSize.width|intval}{/if}" height="{if isset($mediumSize.height) && $mediumSize.height}{$mediumSize.height|intval}{/if}" />
<div class="hover_descr">
    <h5><a href="{$link->getModuleLink('addprod', 'user', ['delimg' => $eimage.id_image, 'edit' => $eproduct->id])|escape:'htmlall':'UTF-8'} ">{l s='Delete' mod='addprod'}</a></h5>
</div>
						</li>
					{/foreach}
		</ul>
		<br class="clear"/>
	{/if}
		{if isset($filename) && $filename != ''}
			<div style='clear:both;'></div>
			<div>
		<p>{l s='Product file' mod='addprod'}: <b>{$filename|escape:'htmlall':'UTF-8'}</b></p><br>
		<p class="warning alert alert-warning" style="padding:6px 12px;">{l s='Do not upload the file if you want to leave it unchanged.' mod='addprod'}</p>
			</div>
		{/if}
		</div>
		<div style='clear:both;'></div>
		<div style="margin:12px;">
		    <label for="image_product" style='float: left; width: 150px;' class="form-lbl">{l s='Image' mod='addprod'}</label>
                    <div style="width: 300px;display: inline;">
                        <input name="image_product[]" type="file" class="multi form-txt" />
                    </div>
		</div>
		<div style="margin:12px; {if isset($isVirtualProduct) && !$isVirtualProduct} display: none; {/if}" id='virtualProductDiv' >
                    <label for="virtual_product_file" style="float: left; width: 150px;" class="form-lbl">{l s='Product file' mod='addprod'}</label>
		    <input type="file" name="virtual_product_file" class="form-txt" />
		</div>
		<div style="margin:12px;">
		    <br/>
		<input type="submit" class="button_large" name="submitProduct" value="{l s='Submit' mod='addprod'}" />
                {if isset($eproduct->id)}
                    <a href='{$link->getModuleLink('addprod', 'user')|escape:'htmlall':'UTF-8'}' class="button_large">{l s='Cancel' mod='addprod'}</a>
                {/if}
		</div>
		</div>		
	    </fieldset>
	</form>
    </div>

    <div id="idTab2">
        <div style="clear: both; width: 100%; height: 15px;"></div>
	<div class="block rte">
	{if $products}
	<table class="table" style="width: 100%;">
	    <thead>
		<tr>
		    <td width="40%"><b>{l s='Product name' mod='addprod'}</b></td>
		    <td width="15%"><b>{l s='Price' mod='addprod'}</b></td>
		    <td width="10%"><b>{l s='Sold' mod='addprod'}</b></td>
		    <td width="20%"><b>{l s='Sum' mod='addprod'}</b></td>
		    <td width="15%"><b>{l s='Actions' mod='addprod'}</b></td>
		</tr>
	    </thead>
	    <tbody>
	    {foreach from=$products item=product}
            {if $product.active}
            <tr>
                <td width="40%"><a href="{$link->getProductLink($product.id_product,$product.link_rewrite)|escape:'htmlall':'UTF-8'}" target=new>{$product.name|escape:'htmlall':'UTF-8'}</a></td>
                <td width="15%">{$product.price|escape:'htmlall':'UTF-8'}</td>
                <td width="10%">{$product.sold_q|escape:'htmlall':'UTF-8'}</td>
                <td width="20%">{$product.sold_total|escape:'htmlall':'UTF-8'}</td>
                <td width="15%">
                            <a href="{$link->getModuleLink('addprod', 'user', ['edit' => $product.id_product])|escape:'htmlall':'UTF-8'}"><img src="{$img_ps_dir|escape:'htmlall':'UTF-8'}admin/edit.gif" /></a>
                            <a href="{$link->getModuleLink('addprod', 'user', ['delete' => $product.id_product])|escape:'htmlall':'UTF-8'}"><img src="{$img_ps_dir|escape:'htmlall':'UTF-8'}admin/delete.gif" /></a>
                </td>
            </tr>
            {else}
            <tr>
                <td width="40%">{$product.name|escape:'htmlall':'UTF-8'}</td>
                <td width="15%">{$product.price|escape:'htmlall':'UTF-8'}</td>
                <td width="10%">-</td>
                <td width="20%">-</td>
                <td width="15%">
                   <span class="on_moderation">{l s='On moderation' mod='addprod'}</span>
                </td>
            </tr>
            {/if}
	    {/foreach}
	    </tbody>
	    <tfoot>
		<tr>
		    <td width="40%"><b>{l s='Total: ' mod='addprod'}</b></td>
		    <td width="15%"> </td>
		    <td width="10%">{$total_q|escape:'htmlall':'UTF-8'}</td>
		    <td width="20%"><b>{$total_summ|escape:'htmlall':'UTF-8'}</b></td>
		    <td width="15%"> </td>
		</tr>
	    </tfoot>
	</table>
	{else}
            <div style="clear: both; width: 100%;"></div>
	    <div class="warning alert alert-warning">{l s='You have no products!' mod='addprod'}</div>
	{/if}
	</div>
    </div>

    <div id="idTab3">
	<div class="rte">
            <div style="clear: both; width: 100%;"></div>            
	    <p class="info" style="padding:12px; margin-bottom: -20px;">
                {l s='Author rewards' mod='addprod'}: <b>{$rewards|escape:'htmlall':'UTF-8'}</b>%<br/>
                {l s='Your balance is: ' mod='addprod'} <b>{$ballance|escape:'htmlall':'UTF-8'}</b>
            </p>
	<form method="post" action="{$link->getModuleLink('addprod', 'user')|escape:'htmlall':'UTF-8'}" class="std">
	    <fieldset class="block corners"  style="padding:0px 12px 12px 12px;">
		<label style="width:100%;"><b>{l s='Withdraw query' mod='addprod'}</b></label></br>
		<p>
		    <label for="summ" class="form-lbl" style='display: block; float: left; width: 120px;'>{l s='Amount' mod='addprod'}:</label>
		    <input type="text" class="form-txt" name="summ" placeholder="{l s='Amount' mod='addprod'}" size="7" /> {$currency_sign|escape:'htmlall':'UTF-8'}
		</p>
		<p>
		    <label for="message" style='display: block; float: left; width: 120px;' class="form-lbl">{l s='Comment' mod='addprod'}:</label>
		    <textarea name="message" rows="5" cols="45" placeholder="{l s='Comment' mod='addprod'}" ></textarea>
		</p>
		<input type="submit" class="exclusive" name="submitPayment" value="{l s='Order' mod='addprod'}" />
	    </fieldset>
	</form>
    {if isset($payments) && count($payments)}
	<p><b>{l s='Operation history' mod='addprod'}</b></p>
	<table class="table" style="width: 100%;">
	    <thead>
		<tr>
		    <td width="20%"><b>{l s='Operation' mod='addprod'}</b></td>
		    <td width="35%"><b>{l s='Comment' mod='addprod'}</b></td>
		    <td width="20%"><b>{l s='Amount' mod='addprod'}</b></td>
		    <td width="25%"><b>{l s='Date' mod='addprod'}</b></td>
		</tr>
	    </thead>
	    <tbody>
	    {foreach from=$payments item=payment}
		<tr>
		    <td>
                {if $payment.status==1}
                    {l s='Query' mod='addprod'}
                {elseif $payment.status==2}
                    {l s='Payment' mod='addprod'}
                {elseif $payment.status==3}
                    {l s='BID' mod='addprod'}
                {elseif $payment.status==4}
                    {l s='Up Account' mod='addprod'}
                {elseif $payment.status==5}
                    {l s='Refund' mod='addprod'}
                {else}
                    {l s='Sale' mod='addprod'} {if !$payment.is_virtual}<a href="#" class="viewOrderInfo"><img src="{$addprod_img_dir}card_address.png" alt="{l s='See details...' mod='addprod'}" title="{l s='See details...' mod='addprod'}"/></a>
                    <div style="display: none;" class="order_info">
                        <a class="close_oi" href="#">×</a>
                        <div class="title_oi">{l s='Customer address:' mod='addprod'}</div>
                        {$payment.address|nl2br|escape:'quotes':'UTF-8'}
                    </div>
                    {/if}
                {/if}
            </td>
		    <td>{$payment.description|escape:'htmlall':'UTF-8'}</td>
		    <td>{$payment.summ|escape:'htmlall':'UTF-8'}</td>
		    <td>{$payment.date_upd|escape:'htmlall':'UTF-8'}</td>
		</tr>
	    {/foreach}
	    </tbody>
	</table>
    {else}
        <div class="warning alert alert-warning">{l s='Your history is empty!' mod='addprod'}</div>
    {/if}
	</div>
</div>
    <script>
        if (typeof $.fn.setCenterPositionAbsoluteBlock == 'undefined')
            $.fn.setCenterPositionAbsoluteBlock = function ()
            {
                var offsetElemTop = 20;
                //var scrollTop = $(document).scrollTop();
                var scrollTop = 0;
                var elemWidth = $(this).width();
                var windowWidth = $(window).width();
                $(this).css({
                    top: ($(this).height() > $(window).height() ? scrollTop + offsetElemTop : scrollTop + (($(window).height()-$(this).height())/2)),
                    left: ((windowWidth-elemWidth)/2)
                });
            };
        $('.viewOrderInfo').live('click', function (e) {
            e.preventDefault();
            $('body').prepend('<div class="stage_order_info"></div>');
            $('.stage_order_info').html($(this).next().clone().show());
            $('.order_info').setCenterPositionAbsoluteBlock();
        });
        $('.stage_order_info, .close_oi').live('click', function (e) {
            e.preventDefault();
            $('.stage_order_info').remove();
        });
        $(window).resize(function () {
            $('.order_info').setCenterPositionAbsoluteBlock();
        });
    </script>

    <div id="idTab4">
        <label for="name">{l s='Name/title' mod='addprod'}</label>
        <div>
            <input value="{$manufacturer->name}" type="text" id="name" name="name"/>
        </div>
        <div id="tabs2">
            <ul style="margin:12px;">
                {foreach from=$lngs item=lang}
                    <li>
                        <a href="#tabs-{$lang.id_lang|intval}"><img src="{$img_lang_dir|escape:'htmlall':'UTF-8'}{$lang.id_lang|intval}.jpg" alt="{$lang.iso_code|escape:'htmlall':'UTF-8'}" title="{$lang.name|escape:'htmlall':'UTF-8'}" /></a>
                    </li>
                {/foreach}

            </ul>
            {foreach from=$lngs item=lang}
            <div id="tabs-{$lang.id_lang|intval}">
                <label for="description_{$lang.id_lang|intval}">{l s='About you' mod='addprod'}</label>
                <div>
                    <textarea name="description[{$lang.id_lang|intval}]" id="man_description_{$lang.id_lang|intval}" cols="30" rows="10">{$manufacturer->description[$lang.id_lang]}</textarea>
                </div>
            </div>
            {/foreach}
         </div>
        <label for="logo">{l s='Logo' mod='addprod'}</label>
        <div>
            <input type="file" id="logo" name="logo"/>
            <div class="preview_logo">
                {if file_exists("`$smarty.const._PS_MANU_IMG_DIR_``$manufacturer->id`.jpg")}
                    <img src="{$smarty.const._PS_IMG_}/m/{$manufacturer->id}.jpg" />
                {/if}
            </div>
        </div>
        <br>
        <div>
            <input id="saveManufacturerInfo" class="button_large" type="button" value="{l s='Save' mod='addprod'}">
        </div>
    </div>
    <script>
        var file_reader = new FileReader();
        file_reader.onloadend = function (r) {
            $('.preview_logo').html('<img src="'+ r.target.result+'">');
        };
        $('#logo').live('change', function () {
            file_reader.readAsDataURL($(this).prop('files')[0]);
        });

        $('#saveManufacturerInfo').live('click', function () {
            tinyMCE.triggerSave();
            $('.success, .error').remove();
            var form_data = new FormData();
            $('#idTab4').find('input[type=text], textarea').each(function () {
                form_data.append($(this).attr('name'), $(this).val());
            });
            form_data.append('logo', $('#logo').get(0).files[0]);
            form_data.append('ajax', true);
            form_data.append('saveManufacturerInfo', true);
            jq111.ajax({
                url: document.location.href,
                type: 'POST',
                dataType: 'json',
                processData: false,
                contentType: false,
                data: form_data,
                success: function (r) {
                    if (r.hasError)
                    {
                        $('.block_header.corners').after('<div class="error alert alert-danger">'+ r.errors.join('<br>')+'</div>');
                    }
                    else
                    {
                        $('.block_header.corners').after('<div class="success alert alert-success">'+ r.message +'</div>');
                    }
                }
            });
        });
    </script>
{else}
    <div id="idTab0">
        <form method="POST" class="form_agree" action="{$smarty.server.REQUEST_URI|escape:'quotes':'UTF-8'}">
            {l s='Do you want to sell in our store?' mod='addprod'}
            {if $cms}
            <br>
            <br>
            <input type="checkbox" name="agree">&nbsp;{l s='I have read and agree to the' mod='addprod'} <a class="showPopup" href="#showPopup">{l s='"Terms of Service"' mod='addprod'}</a>
            <div class="hidden">
                <div id="showPopup" class="rte">
                    {$cms->content|escape:'quotes':'UTF-8'}
                </div>
            </div>
            <script>
                $(function () {
                    $('.showPopup').fancybox({
                        width: '600px'
                    })
                    $('.form_agree').submit(function () {
                        if (!$('[name=agree]').is(':checked'))
                        {
                            alert('{l s='Please read the Terms of Service anf confirm your agreement' mod='addprod'}');
                            return false;
                        }
                    });
                });
            </script>
            {/if}
            <br>
            <br>
            <input value="&nbsp;{l s='Become a seller >>' mod='addprod'}&nbsp;" class="btn btn-default button button-small exclusive" name="submitAgree" type="submit">
        </form>
    </div>
{/if}
            
{if isset($is16) && $is16}         
      <ul class="footer_links clearfix" style="margin-top: 20px;">
        <li><a class="btn btn-default button button-small" href="{$link->getPageLink('my-account.php', true)|escape:'htmlall':'UTF-8'}" title="{l s='Back to Your Account' mod='addprod'}"><span><i class="icon-chevron-left"></i>{l s='Back to Your Account' mod='addprod'}</span></a></li>
        <li><a class="btn btn-default button button-small" href="{$base_dir|escape:'htmlall':'UTF-8'}" title="{l s='Home' mod='addprod'}"><span><i class="icon-chevron-left"></i>{l s='Home' mod='addprod'}</span></a></li>
    </ul>      
{else}
    <ul class="footer_links">
        <li><a href="{$link->getPageLink('my-account.php', true)|escape:'htmlall':'UTF-8'}"><img src="{$img_dir|escape:'htmlall':'UTF-8'}icon/my-account.gif" alt="" class="icon" /></a><a href="{$link->getPageLink('my-account.php', true)|escape:'htmlall':'UTF-8'}">{l s='Back to Your Account' mod='addprod'}</a></li>
        <li><a href="{$base_dir|escape:'htmlall':'UTF-8'}"><img src="{$img_dir|escape:'htmlall':'UTF-8'}icon/home.gif" alt="" class="icon" /></a><a href="{$base_dir|escape:'htmlall':'UTF-8'}">{l s='Home' mod='addprod'}</a></li>
    </ul>
{/if}
{if $PS_STOCK_MANAGEMENT}
<script>
    $('[name=is_virtual_product]').change(function () {
        if ($(this).is(':checked'))
            $('.field_quantity, .field_carriers').hide();
        else
            $('.field_quantity, .field_carriers').show();
    });
    $('[name=is_virtual_product]').trigger('change');
</script>
{else}
    <script>
        $('[name=is_virtual_product]').change(function () {
            if ($(this).is(':checked'))
                $('.field_carriers').hide();
            else
                $('.field_carriers').show();
        });
        $('[name=is_virtual_product]').trigger('change');
    </script>
{/if}