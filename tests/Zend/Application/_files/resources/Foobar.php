<?php
class Zend_Application_BootstrapTest_Resource_Foobar extends Zend_Application_Resource_Base
{
    public function init()
    {
        $this->getBootstrap()->executedFoobarResource = true;
    }
}
