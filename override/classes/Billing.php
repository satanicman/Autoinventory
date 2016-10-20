<?php

class Billing extends ObjectModel
{
    public $id_state;
    public $business_name;
    public $adress_1;
    public $adress_2;
    public $city;
    public $zip_code;
    public $card_name;
    public $card_number;
    public $cvv;
    public $month;
    public $day;

    public static $definition = array(
        'table' => 'billing_info',
        'primary' => 'id_billing_info',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            'id_state' =>                   array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'business_name' =>              array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 128),
            'adress_1' =>                   array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'adress_2' =>                   array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'city' =>                       array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'zip_code' =>                   array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'card_name' =>                  array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 128),
            'card_number' =>                array('type' => self::TYPE_STRING, 'validate' => 'isCreditCard', 'size' => 128),
            'cvv' =>                        array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'month' =>                      array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 128),
            'day' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 128),
        ),
        'associations' => array(
            'state' =>                      array('type' => self::HAS_ONE)
        ),
    );

    public function __construct($id_billing_info = null)
    {
        $this->id_default_group = (int)Configuration::get('PS_CUSTOMER_GROUP');
        parent::__construct($id_billing_info);
    }



    public function add($autodate = true, $null_values = true)
    {
        $success = parent::add($autodate, $null_values);
        return $success;
    }

    public function update($nullValues = false)
    {
        return parent::update(true);
    }

    public function delete()
    {
        return parent::delete();
    }
}
