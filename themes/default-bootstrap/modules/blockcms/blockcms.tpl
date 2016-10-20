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

{if isset($top) && $top}
	<div id="cmsBlock_top">
		<ul>
			{foreach from=$cms_titles key=cms_key item=cms_title}
				{foreach from=$cms_title.categories item=cms_page}
					{if isset($cms_page.link)}
						<li class="bullet">
							<a href="{$cms_page.link|escape:'html':'UTF-8'}" title="{$cms_page.name|escape:'html':'UTF-8'}">
								{$cms_page.name|escape:'html':'UTF-8'}
							</a>
						</li>
					{/if}
				{/foreach}
				{foreach from=$cms_title.cms item=cms_page}
					{if isset($cms_page.link)}
						<li>
							<a href="{$cms_page.link|escape:'html':'UTF-8'}" title="{$cms_page.meta_title|escape:'html':'UTF-8'}">
								{$cms_page.meta_title|escape:'html':'UTF-8'}
							</a>
						</li>
					{/if}
				{/foreach}
			{/foreach}
			<li>
				<a href="#" title="{l s='Watch List' mod='blockcms'}">
					{l s='Watch List' mod='blockcms'} <span>1</span>
				</a>
			</li>
		</ul>
	</div>
{elseif $block == 1}
	<!-- Block CMS module -->
	{foreach from=$cms_titles key=cms_key item=cms_title}
		<section id="informations_block_left_{$cms_key}" class="block informations_block_left">
			<p class="title_block">
				<a href="{$cms_title.category_link|escape:'html':'UTF-8'}">
					{if !empty($cms_title.name)}{$cms_title.name}{else}{$cms_title.category_name}{/if}
				</a>
			</p>
			<div class="block_content list-block">
				<ul>
					{foreach from=$cms_title.categories item=cms_page}
						{if isset($cms_page.link)}
							<li class="bullet">
								<a href="{$cms_page.link|escape:'html':'UTF-8'}" title="{$cms_page.name|escape:'html':'UTF-8'}">
									{$cms_page.name|escape:'html':'UTF-8'}
								</a>
							</li>
						{/if}
					{/foreach}
					{foreach from=$cms_title.cms item=cms_page}
						{if isset($cms_page.link)}
							<li>
								<a href="{$cms_page.link|escape:'html':'UTF-8'}" title="{$cms_page.meta_title|escape:'html':'UTF-8'}">
									{$cms_page.meta_title|escape:'html':'UTF-8'}
								</a>
							</li>
						{/if}
					{/foreach}
					{if $cms_title.display_store}
						<li>
							<a href="{$link->getPageLink('stores')|escape:'html':'UTF-8'}" title="{l s='Our stores' mod='blockcms'}">
								{l s='Our stores' mod='blockcms'}
							</a>
						</li>
					{/if}
				</ul>
			</div>
		</section>
	{/foreach}
	<!-- /Block CMS module -->
{else}
	<!-- Block CMS module footer -->
	<section class="footer-block col-xs-12 col-sm-4" id="block_various_links_footer">
		{assign var="elementNum" value=3}
		{assign var="count" value=0}
		<table id="blockcms-table">
			<tbody>
			{foreach from=$cmslinks item=cmslink}
				{if $cmslink.meta_title != ''}
					{if !$count}
						<tr>
					{/if}
					{assign var="count" value=$count+1}
					<td class="item">
						<a href="{$cmslink.link|escape:'html':'UTF-8'}" title="{$cmslink.meta_title|escape:'html':'UTF-8'}">
							{$cmslink.meta_title|escape:'html':'UTF-8'}
						</a>
					</td>
					{if $count>=$elementNum}
						{assign var="count" value=0}
						</tr>
					{/if}
				{/if}
			{/foreach}
			</tbody>
		</table>
		<p id="copyright">
			{$footer_text}
		</p>
	</section>
	{if $display_poweredby}
	<section class="bottom-footer col-xs-12">
		<div>
			{l s='[1] %3$s %2$s - Ecommerce software by %1$s [/1]' mod='blockcms' sprintf=['PrestaShop™', 'Y'|date, '©'] tags=['<a class="_blank" href="http://www.prestashop.com">'] nocache}
		</div>
	</section>
	{/if}
	<!-- /Block CMS module footer -->
{/if}
