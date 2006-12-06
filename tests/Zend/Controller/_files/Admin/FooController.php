<?php
require_once 'Zend/Controller/Action.php';
require_once dirname(__FILE__) . '/../FooController.php';

class Admin_FooController extends FooController
{
    public function barAction()
    {
        $this->_response->appendBody("Admin_Foo::bar action called\n");
    }
}
