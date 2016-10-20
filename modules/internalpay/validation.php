<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/internalpay.php');
require_once(dirname(__FILE__).'/../addprod/PaymentsCore.php');

if (!isset($cart)) global $cart;
if (!isset($currency)) global $currency;

$internalpay = new internalpay();

$customer = new Customer((int)$cart->id_customer);

if (!Validate::isLoadedObject($customer))
	Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

$ballance = PaymentsCore::getBallance((int)$customer->id);
$total_not_converted = $cart->getOrderTotal(true, 3);
$total = $total_not_converted / $currency->conversion_rate;
if ($ballance < $total)
	Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

$internalpay->validateOrder((int)($cart->id), _PS_OS_PAYMENT_, $total_not_converted, $internalpay->displayName, null, array(), null, false, $customer->secure_key);
$order = new Order((int)($internalpay->currentOrder));
	$payment = new PaymentsCore();
	$payment->id_seller = PaymentsCore::getManufacturer((int)$customer->id);
	$payment->summ = - abs($total);
	$payment->id_order = $order->id;
	$payment->status = 3;
	$payment->description = 'Payment of cart № '.$order->id;
	if (!$payment->add())
		Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');
Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?key='.$customer->secure_key.'&id_cart='.(int)($cart->id).'&id_module='.(int)($internalpay->id).'&id_order='.(int)($internalpay->currentOrder));
?>