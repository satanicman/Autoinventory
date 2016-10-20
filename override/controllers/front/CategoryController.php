<?php

class CategoryController extends CategoryControllerCore
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

    public function init()
    {
        parent::init();
        $customer = new Customer(Category::getCustomer($this->category->id));
        $this->category->address = new Address(Address::getFirstCustomerAddressId($customer->id));
        $this->category->customer = $customer;
        $this->category->time = $customer->getTime();
        $this->category->billing = $customer->getBilling();
        $this->category->offers = $customer->getOffers();
        $this->category->phones = $customer->getPhones();
        if($this->category->address->id_state) {
            $this->category->address->state = State::getNameById($this->category->address->id_state);
        }
    }
}
