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
	<ul{if isset($id) && $id} id="{$id}"{/if} class="product_list list{if isset($class) && $class} {$class}{/if}">
	{foreach from=$products item=product name=products}
		{math equation="(total%perLine)" total=$smarty.foreach.products.total perLine=$nbItemsPerLine assign=totModulo}
		{math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineTablet assign=totModuloTablet}
		{math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineMobile assign=totModuloMobile}
		{if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
		{if $totModuloTablet == 0}{assign var='totModuloTablet' value=$nbItemsPerLineTablet}{/if}
		{if $totModuloMobile == 0}{assign var='totModuloMobile' value=$nbItemsPerLineMobile}{/if}
		<li class="ajax_block_product">
			<div class="product-container clearfix" itemscope itemtype="https://schema.org/Product">
				<div class="left-block col-lg-4 col-md-4 col-sm-12">
					<div class="product-image-container">
						<a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
							<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
						</a>
                        <div class="functional-buttons">
                            {if isset($comparator_max_item) && $comparator_max_item}
                                <div class="compare">
                                    <a class="add_to_compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}"><span class="checkbox"></span>{l s='Compare'}</a>
                                </div>
                            {/if}
                            {hook h='displayProductListFunctionalButtons' product=$product}
                        </div>
					</div>
				</div>
				<div class="center-block col-lg-8 col-md-8 col-sm-12">
					<h5 itemprop="name" class="clearfix">
						{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
						<a class="product-name medium" href="{$product.link|escape:'html':'UTF-8'}"
						   title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
							{$product.name|truncate:45:'...'|escape:'html':'UTF-8'}
						</a>
					</h5>
					<div class="left col-lg-9 col-md-9 col-sm-12">
						<table class="product-features-table">
							{assign var=open value=0}
							{foreach from=$product.features item=feature name=features}
								{if ($smarty.foreach.features.index + 1) % 2}{assign var=open value=$open+1}<tr class="product-features-line">{/if}
								<td class="product-features-line-item product-features-line-name medium">{$feature.name}</td>
								<td class="product-features-line-item product-features-line-value">{$feature.name}</td>
								{if !($smarty.foreach.features.index + 1) % 2}{assign var=open value=$open-1}</tr>{/if}
							{/foreach}
							{if $open}
								<td colspan="2"></td>
								</tr>{/if}
						</table>
						<p class="manufacturer-contacts"><a href="#">Ford Lincoln of Smithtown 51 mi. Away</a><span
									class="manufacturer-contacts-phone medium">(800) 356 36 35</span></p>
						<div class="product-logos">
							<div class="product-logo speccial"><span>Special Features</span></div>
							<div class="product-logo carfax"><span>Carfax</span></div>
							<div class="product-logo auto"><img src="{$img_dir}/icon/check.png"
																alt="{l s="auto check"}"></div>
						</div>
					</div>
					<div class="right-block col-lg-3 col-md-3 col-sm-12">
						{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
							<div class="content_price">
								{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
									{hook h="displayProductPriceBlock" product=$product type='before_price'}
									<span class="price product-price">
												{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
											</span>
									{if $product.price_without_reduction > 0 && isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
										{hook h="displayProductPriceBlock" product=$product type="old_price"}
										<span class="old-price product-price">
													{displayWtPrice p=$product.price_without_reduction}
												</span>
										{hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}
										{if $product.specific_prices.reduction_type == 'percentage'}
											<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}
												%</span>
										{/if}
									{/if}
									{hook h="displayProductPriceBlock" product=$product type="price"}
									{hook h="displayProductPriceBlock" product=$product type="unit_price"}
									{hook h="displayProductPriceBlock" product=$product type='after_price'}
								{/if}
							</div>
						{/if}
						{if isset($product.miles) && $product.miles}
							<div class="miles product-miles">{$product.miles}/{l s="Miles"}</div>
						{/if}
					</div>
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
