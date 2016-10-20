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

if (!class_exists('Contact'))
	require_once(dirname(__FILE__).'/../../classes/Contact.php');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions.php');

class PaymentsCore extends ObjectModel
{
	public $id_product;
	public $id_order;

	public $id_seller;

	public $summ;

	public $description;
/*
 * 0 - продажа
 * 1 - запрос выплаты
 * 2 - выплата
 * 3 - покупка за внутренний счет
 */
	public $status = 0;
	public $date_add;

	public $date_upd;

		public static $definition = array(
		'table' => 'payment',
		'primary' => 'id_payment',
		'fields' => array(
			'id_product' => 		array('type' => 1, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
			'id_order' =>                   array('type' => 1, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
			'summ' =>                       array('type' => 1, 'required' => true, 'copy_post' => false),
			'description' => 		array('type' => 3, 'copy_post' => true),
			'status' => 			array('type' => 1, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
			'id_seller' => 			array('type' => 1, 'validate' => 'isUnsignedId', 'required' => true, 'size' => 32),
			'date_add' => 			array('type' => 5, 'validate' => 'isDateFormat', 'copy_post' => false),
			'date_upd' => 			array('type' => 5, 'validate' => 'isDateFormat', 'copy_post' => false)),
	);
	protected $fieldsRequired = array('id_seller','summ');
	protected $fieldsSize = array('description' => 255);
	protected $fieldsValidate = array('id_product' => 'isUnsignedId',
		'id_order' => 'isUnsignedId','id_seller' => 'isUnsignedId','summ' => 'isFloat','description' => 'isGenericName');


	protected $table = 'payment';
	protected $identifier = 'id_payment';
	public $def_currency_sign;

	public function getFields()
	{
				$fields = array();
		parent::validateFields();
		if (isset($this->id))
		$fields['id_payment'] = (int)$this->id;
		$fields['id_product'] = (int)$this->id_product;
		$fields['id_order'] = (int)$this->id_order;
		$fields['id_seller'] = (int)$this->id_seller;
		$fields['summ'] = (float)$this->summ;
		$fields['description'] = pSQL($this->description);
		$fields['status'] = (int)$this->status;
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		return $fields;
	}

	public static function getBallance($id_customer)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT SUM(summ)
		FROM `'._DB_PREFIX_.'payment`
		WHERE `id_seller` = "'.PaymentsCore::getManufacturer($id_customer).'"');
	}

	public static function getPayments($id_manufacturer)
	{
		if ($id_manufacturer)
		{
			return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT p.*, '.(_PS_VERSION_ < 1.5 ? '0 as is_virtual' : 'prod.`is_virtual`').'
			FROM `'._DB_PREFIX_.'payment` p
			LEFT JOIN `'._DB_PREFIX_.'product` prod ON prod.`id_product` = p.`id_product`
			LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = p.`id_order`
			WHERE p.`id_seller` = '.$id_manufacturer.' AND (o.`valid` = 1 OR p.`id_product` = 0 OR o.`module` = \'internalpay\')
			ORDER BY p.`date_upd` DESC');
		}
		else return Array();
	}

	public static function paymentExists($id_order, $id_product)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT *
		FROM `'._DB_PREFIX_.'payment`
		WHERE `id_order` = '.(int)$id_order.
		' AND `id_product` = '.(int)$id_product.' AND `status` != 5 ');
	}
	public static function sendMail($title, $s_template = '', $a_template_vars = array())
	{
				if (!Configuration::get('ADDPROD_IDSERV')) return true;
				$default_lang = new Language(Configuration::get('PS_LANG_DEFAULT'));
		$contact = new Contact(Configuration::get('ADDPROD_IDSERV'), $default_lang->id);
		$from = Configuration::get('PS_SHOP_EMAIL');
		return sendMail(Mail::l($title), $s_template, $contact->name, $contact->email, $a_template_vars, $from, $default_lang->id);
	}
	public static function groupEnabled($id_customer)
	{
		$groups = Customer::getGroupsStatic($id_customer);
		$groups_enabled = Tools::jsonDecode(Configuration::get('ADDPROD_GROUP'));
		$in_groups_array = false;
		foreach ($groups as $group)
		{
			if (in_array($group, $groups_enabled))
			{
				$in_groups_array = true;
				break;
			}
		}
		return $in_groups_array;
	}
	public static function getManufacturer($user_id)
	{
		$query = 'SELECT `manufacturer_id` FROM `'._DB_PREFIX_."addprod_manufacturers` WHERE `user_id` = '{$user_id}'";
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}
	public static function getCustomer($id_manufacture = null)
	{
		$query = 'SELECT `user_id` FROM `'._DB_PREFIX_."addprod_manufacturers` WHERE `manufacturer_id` = '{$id_manufacture}'";
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}
	public static function getManufacturerPercent($manufacturer_id)
	{
		$query = 'SELECT `user_id` FROM `'._DB_PREFIX_."addprod_manufacturers` WHERE `manufacturer_id` = '$manufacturer_id'";
		$customer = new Customer(Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query));
		$i_default_group = (int)$customer->id_default_group;
		$percents = Tools::jsonDecode(Configuration::get('ADDPROD_PERCENT'));
	$groups_enabled = Tools::jsonDecode(Configuration::get('ADDPROD_GROUP'));
		$max_percent = 100;
		if (!in_array($i_default_group, $groups_enabled)) return $max_percent;
		$i_tmp_percent = (isset($percents->{$i_default_group}) ? $percents->{$i_default_group} : $max_percent);
		return $i_tmp_percent;
		/*
		foreach($customer_groups as $group_id)
		{
    		if (in_array($group_id, $groups_enabled) && (int)$percents->{$group_id} < $max_percent) $max_percent = (int)$percents->{$group_id};
		}
		return $max_percent;
		*
		*/
	}
	public static function createManufacturer($user_id, $manufacturer_id)
	{
		$query = 'INSERT INTO `'._DB_PREFIX_."addprod_manufacturers` (`user_id`, `manufacturer_id`)
				VALUES ('$user_id', '$manufacturer_id')";
		$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($query);
		return $res;
	}
	public static function getAllSalers()
	{
		$sql = "SELECT am.`manufacturer_id` as id, CONCAT_WS(' ', am.`manufacturer_id`, m.`name`) as name FROM `"._DB_PREFIX_.'addprod_manufacturers` am
				INNER JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = am.`manufacturer_id`';
		return Db::getInstance()->ExecuteS($sql);
	}
	public static function deletePaymentByOrderIdAndProductId($i_order_id = null, $i_product_id = null)
	{
		if (!$i_order_id || !$i_product_id) return false;
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'payment` WHERE `id_order` = '.(int)$i_order_id.' AND `id_product` = '.(int)$i_product_id);
	}
}
