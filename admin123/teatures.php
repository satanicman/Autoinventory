<?php

if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', getcwd());
}
include(_PS_ADMIN_DIR_.'/../config/config.inc.php');

$context = Context::getContext();

Db::getInstance()->execute("TRUNCATE TABLE ". _DB_PREFIX_ ."feature_product");
Db::getInstance()->execute("TRUNCATE TABLE ". _DB_PREFIX_ ."feature_value_lang");
Db::getInstance()->execute("TRUNCATE TABLE ". _DB_PREFIX_ ."feature_value");

foreach (Feature::getFeatures($context->language->id) as $feature) {
    $ids = array();
    $fv = new FeatureValue();
    $fv->id_feature = $feature['id_feature'];
    $fv->value[$context->language->id] = 'one';
    $fv->add();
    $ids[] = $fv->id;
    $fv->value[$context->language->id] = 'two';
    $fv->add();
    $ids[] = $fv->id;
    $fv->value[$context->language->id] = 'three';
    $fv->add();
    $ids[] = $fv->id;
    foreach (Db::getInstance()->executeS("SELECT id_product FROM " . _DB_PREFIX_ . "product GROUP BY id_product;") as $p) {
        Product::addFeatureProductImport($p['id_product'], $feature['id_feature'], $ids[array_rand($ids)]);
    }
}
die('Done');
