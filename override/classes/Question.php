<?php

class Question extends ObjectModel
{
    public $name;
    public $phone;
    public $mail;
    public $text;
    public $id_product;
    public $id_customer;
    public $status;

    public static $definition = array(
        'table' => 'question',
        'primary' => 'id_question',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            'name' =>         array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 128, 'required' => true),
            'phone' =>        array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 128, 'required' => true),
            'mail' =>         array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 128, 'required' => true),
            'text' =>         array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
            'id_product' =>   array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            'id_customer' =>  array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            'status' =>       array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
        ),
    );

    public function __construct($id_question = null)
    {
        $this->id_question = (int)Configuration::get('PS_CUSTOMER_GROUP');
        parent::__construct($id_question);
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

    public static function getCustomerMassage($id_customer = false, $status = null) {
        if(!$id_customer)
            return false;
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "question";
        $where = " WHERE id_customer = " . $id_customer;

        if(!is_null($status))
            $where .= " AND status = " . $status;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql.$where);
    }

    public static function changeStatus($id_question = false, $status = 2) {
        if(!$id_question)
            return false;

        Db::getInstance(_PS_USE_SQL_SLAVE_)->update('question', array('status' => $status), "id_question = " . $id_question);
        return true;
    }
}
