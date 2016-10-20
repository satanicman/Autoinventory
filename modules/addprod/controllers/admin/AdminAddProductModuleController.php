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

require_once(dirname(__FILE__).'../../../PaymentsCore.php');

class AdminAddProductModuleController extends ModuleAdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
		$this->meta_title = $this->l('Payments');
		$this->table = 'payment';
		$this->className = 'PaymentsCore';
		$this->identifier = 'id_payment';
		$this->explicitSelect = true;
		$this->lang = false;
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->_select = 'c.`firstname` as firstname ';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'addprod_manufacturers` m ON (m.`manufacturer_id` = a.`id_seller`)
		LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = m.`user_id`)';
		$this->_where = 'AND a.`status`=1';
		parent::__construct();
		if (!$this->module->active)
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
		$this->fields_list = array();
		$this->fields_list['id_payment'] = array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 10
		);
		$this->fields_list['firstname'] = array(
				'title' => $this->l('Vendor'),
				'align' => 'center',
				'width' => 50,
		);
		$this->fields_list['summ'] = array(
				'title' => $this->l('Amount'),
				'align' => 'center',
				'width' => 50,
		);
		$this->fields_list['description'] = array(
				'title' => $this->l('Description'),
				'align' => 'left',
				'width' => 170,
		);

		if (version_compare(_PS_VERSION_, '1.5.1', '<'))
			$this->fieldsDisplay = $this->fields_list;
	}
	public function renderList()
	{
			$this->addRowAction('edit');
			$this->addRowAction('delete');

			return parent::renderList();
	}
	public function renderForm()
	{
		$this->table = 'payment';
		$this->identifier = 'id_payment';

		$group_statuses = array(
				array(
						'id' => 0,
						'name' => $this->l('Sale')
				),
				array(
						'id' => 1,
						'name' => $this->l('Withdraw query')
				),
				array(
						'id' => 2,
						'name' => $this->l('Approved')
				),
				array(
						'id' => 4,
						'name' => $this->l('Up Account')
				),
		);
		$salers = array(array('id' => '', 'name' => $this->l('Select vendor')));
		$salers = array_merge($salers, PaymentsCore::getAllSalers());
		$this->fields_form['legend'] = array('title' => $this->l('Payments'),
											/*'image' => '../img/admin/asterisk.gif'*/);
		if (isset($this->object->id_seller))
		{
			$this->fields_form['input'][] = array('type' => 'hidden',
												'name' => 'id_seller',
												'size' => 3,
												'required' => true,
												'readonly' => true);
			$this->fields_form['input'][] = array('type' => 'text',
												'label' => $this->l('Vendor ID'),
												'name' => 'show_saller',
												'size' => 33,
												'value' => 'yes',
												'required' => true,
												'readonly' => true);
			$manufacturer = new Manufacturer($this->object->id_seller);
			$this->fields_value['show_saller'] = $manufacturer->id.' '.$manufacturer->name;
		}
		else
		{
			$this->fields_form['input'][] = array('type' => 'select',
												'label' => $this->l('Vendor ID'),
												'name' => 'id_seller',
												'required' => true,
												'options' => array(
														'query' => $salers,
														'id' => 'id',
														'name' => 'name'));
		}
		$this->fields_form['input'][] = array('type' => 'text',
											'label' => $this->l('Amount'),
											'name' => 'summ',
											'size' => 20,
											'required' => true,
											'hint' => $this->l('Float value only'));
		$this->fields_form['input'][] = array('type' => 'select',
											'label' => $this->l('Status'),
											'name' => 'status',
											'required' => true,
											'options' => array(
													'query' => $group_statuses,
													'id' => 'id',
													'name' => 'name'));
		$this->fields_form['input'][] = array('type' => 'textarea',
											'label' => $this->l('Comment'),
											'name' => 'description',
											'required' => false,
											'cols' => 25,
											'rows' => 5);
		if (!($this->loadObject(true)))
				return;
		$this->fields_form['submit'] = array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
		);

		return parent::renderForm();
	}
}
