<?php

class ProductController extends ProductControllerCore
{
    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(array(
            _PS_JS_DIR_.'validate.js'
        ));
    }
}
