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
class Link extends LinkCore
{
	/*
    * module: addprod
    * date: 2016-07-18 00:58:37
    * version: 1.6.1
    */
    public function getLanguageLink($id_lang, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();
		$params = $_GET;
		unset($params['isolang'], $params['controller']);
		if (!$this->allow)
			$params['id_lang'] = $id_lang;
		else
			unset($params['id_lang']);
		$controller = Dispatcher::getInstance()->getController();
		if (!empty(Context::getContext()->controller->php_self))
			$controller = Context::getContext()->controller->php_self;
		if ($controller == 'product' && isset($params['id_product']))
			return $this->getProductLink((int)$params['id_product'], null, null, null, (int)$id_lang);
		elseif ($controller == 'category' && isset($params['id_category']))
			return $this->getCategoryLink((int)$params['id_category'], null, (int)$id_lang);
		elseif ($controller == 'supplier' && isset($params['id_supplier']))
			return $this->getSupplierLink((int)$params['id_supplier'], null, (int)$id_lang);
		elseif ($controller == 'manufacturer' && isset($params['id_manufacturer']))
			return $this->getManufacturerLink((int)$params['id_manufacturer'], null, (int)$id_lang);
		elseif ($controller == 'cms' && isset($params['id_cms']))
			return $this->getCMSLink((int)$params['id_cms'], null, false, (int)$id_lang);
		elseif ($controller == 'cms' && isset($params['id_cms_category']))
			return $this->getCMSCategoryLink((int)$params['id_cms_category'], null, (int)$id_lang);
		elseif (isset($params['fc']) && $params['fc'] == 'module')
		{
			$module = Validate::isModuleName(Tools::getValue('module')) ? Tools::getValue('module') : '';
			if (!empty($module))
			{
				unset($params['fc'], $params['module']);
				return $this->getModuleLink($module, $controller, $params, false, (int)$id_lang);
			}
		}
		return $this->getPageLink($controller, false, $id_lang, $params);
	}
}