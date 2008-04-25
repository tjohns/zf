<?php

class Zend_Tool_Endpoint_Cli extends Zend_Tool_Endpoint_Abstract
{
    
    public static function main()
    {
        $cliEndpoint = new self();
        $cliEndpoint->setDefaults();
        $cliEndpoint->handle();
    }
    
    protected function _preHandle()
    {
        
        $this->_actionName   = $this->_filterArg($_SERVER['argv'][1]);
        $this->_providerName = $this->_filterArg($_SERVER['argv'][2]);

        // decompose cli args into action/providers
    }
    
    protected function _postHandle()
    {
        echo $this->_providerOutput . PHP_EOL;
    }

    public function setDefaults()
    {
        /*
        $cliEndpoint->setWorkingDirectory($_SERVER['PWD']);
        */
        
        // set working directory
        
        // setup different manifests
        
        $manifest = $this->getManifest();
        $manifest->addLoader(new Zend_Tool_Manifest_Loader_PluginDirectory(array('Zend_Tool_Provider' => 'Zend/Tool/Provider')));

        $manifest->load();
        
    }
    
    public function wordToActionName($arg)
    {
        $filter = new Zend_Filter();
        $filter->addFilter(new Zend_Filter_Word_DashToCamelCase());
        return $filter->filter($arg);
    }
    
    public function wordToProviderName($providerName)
    {
        
    }
    
    
    // OVERALL LIST
    // get arguments
    // parse arguments
    // get manifest
    // assign action/provider
    // run action/provider
    // return output
    
}