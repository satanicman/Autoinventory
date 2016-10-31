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

class Image extends ImageCore
{
    public static function getImageIdByLegend($legend, $product_id) {
        if(!$legend || !$product_id)
            return false;

        $sql = "SELECT i.id_image FROM ". _DB_PREFIX_ ."image_lang il
                LEFT JOIN ". _DB_PREFIX_ ."image i ON i.id_image = il.id_image
                WHERE il.`legend` = '".stripslashes($legend) . "' AND i.id_product = ".$product_id;

        return Db::getInstance()->getValue($sql);
    }
}
