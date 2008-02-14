<?php

require_once 'Zend/Tool/Cli/Context/Abstract.php';
require_once 'Zend/Build/Manifest.php';
require_once 'Zend/Console/Getopt.php';

class Zend_Tool_Cli_Context_Action extends Zend_Tool_Cli_Context_Abstract
{

    public function parse()
    {

        // get actionname from arguments
        if (count($this->_arguments) == 0) {
            return;
        }
        
        // action name will be a free floating string
        $actionName = array_shift($this->_arguments);
        
        // check to make sure that the action exists
        if (!($actionContext = $this->_buildManifest->getContext('action', $actionName)) instanceof Zend_Build_Manifest_Context) {
            require_once 'Zend/Tool/Cli/Context/Exception.php';
            throw new Zend_Tool_Cli_Context_Exception('No action context by name ' . $actionName . ' was found in the manifest.');
        }
        
        $getoptRules = array();
        
        // get the attributes from this action context
        $actionContextAttrs = $actionContext->getAttributes();
        foreach ($actionContextAttrs as $actionContextAttr) {
            if (isset($actionContextAttr['attributes']['getopt'])) {
                $getoptRules[$actionContextAttr['attributes']['getopt']] = $actionContextAttr['usage'];
            }
        }
        
        // parse those options out of the arguments array
        $getopt = new Zend_Console_Getopt($getoptRules, $this->_arguments, array());
        $getopt->parse();
        
        // put remaining args into local property
        $this->_arguments = $getopt->getRemainingArgs();
        
        // 
        // @todo resource integration
        // create actual action
        $actionContextClassName = $actionContext->getClassName();
        // new $actionContextClass();
        // 
        // somehow pass the options to this class
        //
        
        echo 'Creating action ' . $actionContextClassName . PHP_EOL;
        
        
        return; // everything succeeded
    }
    
    public function execute()
    {
        
    }
	
}
