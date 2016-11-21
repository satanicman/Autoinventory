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

class MyAccountController extends MyAccountControllerCore
{
    public $auth = true;
    public $php_self = 'my-account';
    public $authRedirection = 'my-account';
    public $ssl = true;
    public $tab = 'main';
    protected $product;

    public function __construct()
    {
        parent::__construct();
        if (Tools::getValue('tab'))
            $this->tab = Tools::getValue('tab');
    }

    public function postProcess()
    {
        parent::postProcess();

        $this->ajax = Tools::isSubmit('ajax', 0);

        if(Tools::isSubmit('downloadImages'))
            $this->downloadImages();
        elseif(Tools::isSubmit('removeImages')) {
            $this->removeImages();
        } elseif(Tools::isSubmit('unSubscribe')) {
            $this->unSubscribe();
        } else {

            if (Tools::isSubmit('updateCustomer'))
                $this->updateCustomer();

            if (Tools::isSubmit('updateBilling'))
                $this->updateBilling();

            if (Tools::isSubmit('updateEmail'))
                $this->updateEmail();

            if (Tools::isSubmit('updatePassword'))
                $this->updatePassword();
        }

    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_ . 'my-account.css');
        $this->addJS(array(
            _MODULE_DIR_ . 'addprod/views/js/tiny_mce/tiny_mce.js',
            _THEME_JS_DIR_ . 'my-account.js',
        ));

        if ($this->tab == 'list-a-car') {
            $this->addJS(
                array(
                    _THEME_JS_DIR_ . 'list.a.car.js',
                    _THEME_JS_DIR_ . 'jquery.filer.min.js',
                    _THEME_JS_DIR_ . 'edmunds-logic.js',
                    _THEME_JS_DIR_ . 'edmunds.js',
                )
            );
            $this->addCSS(
                array(
                    _THEME_CSS_DIR_ . 'jquery.filer.css',
                    _THEME_CSS_DIR_ . 'jquery.filer-dragdropbox-theme.css',
                    _THEME_CSS_DIR_ . 'list-a-car.css'
                )
            );
        } elseif ($this->tab == 'main') {
            $this->addJS(
                array()
            );
            $this->addCSS(
                array(
                    _THEME_CSS_DIR_ . 'product-list-myacc.css',
                    _THEME_CSS_DIR_ . 'inventory.css'
                )
            );
        } elseif ($this->tab == 'leads') {
            $this->addCSS(
                array(
                    _THEME_CSS_DIR_ . 'leads.css'
                )
            );
        } elseif ($this->tab == 'dealer') {
            $this->addCSS(
                array(
                    _THEME_CSS_DIR_ . 'dealer.css'
                )
            );
            $this->addJS(
                array(
                    _THEME_JS_DIR_ . 'dealer.js'
                )
            );
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if (Tools::isSubmit('myAccountAddProduct')) {
            $this->addProduct();
        }

        $has_address = $this->context->customer->getAddresses($this->context->language->id);
        $this->context->smarty->assign(array(
            'has_customer_an_address' => empty($has_address),
            'voucherAllowed' => (int)CartRule::isFeatureActive(),
            'returnAllowed' => (int)Configuration::get('PS_ORDER_RETURN')
        ));
        $this->context->smarty->assign('HOOK_CUSTOMER_ACCOUNT', Hook::exec('displayCustomerAccount'));

        if ($this->tab == 'main') {
            $content = $this->initInventory();
        } elseif($this->tab == 'leads') {
            $content = $this->initLeads();
        } elseif($this->tab == 'dealer') {
            $content = $this->initDealer();
        } elseif($this->tab == 'list-a-car') {
            $content = $this->initListCar();
        } else {
            Tools::redirect('my-account');
        }

        if (isset($content) && $content)
            $this->context->smarty->assign('content', $content);

        $messages = Question::getCustomerMassage($this->context->customer->id, 0);
        if (isset($messages) && $messages)
            $this->context->smarty->assign('massage_count', count($messages));

        $this->setTemplate(_PS_THEME_DIR_ . 'my-account.tpl');
    }

