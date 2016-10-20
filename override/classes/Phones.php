<?php

class Phones extends ObjectModel
{
    public $phone;
    public $ext;
    public $type;
    public $id_customer;

    public static $definition = array(
        'table' => 'phones',
        'primary' => 'id_phone',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            'phone' =>                      array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 128, 'required' => true),
            'ext' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 128, 'required' => true),
            'type' =>                       array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 128, 'required' => true),
            'id_customer' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
        )
    );

    public function __construct($id_phone = null)
    {
        parent::__construct($id_phone);
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

    public static function getCustomerPhones($id_customer = false) {
        if(!$id_customer)
            return false;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'phones WHERE id_customer = ' . (int)$id_customer);
    }

    public static function removeCustomerPhones($id_customer = false) {
        if (!$id_customer)
            return false;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->delete('phones', 'id_customer = '.$id_customer);
    }
}
