<?php
/**
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

class FrontController extends FrontControllerCore
{
    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'fonts.css', 'all');

//        flexslider
        $this->addCSS(_THEME_CSS_DIR_.'flexslider.css', 'all');
        $this->addJS(_THEME_JS_DIR_.'jquery.flexslider-min.js');
    }

    public function init()
    {
        parent::init();


        $date = new DateTime('now', new DateTimeZone('Europe/Kiev'));
        $date_end = $date->format("Y-m-d H:i:s");
        $sql = "SELECT id_customer, id_billing_info FROM "._DB_PREFIX_."customer WHERE date_end <= '".$date_end."'";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if($result) {
            foreach ($result as $customer) {
                $category_id = Category::getManufacturer($customer['id_customer']);
                $billing = new Billing($customer['id_billing_info']);
                $response = $billing->createSubscription(30);
                $customer = new Customer($customer['id_customer']);
                if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") ) {
                    $date = new DateTime('+ 30 day', new DateTimeZone('Europe/Kiev'));
                    $subscription_id = $response->getSubscriptionId();
                    $customer->date_end = $date->format("Y-m-d H:i:s");
                    $customer->subscription_id = $subscription_id;
                    $customer->update();
                    continue;
                } elseif ($category_id) {
                    $c = new Category($category_id);
                    $c->active = 0;
                    $c->update();
                    Mail::Send(
                        $this->context->language->id,
                        'subscription',
                        Mail::l('Error!'),
                        array(
                            '{business_name}' => $customer->business_name,
                            '{email}' => $customer->email
                        ),
                        Configuration::get('PS_SHOP_EMAIL'),
                        Configuration::get('PS_SHOP_NAME')
                    );
                }
            }
        }
    }
}
