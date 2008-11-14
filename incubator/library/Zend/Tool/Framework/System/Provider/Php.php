<?php

class Zend_Tool_Framework_System_Provider_PhpInfo implements Zend_Tool_Framework_Provider_Interface
{
    
    public function showAction()
    {
        phpinfo();
    }
    
}