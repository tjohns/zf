<?php

require_once 'Zend/Build/Action/Abstract.php';

class Zend_Build_Action_Create extends Zend_Build_Action_Abstract
{
    
    public function getName()
    {
        return 'create';
    }
    
    public function validate()
    {
        
    }
    
    public function execute()
    {
        return true;
    }
    
}