    private function initListCar()
    {
        $features = Feature::getFeatureFeatures($this->context->language->id, (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP));
        $fields = Feature::getFieldFeatures($this->context->language->id, (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP));

        $categories = FeatureValue::getFeatureValuesWithLang($this->context->language->id, 31);

        $this->context->smarty->assign(
            array(
                'available_features' => $features,
                'fields' => $fields,
                'categories' => $categories
            )
        );
//        Редактирование товара
        $addprod_manufacturer_id = PaymentsCore::getManufacturer((int)$this->context->cookie->id_customer);
        if($addprod_manufacturer_id && isset($_GET['id_product']) && $id_product = $_GET['id_product']) {
            if(!Product::idIsOnCategoryId($id_product, array(array('id_category' => $addprod_manufacturer_id)))) {
                $this->errors[] = Tools::displayError("Wow wow! This is not your car dude!!!");
            }
            if(isset($_GET['status']) && $status = $_GET['status']) {
                $product = new Product($id_product, true, $this->context->language->id, $this->context->shop->id);
                if($status != 4) {
                    $product->status = $status == -1 ? 0 : $status;
                    if ($status == 3)
                        $product->active = 0;

                    $product->update();
                } else {
                    $product->delete();
                }
                Tools::redirect('my-account');
            }
            if(!$this->errors) {
                $this->product = new Product($id_product, true, $this->context->language->id, $this->context->shop->id);
                $feature = array();
                $fields = array();
                foreach($this->product->getFrontFeatures($this->context->language->id) as $f) {
                    $feature[$f['id_feature']] = $f;
                }
                foreach($this->product->getFrontFeatures($this->context->language->id, 0) as $f) {
                    $fields[$f['id_feature']] = $f;
                }
                $this->product->features = $feature;
                $this->product->fields = $fields;
                $this->context->smarty->assign('product', $this->product);
            }
        }

        return $this->context->smarty->fetch(_PS_THEME_DIR_ . 'list-a-car.tpl');
    }

    private function initInventory()
    {
        $this->orderBy = Tools::getValue('orderBy') ? Tools::getValue('orderBy') : 'position';
        $this->orderWay = Tools::getValue('orderWay') ? Tools::getValue('orderWay') : 'asc';
        $manufacturer_id = PaymentsCore::getManufacturer((int)$this->context->cookie->id_customer);
        if($manufacturer_id) {
            $category = new Category($manufacturer_id, Configuration::get('PS_LANG_DEFAULT'));
            $products = array();
            $products[0]['nbProducts'] = $category->getProducts(null, null, null, $this->orderBy, $this->orderWay, true);
            $products['pending']['nbProducts'] = $category->getProducts(null, null, null, $this->orderBy, $this->orderWay, true, false);
            $products[3]['nbProducts'] = $category->getProducts(null, null, null, $this->orderBy, $this->orderWay, true, false, false, 1, true, null, 3);

            foreach ($products as $type => $product) {
                $this->pagination((int)$product['nbProducts']); // Pagination must be call after "getProducts"

                if($type === 'pending') {
                    $product['cat_products'] = $products[$type]['cat_products'] = $category->getProducts($this->context->language->id, (int)$this->p, (int)$this->n, $this->orderBy, $this->orderWay, false, false);
                } else
                    $product['cat_products'] = $products[$type]['cat_products'] = $category->getProducts($this->context->language->id, (int)$this->p, (int)$this->n, $this->orderBy, $this->orderWay, false, ($type == 3 ? false : true), false, 1, true, null, $type);

                $this->addColorsToProductList($product['cat_products']);

                Hook::exec('actionProductListModifier', array(
                    'nb_products' => &$product['nbProducts'],
                    'cat_products' => &$product['cat_products'],
                ));

                foreach ($product['cat_products'] as &$product) {
                    if (isset($product['id_product_attribute']) && $product['id_product_attribute'] && isset($product['product_attribute_minimal_quantity'])) {
                        $product['minimal_quantity'] = $product['product_attribute_minimal_quantity'];
                    }
                }
            }

            $this->context->smarty->assign(array(
                'products' => $products,
            ));
        } else {
            $this->context->smarty->assign(array(
                'massage' => 'Sorry but you don`t have any products!'
            ));
        }

        return $this->context->smarty->fetch(_PS_THEME_DIR_ . 'inventory.tpl');
    }

