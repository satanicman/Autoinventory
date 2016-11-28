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
{if isset($products) && $products}
	<ul class="display hidden-xs list-type">
		<li id="list">
			<a rel="nofollow" href="#" title="{l s='List'}">
			</a>
		</li>
		<li id="grid">
			<a rel="nofollow" href="#" title="{l s='Grid'}">
			</a>
		</li>
	</ul>
	{*define number of products per line in other page for desktop*}
	{if $page_name !='index' && $page_name !='product'}
		{assign var='nbItemsPerLine' value=3}
		{assign var='nbItemsPerLineTablet' value=2}
		{assign var='nbItemsPerLineMobile' value=3}
	{else}
		{assign var='nbItemsPerLine' value=4}
		{assign var='nbItemsPerLineTablet' value=3}
		{assign var='nbItemsPerLineMobile' value=2}
	{/if}
	{*define numbers of product per line in other page for tablet*}
	{assign var='nbLi' value=$products|@count}
	{math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
	{math equation="nbLi/nbItemsPerLineTablet" nbLi=$nbLi nbItemsPerLineTablet=$nbItemsPerLineTablet assign=nbLinesTablet}
	<!-- Products list -->
	<ul{if isset($id) && $id} id="{$id}"{/if} class="clearfix product_list grid row{if isset($class) && $class} {$class}{/if}">
	{foreach from=$products item=product name=products}
		{math equation="(total%perLine)" total=$smarty.foreach.products.total perLine=$nbItemsPerLine assign=totModulo}
		{math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineTablet assign=totModuloTablet}
		{math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineMobile assign=totModuloMobile}
		{if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
		{if $totModuloTablet == 0}{assign var='totModuloTablet' value=$nbItemsPerLineTablet}{/if}
		{if $totModuloMobile == 0}{assign var='totModuloMobile' value=$nbItemsPerLineMobile}{/if}
		<li class="ajax_block_product col-lg-4 col-md-6 clearfix">
			<div class="product-container clearfix" itemscope itemtype="https://schema.org/Product">
				<div class="left-block col-lg-3 col-md-4">
					<div class="product-image-container">
						<a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
							<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
						</a>
					</div>
				</div>
				<div class="center-block col-lg-9 col-md-8">
					<h5 itemprop="name" class="title-wrap">
						{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
						<a class="product-name medium" href="{$product.link|escape:'html':'UTF-8'}"
						   title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
							{$product.name|truncate:45:'...'|escape:'html':'UTF-8'}
							{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                                {if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
                                        {if !$priceDisplay} - {convertPrice price=$product.price}{else} - {convertPrice price=$product.price_tax_exc}{/if}
                                {/if}
							{/if}
						</a>
					</h5>
					<table class="myacc-features auto-table">
						{if isset($product.vin) && $product.vin}
						<tr class="myacc-feature">
							<td class="myacc-feature-name auto-table-name">{l s="VIN"}</td>
							<td class="myacc-feature-value auto-table-value">{$product.vin}</td>
						</tr>
						{/if}
						{if isset($product.stock) && $product.stock}
						<tr class="myacc-feature">
							<td class="myacc-feature-name auto-table-name">{l s="Stock"}</td>
							<td class="myacc-feature-value auto-table-value">{$product.stock}</td>
						</tr>
						{/if}
						{if isset($product.listing_type) && $product.listing_type}
						<tr class="myacc-feature">
							<td class="myacc-feature-name auto-table-name">{l s="Listing Type"}</td>
							<td class="myacc-feature-value auto-table-value">{$product.listing_type}</td>
						</tr>
						{/if}
						{if isset($product.expries) && $product.expries}
						<tr class="myacc-feature">
							<td class="myacc-feature-name auto-table-name">{l s="Expries"}</td>
							<td class="myacc-feature-value auto-table-value">{$product.expries}</td>
						</tr>
						{/if}
					</table>
					{if $status != 3}
						<div class="button-container">
							<a href="{$link->getPageLink('my-account', true, null, 'tab=list-a-car'|cat:'&id_product='|cat:$product.id_product)|escape:'html':'UTF-8'}" class="myacc-btn">{l s="Edit"}</a>
							<a href="{$link->getPageLink('my-account', true, null, 'tab=list-a-car'|cat:'&id_product='|cat:$product.id_product|cat:'&status=3')|escape:'html':'UTF-8'}" class="myacc-btn red">{l s="Delete"}</a>
						</div>
					{else}
						<div class="button-container">
							<a href="{$link->getPageLink('my-account', true, null, 'tab=list-a-car'|cat:'&id_product='|cat:$product.id_product|cat:'&status=-1')|escape:'html':'UTF-8'}" class="myacc-btn">{l s="Relist"}</a>
							<a href="{$link->getPageLink('my-account', true, null, 'tab=list-a-car'|cat:'&id_product='|cat:$product.id_product|cat:'&status=4')|escape:'html':'UTF-8'}" class="myacc-btn red">{l s="Delete"}</a>
						</div>
					{/if}
				</div>
			</div><!-- .product-container> -->
		</li>
	{/foreach}
	</ul>
{addJsDefL name=min_item}{l s='Please select at least one product' js=1}{/addJsDefL}
{addJsDefL name=max_item}{l s='You cannot add more than %d product(s) to the product comparison' sprintf=$comparator_max_item js=1}{/addJsDefL}
{addJsDef comparator_max_item=$comparator_max_item}
{addJsDef comparedProductsIds=$compared_products}
{/if}
