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

include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');
require_once(dirname(__FILE__).'/PaymentsCore.php');

class AdminPayments extends AdminTab
{
	public function __construct()
	{
		$this->table = 'payment';
		$this->className = 'PaymentsCore';
		$this->add = true;
		$this->edit = true;
		$this->delete = true;

		$this->_select = 'c.`firstname` as firstname ';
				$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'addprod_manufacturers` m ON (m.`manufacturer_id` = a.`id_seller`)
				LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = m.`user_id`)';
		$this->_where = 'AND a.`status`=1';

		$this->fieldsDisplay = array(
		'id_payment' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'firstname' => array('title' => $this->l('Vendor'), 'width' => 130),
		'summ' => array('title' => $this->l('Amount'), 'width' => 130),
		'description' => array('title' => $this->l('Description'), 'width' => 150));

		parent::__construct();
	}

	public function displayForm()
	{
		$currentIndex = $GLOBALS['currentIndex'];
		parent::displayForm();

		if (!($obj = $this->loadObject(true)))
			return;

		echo '
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend>'.$this->l('Payments').'</legend>
				<label>'.$this->l('Vendor ID:').' </label>
				<div class="margin-form">
				    <input type="text" size="33" name="id_seller" value="'.htmlentities($this->getFieldValue($obj, 'id_seller'), ENT_COMPAT, 'UTF-8').'" />
				<p style="clear: both">'.$this->l('At manufacturers tab').'</p>
				</div>
				<label>'.$this->l('Amount').'</label>
				<div class="margin-form">
					<input type="text" size="33" name="summ" value="'.htmlentities($this->getFieldValue($obj, 'summ'), ENT_COMPAT, 'UTF-8').'" />
					<p style="clear: both">'.$this->l('Amount').'</p>
				</div>
				<label>'.$this->l('Status').'</label>
				<div class="margin-form">
				    <select name="status">
					<option value="0" '.($this->getFieldValue($obj, 'status') == 0?'selected="selected"':'').'>'.$this->l('Sale').'</option>
					<option value="1" '.($this->getFieldValue($obj, 'status') == 1?'selected="selected"':'').'>'.$this->l('Withdraw query').'</option>
					<option value="2" '.($this->getFieldValue($obj, 'status') == 2?'selected="selected"':'').'>'.$this->l('Approved').'</option>
					<option value="4" '.($this->getFieldValue($obj, 'status') == 4?'selected="selected"':'').'>'.$this->l('Up Account').'</option>
				    </select>
				</div><div class="clear">&nbsp;</div>
				<label>'.$this->l('Comment').'</label>
				<div class="margin-form">
					<div>
						<textarea name="description" cols="36" rows="5">'.htmlentities($this->getFieldValue($obj, 'description'), ENT_COMPAT, 'UTF-8').'</textarea>
					<p style="clear: both">'.$this->l('Message').'</p>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
			</fieldset>
		</form>';
	}
}