    private function initLeads()
    {
        if($id_question = Tools::getValue('id_question')) {
            $q = new Question($id_question);
            if($q->id_customer != $this->context->customer->id)
                $this->errors[] = Tools::displayError('This is not you`r message');
            if(!$this->errors) {
                if(Tools::getValue('delete'))
                    Question::changeStatus($id_question);
                else
                    Question::changeStatus($id_question, 1);
            }
        }
        $messages = Question::getCustomerMassage($this->context->customer->id);

        $message = array();
        foreach ($messages as $key => $m) {
            $product = new Product($m['id_product'], true, (int)$this->context->cookie->id_lang);
            $messages[$key]['product']['id'] = $product->id;
            $messages[$key]['product']['name'] = $product->name[$this->context->language->id];
            $messages[$key]['product']['link_rewrite'] = $product->link_rewrite[$this->context->language->id];
            $messages[$key]['product']['image'] = Image::getCover((int)$product->id);
            $message[$m['status']][] = $messages[$key];
        }

//        for ($i = 1; $i <= 10; $i++) {
//            $leads_new[$i] = array(
//                'product' => array(
//                    'id_product' => 41,
//                    'name' => "2015 Bmw M6 Grand Courpe - $i",
//                    'link_rewrite' => '1',
//                    'link' => 'http://auto.dalsund.com/buy/41-1.html',
//                    'id_image' => '41-70',
//                ),
//                'name' => "Name user $i",
//                'phone' => "123-32$i",
//                'email' => "test$i@test.test",
//                'message' => "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
//tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
//quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
//consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
//cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
//proident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
//            );
//        }
//
//        $leads_checked = $leads_new;

        $this->context->smarty->assign(array(
            'messages' => $message
        ));

        return $this->context->smarty->fetch(_PS_THEME_DIR_ . 'leads.tpl');
    }

    private function initDealer()
    {
        $customer = new Customer((int)$this->context->cookie->id_customer);
        $address = new Address(Address::getFirstCustomerAddressId($customer->id));

        $customer->address = $address->address1;
        $customer->zip = $address->postcode;
        $customer->city = $address->city;
        $customer->state = $address->id_state;
        $states = State::getStates((int)$this->context->language->id, true);

        $phones = Phones::getCustomerPhones($customer->id);

        $customer_phones = array();
        foreach ($phones as $phone) {
            $type = $phone['type'];
            unset($phone['type']);
            unset($phone['id_phone']);
            unset($phone['id_customer']);
            $customer_phones[$type][] = $phone;
        }
        $customer_offers = Offers::getCustomerOffers($customer->id);
        foreach ($customer_offers as $key => $offer) {
            $customer_offers[$key] = $offer['id_offers'];
        }

        $billing = new Billing($customer->id_billing_info);

        $category_id = $this->getManufacturer((int)$this->context->customer->id);

        $category = array();
        if ($category_id)
            $category = new Category($category_id, $this->context->language->id);

        $this->context->smarty->assign(array(
            'customer' => $customer,
            'states' => $states,
            'phones' => $customer_phones,
            'offers' => Offers::getOffers(),
            'customer_offers' => $customer_offers,
            'times' => $customer->getTime(),
            'billing' => $billing,
            'category' => $category,
        ));

        return $this->context->smarty->fetch(_PS_THEME_DIR_ . 'dealer.tpl');
    }

