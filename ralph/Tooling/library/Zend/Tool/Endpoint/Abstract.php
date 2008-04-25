<?php

abstract class Zend_Tool_Endpoint_Abstract
{
    
    protected $_providerOutput = null;
    protected $_manifest = null;
    
    protected $_actionName = null;
    protected $_providerName = null;
    
    /*
    protected $_workingDirectory = null;
    */
    
    abstract protected function _preHandle();
    
    abstract protected function _postHandle();
    
    final public function __construct()
    {

    }
    
    public function setManifest(Zend_Tool_Manifest $manifest)
    {
        $this->_manifest = $manifest;
        return $this;
    }
    
    public function getManifest()
    {
        if ($this->_manifest == null) {
            $this->setManifest(new Zend_Tool_Manifest());
        }
        
        return $this->_manifest;
    }
    
    /*
    
    public function setWorkingDirectory($workingDirectory)
    {
        if (!is_dir($workingDirectory)) {
            throw new Zend_Tool_Exception($workingDirectory . ' is not a directory.');
        }
        
        $this->_workingDirectory = $workingDirectory;
        
        return $this;
    }
    
    public function getWorkingDirectory()
    {
        if ($this->_workingDirectory == null) {
            throw new Zend_Tool_Exception('Working directory was not set by the endpoint, please set.');
        }
        
        return $this->_workingDirectory;
    }

    */

    public function handle()
    {
        $manifest = $this->getManifest();
        
        $this->_preHandle();
        
        if ($this->_actionName == null) {
            throw new Zend_Tool_Endpoint_Exception('Endpoint failed to setup the action name.');
        }

        if ($this->_providerName == null) {
            throw new Zend_Tool_Endpoint_Exception('Endpoint failed to setup the provider name.');
        }
        
        ob_start();
        
        $provider = $manifest->getProvider($this->_providerName);
        $provider->execute($this->_actionName);
        
        $this->_providerOutput = ob_get_clean();
        
        $this->_postHandle();
    }

}