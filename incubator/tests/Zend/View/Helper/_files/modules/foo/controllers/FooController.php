<?php
/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

class Foo_FooController extends Zend_Controller_Action
{
    public function barAction()
    {
        $this->view->bar = 'bar';
    }
}