    private function addProduct()
    {
        $addprod_manufacturer_id = PaymentsCore::getManufacturer((int)$this->context->cookie->id_customer);
        $customer = new Customer((int)$this->context->cookie->id_customer);
        $default_lang = Configuration::get('PS_LANG_DEFAULT');

        if ($addprod_manufacturer_id)
            $manufacturer = new Category($addprod_manufacturer_id, $default_lang);
        else {
            $manufacturer = new Category(null, $default_lang);
            $manufacturer->name = ($customer->firstname . ' ' . $customer->lastname);
            $manufacturer->link_rewrite = Tools::link_rewrite($customer->firstname . ' ' . $customer->lastname);
            $manufacturer->id_parent = Configuration::get('PS_WRAP_CATEGORY');
            $manufacturer->add();
            PaymentsCore::createManufacturer($this->context->cookie->id_customer, $manufacturer->id);
        }

        //Редактируем
        if ($id_edit = Tools::GetValue('product_id')) {
            if(!Product::idIsOnCategoryId($id_edit, array(array('id_category' => $manufacturer->id))))
                $this->errors[] = Tools::displayError('Do not touch someone elses product');

            $product = new Product((int)$id_edit, false, $default_lang);
        } else {
            //Продукт
            $product = new Product(null, false, $default_lang);
        }

        // custom fields
        foreach (Tools::getValue('features') as $id => $feature) {
            if(isset($feature['required']) && $feature['required']) {
                if(!isset($feature['value']) || empty($feature['value'])) {
                    $this->errors[] = Tools::displayError('Do not enter a ' . $feature['required'] . ' of the car');
                }
                if (strlen($feature['value']) > 250) {
                    $this->errors[] = Tools::displayError($feature['required'] . ' is too long. Max length 250 characters.');
                }
            }
        }

        if (!Validate::isInt($year = Tools::getValue('year'))
            || empty($year)
        )
            $this->errors[] = Tools::displayError('Year is not valid');

        if ((!Validate::isInt($miles = Tools::getValue('miles'))
            || empty($miles)) && Tools::getValue('type') != 'leas'
        )
            $this->errors[] = Tools::displayError('Mileage is not valid');

        if (!Validate::isCatalogName($category = Tools::getValue('categories'))
            || empty($category)
        )
            $this->errors[] = Tools::displayError('Category Not selected');

        if ((!Validate::isCleanHtml($description = Tools::getValue('description'))
            || Tools::strlen($description) < 10) && Tools::getValue('type') != 'leas'
        )
            $this->errors[] = Tools::displayError('No description, or it is too short');

        if ($_FILES && $_FILES['image_product'] && $_FILES['image_product']['name']) {
            $i = 0;
            foreach ($_FILES['image_product']['tmp_name'] as $image_product) {
                if (file_exists($image_product)) {
                    if (!ImageManager::isRealImage($image_product, $_FILES['image_product']['type'][$i])) {
                        $this->errors[] = Tools::displayError('This file type of image is not allowed.');
                        break;
                    }
                }
                $i++;
            }
        }

        if (!count($this->errors)) {
            $features = Tools::getValue('features');

            $product->name[$default_lang] = $features[19]['value'] . ' ' . $features[20]['value'];
            $product->link_rewrite[$default_lang] = Tools::link_rewrite($features[19]['value']);

            $product->description[$default_lang] = $description;
            $product->price = (float)Tools::getValue('price');
            $product->year = $year;
            $product->miles = $miles;

            $product->quantity = 1;
            $product->minimum_quantity = 1;
            $product->id_tax = 0;
            $product->id_category_default = $manufacturer->id;

            if ($id_edit) {
                $product->active = Configuration::get('ADDPROD_UPDACTIVE');
                $result = $product->update();
                $message['text'] = 'Car successfully update';
            } else {
                $product->active = Configuration::get('ADDPROD_ADDACTIVE');
                $result = $product->add();
                $message['text'] = 'Car successfully added';

                $this->context->smarty->assign(array(
                    'add' => 1,
                    'product' => $product,
                ));
            }
            StockAvailable::setProductOutOfStock((int)$product->id, 2);
            if (!$result)
                $this->errors[] = Tools::displayError('This product is not added. Contact the administrator.');
            else {
                $this->context->smarty->assign('message', $message);
                if($id_edit) {
                    $product->deleteFeatures();
                } else {
                    $cats = array(Configuration::get('PS_WRAP_CATEGORY'), $manufacturer->id);
                    $product->addToCategories($cats);
                }

                foreach ($features as $id => $feature) {
                    if ($id === 'features' && $feature) {
                        foreach ($feature as $fid => $f) {
                            $sql = 'SELECT * 
                                    FROM `'._DB_PREFIX_.'feature_value_lang` fvl
                                    LEFT JOIN `'._DB_PREFIX_.'feature_value` fv ON fv.id_feature_value = fvl.id_feature_value
                                    WHERE `value` = "'.(string)$f.'" AND `id_feature` = "' . $fid . '"';
                            if(!$id_feature_value = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
                                $fv = new FeatureValue();
                                $fv->id_feature = $fid;
                                $fv->value[$default_lang] = (string)$f;
                                $fv->add();
                                $id_feature_value = $fv->id;
                            } else {
                                $id_feature_value = $id_feature_value[0]['id_feature_value'];
                            }
                            Product::addFeatureProductImport($product->id, $fid, $id_feature_value);
                        }
                        continue;
                    }

                    if(!$feature['value']) {
                        continue;
                    }

                    $feature_value = strip_tags($feature['value']);
                    if (strlen($feature['value']) > 250) {
                        $feature_value = substr($feature_value, 0, 250);
                    }


                    $sql = 'SELECT * 
                                    FROM `'._DB_PREFIX_.'feature_value_lang` fvl
                                    LEFT JOIN `'._DB_PREFIX_.'feature_value` fv ON fv.id_feature_value = fvl.id_feature_value
                                    WHERE `value` = "'.(string)$feature['value'].'" AND `id_feature` = "' . $id . '"';

                    if(!$id_feature_value = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
                        $fv = new FeatureValue();
                        $fv->id_feature = $id;
                        $fv->value[$default_lang] = (string)$feature_value;
                        $fv->add();
                        $id_feature_value = $fv->id;
                    } else {
                        $id_feature_value = $id_feature_value[0]['id_feature_value'];
                    }

                    Product::addFeatureProductImport($product->id, $id, $id_feature_value);
                }
                //category
                Product::addFeatureProductImport($product->id, 33, $category);

                StockAvailable::setQuantity($product->id, 0, 1);
                if ($_FILES && $_FILES['image_product']['name']) {
                    foreach ($_FILES['image_product']['tmp_name'] as $image_product) {
                        if (file_exists($image_product)) {
                            $image = new Image();
                            $image->id_product = (int)$product->id;
                            $image->position = Image::getHighestPosition($product->id) + 1;
                            $image->cover = !count($product->getImages(Configuration::get('PS_LANG_DEFAULT')));
                            $image->add();

                            if (!$new_path = $image->getPathForCreation())
                                $this->errors[] = Tools::displayError('An error occurred during new folder creation');

                            $tmp_name = $image_product;
                            if (empty($this->errors)) {
                                if (!ImageManager::resize($tmp_name, $new_path . '.' . $image->image_format, null, null, $image->image_format))
                                    $this->errors[] = Tools::displayError('An error occurred while copying base image:');

                                $images_types = ImageType::getImagesTypes('products');
                                foreach ($images_types as $image_type) {
                                    if (!ImageManager::resize($tmp_name, $new_path . '-' .
                                        Tools::stripslashes($image_type['name']) . '.' . $image->image_format, $image_type['width'], $image_type['height'], $image->image_format)
                                    )
                                        $this->errors[] = Tools::displayError('An error occurred while copying image:') . ' ' . Tools::stripslashes($image_type['name']);
                                }
                            }

                            unlink($tmp_name);
                            Hook::exec('actionWatermark', array('id_image' => $image->id, 'id_product' => $product->id));

                            if (!$image->update())
                                $this->errors[] = Tools::displayError('Error while updating status');

                            if (version_compare(_PS_VERSION_, '1.5.1', '>=')) {
                                $shops = Shop::getContextListShopID();
                                $image->associateTo($shops);
                            }
                        }
                    }
                }
            }
        }
    }

