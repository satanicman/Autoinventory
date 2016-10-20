<?php
/**
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
 */

function getDefaultCurrency()
{
	$cur_id = Configuration::get('PS_CURRENCY_DEFAULT');
	$query_str = 'SELECT `sign` FROM '._DB_PREFIX_."currency WHERE id_currency = '$cur_id'";
	$cur_row = Db::getInstance()->getRow($query_str);
	return $cur_row['sign'];
}

function formatMoney($sum)
{
	$smarty = &$GLOBALS['smarty'];
	$currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
	return Product::convertPriceWithCurrency(array('price' => $sum, 'currency' => $currency), $smarty);
}

function copyImage($id_product, $id_image, $image_file)
{
	$error = '';
	if (!isset($image_file) || !file_exists($image_file))
		return false;
	if (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($image_file, $tmp_name))
		$error = Tools::displayError('An error occured during the image upload');
	elseif (!imageResize($tmp_name, _PS_IMG_DIR_.'p/'.$id_product.'-'.$id_image.'.jpg'))
			$error = Tools::displayError('an error occurred while copying image');
	else
	{
		$images_types = ImageType::getImagesTypes('products');
		foreach ($images_types as $image_type)
			if (!imageResize($tmp_name, _PS_IMG_DIR_.'p/'.$id_product.'-'.$id_image.'-'
				.Tools::stripslashes(($image_type['name']).'.jpg', $image_type['width'], $image_type['height'])))
							$error = Tools::displayError('an error occurred while copying image').' '.Tools::stripslashes($image_type['name']);
	}
	unlink($tmp_name);
	return $error;
}

function prodAlert($customer, $id_product, $type = 'new')
{
	$cookie = &$GLOBALS['cookie'];
	if ($id_contact = Configuration::get('ADDPROD_IDSERV'))
	{
		$ct = new CustomerThread();
		$ct->id_customer = (int)$customer->id;
		$ct->id_contact = (int)$id_contact;
		$ct->id_lang = (int)$cookie->id_lang;
		$ct->email = $customer->email;
		$ct->status = 'open';
		$ct->token = Tools::passwdGen(12);
		$ct->add();
		if ($ct->id)
		{
			$cm = new CustomerMessage();
			$cm->id_customer_thread = $ct->id;
						$default_lang = new Language(Configuration::get('PS_LANG_DEFAULT'));
						$s_template_name = '';
						$s_message = '';
			if ($type == 'new')
			{
				$s_message = getCustomTranslation('Added the new product #', $default_lang->id).$id_product;
				$s_template_name = 'notice_delete_product';
			}
			elseif ($type == 'del')
			{
				$s_message = getCustomTranslation('Deleted product #', $default_lang->id).$id_product;
				$s_template_name = 'notice_new_product';
			}
			else
			{
						$s_message = getCustomTranslation('Updated product #', $default_lang->id).$id_product;
						$s_template_name = 'notice_update_product';
			}
						$cm->message = $s_message;
			$cm->ip_address = ip2long($_SERVER['REMOTE_ADDR']);
			$cm->user_agent = $_SERVER['HTTP_USER_AGENT'];
			$cm->add();
						$a_template_var = array('{message}' => $s_message,
												'{ticket_id}' => $ct->id,
												'{customer_id}' => $ct->id_customer);
			if (($type == 'new' && !(int)Configuration::get('ADDPROD_ADDACTIVE'))
				|| $type != 'new' && $type != 'del' && !(int)Configuration::get('ADDPROD_UPDACTIVE') || $type == 'del')
						PaymentsCore::sendMail($s_message, $s_template_name, $a_template_var);
		}
	}
}

function getTree($result_parents, $result_ids, $max_depth, $id_category = null, $current_depth = 0)
{
	$link = &$GLOBALS['link'];

	if (is_null($id_category) && version_compare(_PS_VERSION_, '1.5', '>='))
		$id_category = Context::getContext()->shop->getCategory();

	$children = array();
	if (isset($result_parents[$id_category]) && count($result_parents[$id_category]) && ($max_depth == 0 || $current_depth < $max_depth))
			foreach ($result_parents[$id_category] as $subcat)
				$children[] = getTree($result_parents, $result_ids, $max_depth, $subcat['id_category'], $current_depth + 1);

	if (!isset($result_ids[$id_category]))
			return false;

	$result = array('id' => $id_category, 'link' => $link->getCategoryLink($id_category, $result_ids[$id_category]['link_rewrite']),
					'name' => $result_ids[$id_category]['name'], 'desc'=> $result_ids[$id_category]['description'],
					'children' => $children);
	return $result;
}

