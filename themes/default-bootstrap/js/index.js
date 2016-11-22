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

	$('.make_select').change(function () {
		var id_feature_value = $(this).children('option:selected').attr('data-id_feature_value');
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + 'index.php' + '?rand=' + new Date().getTime(),
			async: false,
			cache: false,
			dataType : "json",
			data: '&ajax=true&id_feature_value='+id_feature_value+'&token=' + static_token ,
			success: function(jsonData)
			{
				$('#model_select_buy').empty();
				for ( sellerId in jsonData ) {
					$('#model_select_buy').append('<option value="'+jsonData[sellerId].url_name+'">'+jsonData[sellerId].value+'</option>');
				}
			},

		});
	});

	$('#home-page-tabs li:first, #index .tab-content ul:first').addClass('active');
	var slider = $('#price_slider');
	$(document).on('click', '#find_my_car', function () {
		var type = $(this).closest('.searchForm').data('type');
		var category_layered_href = $('#id_category_layered').val() + '#';
		category_layered_href += '/' + $('#make_select_' + type).attr('data-url') + '-' + $('#make_select_' + type).val();
		category_layered_href += '/' + $('#model_select_' + type).attr('data-url') + '-' + $('#model_select_' + type).val();
		if(type === 'buy') {
			category_layered_href += '/' + 'price-' + slider.slider("values", 0) + '-' + slider.slider("values", 1);
		} else {
			category_layered_href += '/' + $('#payment_select_' + type).attr('data-url') + '-' + $('#payment_select_' + type).val();
			category_layered_href += '/' + $('#remaining_select_' + type).attr('data-url') + '-' + $('#remaining_select_' + type).val();
		}
		category_layered_href += '/' + $('#zip_' + type).attr('data-url') + '-' + $('#zip_' + type).val();
		category_layered_href += '/' + $('#distance_select_' + type).attr('data-url') + '-' + $('#distance_select_' + type).val() + '-' + $('#distance_select_' + type).attr('data-max');
		category_layered_href += '/type' + '-' + type;

		location.href = category_layered_href;
		return false;
	});

	var step = parseInt(slider.data('max') / 100);
	slider.slider(
		{
			range: true,
			step: step,
			min: parseFloat(slider.data('min')),
			max: parseFloat(slider.data('max')),
			values: [parseFloat(slider.data('min')), parseFloat(slider.data('max'))],
			slide: function(event, ui) {

				from = formatCurrency(ui.values[0], 1, currencySign);
				to = formatCurrency(ui.values[1], 1, currencySign);

				$('#price_slider_range').html(from + ' - ' + to);
			}
		}
	);
});