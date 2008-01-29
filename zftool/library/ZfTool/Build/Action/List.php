<?php

require_once 'Zend/Build/Task/Action/Abstract.php';
require_once 'Zend/Console/Getopt/Provider/Interface.php';

class ZfTool_Build_Action_List extends Zend_Build_Task_Action_Abstract implements Zend_Console_Getopt_Provider_Interface 
{
    
    public function getName()
    {
        return 'list';
    }
    
    public function getGetoptOptions()
    {
        return array();
    }
    
}