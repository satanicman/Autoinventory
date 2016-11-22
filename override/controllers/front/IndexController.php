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

class IndexController extends IndexControllerCore
{
    protected $id_category_layered = 14;

    protected $id_feature_make = 19;

    protected $id_feature_model = 20;

    protected $id_feature_type = 31;

    protected $id_feature_MonthlyPayment = 32;

    protected $id_feature_MonthsRemaining= 33;

    protected $id_feature_zip= 34;

    protected $id_feature_distance= 17;

    protected function getUrlFeatureValue($data){
        if(!$data)
            return array();
        if (!$anchor = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR'))
            $anchor = '-';

        foreach ($data as &$part){
            $part['url_name'] = str_replace($anchor, '_', Tools::link_rewrite($part['value']));
        }
        return $data;
    }

    protected function getUrlFeature($id_feature){
        $sql = "SELECT url_name FROM "._DB_PREFIX_."layered_indexable_feature_lang_value 
            WHERE id_feature={$id_feature} AND id_lang = {$this->context->language->id}";
        return Db::getInstance()->getValue($sql);
    }

    public function initContent()
    {
        parent::initContent();
        $this->addCSS(_THEME_CSS_DIR_.'index.css');
        $this->context->controller->addJQueryUI('ui.slider');
        $this->context->controller->addCSS(_PS_CSS_DIR_.'jquery-ui-1.8.10.custom.css');
        
        $make_feature_url = $this->getUrlFeature($this->id_feature_make);
        $makes = FeatureValue::getFeatureValuesWithLang($this->context->language->id,$this->id_feature_make);
        $makes = $this->getUrlFeatureValue($makes);

        $model_feature_url = $this->getUrlFeature($this->id_feature_model);
        $models = FeatureValue::getFeatureValuesWithLang($this->context->language->id,$this->id_feature_model);
        $models = $this->getUrlFeatureValue($models);

        $type_feature_url = $this->getUrlFeature($this->id_feature_type);
        $types = FeatureValue::getFeatureValuesWithLang($this->context->language->id,$this->id_feature_type);
        $types = $this->getUrlFeatureValue($types);

        $MonthlyPayment_url = $this->getUrlFeature($this->id_feature_MonthlyPayment);
        $MonthlyPayment = FeatureValue::getFeatureValuesWithLang($this->context->language->id,$this->id_feature_MonthlyPayment);
        $MonthlyPayment = $this->getUrlFeatureValue($MonthlyPayment);

        $MonthsRemaining_url = $this->getUrlFeature($this->id_feature_MonthsRemaining);
        $MonthsRemaining = FeatureValue::getFeatureValuesWithLang($this->context->language->id,$this->id_feature_MonthsRemaining);
        $MonthsRemaining = $this->getUrlFeatureValue($MonthsRemaining);

        $zip_url = $this->getUrlFeature($this->id_feature_zip);
        $zip = FeatureValue::getFeatureValuesWithLang($this->context->language->id,$this->id_feature_zip);
        $zip = $this->getUrlFeatureValue($zip);

        $distance_url = $this->getUrlFeature($this->id_feature_distance);
//        $distance = FeatureValue::getFeatureValuesWithLang($this->context->language->id,$this->id_feature_distance);
//        $distance = $this->getUrlFeatureValue($distance);
        $distance = array();
        $result =  Db::getInstance()->executeS("SELECT DISTINCT(`miles`) FROM "._DB_PREFIX_."product WHERE `miles` > 0  ORDER BY `miles` ASC");
        foreach ($result as $row)
            $distance[] = $row['miles'];
        $distance_max = Db::getInstance()->getValue("SELECT MAX(`miles`) FROM "._DB_PREFIX_."product");
        // category
        $category = new Category(Configuration::get('PS_WRAP_CATEGORY'), $this->context->language->id);
        $products = $category->getProducts($this->context->language->id, false, false, null, null, false, true, false, 1, true, null, 0, true);

        $price = array(
            'max' => 0,
            'min' => null,
        );

        foreach ($products as $product) {
            if(isset($product['price'])) {
                if(is_null($price['min'])) {
                    $price['min'] = $product['price'];
                } elseif($price['min'] > $product['price']) {
                    $price['min'] = $product['price'];
                }

                if($product['price'] > $price['max'])
                    $price['max'] = $product['price'];
            }
        }

        $this->context->smarty->assign(array(
            'prices' => $price,
            'layered_category_url' =>$this->context->link->getCategoryLink($this->id_category_layered),
            'make_feature_url' => $make_feature_url,
            'model_feature_url' => $model_feature_url,
            'type_feature_url' => $type_feature_url,
            'MonthlyPayment_url' => $MonthlyPayment_url,
            'MonthsRemaining_url' => $MonthsRemaining_url,
            'zip_url' => $zip_url,
            'distance_url' => $distance_url,
            'distance_max' => $distance_max,
            'MonthlyPayment' => $MonthlyPayment,
            'MonthsRemaining' => $MonthsRemaining,
            'distance' => $distance,
            'zip' => $zip,
            'id_feature_make' => $this->id_feature_make,
            'id_feature_model' => $this->id_feature_model,
            'id_feature_MonthlyPayment' => $this->id_feature_MonthlyPayment,
            'id_feature_MonthsRemaining' => $this->id_feature_MonthsRemaining,
            'id_feature_zip' => $this->id_feature_zip,
            'id_feature_distance' => $this->id_feature_distance,
            'makes' => $makes,
            'models' => $models,
            'types' => $types,
        ));
    }

    public function postProcess()
    {

        parent::postProcess(); // TODO: Change the autogenerated stub
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $id_feature_value = Tools::getValue('id_feature_value');
            $models = Feature::getFeatureValuesWithLang($this->context->language->id,$this->id_feature_model,false,$id_feature_value);
            $models = $this->getUrlFeatureValue($models);
            die(Tools::jsonEncode($models));

        }

    }
}
