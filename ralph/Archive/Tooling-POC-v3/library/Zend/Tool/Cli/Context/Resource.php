<?php

require_once 'Zend/Tool/Cli/Context/Abstract.php';
require_once 'Zend/Build/Manifest.php';
require_once 'Zend/Console/Getopt.php';

class Zend_Tool_Cli_Context_Resource extends Zend_Tool_Cli_Context_Abstract
{

    protected $_resource = null;
    
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
        $getopt = new Zend_Console_Getopt($getoptRules, $this->_arguments, array('parseAll' => false));
        $getopt->parse();
        
        $this->_arguments = $getopt->getRemainingArgs();
        
        // get class name
        $resourceClassName = $resourceContext->getClassName();
        
        // load appropriate file
        try {
            Zend_Loader::loadClass($resourceClassName);
        } catch (Zend_Loader_Exception $e) {
            echo 'couldnt load ' . $resourceClassName . PHP_EOL;
        }
        
        // get actual resource object given class name
        $this->_resource = new $resourceClassName();
        
        // make sure its somewhat sane (implements proper interface)
        if (!$this->_resource instanceof Zend_Build_Resource_Abstract) {
            echo 'does not implement Zend_Build_Resource_Abstract ' . PHP_EOL;
        }
        
        $parameters = array();
        foreach ($getopt->getOptions() as $getoptParsedOption) {
            $parameters[$getoptParsedOption] = $getopt->getOption($getoptParsedOption);
        }
        
        $this->_resource->setParameters($parameters);
        $this->_resource->validate();
        
        $this->setExecutable();
        return; // everything succeeded
    }
    
    public function execute(Zend_Tool_Cli_Context_Action $actionContext)
    {
        
        $this->_resource->execute($actionContext->getAction()->getName());
    }
	
}