    private function updateCustomer()
    {
        $customer = new Customer($this->context->customer->id);
        $customer->business_name = Tools::getValue('business_name');
        $customer->firstname = $customer->business_name;
        $customer->business_description = Tools::getValue('business_description');
        $customer->product_description = Tools::getValue('product_description');
        $customer->facebook = Tools::getValue('facebook');
        $customer->instagram = Tools::getValue('instagram');
        $customer->twitter = Tools::getValue('twitter');
        $customer->site = Tools::getValue('website');
        $this->errors = array_unique(array_merge($this->errors, $customer->validateController()));
        $this->errors = $this->errors + $customer->validateFieldsRequiredDatabase();

        $address = new Address(Address::getFirstCustomerAddressId($this->context->customer->id));
        $address->address1 = Tools::getValue('address');
        $address->city = Tools::getValue('city');
        $address->postcode = Tools::getValue('zip');
        $address->id_state = Tools::getValue('state');
        $this->errors = array_unique(array_merge($this->errors, $address->validateController()));
        $this->errors = $this->errors + $address->validateFieldsRequiredDatabase();

        if(isset($_FILES['images']) && !$_FILES['images']['error']) {
            preg_match('/[\.(jpg|png)]+$/i', $_FILES['images']['name'], $format);
            if(!$format)
                $this->errors[] = Tools::displayError('File format is incorrect');
        }

        if(!$this->errors) {
            $customer->update();
            $address->update();

            if($offers = Tools::getValue('offers')) {
                $customer->setOffers($offers);
            }

            if($time = Tools::getValue('time')) {
                $customer->setTimes($time);
            }

            if($phones = Tools::getValue('phones')) {

                Phones::removeCustomerPhones($this->context->customer->id);

                $p = new Phones();
                foreach ($phones as $type => $phone) {
                    if(!isset($phone['phone']) && is_array($phone)) {
                        foreach($phone as $phone2) {
                            if(!$phone2['phone'])
                                continue;
                            $p->phone = $phone2['phone'];
                            $p->ext = $phone2['ext'];
                            $p->type = $type;
                            $p->id_customer = $customer->id;
                            $p->add();
                        }
                    } else if($phone['phone']) {
                        $p->phone = $phone['phone'];
                        $p->ext = $phone['ext'];
                        $p->type = $type;
                        $p->id_customer = $customer->id;
                        $p->add();
                    }
                }
            }
            $this->updateContext($customer);


            $category_id = $this->getManufacturer((int)$this->context->customer->id);

            if ($category_id)
                $category = new Category($category_id, $this->context->language->id);
            else {
                $category = new Category(null, $this->context->language->id);
            }

            $category->name = $customer->business_name;
            $category->link_rewrite = Tools::link_rewrite($customer->business_name);
            $category->id_parent = Configuration::get('PS_WRAP_CATEGORY');

            if(isset($_FILES['images']) && !$_FILES['images']['error'] && $_FILES['images']['tmp_name']) {
                $category->deleteImage(true);
                preg_match('/[\.(jpg|png)]+$/i', $_FILES['images']['name'], $format);

                $tmpName = _PS_ROOT_DIR_ . _THEME_CAT_DIR_ . $category->id . $format[0];
                if (!$_FILES['images']['error'] && count($format) && !file_exists($tmpName)) {
                    move_uploaded_file($_FILES['images']['tmp_name'], $tmpName);
                }
                $images_types = ImageType::getImagesTypes('categories');
                foreach ($images_types as $k => $image_type) {
                    if (!ImageManager::resize(
                        $tmpName,
                        _PS_ROOT_DIR_ . _THEME_CAT_DIR_ . $category->id . '-' . stripslashes($image_type['name']) . '.' . $format[0],
                        (int)$image_type['width'],
                        (int)$image_type['height']
                    )
                    ) {
                        $this->errors = Tools::displayError('An error occurred while uploading category image.');
                    }
                }
            }

            if ($category_id)
                $category->update();
            else {
                $category->add();
                $category->createManufacturer($this->context->cookie->id_customer, $category->id);
            }
        }
    }

