<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ManufacturerController extends ManufacturerControllerCore
{
    public function setMedia()
    {
        parent::setMedia();
        $this->context->controller->addJS(_PS_JS_DIR_.'jquery/jquery-ui-1.8.10.custom.min.js');
        $this->context->controller->addJQueryUI('ui.slider');
        $this->context->controller->addCSS(_PS_CSS_DIR_.'jquery-ui-1.8.10.custom.css');

        $this->addJS(_THEME_JS_DIR_.'manufacturer.js');
        $this->addCSS(_THEME_CSS_DIR_.'manufacturer.css');
        $this->addCSS(_THEME_CSS_DIR_.'product_list.css');
    }
}
