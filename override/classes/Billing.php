<?php

require 'tools/anet/vendor/autoload.php';
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

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
    public $year;

    public static $definition = array(
        'table' => 'billing_info',
        'primary' => 'id_billing_info',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            'id_state' =>                   array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'business_name' =>              array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 128),
            'adress_1' =>                   array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'adress_2' =>                   array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'city' =>                       array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'zip_code' =>                   array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'card_name' =>                  array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 128, 'required' => true),
            'card_number' =>                array('type' => self::TYPE_STRING, 'validate' => 'isCreditCard', 'size' => 128, 'required' => true),
            'cvv' =>                        array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'month' =>                      array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'year' =>                       array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
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

    public function createSubscription($intervalLength) {
        $amount = Configuration::get('PS_ANET_AMOUNT');

        // Common Set Up for API Credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(Configuration::get('PS_ANET_LOGIN_ID'));
        $merchantAuthentication->setTransactionKey(Configuration::get('PS_ANET_TRANSACTION_KEY'));

        $refId = 'ref' . time();

        // Subscription Type Info
        $subscription = new AnetAPI\ARBSubscriptionType();
        $subscription->setName("Autoinventory subscription");

        $interval = new AnetAPI\PaymentScheduleType\IntervalAType();
        $interval->setLength($intervalLength);
        $interval->setUnit("days");

        $paymentSchedule = new AnetAPI\PaymentScheduleType();
        $paymentSchedule->setInterval($interval);
        $paymentSchedule->setStartDate(new DateTime('now', new DateTimeZone('Europe/Kiev')));
        $paymentSchedule->setTotalOccurrences("9999");
//        $paymentSchedule->setTrialOccurrences("1");

        $subscription->setPaymentSchedule($paymentSchedule);
        $subscription->setAmount($amount ? $amount : 499);
//        $subscription->setTrialAmount("0.00");

        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($this->card_number);
        $creditCard->setExpirationDate($this->year."-".$this->month);

        $payment = new AnetAPI\PaymentType();
        $payment->setCreditCard($creditCard);

        $subscription->setPayment($payment);

        $billTo = new AnetAPI\NameAndAddressType();
        $names = explode(' ', $this->card_name);
        if(isset($names['0']))
            $billTo->setFirstName($names['0']);
        if(isset($names['1']))
            $billTo->setLastName($names['1']);

        $subscription->setBillTo($billTo);

        $request = new AnetAPI\ARBCreateSubscriptionRequest();
        $request->setmerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setSubscription($subscription);
        $controller = new AnetController\ARBCreateSubscriptionController($request);

        $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

//        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
//        {
//            echo "SUCCESS: Subscription ID : " . $response->getSubscriptionId() . "\n";
//        }
//        else
//        {
//            echo "ERROR :  Invalid response\n";
//            $errorMessages = $response->getMessages()->getMessage();
//            echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
//        }

        return $response;
    }

    public static function cancelSubscription($subscriptionId) {

        // Common Set Up for API Credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(Configuration::get('PS_ANET_LOGIN_ID'));
        $merchantAuthentication->setTransactionKey(Configuration::get('PS_ANET_TRANSACTION_KEY'));
        $refId = 'ref' . time();

        $request = new AnetAPI\ARBCancelSubscriptionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setSubscriptionId($subscriptionId);

        $controller = new AnetController\ARBCancelSubscriptionController($request);

        $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

//        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok"))
//        {
//            $successMessages = $response->getMessages()->getMessage();
//            echo "SUCCESS : " . $successMessages[0]->getCode() . "  " .$successMessages[0]->getText() . "\n";
//
//        }
//        else
//        {
//            echo "ERROR :  Invalid response\n";
//            $errorMessages = $response->getMessages()->getMessage();
//            echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
//
//        }

        return $response;

    }
}