function cleanBreaks($str)
{
	return str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"), ' ', $str);
}

function edit_product($id_edit)
{
	$cookie = &$GLOBALS['cookie'];
	$lngs = &$GLOBALS['lngs'];
	$errors = &$GLOBALS['errors'];
	$smarty = &$GLOBALS['smarty'];
	$addprod_manufacturer_id = &$GLOBALS['addprod_manufacturer_id'];
	$product = &$GLOBALS['product'];
	$errors_hook = $errors;
	$errors = Array();
	$product = new Product((int)$id_edit, false, (int)$cookie->id_lang);
	$download = new ProductDownload(ProductDownload::getIdFromIdProduct($product->id));

		$a_product_categories = array();
		$a_product_categories_ids = Product::getProductCategories($product->id);
			foreach ($a_product_categories_ids as $id)
				$a_product_categories[] = new Category((int)$id, $cookie->id_lang);
	$count_l = count($lngs);
	for ($i = 0; $i < $count_l; $i++)
	{
		$lngs[$i]['product'] = new Product((int)$id_edit, false, $lngs[$i]['id_lang']);
		$lngs[$i]['posted_name'] = Tools::getIsset('name_'.$lngs[$i]['id_lang']) ? Tools::getValue('name_'.$lngs[$i]['id_lang']) : '';
		$lngs[$i]['posted_s_descr'] = Tools::getIsset('description_short_'.$lngs[$i]['id_lang']) ?
			Tools::getValue('description_short_'.$lngs[$i]['id_lang']) : '';
		$lngs[$i]['posted_description'] = Tools::getIsset('description_'.$lngs[$i]['id_lang']) ? Tools::getValue('description_'.$lngs[$i]['id_lang']) : '';
	}
	if ($product->id_manufacturer != $addprod_manufacturer_id)
		$errors[] = Tools::displayError('Do not touch someone elses product');
	if (!count($errors))
	{
		if ($id_image = Tools::GetValue('delimg'))
		{
			$image = new Image($id_image);
			if (Validate::isLoadedObject($image) && ($product->id == $image->id_product))
			{
				$image->delete();
				deleteImage($product->id, (int)$id_image);
			}
			else
				$errors[] = Tools::displayError('Do not touch someone elses product');
		}
		$errors = array_merge($errors_hook, $errors);

		if (Validate::isLoadedObject($product))
			$product->quantity = Product::getQuantity($product->id);

		$smarty->assign(array(
				'eimages' => $product->getImages((int)$cookie->id_lang),
				'eproduct' => $product,
				'filename' => $download->display_filename,
				'post' => $_POST,
								'isVirtualProduct' => ($download->display_filename != '') ? 1 : 0,
								'aProductCategoriesIds' => $a_product_categories_ids,
								'aProductCategories' => $a_product_categories,
		));
	}
}

function getProducts($id_manufacturer, $active = true)
{
	$cookie = $GLOBALS['cookie'];

	$sql = 'SELECT p.`id_product`, p.`active` , p.`price`, pl.`link_rewrite`, pl.`name`, pl.`link_rewrite`, SUM(od.`product_quantity`) as sold_q,
			SUM(od.`product_quantity`*od.`product_price`/od.`conversion_rate`) as sold_total
			FROM `'._DB_PREFIX_.'product` p
			LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$cookie->id_lang.')
			LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
			LEFT JOIN (SELECT odt.`product_quantity`, odt.`product_price`, odt.`product_id`, o.`conversion_rate`
			FROM `'._DB_PREFIX_.'order_detail` odt LEFT JOIN `'._DB_PREFIX_.'orders` o ON odt.`id_order` = o.`id_order`
			WHERE o.`valid` = 1) od ON p.`id_product` = od.`product_id`
			WHERE p.`id_manufacturer` = '.(int)$id_manufacturer.($active ? ' AND p.`active` = 1' : '').
			' GROUP BY p.`id_product` ORDER BY p.`date_upd` DESC';
	$result = Db::getInstance()->ExecuteS($sql);
	if (!$result)
		return Array();
		/*
        foreach ($result as &$prod)
		{
            $prod['price'] = formatMoney($prod['price']);
            $prod['sold_total'] = formatMoney(!$prod['sold_total'] ? 0 : $prod['sold_total']);
        }
		*/
	return $result;
}

