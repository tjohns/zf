<?php

require_once 'Zend/Tool/Cli/Context/Abstract.php';
require_once 'Zend/Build/Manifest.php';
require_once 'Zend/Console/Getopt.php';

class Zend_Tool_Cli_Context_Action extends Zend_Tool_Cli_Context_Abstract
{

    public function parse()
    {
        echo 'Parsing actions' . PHP_EOL;
        
        /* interact with manifest here? */
        
        // get actionname from arguments
        if ($this->_arguments == 0) {
            return;
        }
        
        $actionName = array_shift($this->_arguments);
        
        // get rules based on action name
        $getoptRules = array(); // this should come from manifest
        
        $getopt = null; //new Zend_Console_Getopt($getoptRules, $this->_arguments, array());
        
        // create actual action
        // new Zend_Build_Action_$actionName()
        
        return; // everything succeeded
    }
    
    public function execute()
    {
        
    }
	
}
