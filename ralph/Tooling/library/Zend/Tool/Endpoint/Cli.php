<?php

class Zend_Tool_Endpoint_Cli extends Zend_Tool_Endpoint_Abstract
{
    
    public static function main()
    {
        $cliEndpoint = new self();
        $cliEndpoint->setDefaults();
        $cliEndpoint->handle();
    }
    
    protected function _init()
    {
        $this->setInflector(new Zend_Tool_Endpoint_Cli_Inflector());
    }
    
    protected function _preHandle()
    {
        $optParser = new Zend_Tool_Endpoint_Cli_GetoptParser($this, $_SERVER['argv']);
        $optParser->parse();

        //$this->_request->setActionName($optParser->getActionName());
        //$this->_request->setFullProviderName($optParser->getProviderName());
        

        // decompose cli args into action/providers
    }
    
    protected function _postHandle()
    {
        echo $this->_response->getContent() . PHP_EOL;
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