    protected function updateContext(Customer $customer)
    {
        $this->context->customer = $customer;
        $this->context->smarty->assign('confirmation', 1);
        $this->context->cookie->id_customer = (int)$customer->id;
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->logged = 1;
        // if register process is in two steps, we display a message to confirm account creation
        if (!Configuration::get('PS_REGISTRATION_PROCESS_TYPE')) {
            $this->context->cookie->account_created = 1;
        }
        $customer->logged = 1;
        $this->context->cookie->email = $customer->email;
        $this->context->cookie->is_guest = !Tools::getValue('is_new_customer', 1);
        // Update cart address
        $this->context->cart->secure_key = $customer->secure_key;
    }

    public static function getManufacturer($user_id)
    {
        $query = 'SELECT `manufacturer_id` FROM `'._DB_PREFIX_."addprod_manufacturers` WHERE `user_id` = '{$user_id}'";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    private function updateBilling()
    {
        if($this->context->customer->id_billing_info)
            $billing = new Billing($this->context->customer->id_billing_info);
        else
            $billing = new Billing();

        if(Tools::getValue('business_name') && !Validate::isString(Tools::getValue('business_name'))) {
            $this->errors[] = Tools::displayError('Invalid business_name value');
        }
        if(Tools::getValue('adress_1') && !Validate::isString(Tools::getValue('adress_1'))) {
            $this->errors[] = Tools::displayError('Invalid adress_1 value');
        }
        if(Tools::getValue('adress_2') && !Validate::isString(Tools::getValue('adress_2'))) {
            $this->errors[] = Tools::displayError('Invalid adress_2 value');
        }
        if(Tools::getValue('city') && !Validate::isString(Tools::getValue('city'))) {
            $this->errors[] = Tools::displayError('Invalid city value');
        }
        if(Tools::getValue('id_state') && !Validate::isUnsignedId(Tools::getValue('id_state'))) {
            $this->errors[] = Tools::displayError('Invalid state value');
        }
        if(Tools::getValue('zip_code') && !Validate::isInt(Tools::getValue('zip_code'))) {
            $this->errors[] = Tools::displayError('Invalid zip_code value');
        }
        if(Tools::getValue('card_name') && !Validate::isString(Tools::getValue('card_name'))) {
            $this->errors[] = Tools::displayError('Invalid card_name value');
        }
        if(Tools::getValue('card_number') && !Validate::isCreditCard(Tools::getValue('card_number'))) {
            $this->errors[] = Tools::displayError('Invalid card_number value');
        }
        if(Tools::getValue('cvv') && !Validate::isInt(Tools::getValue('cvv'))) {
            $this->errors[] = Tools::displayError('Invalid cvv value');
        }
        if(Tools::getValue('month') && !Validate::isString(Tools::getValue('month'))) {
            $this->errors[] = Tools::displayError('Invalid month value');
        }
        if(Tools::getValue('day') && !Validate::isString(Tools::getValue('day'))) {
            $this->errors[] = Tools::displayError('Invalid day value');
        }
        if (!$this->errors) {
            if ($business_name = Tools::getValue('business_name')) {
                $billing->business_name = $business_name;
            }
            if ($adress_1 = Tools::getValue('adress_1')) {
                $billing->adress_1 = $adress_1;
            }
            if ($adress_2 = Tools::getValue('adress_2')) {
                $billing->adress_2 = $adress_2;
            }
            if ($city = Tools::getValue('city')) {
                $billing->city = $city;
            }
            if ($zip_code = Tools::getValue('zip_code')) {
                $billing->zip_code = $zip_code;
            }
            if ($id_state = Tools::getValue('id_state')) {
                $billing->id_state = $id_state;
            }
            if ($card_name = Tools::getValue('card_name')) {
                $billing->card_name = $card_name;
            }
            if ($card_number = Tools::getValue('card_number')) {
                $billing->card_number = $card_number;
            }
            if ($cvv = Tools::getValue('cvv')) {
                $billing->cvv = $cvv;
            }
            if ($month = Tools::getValue('month')) {
                $billing->month = $month;
            }
            if ($day = Tools::getValue('day')) {
                $billing->day = $day;
            }

            if($this->context->customer->id_billing_info)
                $billing->update();
            else
                $billing->add();
        }
    }

    private function updateEmail()
    {
        $email = Tools::getValue('email');
        $customer = new Customer($this->context->customer->id);
        if($c = $customer->getByEmail($email['old']))
            $customer_id = $c->id;

        if(!isset($email['old']) || !Validate::isEmail($email['old']) || !isset($customer_id) || !$customer_id || $this->context->customer->id != $customer_id)
            $this->errors[] = Tools::displayError('Old email is incorrect');

        if(!isset($email['new']) || !$email['new'] || !Validate::isEmail($email['new']))
            $this->errors[] = Tools::displayError('New email is incorrect');

        if(!isset($email['confirm']) || !$email['confirm'] || !Validate::isEmail($email['confirm']))
            $this->errors[] = Tools::displayError('Second email is incorrect');

        if($email['new'] != $email['confirm'])
            $this->errors[] = Tools::displayError('The second email does not match');

        if(Customer::getCustomersByEmail($email['new']))
            $this->errors[] = Tools::displayError('Customer with this email is exist');

        if(!$this->errors) {
            $customer->email = $email['new'];
            $customer->update();
            $this->updateContext($customer);
        }
    }

    private function updatePassword()
    {
        $passwd = $_POST['password'];
        if(!isset($passwd['old']) || !Validate::isPasswd($passwd['old']) || $this->context->customer->passwd != md5(_COOKIE_KEY_ . $passwd['old']))
            $this->errors[] = Tools::displayError('Old password is incorrect');

        if(!isset($passwd['new']) || !$passwd['new'] || !Validate::isPasswd($passwd['new']))
            $this->errors[] = Tools::displayError('New password is incorrect');

        if(!isset($passwd['confirm']) || !$passwd['confirm'] || !Validate::isPasswd($passwd['confirm']))
            $this->errors[] = Tools::displayError('Second password is incorrect');

        if($passwd['new'] != $passwd['confirm'])
            $this->errors[] = Tools::displayError('The second password does not match');

        if(!$this->errors) {
            $customer = new Customer($this->context->customer->id);
            $customer->passwd = md5(_COOKIE_KEY_.$passwd['new']);
            $customer->update();
            $this->updateContext($customer);
        }
    }

    private function downloadImages()
    {
        $photos = $_FILES['image_product'];
        $product_id = Tools::getValue('id');
        if(!$product_id) {
            if ($this->ajax)
                die(false);
            else
                return false;
        }


        if(!$this->checkProduct($product_id)) {
            if ($this->ajax)
                die(false);
            else
                return false;
        }

        $product = new Product($product_id, true, $this->context->language->id, $this->context->shop->id);

        $image = new Image();
        $image->id_product = $product->id;
        if(!Image::getCover($product->id))
            $image->cover = 1;
        $image->position = 0;
        $image->legend = array_fill_keys(Language::getIDs(), (string)$photos['name'][0] . ' - ' . $product->name);
        $image->save();
        $name = $image->getPathForCreation();

        for ($i = 0; $i < count($photos['tmp_name']); $i++) {
            if ($photos['size'][$i] < 5000000) {
                move_uploaded_file($photos['tmp_name'][$i], $name . '.' . $image->image_format);
            } else {
                if($this->ajax)
                    die(false);
                else
                    return false;
            }
        }
        $types = ImageType::getImagesTypes('products');
        foreach ($types as $type)
            ImageManager::resize($name . '.' . $image->image_format, $name . '-' . $type['name'] . '.' . $image->image_format, $type['width'], $type['height'], $image->image_format);

        if($this->ajax)
            die(true);

        return true;
    }
    private function removeImages()
    {
        $name = Tools::getValue('name');
        $product_id = Tools::getValue('id');

        if(!$this->checkProduct($product_id)) {
            if ($this->ajax)
                die(false);
            else
                return false;
        }

        $product = new Product($product_id, true, $this->context->language->id, $this->context->shop->id);

        if(!$product->name) {
            if ($this->ajax)
                die(false);
            else
                return false;
        }

        $image_id = Image::getImageIdByLegend($name . " - " . $product->name, $product_id);
        
        $image = new Image($image_id);
        $image->deleteImage();

        if ($this->ajax)
            die(false);

        return true;
    }

    private function checkProduct($product_id = false) {
        if(!$product_id)
            return false;

        $addprod_manufacturer_id = Category::getManufacturer((int)$this->context->cookie->id_customer);
        if(!Product::idIsOnCategoryId($product_id, array(array('id_category' => $addprod_manufacturer_id))))
            return false;

        return true;
    }

    private function unSubscribe()
    {
        $customer = new Customer($this->context->cookie->id_customer);
        if($customer->subscription_id) {
            $response = Billing::cancelSubscription($customer->subscription_id);
            if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
//                $customer->subscription_id = '';
//                $customer->update();
//                echo "<pre>";
//                print_r($customer->subscription_id);
//                echo "</pre>";
                if($this->ajax) {
                    die(true);
                } else {
                    return true;
                }
            } else {
                $errorMessages = $response->getMessages()->getMessage();
                $this->errors[] = "Response : " . $errorMessages[0]->getCode() . "  " . $errorMessages[0]->getText();
            }
        } else {
            $this->errors[] = "Sorry but you don't subscribe now";
        }

        if($this->ajax && $this->errors) {
            die(Tools::jsonEncode(
                array(
                    'hasError' => (bool)$this->errors,
                    'errors' => (array)$this->errors
                )
            ));
        }
    }
}