function sendMail($title, $template_name, $to_name, $to_email, $template_vars, $from = false, $id_land = false)
{
	$cookie = $GLOBALS['cookie'];
	if (!$id_land) $id_land = $cookie->id_lang;
	if (!$from) $from = Configuration::get('PS_SHOP_EMAIL');   //Sender's email
	$from_name = Configuration::get('PS_SHOP_NAME');
	$mail_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'mails'.DIRECTORY_SEPARATOR; //Directory with message templates

	$default_lang = new Language(Configuration::get('PS_LANG_DEFAULT'));

	$i_mail_type_html = 1;
	if (version_compare(_PS_VERSION_, '1.5', '>='))
		$i_mail_type_html = Mail::TYPE_HTML;

	$mail_type = (int)Configuration::get('PS_MAIL_TYPE');

	$lang = $o_final_language = new Language($id_land);
	$a_sorted_languages = array();

	$a_sorted_languages[] = $lang;
	if ($lang->iso_code != $default_lang->iso_code)
		$a_sorted_languages[] = $default_lang;

	$o_english_lang = new Language(Language::getIdByIso('en'));
	if ($o_english_lang->id)
		$a_sorted_languages[] = $o_english_lang;

	$a_laguages = Language::getLanguages(true);

	foreach ($a_laguages as $a_lang)
	{
		$o_lang = new Language($a_lang['id_lang']);
		if ($o_lang->iso_code != $lang->iso_code && $o_lang->iso_code != $default_lang->iso_code)
			$a_sorted_languages[] = $o_lang;
	}

	foreach ($a_sorted_languages as $lang)
	{
		$template_path = $mail_dir.$lang->iso_code.DIRECTORY_SEPARATOR.$template_name.(($mail_type == $i_mail_type_html) ? '.html' : '.txt');
		if (file_exists($template_path))
		{
			$o_final_language = $lang;
			break;
		}
	}

	if (Mail::Send($o_final_language->id, $template_name, $title, $template_vars, $to_email, $to_name, $from, $from_name, null, null, $mail_dir))
		return true;
	else
		return false;
}

function getCustomTranslation($s_string = '', $i_language_id = null)
{
	if (!$s_string || !$i_language_id) return $s_string;
	$_MODULE = array();
	$o_language = new Language($i_language_id);
	if (!ValidateCore::isLoadedObject($o_language))
		return $s_string;
	$s_file_path = _PS_MODULE_DIR_.'addprod/translations/'.$o_language->iso_code.'.php';
	if (!file_exists($s_file_path)) return $s_string;
	include _PS_MODULE_DIR_.'addprod/translations/'.$o_language->iso_code.'.php';
	$s_key = '<{addprod}prestashop>addprod_'.md5($s_string);
	if (isset($_MODULE[$s_key]) && $_MODULE[$s_key]) return $_MODULE[$s_key];
	return $s_string;
}

function getLanguageForCustomer($i_customer_id = null)
{
	$query = 'select o.id_lang from `'._DB_PREFIX_.'orders` o where o.id_customer = '.(int)$i_customer_id.' ORDER BY o.id_order DESC ';
	return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
}
?><?php
function checkActivationStateAddProd($s_activete_code = '')
{
	return ((string)$s_activete_code === (string)getModuleHashAddProd());
}

function getModuleHashAddProd()
{
	$o_add_prod = new addprod();
	return md5($o_add_prod->shop_url.$o_add_prod->version.'salt'.$o_add_prod->version.$o_add_prod->version.$o_add_prod->shop_url);
}