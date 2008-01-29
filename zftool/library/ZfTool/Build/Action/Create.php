<?php

require_once 'Zend/Build/Task/Action/Create.php';
require_once 'Zend/Console/Getopt/Provider/Interface.php';

class ZfTool_Build_Action_Create extends Zend_Build_Task_Action_Create  implements Zend_Console_Getopt_Provider_Interface 
{
    public function getGetoptOptions()
    {
        return array();
    }
    

    
}
