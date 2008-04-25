<?php

class Zend_Tool_Manifest
{

    protected $_loaders     = array();
    protected $_providers   = array();

    public function __construct()
    {
    }

    public function addLoader(Zend_Tool_Manifest_Loader_Abstract $manifestLoader)
    {
        $this->_loaders[$manifestLoader->getName()] = $manifestLoader;
        $manifestLoader->setManifest($this);
    }

    public function load()
    {
        foreach ($this->_loaders as $loader) {
            $loader->load();
        }
    }

    public function addProvider(Zend_Tool_Provider_Abstract $provider)
    {
        $this->_providers[$provider->getName()] = $provider;
        return $this;
    }

    public function getProvider($name)
    {
        if (!array_key_exists($name, $this->_providers)) {
            throw new Zend_Tool_Manifest_Exception('Exception');
        }
        
        return $this->_providers[$name];
    }
    
    public function resetProviders()
    {
        $this->_providers = array();
        return $this;
    }
    
    protected function _scan()
    {
        
    }
    
}
