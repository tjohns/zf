<?php
require_once 'Zend/Controller/Action.php';

class BazController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $this->_response->appendBody("preDispatch called\n");
    }

    public function postDispatch()
    {
        $this->_response->appendBody("postDispatch called\n");
    }

    public function barAction()
    {
        $this->_response->appendBody("Admin's Baz::bar action called\n");
    }
}
