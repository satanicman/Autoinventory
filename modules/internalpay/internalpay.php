<?php

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class Internalpay extends PaymentModule
{	
	public function __construct()
	{
		$this->name = 'internalpay';
		$this->tab = 'payments_gateways';
		$this->version = '1.6.0';
		
		$this->currencies = false;

		parent::__construct();

		$this->displayName = $this->l('Internal payment');
		$this->description = $this->l('Payment with internal account');
		$this->author = 'ORS&DariusAkaFest';
	}

	public function install()
	{
			if (!parent::install() || !$this->registerHook('payment') || !$this->registerHook('paymentReturn'))
					return false;
			return true;
	}

	public function hookPayment($params)
	{
		global $smarty, $cookie, $currency;
		if (!$this->active)
			return;

		$ballance = $this->getBallance((int)$cookie->id_customer) * $currency->conversion_rate;
		$total = $params['cart']->getOrderTotal(true, 3);
		
		if ($ballance < $total)
			return;

		$smarty->assign(array(
			'new_ballance'=>$ballance - $total,
			'this_path' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));
		return $this->display(__FILE__, 'payment.tpl');
	}
	
	public function hookPaymentReturn($params)
	{
		global $cookie,$smarty;
		if (!$this->active)
			return;

		if (!$order = $params['objOrder'])
			return;

		if ($cookie->id_customer != $order->id_customer)
			return;
		if (!$order->hasBeenPaid())
			return;

				$aProducts = $order->getProducts();
				$bIs15 = version_compare(_PS_VERSION_, '1.5', '>=');
				if ($bIs15)
				{
					foreach ($aProducts as &$product)
					{
						$download = new ProductDownload();
						if ($iProductDownloadId = ProductDownload::getIdFromIdProduct($product['id_product']))
							$download = new ProductDownload($iProductDownloadId);
						$product['download'] = $download;
					}
				}
		$smarty->assign(array(
			'products' => $aProducts,
			'bIs15' => $bIs15
		));
		return $this->display(__FILE__, 'confirmation.tpl');
	}
	
	public function getManufacturer($user_id)
	{
		$query = "SELECT `manufacturer_id` FROM `"._DB_PREFIX_."addprod_manufacturers` WHERE `user_id` = '$user_id'";
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}
	
	public function getBallance($id_customer)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT SUM(summ)
		FROM `'._DB_PREFIX_.'payment`
		WHERE `id_seller` = "'.$this->getManufacturer($id_customer).'"');
	}
}
?>