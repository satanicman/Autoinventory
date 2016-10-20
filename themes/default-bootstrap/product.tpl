{*
* 2007-2016 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{include file="$tpl_dir./errors.tpl"}
{if $errors|@count == 0}
    {if !isset($priceDisplayPrecision)}
        {assign var='priceDisplayPrecision' value=2}
    {/if}
    {if !$priceDisplay || $priceDisplay == 2}
        {assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, 6)}
        {assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
    {elseif $priceDisplay == 1}
        {assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, 6)}
        {assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
    {/if}
    <div itemscope itemtype="https://schema.org/Product">
        <meta itemprop="url" content="{$link->getProductLink($product)}">
        <div class="primary_block row">
            {if isset($adminActionDisplay) && $adminActionDisplay}
                <div id="admin-action" class="container">
                    <p class="alert alert-info">{l s='This product is not visible to your customers.'}
                        <input type="hidden" id="admin-action-product-id" value="{$product->id}"/>
                        <a id="publish_button" class="btn btn-default button button-small" href="#">
                            <span>{l s='Publish'}</span>
                        </a>
                        <a id="lnk_view" class="btn btn-default button button-small" href="#">
                            <span>{l s='Back'}</span>
                        </a>
                    </p>
                    <p id="admin-action-result"></p>
                </div>
            {/if}
            {if isset($confirmation) && $confirmation}
                <p class="confirmation">
                    {$confirmation}
                </p>
            {/if}
            <!-- left infos-->
            <div class="pb-left-column col-xs-12 col-sm-9 col-md-9">
                <div class="back"><a class="back-link">{l s="< Return to search results"}</a></div>
                <h1 itemprop="name" class="product-name">{$product->name|escape:'html':'UTF-8'}</h1>
                <div class="content_prices clearfix">
                    {if $product->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
                        <!-- prices -->
                        <div>
                            <p class="our_price_display" itemprop="offers" itemscope
                               itemtype="https://schema.org/Offer">{strip}
                                    {if $product->quantity > 0}
                                        <link itemprop="availability" href="https://schema.org/InStock"/>
                                    {/if}
                                    {if $priceDisplay >= 0 && $priceDisplay <= 2}
                                        <span id="our_price_display" class="price" itemprop="price"
                                              content="{$productPrice}">{convertPrice price=$productPrice|floatval}</span>
                                        {if $tax_enabled  && ((isset($display_tax_label) && $display_tax_label == 1) || !isset($display_tax_label))}
                                            {if $priceDisplay == 1} {l s='tax excl.'}{else} {l s='tax incl.'}{/if}
                                        {/if}
                                        <meta itemprop="priceCurrency" content="{$currency->iso_code}"/>
                                        {hook h="displayProductPriceBlock" product=$product type="price"}
                                    {/if}
                                {/strip}</p>
                            <p id="reduction_percent" {if $productPriceWithoutReduction <= 0 || !$product->specificPrice || $product->specificPrice.reduction_type != 'percentage'} style="display:none;"{/if}>{strip}
                                    <span id="reduction_percent_display">
										{if $product->specificPrice && $product->specificPrice.reduction_type == 'percentage'}-{$product->specificPrice.reduction*100}%{/if}
									</span>
                                {/strip}</p>
                            <p id="reduction_amount" {if $productPriceWithoutReduction <= 0 || !$product->specificPrice || $product->specificPrice.reduction_type != 'amount' || $product->specificPrice.reduction|floatval ==0} style="display:none"{/if}>{strip}
                                    <span id="reduction_amount_display">
									{if $product->specificPrice && $product->specificPrice.reduction_type == 'amount' && $product->specificPrice.reduction|floatval !=0}
                                        -{convertPrice price=$productPriceWithoutReduction|floatval-$productPrice|floatval}
                                    {/if}
									</span>
                                {/strip}</p>
                            <p id="old_price"{if (!$product->specificPrice || !$product->specificPrice.reduction)} class="hidden"{/if}>{strip}
                                    {if $priceDisplay >= 0 && $priceDisplay <= 2}
                                        {hook h="displayProductPriceBlock" product=$product type="old_price"}
                                        <span id="old_price_display"><span
                                                    class="price">{if $productPriceWithoutReduction > $productPrice}{convertPrice price=$productPriceWithoutReduction|floatval}{/if}</span>{if $productPriceWithoutReduction > $productPrice && $tax_enabled && $display_tax_label == 1} {if $priceDisplay == 1}{l s='tax excl.'}{else}{l s='tax incl.'}{/if}{/if}</span>
                                    {/if}
                                {/strip}</p>
                            {if $priceDisplay == 2}
                                <br/>
                                <span id="pretaxe_price">{strip}

                                        <span id="pretaxe_price_display">{convertPrice price=$product->getPrice(false, $smarty.const.NULL)}</span>
                                             {l s='tax excl.'}
									{/strip}</span>
                            {/if}
                        </div>
                        <!-- end prices -->
                        {if $packItems|@count && $productPrice < $product->getNoPackPrice()}
                            <p class="pack_price">{l s='Instead of'} <span
                                        style="text-decoration: line-through;">{convertPrice price=$product->getNoPackPrice()}</span>
                            </p>
                        {/if}
                        {if $product->ecotax != 0}
                            <p class="price-ecotax">{l s='Including'} <span
                                        id="ecotax_price_display">{if $priceDisplay == 2}{$ecotax_tax_exc|convertAndFormatPrice}{else}{$ecotax_tax_inc|convertAndFormatPrice}{/if}</span> {l s='for ecotax'}
                                {if $product->specificPrice && $product->specificPrice.reduction}
                                    <br/>
                                    {l s='(not impacted by the discount)'}
                                {/if}
                            </p>
                        {/if}
                        {if !empty($product->unity) && $product->unit_price_ratio > 0.000000}
                            {math equation="pprice / punit_price" pprice=$productPrice  punit_price=$product->unit_price_ratio assign=unit_price}
                            <p class="unit-price"><span
                                        id="unit_price_display">{convertPrice price=$unit_price}</span> {l s='per'} {$product->unity|escape:'html':'UTF-8'}
                            </p>
                            {hook h="displayProductPriceBlock" product=$product type="unit_price"}
                        {/if}
                    {/if} {*close if for show price*}
                    {hook h="displayProductPriceBlock" product=$product type="weight" hook_origin='product_sheet'}
                    {hook h="displayProductPriceBlock" product=$product type="after_price"}
                    <div class="clear"></div>
                </div> <!-- end content_prices -->
                <!-- product img-->
                {if isset($images) && count($images) > 0}
                    <!-- thumbnails -->
                    {if isset($images) && count($images) < 2}
                        {foreach from=$images item=image name=thumbnails}
                            {assign var=imageIds value="`$product->id`-`$image.id_image`"}
                            {if !empty($image.legend)}
                                {assign var=imageTitle value=$image.legend|escape:'html':'UTF-8'}
                            {else}
                                {assign var=imageTitle value=$product->name|escape:'html':'UTF-8'}
                            {/if}
                            <a{if $jqZoomEnabled && $have_image && !$content_only} href="javascript:void(0);" rel="{literal}{{/literal}gallery: 'gal1', smallimage: '{$link->getImageLink($product->link_rewrite, $imageIds, 'large_default')|escape:'html':'UTF-8'}',largeimage: '{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')|escape:'html':'UTF-8'}'{literal}}{/literal}"{else} href="{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')|escape:'html':'UTF-8'}" data-fancybox-group="other-views" class="fancybox{if $image.id_image == $cover.id_image} shown{/if}"{/if}
                                    title="{$imageTitle}">
                                <img class="img-responsive" id="thumb_{$image.id_image}"
                                     src="{$link->getImageLink($product->link_rewrite, $imageIds, 'large_default')|escape:'html':'UTF-8'}"
                                     alt="{$imageTitle}"
                                     title="{$imageTitle}" width="100%"
                                     itemprop="image"/>
                            </a>
                        {/foreach}
                    {else}
                        <div id="views_block" class="clearfix">
                            <div id="thumbs_list" class="flexslider">
                                <ul id="thumbs_list_frame" class="slides">
                                    {if isset($images)}
                                        {foreach from=$images item=image name=thumbnails}
                                            {assign var=imageIds value="`$product->id`-`$image.id_image`"}
                                            {if !empty($image.legend)}
                                                {assign var=imageTitle value=$image.legend|escape:'html':'UTF-8'}
                                            {else}
                                                {assign var=imageTitle value=$product->name|escape:'html':'UTF-8'}
                                            {/if}
                                            <li id="thumbnail_{$image.id_image}" data-thumb="{$link->getImageLink($product->link_rewrite, $imageIds, 'large_default')|escape:'html':'UTF-8'}">
                                                <a{if $jqZoomEnabled && $have_image && !$content_only} href="javascript:void(0);" rel="{literal}{{/literal}gallery: 'gal1', smallimage: '{$link->getImageLink($product->link_rewrite, $imageIds, 'large_default')|escape:'html':'UTF-8'}',largeimage: '{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')|escape:'html':'UTF-8'}'{literal}}{/literal}"{else} href="{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')|escape:'html':'UTF-8'}" data-fancybox-group="other-views" class="fancybox{if $image.id_image == $cover.id_image} shown{/if}"{/if}
                                                        title="{$imageTitle}">
                                                    <img class="img-responsive" id="thumb_{$image.id_image}"
                                                         src="{$link->getImageLink($product->link_rewrite, $imageIds, 'large_default')|escape:'html':'UTF-8'}"
                                                         alt="{$imageTitle}"
                                                         title="{$imageTitle}"{if isset($cartSize)} height="{$cartSize.height}" width="{$cartSize.width}"{/if}
                                                         itemprop="image"/>
                                                </a>
                                            </li>
                                        {/foreach}
                                    {/if}
                                </ul>
                            </div> <!-- end views-block -->
                        </div>
                    {/if}
                {else}
                    <span id="view_full_size">
						<img itemprop="image" src="{$img_prod_dir}{$lang_iso}-default-large_default.jpg" id="bigpic"
                             alt="" title="{$product->name|escape:'html':'UTF-8'}" width="{$largeSize.width}"
                             height="{$largeSize.height}"/>
					</span>
                {/if}
            </div> <!-- end pb-left-column -->
            <div class="pb-right-column col-xs-12 col-sm-3 col-md-3">
                <ul class="top_buttons product">
                    {if isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS}
                        {$HOOK_PRODUCT_ACTIONS}
                    {/if}
                    <li>
                        <a href="javascript:print();" class="print">
                            <i class="icon__print"></i>
                            {l s='Print'}
                        </a>
                    </li>
                </ul>
                <div class="manufacturer-block">
                    <h3 class="manufacturer-block-title product-title_medium">{l s="Manhattan Motorcars"}</h3>
                    <p>{l s="Ext 132 1538 325 (866)1"}</p>
                    <p>{l s="Elevent Ave 270, New York, NY 10001"}</p>
                    <p><a href="#" class="manufacturer-block-email">{l s="manhattanmotorcars.com"}</a></p>
                    <p class="manufacturer-block-detail"><a href="#">{l s="See dealers Inventory"}</a></p>
                </div>
                <form method="POST" action="{$link->getPageLink('ajax', true)|escape:'html':'UTF-8'}" class="product-question-block" id="product-question-block">
                    <h3 class="product-question-block-title product-title_medium">{l s="Ask a quastion"}</h3>
                    <div class="form-group">
                        <input type="text" class="form-control is_required validate" data-validate="isName" placeholder="{l s='Your Name'}" name="name">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control is_required validate" data-validate="isPhoneNumber" placeholder="{l s='Phone'}" name="phone">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control is_required validate" data-validate="isEmail" placeholder="{l s='Mail'}" name="mail">
                    </div>
                    <div class="form-group">
                        <textarea name="text" id="" cols="30" rows="10" class="form-control" placeholder="{l s='Enter your text here'}"></textarea>
                    </div>
                    <input type="hidden" name="id_product" value="{$product->id}">
                    <div class="product-question-block-buttons">
                        <button class="btn btn-default" name="askQuestion">{l s="Send"}</button>
                    </div>
                </form>
            </div> <!-- end pb-right-column-->
        </div> <!-- end primary_block -->
        <div class="product_bottom-block clearfix">
            <div class="product_bottom-block-title-wrap col-lg-3 col-sm-12">
                <h5 class="product_bottom-block-title">{l s="Vehicle Details"}</h5>
            </div>
            <div class="product_bottom-block-list col-lg-9 col-sm-12">
                <table class="product_bottom-block-table">
                    <tr>
                        <td class="label">{l s="Vehicle Details"}</td>
                        <td>{l s="Vehicle Details"}</td>
                        <td class="label">{l s="Engine"}</td>
                        <td>{l s="4.4L V8 32 GDI DCHC Twin Turbo"}</td>
                    </tr>
                    <tr>
                        <td class="label">{l s="Exterior Color"}</td>
                        <td>{l s="Exterior Color"}</td>
                        <td class="label">{l s="Transmission"}</td>
                        <td>{l s="8-Speed Automatic"}</td>
                    </tr>
                    <tr>
                        <td class="label">{l s="Interior Color"}</td>
                        <td>{l s="Interior Color"}</td>
                        <td class="label">{l s="Drivetrain"}</td>
                        <td>{l s="RWD"}</td>
                    </tr>
                    <tr>
                        <td class="label">{l s="Stock #"}</td>
                        <td>{l s="# Stock"}</td>
                        <td class="label">{l s="BodyStyle"}</td>
                        <td>{l s="Suv"}</td>
                    </tr>
                    <tr>
                        <td class="label">{l s="VIN"}</td>
                        <td>{l s="VIN"}</td>
                        <td class="label">{l s="Status"}</td>
                        <td>{l s="Used"}</td>
                    </tr>
                    <tr>
                        <td class="label">{l s="Fuel"}</td>
                        <td>{l s="Fuel"}</td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
                <div class="product_bottom-block-labels">
                    <img src="{$img_dir}/auto-check-product.jpg" alt="auto check">
                    <img src="{$img_dir}/carfax-product.jpg" alt="carfax">
                </div>
            </div>
        </div>
        <div class="product_bottom-block clearfix">
            <div class="product_bottom-block-title-wrap col-lg-3 col-sm-12">
                <h5 class="product_bottom-block-title">{l s="Features"}</h5>
            </div>
            <div class="product_bottom-block-list col-lg-9 col-sm-12">
                <table class="product_bottom-block-table">
                    <tr>
                        <td class="checked">{l s="Leathe interior surface"}</td>
                        <td class="checked">
                            <p class="label">{l s="Packages"}</p>
                            <p>{l s="Excutive Package"}</p>
                            <p>{l s="Competition Package"}</p>
                        </td>
                        <td class="checked">
                            <p class="label">{l s="Packages"}</p>
                            <p>{l s="Excutive Package"}</p>
                            <p>{l s="Competition Package"}</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="checked" colspan="3">{l s="Other feature"}</td>
                    </tr>
                    <tr>
                        <td class="checked">{l s="Some other feature"}</td>
                        <td class="checked">{l s="Navigation"}</td>
                        <td class="checked">{l s="Navigation"}</td>
                    </tr>
                    <tr>
                        <td class="checked">{l s="Night Vision"}</td>
                        <td class="checked">{l s="Night Vision"}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="product_bottom-block clearfix">
            <div class="product_bottom-block-title-wrap col-lg-3 col-sm-12">
                <h5 class="product_bottom-block-title">{l s="Special Features"}</h5>
            </div>
            <div class="product_bottom-block-list col-lg-9 col-sm-12">
                <table class="product_bottom-block-table">
                    <tr>
                        <td class="checked">{l s="Remote keyless entry"}</td>
                        <td class="checked">{l s="Remote keyless entry"}</td>
                    </tr>
                    <tr>
                        <td class="checked">{l s="Real-seat DVD player"}</td>
                        <td class="checked">{l s="Real-seat DVD player"}</td>
                    </tr>
                    <tr>
                        <td class="checked">{l s="Navigation"}</td>
                        <td class="checked">{l s="Navigation"}</td>
                    </tr>
                    <tr>
                        <td class="checked">{l s="Night Vision"}</td>
                        <td class="checked">{l s="Night Vision"}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="product_bottom-block clearfix">
            <div class="product_bottom-block-title-wrap col-lg-3 col-sm-12">
                <h5 class="product_bottom-block-title">{l s="Standart Equipment"}</h5>
            </div>
            <div class="product_bottom-block-list col-lg-9 col-sm-12">
                <table class="product_bottom-block-table">
                    <tr>
                        <td>{l s="1st and 2nd row curtain head airbags"}</td>
                        <td>{l s="1st and 2nd row curtain head airbags"}</td>
                    </tr>
                    <tr>
                        <td>{l s="Anti-theft system"}</td>
                        <td>{l s="Anti-theft system"}</td>
                    </tr>
                    <tr>
                        <td>{l s="Audio system memory card slot"}</td>
                        <td>{l s="Audio system memory card slot"}</td>
                    </tr>
                    <tr>
                        <td><a href="#">{l s="Show More"}</a></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="product_bottom-block clearfix">
            <div class="product_bottom-block-title-wrap col-lg-3 col-sm-12">
                <h5 class="product_bottom-block-title">{l s="Seller’s Notes"}</h5>
            </div>
            <div class="product_bottom-block-list col-lg-9 col-sm-12">
                <table class="product_bottom-block-table">
                    <tr>
                        <td class="description">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin tellus magna, tincidunt at commodo eget, tincidunt vel ex. Etiam tincidunt, lacus at malesuada fermentum, enim massa mollis dolor, non eleifend justo leo eget neque. Donec elementum posuere turpis, at vulputate odio lobortis at. Integer nisi felis, volutpat nec accumsan id, dapibus eget ligula. Nulla venenatis diam leo, ac dictum mauris ultrices eleifen. </td>
                    </tr>
                    <tr>
                        <td><a href="#">{l s="Show More"}</a></td>
                    </tr>
                </table>
            </div>
        </div>
        {*{if !$content_only}*}
            {*{if (isset($quantity_discounts) && count($quantity_discounts) > 0)}*}
                {*<!-- quantity discount -->*}
                {*<section class="page-product-box">*}
                    {*<h3 class="page-product-heading">{l s='Volume discounts'}</h3>*}
                    {*<div id="quantityDiscount">*}
                        {*<table class="std table-product-discounts">*}
                            {*<thead>*}
                            {*<tr>*}
                                {*<th>{l s='Quantity'}</th>*}
                                {*<th>{if $display_discount_price}{l s='Price'}{else}{l s='Discount'}{/if}</th>*}
                                {*<th>{l s='You Save'}</th>*}
                            {*</tr>*}
                            {*</thead>*}
                            {*<tbody>*}
                            {*{foreach from=$quantity_discounts item='quantity_discount' name='quantity_discounts'}*}
                                {*{if $quantity_discount.price >= 0 || $quantity_discount.reduction_type == 'amount'}*}
                                    {*{$realDiscountPrice=$productPriceWithoutReduction|floatval-$quantity_discount.real_value|floatval}*}
                                {*{else}*}
                                    {*{$realDiscountPrice=$productPriceWithoutReduction|floatval-($productPriceWithoutReduction*$quantity_discount.reduction)|floatval}*}
                                {*{/if}*}
                                {*<tr id="quantityDiscount_{$quantity_discount.id_product_attribute}"*}
                                    {*class="quantityDiscount_{$quantity_discount.id_product_attribute}"*}
                                    {*data-real-discount-value="{convertPrice price = $realDiscountPrice}"*}
                                    {*data-discount-type="{$quantity_discount.reduction_type}"*}
                                    {*data-discount="{$quantity_discount.real_value|floatval}"*}
                                    {*data-discount-quantity="{$quantity_discount.quantity|intval}">*}
                                    {*<td>*}
                                        {*{$quantity_discount.quantity|intval}*}
                                    {*</td>*}
                                    {*<td>*}
                                        {*{if $quantity_discount.price >= 0 || $quantity_discount.reduction_type == 'amount'}*}
                                            {*{if $display_discount_price}*}
                                                {*{if $quantity_discount.reduction_tax == 0 && !$quantity_discount.price}*}
                                                    {*{convertPrice price = $productPriceWithoutReduction|floatval-($productPriceWithoutReduction*$quantity_discount.reduction_with_tax)|floatval}*}
                                                {*{else}*}
                                                    {*{convertPrice price=$productPriceWithoutReduction|floatval-$quantity_discount.real_value|floatval}*}
                                                {*{/if}*}
                                            {*{else}*}
                                                {*{convertPrice price=$quantity_discount.real_value|floatval}*}
                                            {*{/if}*}
                                        {*{else}*}
                                            {*{if $display_discount_price}*}
                                                {*{if $quantity_discount.reduction_tax == 0}*}
                                                    {*{convertPrice price = $productPriceWithoutReduction|floatval-($productPriceWithoutReduction*$quantity_discount.reduction_with_tax)|floatval}*}
                                                {*{else}*}
                                                    {*{convertPrice price = $productPriceWithoutReduction|floatval-($productPriceWithoutReduction*$quantity_discount.reduction)|floatval}*}
                                                {*{/if}*}
                                            {*{else}*}
                                                {*{$quantity_discount.real_value|floatval}%*}
                                            {*{/if}*}
                                        {*{/if}*}
                                    {*</td>*}
                                    {*<td>*}
                                        {*<span>{l s='Up to'}</span>*}
                                        {*{if $quantity_discount.price >= 0 || $quantity_discount.reduction_type == 'amount'}*}
                                            {*{$discountPrice=$productPriceWithoutReduction|floatval-$quantity_discount.real_value|floatval}*}
                                        {*{else}*}
                                            {*{$discountPrice=$productPriceWithoutReduction|floatval-($productPriceWithoutReduction*$quantity_discount.reduction)|floatval}*}
                                        {*{/if}*}
                                        {*{$discountPrice=$discountPrice * $quantity_discount.quantity}*}
                                        {*{$qtyProductPrice=$productPriceWithoutReduction|floatval * $quantity_discount.quantity}*}
                                        {*{convertPrice price=$qtyProductPrice - $discountPrice}*}
                                    {*</td>*}
                                {*</tr>*}
                            {*{/foreach}*}
                            {*</tbody>*}
                        {*</table>*}
                    {*</div>*}
                {*</section>*}
            {*{/if}*}
            {*{if isset($features) && $features}*}
                {*<!-- Data sheet -->*}
                {*<section class="page-product-box">*}
                    {*<h3 class="page-product-heading">{l s='Data sheet'}</h3>*}
                    {*<table class="table-data-sheet">*}
                        {*{foreach from=$features item=feature}*}
                            {*<tr class="{cycle values="odd,even"}">*}
                                {*{if isset($feature.value)}*}
                                    {*<td>{$feature.name|escape:'html':'UTF-8'}</td>*}
                                    {*<td>{$feature.value|escape:'html':'UTF-8'}</td>*}
                                {*{/if}*}
                            {*</tr>*}
                        {*{/foreach}*}
                    {*</table>*}
                {*</section>*}
                {*<!--end Data sheet -->*}
            {*{/if}*}
            {*{if isset($product) && $product->description}*}
                {*<!-- More info -->*}
                {*<section class="page-product-box">*}
                    {*<h3 class="page-product-heading">{l s='More info'}</h3>*}
                    {*<!-- full description -->*}
                    {*<div class="rte">{$product->description}</div>*}
                {*</section>*}
                {*<!--end  More info -->*}
            {*{/if}*}
            {*{if isset($packItems) && $packItems|@count > 0}*}
                {*<section id="blockpack">*}
                    {*<h3 class="page-product-heading">{l s='Pack content'}</h3>*}
                    {*{include file="$tpl_dir./product-list.tpl" products=$packItems}*}
                {*</section>*}
            {*{/if}*}
            {*<!--HOOK_PRODUCT_TAB -->*}
            {*<section class="page-product-box">*}
                {*{$HOOK_PRODUCT_TAB}*}
                {*{if isset($HOOK_PRODUCT_TAB_CONTENT) && $HOOK_PRODUCT_TAB_CONTENT}{$HOOK_PRODUCT_TAB_CONTENT}{/if}*}
            {*</section>*}
            {*<!--end HOOK_PRODUCT_TAB -->*}
            {*{if isset($accessories) && $accessories}*}
                {*<!--Accessories -->*}
                {*<section class="page-product-box">*}
                    {*<h3 class="page-product-heading">{l s='Accessories'}</h3>*}
                    {*<div class="block products_block accessories-block clearfix">*}
                        {*<div class="block_content">*}
                            {*<ul id="bxslider" class="bxslider clearfix">*}
                                {*{foreach from=$accessories item=accessory name=accessories_list}*}
                                    {*{if ($accessory.allow_oosp || $accessory.quantity_all_versions > 0 || $accessory.quantity > 0) && $accessory.available_for_order && !isset($restricted_country_mode)}*}
                                        {*{assign var='accessoryLink' value=$link->getProductLink($accessory.id_product, $accessory.link_rewrite, $accessory.category)}*}
                                        {*<li class="item product-box ajax_block_product{if $smarty.foreach.accessories_list.first} first_item{elseif $smarty.foreach.accessories_list.last} last_item{else} item{/if} product_accessories_description">*}
                                            {*<div class="product_desc">*}
                                                {*<a href="{$accessoryLink|escape:'html':'UTF-8'}"*}
                                                   {*title="{$accessory.legend|escape:'html':'UTF-8'}"*}
                                                   {*class="product-image product_image">*}
                                                    {*<img class="lazyOwl"*}
                                                         {*src="{$link->getImageLink($accessory.link_rewrite, $accessory.id_image, 'home_default')|escape:'html':'UTF-8'}"*}
                                                         {*alt="{$accessory.legend|escape:'html':'UTF-8'}"*}
                                                         {*width="{$homeSize.width}" height="{$homeSize.height}"/>*}
                                                {*</a>*}
                                                {*<div class="block_description">*}
                                                    {*<a href="{$accessoryLink|escape:'html':'UTF-8'}"*}
                                                       {*title="{l s='More'}" class="product_description">*}
                                                        {*{$accessory.description_short|strip_tags|truncate:25:'...'}*}
                                                    {*</a>*}
                                                {*</div>*}
                                            {*</div>*}
                                            {*<div class="s_title_block">*}
                                                {*<h5 itemprop="name" class="product-name">*}
                                                    {*<a href="{$accessoryLink|escape:'html':'UTF-8'}">*}
                                                        {*{$accessory.name|truncate:20:'...':true|escape:'html':'UTF-8'}*}
                                                    {*</a>*}
                                                {*</h5>*}
                                                {*{if $accessory.show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}*}
                                                    {*<span class="price">*}
												{*{if $priceDisplay != 1}*}
                                                    {*{displayWtPrice p=$accessory.price}*}
                                                {*{else}*}
                                                    {*{displayWtPrice p=$accessory.price_tax_exc}*}
                                                {*{/if}*}
                                                        {*{hook h="displayProductPriceBlock" product=$accessory type="price"}*}
											{*</span>*}
                                                {*{/if}*}
                                                {*{hook h="displayProductPriceBlock" product=$accessory type="after_price"}*}
                                            {*</div>*}
                                            {*<div class="clearfix" style="margin-top:5px">*}
                                                {*{if !$PS_CATALOG_MODE && ($accessory.allow_oosp || $accessory.quantity > 0) && isset($add_prod_display) && $add_prod_display == 1}*}
                                                    {*<div class="no-print">*}
                                                        {*<a class="exclusive button ajax_add_to_cart_button"*}
                                                           {*href="{$link->getPageLink('cart', true, NULL, "qty=1&amp;id_product={$accessory.id_product|intval}&amp;token={$static_token}&amp;add")|escape:'html':'UTF-8'}"*}
                                                           {*data-id-product="{$accessory.id_product|intval}"*}
                                                           {*title="{l s='Add to cart'}">*}
                                                            {*<span>{l s='Add to cart'}</span>*}
                                                        {*</a>*}
                                                    {*</div>*}
                                                {*{/if}*}
                                            {*</div>*}
                                        {*</li>*}
                                    {*{/if}*}
                                {*{/foreach}*}
                            {*</ul>*}
                        {*</div>*}
                    {*</div>*}
                {*</section>*}
                {*<!--end Accessories -->*}
            {*{/if}*}
            {*{if isset($HOOK_PRODUCT_FOOTER) && $HOOK_PRODUCT_FOOTER}{$HOOK_PRODUCT_FOOTER}{/if}*}
            {*<!-- description & features -->*}
            {*{if (isset($product) && $product->description) || (isset($features) && $features) || (isset($accessories) && $accessories) || (isset($HOOK_PRODUCT_TAB) && $HOOK_PRODUCT_TAB) || (isset($attachments) && $attachments) || isset($product) && $product->customizable}*}
                {*{if isset($attachments) && $attachments}*}
                    {*<!--Download -->*}
                    {*<section class="page-product-box">*}
                        {*<h3 class="page-product-heading">{l s='Download'}</h3>*}
                        {*{foreach from=$attachments item=attachment name=attachements}*}
                            {*{if $smarty.foreach.attachements.iteration %3 == 1}<div class="row">{/if}*}
                            {*<div class="col-lg-4">*}
                                {*<h4>*}
                                    {*<a href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")|escape:'html':'UTF-8'}">{$attachment.name|escape:'html':'UTF-8'}</a>*}
                                {*</h4>*}
                                {*<p class="text-muted">{$attachment.description|escape:'html':'UTF-8'}</p>*}
                                {*<a class="btn btn-default btn-block"*}
                                   {*href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")|escape:'html':'UTF-8'}">*}
                                    {*<i class="icon-download"></i>*}
                                    {*{l s="Download"} ({Tools::formatBytes($attachment.file_size, 2)})*}
                                {*</a>*}
                                {*<hr/>*}
                            {*</div>*}
                            {*{if $smarty.foreach.attachements.iteration %3 == 0 || $smarty.foreach.attachements.last}</div>{/if}*}
                        {*{/foreach}*}
                    {*</section>*}
                    {*<!--end Download -->*}
                {*{/if}*}
                {*{if isset($product) && $product->customizable}*}
                    {*<!--Customization -->*}
                    {*<section class="page-product-box">*}
                        {*<h3 class="page-product-heading">{l s='Product customization'}</h3>*}
                        {*<!-- Customizable products -->*}
                        {*<form method="post" action="{$customizationFormTarget}" enctype="multipart/form-data"*}
                              {*id="customizationForm" class="clearfix">*}
                            {*<p class="infoCustomizable">*}
                                {*{l s='After saving your customized product, remember to add it to your cart.'}*}
                                {*{if $product->uploadable_files}*}
                                    {*<br/>*}
                                    {*{l s='Allowed file formats are: GIF, JPG, PNG'}{/if}*}
                            {*</p>*}
                            {*{if $product->uploadable_files|intval}*}
                                {*<div class="customizableProductsFile">*}
                                    {*<h5 class="product-heading-h5">{l s='Pictures'}</h5>*}
                                    {*<ul id="uploadable_files" class="clearfix">*}
                                        {*{counter start=0 assign='customizationField'}*}
                                        {*{foreach from=$customizationFields item='field' name='customizationFields'}*}
                                            {*{if $field.type == 0}*}
                                                {*<li class="customizationUploadLine{if $field.required} required{/if}">{assign var='key' value='pictures_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field}*}
                                                    {*{if isset($pictures.$key)}*}
                                                        {*<div class="customizationUploadBrowse">*}
                                                            {*<img src="{$pic_dir}{$pictures.$key}_small" alt=""/>*}
                                                            {*<a href="{$link->getProductDeletePictureLink($product, $field.id_customization_field)|escape:'html':'UTF-8'}"*}
                                                               {*title="{l s='Delete'}">*}
                                                                {*<img src="{$img_dir}icon/delete.gif"*}
                                                                     {*alt="{l s='Delete'}"*}
                                                                     {*class="customization_delete_icon" width="11"*}
                                                                     {*height="13"/>*}
                                                            {*</a>*}
                                                        {*</div>*}
                                                    {*{/if}*}
                                                    {*<div class="customizationUploadBrowse form-group">*}
                                                        {*<label class="customizationUploadBrowseDescription">*}
                                                            {*{if !empty($field.name)}*}
                                                                {*{$field.name}*}
                                                            {*{else}*}
                                                                {*{l s='Please select an image file from your computer'}*}
                                                            {*{/if}*}
                                                            {*{if $field.required}<sup>*</sup>{/if}*}
                                                        {*</label>*}
                                                        {*<input type="file" name="file{$field.id_customization_field}"*}
                                                               {*id="img{$customizationField}"*}
                                                               {*class="form-control customization_block_input {if isset($pictures.$key)}filled{/if}"/>*}
                                                    {*</div>*}
                                                {*</li>*}
                                                {*{counter}*}
                                            {*{/if}*}
                                        {*{/foreach}*}
                                    {*</ul>*}
                                {*</div>*}
                            {*{/if}*}
                            {*{if $product->text_fields|intval}*}
                                {*<div class="customizableProductsText">*}
                                    {*<h5 class="product-heading-h5">{l s='Text'}</h5>*}
                                    {*<ul id="text_fields">*}
                                        {*{counter start=0 assign='customizationField'}*}
                                        {*{foreach from=$customizationFields item='field' name='customizationFields'}*}
                                            {*{if $field.type == 1}*}
                                                {*<li class="customizationUploadLine{if $field.required} required{/if}">*}
                                                    {*<label for="textField{$customizationField}">*}
                                                        {*{assign var='key' value='textFields_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field}*}
                                                        {*{if !empty($field.name)}*}
                                                            {*{$field.name}*}
                                                        {*{/if}*}
                                                        {*{if $field.required}<sup>*</sup>{/if}*}
                                                    {*</label>*}
                                                    {*<textarea name="textField{$field.id_customization_field}"*}
                                                              {*class="form-control customization_block_input"*}
                                                              {*id="textField{$customizationField}" rows="3"*}
                                                              {*cols="20">{strip}*}
											{*{if isset($textFields.$key)}*}
                                                            {*{$textFields.$key|stripslashes}*}
                                                        {*{/if}*}
										{*{/strip}</textarea>*}
                                                {*</li>*}
                                                {*{counter}*}
                                            {*{/if}*}
                                        {*{/foreach}*}
                                    {*</ul>*}
                                {*</div>*}
                            {*{/if}*}
                            {*<p id="customizedDatas">*}
                                {*<input type="hidden" name="quantityBackup" id="quantityBackup" value=""/>*}
                                {*<input type="hidden" name="submitCustomizedDatas" value="1"/>*}
                                {*<button class="button btn btn-default button button-small" name="saveCustomization">*}
                                    {*<span>{l s='Save'}</span>*}
                                {*</button>*}
                                {*<span id="ajax-loader" class="unvisible">*}
							{*<img src="{$img_ps_dir}loader.gif" alt="loader"/>*}
						{*</span>*}
                            {*</p>*}
                        {*</form>*}
                        {*<p class="clear required"><sup>*</sup> {l s='required fields'}</p>*}
                    {*</section>*}
                    {*<!--end Customization -->*}
                {*{/if}*}
            {*{/if}*}
        {*{/if}*}
    </div>
    <!-- itemscope product wrapper -->
    {strip}
        {if isset($smarty.get.ad) && $smarty.get.ad}
            {addJsDefL name=ad}{$base_dir|cat:$smarty.get.ad|escape:'html':'UTF-8'}{/addJsDefL}
        {/if}
        {if isset($smarty.get.adtoken) && $smarty.get.adtoken}
            {addJsDefL name=adtoken}{$smarty.get.adtoken|escape:'html':'UTF-8'}{/addJsDefL}
        {/if}
        {addJsDef allowBuyWhenOutOfStock=$allow_oosp|boolval}
        {addJsDef availableNowValue=$product->available_now|escape:'quotes':'UTF-8'}
        {addJsDef availableLaterValue=$product->available_later|escape:'quotes':'UTF-8'}
        {addJsDef attribute_anchor_separator=$attribute_anchor_separator|escape:'quotes':'UTF-8'}
        {addJsDef attributesCombinations=$attributesCombinations}
        {addJsDef currentDate=$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}
        {if isset($combinations) && $combinations}
            {addJsDef combinations=$combinations}
            {addJsDef combinationsFromController=$combinations}
            {addJsDef displayDiscountPrice=$display_discount_price}
            {addJsDefL name='upToTxt'}{l s='Up to' js=1}{/addJsDefL}
        {/if}
        {if isset($combinationImages) && $combinationImages}
            {addJsDef combinationImages=$combinationImages}
        {/if}
        {addJsDef customizationId=$id_customization}
        {addJsDef customizationFields=$customizationFields}
        {addJsDef default_eco_tax=$product->ecotax|floatval}
        {addJsDef displayPrice=$priceDisplay|intval}
        {addJsDef ecotaxTax_rate=$ecotaxTax_rate|floatval}
        {if isset($cover.id_image_only)}
            {addJsDef idDefaultImage=$cover.id_image_only|intval}
        {else}
            {addJsDef idDefaultImage=0}
        {/if}
        {addJsDef img_ps_dir=$img_ps_dir}
        {addJsDef img_prod_dir=$img_prod_dir}
        {addJsDef id_product=$product->id|intval}
        {addJsDef jqZoomEnabled=$jqZoomEnabled|boolval}
        {addJsDef maxQuantityToAllowDisplayOfLastQuantityMessage=$last_qties|intval}
        {addJsDef minimalQuantity=$product->minimal_quantity|intval}
        {addJsDef noTaxForThisProduct=$no_tax|boolval}
        {if isset($customer_group_without_tax)}
            {addJsDef customerGroupWithoutTax=$customer_group_without_tax|boolval}
        {else}
            {addJsDef customerGroupWithoutTax=false}
        {/if}
        {if isset($group_reduction)}
            {addJsDef groupReduction=$group_reduction|floatval}
        {else}
            {addJsDef groupReduction=false}
        {/if}
        {addJsDef oosHookJsCodeFunctions=Array()}
        {addJsDef productHasAttributes=isset($groups)|boolval}
        {addJsDef productPriceTaxExcluded=($product->getPriceWithoutReduct(true)|default:'null' - $product->ecotax)|floatval}
        {addJsDef productPriceTaxIncluded=($product->getPriceWithoutReduct(false)|default:'null' - $product->ecotax * (1 + $ecotaxTax_rate / 100))|floatval}
        {addJsDef productBasePriceTaxExcluded=($product->getPrice(false, null, 6, null, false, false) - $product->ecotax)|floatval}
        {addJsDef productBasePriceTaxExcl=($product->getPrice(false, null, 6, null, false, false)|floatval)}
        {addJsDef productBasePriceTaxIncl=($product->getPrice(true, null, 6, null, false, false)|floatval)}
        {addJsDef productReference=$product->reference|escape:'html':'UTF-8'}
        {addJsDef productAvailableForOrder=$product->available_for_order|boolval}
        {addJsDef productPriceWithoutReduction=$productPriceWithoutReduction|floatval}
        {addJsDef productPrice=$productPrice|floatval}
        {addJsDef productUnitPriceRatio=$product->unit_price_ratio|floatval}
        {addJsDef productShowPrice=(!$PS_CATALOG_MODE && $product->show_price)|boolval}
        {addJsDef PS_CATALOG_MODE=$PS_CATALOG_MODE}
        {if $product->specificPrice && $product->specificPrice|@count}
            {addJsDef product_specific_price=$product->specificPrice}
        {else}
            {addJsDef product_specific_price=array()}
        {/if}
        {if $display_qties == 1 && $product->quantity}
            {addJsDef quantityAvailable=$product->quantity}
        {else}
            {addJsDef quantityAvailable=0}
        {/if}
        {addJsDef quantitiesDisplayAllowed=$display_qties|boolval}
        {if $product->specificPrice && $product->specificPrice.reduction && $product->specificPrice.reduction_type == 'percentage'}
            {addJsDef reduction_percent=$product->specificPrice.reduction*100|floatval}
        {else}
            {addJsDef reduction_percent=0}
        {/if}
        {if $product->specificPrice && $product->specificPrice.reduction && $product->specificPrice.reduction_type == 'amount'}
            {addJsDef reduction_price=$product->specificPrice.reduction|floatval}
        {else}
            {addJsDef reduction_price=0}
        {/if}
        {if $product->specificPrice && $product->specificPrice.price}
            {addJsDef specific_price=$product->specificPrice.price|floatval}
        {else}
            {addJsDef specific_price=0}
        {/if}
        {addJsDef specific_currency=($product->specificPrice && $product->specificPrice.id_currency)|boolval} {* TODO: remove if always false *}
        {addJsDef stock_management=$PS_STOCK_MANAGEMENT|intval}
        {addJsDef taxRate=$tax_rate|floatval}
        {addJsDefL name=doesntExist}{l s='This combination does not exist for this product. Please select another combination.' js=1}{/addJsDefL}
        {addJsDefL name=doesntExistNoMore}{l s='This product is no longer in stock' js=1}{/addJsDefL}
        {addJsDefL name=doesntExistNoMoreBut}{l s='with those attributes but is available with others.' js=1}{/addJsDefL}
        {addJsDefL name=fieldRequired}{l s='Please fill in all the required fields before saving your customization.' js=1}{/addJsDefL}
        {addJsDefL name=uploading_in_progress}{l s='Uploading in progress, please be patient.' js=1}{/addJsDefL}
        {addJsDefL name='product_fileDefaultHtml'}{l s='No file selected' js=1}{/addJsDefL}
        {addJsDefL name='product_fileButtonHtml'}{l s='Choose File' js=1}{/addJsDefL}
    {/strip}
{/if}
