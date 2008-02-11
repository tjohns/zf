<?php

require_once 'Zend/Tool/Cli/Context/Abstract.php';
require_once 'Zend/Build/Manifest.php';
require_once 'Zend/Console/Getopt.php';

class Zend_Tool_Cli_Context_Resource extends Zend_Tool_Cli_Context_Abstract
{

    public function parse()
    {
        echo 'Parsing resources' . PHP_EOL;
        
        /* interact with manifest here? */
        
        // get resourceName from arguments
        if ($this->_arguments == 0) {
            return;
        }
        
        $resourceName = array_shift($this->_arguments);
        
        // get rules based on resource name
        $getoptRules = array(); // this should come from manifest
        
        $getopt = null; //new Zend_Console_Getopt($getoptRules, $this->_arguments, array());
        
        // create actual resource
        // new Zend_Build_Resource_$actionName()
        
        return; // everything succeeded
    }
    
    public function execute()
    {
        
    }
	
}
