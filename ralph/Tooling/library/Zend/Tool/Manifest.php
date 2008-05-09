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
        $provider->setManifest($this);
        $this->_providers[$provider->getName()] = $provider;
        return $this;
    }

    public function getProviders()
    {
        return $this->_providers;
    }
    
    /**
     * Enter description here...
     *
     * @param string $name
     * @return Zend_Tool_Provider_Abstract
     */
    public function getProvider($name)
    {
        if (!array_key_exists($name, $this->_providers)) {
            throw new Zend_Tool_Manifest_Exception('Provider does not exist.');
        }
        
        return $this->_providers[$name];
    }

    public function resetProviders()
    {
        $this->_providers = array();
        return $this;
    }

    public function addAction(Zend_Tool_Provider_Action $action)
    {
        $this->_actions[$action->getName()] = $action;
        return $this;
    }
    
    public function getAction($name)
    {
        if (!array_key_exists($name, $this->_actions)) {
            return $this->_actions['Action'];
        }
        
        return $this->_actions[$name];
    }
    
}
