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

{if !isset($errors) OR !sizeof($errors)}
	<div class="manufacturer">
		{if $category->id != 14}
			<div class="manufacturer-top">
				<div class="manufacturer-top-img">
					<img src="{$link->getCatImageLink($category->link_rewrite, $category->id_image, 'category_default')|escape:'html':'UTF-8'}" alt="{$category->name}"/>
					{if (isset($category->customer->facebook) && $category->customer->facebook) || (isset($category->customer->instagram) && $category->customer->instagram) || (isset($category->customer->twitter) && $category->customer->twitter)}
						<div class="social">
							<ul class="social-list">
								{if isset($category->customer->facebook) && $category->customer->facebook}
									<li><a href="{$category->customer->facebook}" class="social-link facebook"><span>{l s="Facebook"}</span></a></li>
								{/if}
								{if isset($category->customer->instagram) && $category->customer->instagram}
									<li><a href="{$category->customer->instagram}" class="social-link instagram"><span>{l s="Instagram"}</span></a></li>
								{/if}
								{if isset($category->customer->twitter) && $category->customer->twitter}
									<li><a href="{$category->customer->twitter}" class="social-link twitter"><span>{l s="Twitter"}</span></a></li>
								{/if}
							</ul>
						</div>
					{/if}
				</div>
				<div class="manufacturer-top-description">
					<h1 class="header main medium">{$category->name|escape:'html':'UTF-8'}</h1>
					<table class="manufacturer-table">
						{if !empty($category->address->address1)}
							<tr>
								<td class="manufacturer-table-label medium">{l s="Address"}</td>
								<td>{$category->address->address1}</td>
							</tr>
						{/if}
						{if !empty($category->customer->business_description)}
							<tr>
								<td class="manufacturer-table-label medium">{l s="Description"}</td>
								<td>{$category->customer->business_description}</td>
							</tr>
						{/if}
						<tr>
							<td class="manufacturer-table-label medium">{l s="Products"}</td>
							<td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin tellus magna, tincidunt at
								commodo eget, tincidunt vel ex.
							</td>
						</tr>
						{if isset($category->phones) && !empty($category->phones)}
						<tr>
							<td class="manufacturer-table-label medium">{l s="Phone"}</td>
							<td>
								{foreach from=$category->phones item=phone}
									<span><b>{$phone.type|capitalize}</b> {if $phone.ext}{$phone.ext}-{/if}{$phone.phone}</span>
								{/foreach}
							</td>
						</tr>
						{/if}
						{if !empty($category->offers)}
						<tr>
							<td class="manufacturer-table-label medium">{l s="Offers"}</td>
							<td>
								{foreach from=$category->offers item=offer}
									<a href="#" class="medium" onclick="return false;">{$offer.name}</a>
								{/foreach}
							</td>
						</tr>
						{/if}
						{if $category->customer->site}
						<tr>
							<td class="manufacturer-table-label medium">{l s="Website"}</td>
							<td>
								<p>{$category->customer->site}</p>
							</td>
						</tr>
						{/if}
					</table>
				</div>
				<div class="manufacturer-top-hours">
					<h3 class="header sub medium">{l s="Business Hours"}</h3>

					<ul class="nav nav-tabs no-border">
						{if $category->time.showroom}
							<li class="active"><a data-toggle="tab" href="#sales">{l s="Sales"}</a></li>
						{/if}
						{if $category->time.service}
							<li><a data-toggle="tab" href="#service">{l s="Service"}</a></li>
						{/if}
					</ul>

					<div class="tab-content">
						{if $category->time.showroom}
							{assign var=showroom value=$category->time.showroom}
							<div id="sales" class="tab-pane fade in active">
								<table>
									<tr>
										<td>{l s="Monday"}</td>
										<td>
											{if $showroom.monday.closed}
												{l s="Closed"}
											{else}
												{$showroom.monday.from} - {$showroom.monday.to}
											{/if}
										</td>
									</tr>
									<tr>
										<td>{l s="Tuesday"}</td>
										<td>
											{if $showroom.tuesday.closed}
												{l s="Closed"}
											{else}
												{$showroom.tuesday.from} - {$showroom.tuesday.to}
											{/if}
										</td>
									</tr>
									<tr>
										<td>{l s="Wednesday"}</td>
										<td>
											{if $showroom.wednesday.closed}
												{l s="Closed"}
											{else}
												{$showroom.wednesday.from} - {$showroom.wednesday.to}
											{/if}
										</td>
									</tr>
									<tr>
										<td>{l s="Thursday"}</td>
										<td>
											{if $showroom.thursday.closed}
												{l s="Closed"}
											{else}
												{$showroom.thursday.from} - {$showroom.thursday.to}
											{/if}
										</td>
									</tr>
									<tr>
										<td>{l s="Friday"}</td>
										<td>
											{if $showroom.friday.closed}
												{l s="Closed"}
											{else}
												{$showroom.friday.from} - {$showroom.friday.to}
											{/if}
										</td>
									</tr>
									<tr>
										<td>{l s="Saturday"}</td>
										<td>
											{if $showroom.saturday.closed}
												{l s="Closed"}
											{else}
												{$showroom.saturday.from} - {$showroom.saturday.to}
											{/if}
										</td>
									</tr>
									<tr>
										<td>{l s="Sunday"}</td>
										<td>
											{if $showroom.sunday.closed}
												{l s="Closed"}
											{else}
												{$showroom.sunday.from} - {$showroom.sunday.to}
											{/if}
										</td>
									</tr>
								</table>
							</div>
						{/if}
						{if $category->time.service}
							{assign var=service value=$category->time.service}
							<div id="sales" class="tab-pane fade in">
								<table>
									<tr>
										<td>{l s="Monday"}</td>
										<td>
											{if $service.monday.closed}
												{l s="Closed"}
											{else}
												{$service.monday.from} - {$service.monday.to}
											{/if}
										</td>
									</tr>
									<tr>
										<td>{l s="Tuesday"}</td>
										<td>
											{if $service.tuesday.closed}
												{l s="Closed"}
											{else}
												{$service.tuesday.from} - {$service.tuesday.to}
											{/if}
										</td>
									</tr>
									<tr>
										<td>{l s="Wednesday"}</td>
										<td>
											{if $service.wednesday.closed}
												{l s="Closed"}
											{else}
												{$service.wednesday.from} - {$service.wednesday.to}
											{/if}
										</td>
									</tr>
									<tr>
										<td>{l s="Thursday"}</td>
										<td>
											{if $service.thursday.closed}
												{l s="Closed"}
											{else}
												{$service.thursday.from} - {$service.thursday.to}
											{/if}
										</td>
									</tr>
									<tr>
										<td>{l s="Friday"}</td>
										<td>
											{if $service.friday.closed}
												{l s="Closed"}
											{else}
												{$service.friday.from} - {$service.friday.to}
											{/if}
										</td>
									</tr>
									<tr>
										<td>{l s="Saturday"}</td>
										<td>
											{if $service.saturday.closed}
												{l s="Closed"}
											{else}
												{$service.saturday.from} - {$service.saturday.to}
											{/if}
										</td>
									</tr>
									<tr>
										<td>{l s="Sunday"}</td>
										<td>
											{if $service.sunday.closed}
												{l s="Closed"}
											{else}
												{$service.sunday.from} - {$service.sunday.to}
											{/if}
										</td>
									</tr>
								</table>
							</div>
						{/if}
					</div>
				</div>
		</div>
		{/if}

		{if $category->id != 14}
			<ul class="nav nav-tabs big manufacturer-nav">
				<li class="active"><a data-toggle="tab" href="#buy">{l s="Buy"}</a></li>
				<li><a data-toggle="tab" href="#lease">{l s="Lease"}</a></li>
			</ul>
		{/if}
		<h1 class="header main medium">{l s="Adjust search results"}</h1>
		<div class="filter-wrap top">
			{capture name='displayTopFilter'}{hook h='displayTopFilter'}{/capture}
			{if $smarty.capture.displayTopFilter}
				{$smarty.capture.displayTopFilter}
			{/if}
			{*<form action="#" class="filter-form">*}
				{*<div class="filter-block select">*}
					{*<select name="make" id="make" class="form-control blue not_uniform medium">*}
						{*<option value="" selected="selected">{l s="Select Make"}</option>*}
						{*<option value="1">{l s="Make 1"}</option>*}
						{*<option value="2">{l s="Make 2"}</option>*}
					{*</select>*}
				{*</div>*}
				{*<div class="filter-block select">*}
					{*<select name="model" id="model" class="form-control blue not_uniform medium">*}
						{*<option value="" selected="selected">{l s="Select Model"}</option>*}
						{*<option value="1">{l s="Model 1"}</option>*}
						{*<option value="2">{l s="Model 2"}</option>*}
					{*</select>*}
				{*</div>*}
				{*<div class="filter-block slider">*}
					{*<p class="filter-header">Year <span class="filter-header-value year medium"><span*}
									{*class="from"></span> - <span class="to"></span></span></p>*}
					{*<div class="filter-slider year" data-max="2016" data-min="2000" data-values="2004-2013"*}
						 {*data-name="year"></div>*}
				{*</div>*}
				{*<div class="filter-block slider">*}
					{*<p class="filter-header">Miles <span class="filter-header-value miles medium"><span*}
									{*class="from"></span> - <span*}
									{*class="to"></span></span></p>*}
					{*<div class="filter-slider miles" data-max="38000" data-min="0" data-values="10000-28000"*}
						 {*data-name="miles"></div>*}
				{*</div>*}
				{*<div class="filter-block slider">*}
					{*<p class="filter-header">Price <span class="filter-header-value price medium"><span*}
									{*class="from"></span> - <span*}
									{*class="to"></span></span></p>*}
					{*<div class="filter-slider price" data-max="38000" data-min="0" data-values="10000-28000"*}
						 {*data-name="price" data-unit="$"></div>*}
				{*</div>*}
			{*</form>*}
		</div>
		<div class="sort-wrap">
			<div class="lincoln-navigator sort-block medium">
				<h6 class="header sub">{l s="Used Lincoln Navigator"}</h6>
				<p class="lincoln-navigator-text">{l s="%d matches" sprintf="22"} <a href="#"
																					 class="lincoln-navigator-link">{l s="within %d miles on %d" sprintf=array(100,10305)}</a>
				</p>
			</div>
			<div class="sort sort-block">
				{include file="./product-sort.tpl"}
			</div>
			{*<div class="sort-block medium">*}
				{*<button class="btn btn-default no-change">{l s="Current Lease Offers"}</button>*}
			{*</div>*}
		</div>

		<div class="sub-column-container clearfix">
			<div class="sub-left-column col-lg-2 col-md-2 col-sm-12">
				<div class="filter-left-wrap">
					{$HOOK_LEFT_COLUMN}
				</div>
			</div> {*sub-left-column*}
			<div class="sub-center-column col-lg-10 col-md-10 col-sm-12">
				<div class="tab-content manufacturer-tab">
					<div id="buy" class="tab-pane fade in active">
						{if $products}
							{include file="./product-list.tpl" products=$products}
							{include file="$tpl_dir./pagination.tpl"}
							{include file="./nbr-product-page.tpl"}
						{else}
							<p class="alert alert-warning">{l s='No buy products for this manufacturer.'}</p>
						{/if}
					</div>
					<div id="lease" class="tab-pane fade">
						{if $products}
							{include file="./product-list.tpl" products=$products}
							{include file="$tpl_dir./pagination.tpl"}
							{include file="./nbr-product-page.tpl"}
						{else}
							<p class="alert alert-warning">{l s='No lease products for this manufacturer.'}</p>
						{/if}
					</div>
				</div>
			</div> {*sub-center-column*}
		</div>

		{if $products}
			{*<div class="content_sortPagiBar">*}
			{*<div class="sortPagiBar clearfix">*}
			{*{include file="./product-sort.tpl"}*}
			{*{include file="./nbr-product-page.tpl"}*}
			{*</div>*}
			{*<div class="top-pagination-content clearfix">*}
			{*{include file="./product-compare.tpl"}*}
			{*{include file="$tpl_dir./pagination.tpl" no_follow=1}*}
			{*</div>*}
			{*</div>*}
			{*{include file="./product-list.tpl" products=$products}*}
			{*<div class="content_sortPagiBar">*}
			{*<div class="bottom-pagination-content clearfix">*}
			{*{include file="./product-compare.tpl"}*}
			{*{include file="./pagination.tpl" no_follow=1 paginationId='bottom'}*}
			{*</div>*}
			{*</div>*}
		{else}
			<p class="alert alert-warning">{l s='No products for this manufacturer.'}</p>
		{/if}
	</div>
{/if}
