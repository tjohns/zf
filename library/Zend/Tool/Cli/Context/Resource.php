<?php

require_once 'Zend/Tool/Cli/Context/Abstract.php';
require_once 'Zend/Build/Manifest.php';
require_once 'Zend/Console/Getopt.php';

class Zend_Tool_Cli_Context_Resource extends Zend_Tool_Cli_Context_Abstract
{

    public function parse()
    {
        // get resourceName from arguments
        if (count($this->_arguments) == 0) {
            return;
        }
        
        $resourceName = array_shift($this->_arguments);

        // check to make sure that the action exists
        if (!($resourceContext = $this->_buildManifest->getContext('resource', $resourceName)) instanceof Zend_Build_Manifest_Context) {
            require_once 'Zend/Tool/Cli/Context/Exception.php';
            throw new Zend_Tool_Cli_Context_Exception('No resource context by name ' . $resourceName . ' was found in the manifest.');
        }

        $getoptRules = array();
        
        // get the attributes from this action context
        $resourceContextAttrs = $resourceContext->getAttributes();
        foreach ($resourceContextAttrs as $resourceContextAttr) {
            if (isset($resourceContextAttr['attributes']['getopt'])) {
                $getoptRules[$resourceContextAttr['attributes']['getopt']] = $resourceContextAttr['usage'];
            }
        }
        
        // parse those options out of the arguments array
        $getopt = new Zend_Console_Getopt($getoptRules, $this->_arguments, array());
        $getopt->parse();

        $this->_arguments = $getopt->getRemainingArgs();
        
        // 
        // @todo Resource Integration
        // create actual resource
        $resourceContextClassName = $resourceContext->getClassName();
        // $resource = new $resourceContextClass();
        // 
        // somehow pass the options to this class
        //
        
        echo 'Creating resource ' . $resourceContextClassName . PHP_EOL;
        
        return; // everything succeeded
    }
    
    public function execute()
    {
        
    }
	
}
