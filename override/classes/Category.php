<?php

class Category extends CategoryCore {
    public static function createManufacturer($user_id, $manufacturer_id)
    {
        $query = 'INSERT INTO `'._DB_PREFIX_."addprod_manufacturers` (`user_id`, `manufacturer_id`)
				VALUES ('$user_id', '$manufacturer_id')";
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($query);
        return $res;
    }

    public static function getCustomer($id_manufacture = null)
    {
        $query = 'SELECT `user_id` FROM `'._DB_PREFIX_."addprod_manufacturers` WHERE `manufacturer_id` = '{$id_manufacture}'";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
    public static function getManufacturer($user_id)
    {
        $query = 'SELECT `manufacturer_id` FROM `'._DB_PREFIX_."addprod_manufacturers` WHERE `user_id` = '{$user_id}'";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    public function getProducts($id_lang, $p, $n, $order_by = null, $order_way = null, $get_total = false, $active = true, $random = false, $random_number_products = 1, $check_access = true, Context $context = null, $status = 0, $all = false)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        if ($check_access && !$this->checkAccess($context->customer->id)) {
            return false;
        }

        $front = in_array($context->controller->controller_type, array('front', 'modulefront'));
        $id_supplier = (int)Tools::getValue('id_supplier');

        /** Return only the number of products */
        if ($get_total) {
            $sql = 'SELECT COUNT(cp.`id_product`) AS total
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
					WHERE cp.`id_category` = '.(int)$this->id.' AND p.`status` = '.$status.
                ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
                ($active ? ' AND product_shop.`active` = 1' : '').
                ($id_supplier ? 'AND p.id_supplier = '.(int)$id_supplier : '');

            return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }

        if ($p < 1) {
            $p = 1;
        }

        /** Tools::strtolower is a fix for all modules which are now using lowercase values for 'orderBy' parameter */
        $order_by  = Validate::isOrderBy($order_by)   ? Tools::strtolower($order_by)  : 'position';
        $order_way = Validate::isOrderWay($order_way) ? Tools::strtoupper($order_way) : 'ASC';

        $order_by_prefix = false;
        if ($order_by == 'id_product' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'p';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        } elseif ($order_by == 'manufacturer' || $order_by == 'manufacturer_name') {
            $order_by_prefix = 'm';
            $order_by = 'name';
        } elseif ($order_by == 'position') {
            $order_by_prefix = 'cp';
        }

        if ($order_by == 'price') {
            $order_by = 'orderprice';
        }

        $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!Validate::isUnsignedInt($nb_days_new_product)) {
            $nb_days_new_product = 20;
        }

        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity'.(Combination::isFeatureActive() ? ', IFNULL(product_attribute_shop.id_product_attribute, 0) AS id_product_attribute,
					product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity' : '').', pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, image_shop.`id_image` id_image,
					il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB("'.date('Y-m-d').' 00:00:00",
					INTERVAL '.(int)$nb_days_new_product.' DAY)) > 0 AS new, product_shop.price AS orderprice
				FROM `'._DB_PREFIX_.'category_product` cp
				LEFT JOIN `'._DB_PREFIX_.'product` p
					ON p.`id_product` = cp.`id_product`
				'.Shop::addSqlAssociation('product', 'p').
            (Combination::isFeatureActive() ? ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
				ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.')':'').'
				'.Product::sqlStock('p', 0).'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE product_shop.`id_shop` = '.(int)$context->shop->id.'
					AND cp.`id_category` = '.(int)$this->id.
					' AND p.`status` = '.(int)$status
            .' AND product_shop.`active` = '. ($active ? 1 : 0)
            .($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
            .($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '');

        if ($random === true) {
            $sql .= ' ORDER BY RAND() LIMIT '.(int)$random_number_products;
        } elseif($all === true) {
            $sql .= ' ORDER BY product_shop.price DESC';
        } else {
            $sql .= ' ORDER BY '.(!empty($order_by_prefix) ? $order_by_prefix.'.' : '').'`'.bqSQL($order_by).'` '.pSQL($order_way).'
			LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);

        if (!$result) {
            return array();
        }

        if ($order_by == 'orderprice') {
            Tools::orderbyPrice($result, $order_way);
        }

        /** Modify SQL result */
        return Product::getProductsProperties($id_lang, $result);
    }

}