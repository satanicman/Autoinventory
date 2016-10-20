<?php

class Offers extends ObjectModel
{
    public $phone;
    public $ext;
    public $type;
    public $id_customer;

    public static $definition = array(
        'table' => 'offers',
        'primary' => 'id_offers',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            'name' =>                      array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
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

    public static function getOffers() {
        $sql = 'SELECT *
				FROM `'._DB_PREFIX_.'offers`
				ORDER BY `id_offers` ASC';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function getCustomerOffers($id_customer = false) {
        if(!$id_customer)
            return false;

        $sql = 'SELECT `id_offers`
				FROM `'._DB_PREFIX_.'customer_offers`
				WHERE `id_customer` = ' . $id_customer . '
				ORDER BY `id_offers` ASC';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
}
