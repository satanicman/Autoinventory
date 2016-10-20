<?phpclass AjaxController extends FrontController{    public $php_self = 'ajax';    public function init()    {        parent::init();    }    public function postProcess()    {        parent::postProcess();        if(Tools::isSubmit('askQuestion')) {            $this->askQuestion();        }    }    private function askQuestion()    {        if(!Tools::getValue('name') || !Validate::isName(Tools::getValue('name')))            $this->errors[] = Tools::displayError('Name is invalid');        if(!Tools::getValue('phone') || !Validate::isPhoneNumber(Tools::getValue('phone')))            $this->errors[] = Tools::displayError('Phone is invalid');        if(!Tools::getValue('mail') || !Validate::isEmail(Tools::getValue('mail')))            $this->errors[] = Tools::displayError('Email is invalid');        if(!Tools::getValue('id_product') || !Validate::isUnsignedId(Tools::getValue('id_product')))            $this->errors[] = Tools::displayError('Email is invalid');        if($this->errors) {            if(Tools::getValue('ajax'))                die(Tools::jsonEncode(array('hasError' => 1, 'errors' => $this->errors)));        } else {            $product = new Product(Tools::getValue('id_product'), $this->context->language->id);            $question = new Question();            $question->name = Tools::getValue('name');            $question->phone = Tools::getValue('phone');            $question->mail = Tools::getValue('mail');            $question->text = Tools::getValue('text');            $question->id_product = Tools::getValue('id_product');            $question->id_customer = Category::getCustomer($product->id_category_default);            $question->status = 0;            if($question->add())                die(true);        }    }}