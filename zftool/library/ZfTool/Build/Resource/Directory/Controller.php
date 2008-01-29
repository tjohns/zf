<?php

require_once 'Zend/Build/Task/Resource/Directory.php';

class ZfTool_Build_Resource_Directory_Controller extends Zend_Build_Task_Resource_Directory implements Zend_Console_Getopt_Provider_Interface
{
    
    public function getName()
    {
        return 'directoryController';
    }
    
    public function getGetoptOptions()
    {
        return array(
            'controllername|d=s' => 'You must specify a controller name.'
            );
    }
}