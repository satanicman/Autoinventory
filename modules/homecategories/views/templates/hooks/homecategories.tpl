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
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2015 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}
<div class="home_categories col-md-12">
   {* <h2>{l s='Categories' mod='homecategories'}</h2>*}
    {if isset($categories) AND $categories}
        <ul id="subcategories">
            {foreach from=$categories item=subcategory}
                <li class="col-lg-4">
                    <div class="subcategory-image">
                        <a href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategory.name|escape:'html':'UTF-8'}" class="img">
                            {if $subcategory.id_image}
                                <img class="replace-2x" src="{$link->getCatImageLink($subcategory.link_rewrite, $subcategory.id_image, 'medium_default')|escape:'html':'UTF-8'}" alt="{$subcategory.name|escape:'html':'UTF-8'}" width="{$mediumSize.width}" height="{$mediumSize.height}" />
                            {else}
                                <img class="replace-2x" src="{$img_cat_dir}{$lang_iso}-default-medium_default.jpg" alt="{$subcategory.name|escape:'html':'UTF-8'}" width="{$mediumSize.width}" height="{$mediumSize.height}" />
                            {/if}
                        </a>
                    </div>
                    <h5><a class="subcategory-name" href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}">{$subcategory.name|truncate:25:'...'|escape:'html':'UTF-8'}</a></h5>
                    {if $subcategory.description}
                        <div class="cat_desc">{$subcategory.description}</div>
                    {/if}
                </li>
            {/foreach}
        </ul>
    {else}
        <p>{l s='No categories' mod='homecategories'}</p>
    {/if}
    <div class="cr"></div>
</div>
<!-- /MODULE Home categories -->