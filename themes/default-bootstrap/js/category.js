/*
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
*/
$(document).ready(function(){
	// var tab = location.href.match(/(type-)+[a-zA-z0-9.]*(&|$)/);
	// if(tab && tab.length)
	// 	tab = tab[0].replace(/(type-)+/, '');
	// if(tab) {
	// 	$('.manufacturer-nav > li').each(function () {
	// 		var that = $(this);
	// 		that.removeClass('active');
	// 		if (that.data('type') === tab)
	// 			that.addClass('active');
	// 	});
	// } else {
	// 	$('.manufacturer-nav > li:first-of-type').addClass('active');
	// }
	var type = $('.manufacturer-nav > li.active').data('type'),
		regex = /(type-)+[a-zA-z0-9.&]*/;
	if(type) {
		if(location.href.search(regex) != -1)
			location.href = location.href.replace(regex, 'type-' + type);
		else
			location.href = (location.href.search(/#\//) === -1 ? '#/' : '') + 'type-' + type;
	}

	$(document).on('click', '.manufacturer-nav > li:not(.active)', function() {
		var href = location.href.replace(/(type-)+[a-zA-z0-9.&]*(\/)*/, '');
		href = href.replace(/(#\/)$/, '');
		href += (href.search(/#\//) === -1 ? '#/' : '/') + 'type-' + $(this).data('type');
		location.href = href;
	});

	resizeCatimg();
});

$(window).resize(function(){
	resizeCatimg();
});

$(document).on('click', '.lnk_more', function(e){
	e.preventDefault();
	$('#category_description_short').hide(); 
	$('#category_description_full').show(); 
	$(this).hide();
});

function resizeCatimg()
{
	var div = $('.content_scene_cat div:first');

	if (div.css('background-image') == 'none')
		return;

	var image = new Image;

	$(image).load(function(){
	    var width  = image.width;
	    var height = image.height;
		var ratio = parseFloat(height / width);
		var calc = Math.round(ratio * parseInt(div.outerWidth(false)));

		div.css('min-height', calc);
	});
	if (div.length)
		image.src = div.css('background-image').replace(/url\("?|"?\)$/ig, '');
}