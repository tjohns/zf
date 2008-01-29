<?php

class ZfTool_Build_Resource_Project extends Zend_Build_Task_Resource_Abstract implements Zend_Console_Getopt_Provider_Interface
{
    public function getGetoptOptions()
    {
        
    }
    
    public function satisfyDependencies()
    {
        // needs parameter profileName &/ profilePath
    }
}