<?php

class MyApp_TestHelper extends Zend_Controller_Action_Helper_Abstract
{
    public function _direct()
    {
        $this->getResponse()->appendBody('running direct call');
    }
}