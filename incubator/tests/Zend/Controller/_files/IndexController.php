<?php
require_once 'Zend/Controller/Action.php';

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_response->appendBody("Index action called\n");
    }

    public function prefixAction()
    {
        $this->_response->appendBody("Prefix action called\n");
    }

    public function argsAction()
    {
        $this->_response->appendBody('Args action called with params ' . implode('; ', $this->getInvokeArgs()) . "\n");
    }
}
