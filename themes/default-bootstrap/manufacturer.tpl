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
        <div class="manufacturer-top">
            <div class="manufacturer-top-img">
                <img src="{$img_manu_dir}{$manufacturer->id}.jpg" alt="{$manufacturer->name}"/>
                <div class="social">
                    <ul class="social-list">
                        <li><a href="#" class="social-link facebook"><span>{l s="Facebook"}</span></a></li>
                        <li><a href="#" class="social-link instagram"><span>{l s="Instagram"}</span></a></li>
                        <li><a href="#" class="social-link twitter"><span>{l s="Twitter"}</span></a></li>
                    </ul>
                </div>
            </div>
            <div class="manufacturer-top-description">
                <h1 class="header main medium">{$manufacturer->name|escape:'html':'UTF-8'}</h1>
                <table class="manufacturer-table">
                    <tr>
                        <td class="manufacturer-table-label medium">{l s="Address"}</td>
                        <td>{l s="535 RT-1, Ramsey, New Jersey 07446"}</td>
                    </tr>
                    {if !empty($manufacturer->description) || !empty($manufacturer->short_description)}
                        <tr>
                            <td class="manufacturer-table-label medium">{l s="Description"}</td
                                    {if !empty($manufacturer->short_description)}>
                            <td>{$manufacturer->short_description}</td>
                            {elseif !empty($manufacturer->description)}}
                            <td>{$manufacturer->description}</td>
                            {/if}
                        </tr>
                    {/if}
                    <tr>
                        <td class="manufacturer-table-label medium">{l s="Products"}</td>
                        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin tellus magna, tincidunt at
                            commodo eget, tincidunt vel ex.
                        </td>
                    </tr>
                    <tr>
                        <td class="manufacturer-table-label medium">{l s="Phone"}</td>
                        <td>
                            <span><b>New</b> 1-800-852-8117</span><span><b>Used</b> 1-800-852-8117</span><span><b>Used</b> 1-800-852-8117</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="manufacturer-table-label medium">{l s="Offers"}</td>
                        <td><a href="#" class="medium">Financing</a><a href="#" class="medium">Trade-In</a><a href="#"
                                                                                                              class="medium">Warranty</a><a
                                    href="#" class="medium">Incentives</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="manufacturer-table-label medium">{l s="Website"}</td>
                        <td>
                            <p>www.landroverusa.com</p>
                            <p>www.autoinventory.com/landroverpeoria</p>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="manufacturer-top-hours">
                <h3 class="header sub medium">{l s="Business Hours"}</h3>

                <ul class="nav nav-tabs no-border">
                    <li class="active"><a data-toggle="tab" href="#sales">{l s="Sales"}</a></li>
                    <li><a data-toggle="tab" href="#service">{l s="Service"}</a></li>
                </ul>

                <div class="tab-content">
                    <div id="sales" class="tab-pane fade in active">
                        <table>
                            <tr>
                                <td>{l s="Monday"}</td>
                                <td>{l s="9:30 am - 8:30 pm"}</td>
                            </tr>
                            <tr>
                                <td>{l s="Tuesday"}</td>
                                <td>{l s="9:30 am - 8:30 pm"}</td>
                            </tr>
                            <tr>
                                <td>{l s="Wednesday"}</td>
                                <td>{l s="9:30 am - 8:30 pm"}</td>
                            </tr>
                            <tr>
                                <td>{l s="Thursday"}</td>
                                <td>{l s="9:30 am - 8:30 pm"}</td>
                            </tr>
                            <tr>
                                <td>{l s="Friday"}</td>
                                <td>{l s="9:30 am - 8:30 pm"}</td>
                            </tr>
                            <tr>
                                <td>{l s="Saturday"}</td>
                                <td>{l s="9:30 am - 8:30 pm"}</td>
                            </tr>
                            <tr>
                                <td>{l s="Sunday"}</td>
                                <td>{l s="Closed"}</td>
                            </tr>
                        </table>
                    </div>
                    <div id="service" class="tab-pane fade">
                        <table>
                            <tr>
                                <td>{l s="Monday"}</td>
                                <td>{l s="9:30 am - 8:30 pm"}</td>
                            </tr>
                            <tr>
                                <td>{l s="Tuesday"}</td>
                                <td>{l s="9:30 am - 8:30 pm"}</td>
                            </tr>
                            <tr>
                                <td>{l s="Wednesday"}</td>
                                <td>{l s="9:30 am - 8:30 pm"}</td>
                            </tr>
                            <tr>
                                <td>{l s="Thursday"}</td>
                                <td>{l s="9:30 am - 8:30 pm"}</td>
                            </tr>
                            <tr>
                                <td>{l s="Friday"}</td>
                                <td>{l s="9:30 am - 8:30 pm"}</td>
                            </tr>
                            <tr>
                                <td>{l s="Saturday"}</td>
                                <td>{l s="9:30 am - 8:30 pm"}</td>
                            </tr>
                            <tr>
                                <td>{l s="Sunday"}</td>
                                <td>{l s="Closed"}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs big manufacturer-nav">
            <li class="active"><a data-toggle="tab" href="#buy">{l s="Buy"}</a></li>
            <li><a data-toggle="tab" href="#lease">{l s="Lease"}</a></li>
        </ul>
        <h1 class="header main medium">{l s="Adjust search results"}</h1>
        <div class="filter-wrap top">
            <form action="#" class="filter-form">
                <div class="filter-block select">
                    <select name="make" id="make" class="form-control blue not_uniform medium">
                        <option value="" selected="selected">{l s="Select Make"}</option>
                        <option value="1">{l s="Make 1"}</option>
                        <option value="2">{l s="Make 2"}</option>
                    </select>
                </div>
                <div class="filter-block select">
                    <select name="model" id="model" class="form-control blue not_uniform medium">
                        <option value="" selected="selected">{l s="Select Model"}</option>
                        <option value="1">{l s="Model 1"}</option>
                        <option value="2">{l s="Model 2"}</option>
                    </select>
                </div>
                <div class="filter-block slider">
                    <p class="filter-header">Year <span class="filter-header-value year medium"><span
                                    class="from"></span> - <span class="to"></span></span></p>
                    <div class="filter-slider year" data-max="2016" data-min="2000" data-values="2004-2013"
                         data-name="year"></div>
                </div>
                <div class="filter-block slider">
                    <p class="filter-header">Miles <span class="filter-header-value miles medium"><span
                                    class="from"></span> - <span
                                    class="to"></span></span></p>
                    <div class="filter-slider miles" data-max="38000" data-min="0" data-values="10000-28000"
                         data-name="miles"></div>
                </div>
                <div class="filter-block slider">
                    <p class="filter-header">Price <span class="filter-header-value price medium"><span
                                    class="from"></span> - <span
                                    class="to"></span></span></p>
                    <div class="filter-slider price" data-max="38000" data-min="0" data-values="10000-28000"
                         data-name="price" data-unit="$"></div>
                </div>
            </form>
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
                    <form action="#" class="filter-left-form">
                        <h6 class="header small medium">{l s="Filters"}</h6>
                        <div class="filter-left-block">
                            <p class="filter-left-header medium">{l s="State"}<span class="expand icon icon-chevron-up"></span></p>
                            <ul class="filter-left-list">
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="state" id="" class="form-control">
                                        <span>{l s="New cars"}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="state" id="" checked="checked" class="form-control">
                                        <span>{l s="Pre owned cars"}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="state" id="" class="form-control">
                                        <span>{l s="Certified pre owned"}</span>
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <div class="filter-left-block">
                            <p class="filter-left-header medium">{l s="Exterior colors"}<span class="expand icon icon-chevron-down"></span></p>
                            <ul class="filter-left-list">
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="exterior_colors" id="" class="form-control">
                                        <span>{l s="New cars"}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="exterior_colors" id="" checked="checked" class="form-control">
                                        <span>{l s="Pre owned cars"}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="exterior_colors" id="" class="form-control">
                                        <span>{l s="Certified pre owned"}</span>
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <div class="filter-left-block">
                            <p class="filter-left-header medium">{l s="Interior colors"}<span class="expand icon icon-chevron-down"></span></p>
                            <ul class="filter-left-list">
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="interior_colors" id="" class="form-control">
                                        <span>{l s="New cars"}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="interior_colors" id="" checked="checked"
                                               class="form-control">
                                        <span>{l s="Pre owned cars"}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="interior_colors" id="" class="form-control">
                                        <span>{l s="Certified pre owned"}</span>
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <div class="filter-left-block">
                            <p class="filter-left-header medium">{l s="Any transmisson"}<span class="expand icon icon-chevron-down"></span></p>
                            <ul class="filter-left-list">
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="any_transmisson" id="" class="form-control">
                                        <span>{l s="New cars"}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="any_transmisson" id="" checked="checked"
                                               class="form-control">
                                        <span>{l s="Pre owned cars"}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="any_transmisson" id="" class="form-control">
                                        <span>{l s="Certified pre owned"}</span>
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <div class="filter-left-block">
                            <p class="filter-left-header medium">{l s="Engine type"}<span class="expand icon icon-chevron-down"></span></p>
                            <ul class="filter-left-list">
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="engine_type" id="" class="form-control">
                                        <span>{l s="New cars"}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="engine_type" id="" checked="checked"
                                               class="form-control">
                                        <span>{l s="Pre owned cars"}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="engine_type" id="" class="form-control">
                                        <span>{l s="Certified pre owned"}</span>
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <div class="filter-left-block">
                            <p class="filter-left-header medium">{l s="Features"}<span class="expand icon icon-chevron-down"></span></p>
                            <ul class="filter-left-list">
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="features" id="" class="form-control">
                                        <span>{l s="New cars"}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="features" id="" checked="checked"
                                               class="form-control">
                                        <span>{l s="Pre owned cars"}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="features" id="" class="form-control">
                                        <span>{l s="Certified pre owned"}</span>
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <div class="filter-left-block">
                            <p class="filter-left-header medium">{l s="Trim"}<span class="expand icon icon-chevron-down"></span></p>
                            <ul class="filter-left-list">
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="trim" id="" class="form-control">
                                        <span>{l s="New cars"}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="trim" id="" checked="checked" class="form-control">
                                        <span>{l s="Pre owned cars"}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="radio" name="trim" id="" class="form-control">
                                        <span>{l s="Certified pre owned"}</span>
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <div class="filter-left-block">
                            <p class="filter-left-header medium">{l s="Fuel type"}<span class="expand icon icon-chevron-up"></span></p>
                            <ul class="filter-left-list">
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="checkbox" name="fuel_type" id="" class="form-control">
                                        <span>{l s="Gasoline"}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="checkbox" name="fuel_type" id="" checked="checked"
                                               class="form-control">
                                        <span>{l s="Hybrid"}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="checkbox" name="fuel_type" id="" class="form-control">
                                        <span>{l s="Diesel"}</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="filter-left-block-line">
                                        <input type="checkbox" name="fuel_type" id="" class="form-control">
                                        <span>{l s="Electric"}</span>
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <div class="filter-left-button-wrap">
                            <button class="filter-left-button btn btn-default">{l s="Search"}</button>
                        </div>
                    </form>
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
