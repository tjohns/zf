<?php

abstract class Zend_Tool_Manifest_Loader_Abstract
{
    
    protected $_name = null;
    
    /**
     * @var Zend_Tool_Manifest
     */
    protected $_manifest = null;

    abstract public function load();
    
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }
    
    public function getName()
    {
        if ($this->_name == null) {
            $className = get_class($this);
            $this->_name = substr($className, strrpos($className, '_')+1);
        }
        
        return $this->_name;
    }
    
    public function setManifest(Zend_Tool_Manifest $manifest)
    {
        $this->_manifest = $manifest;
        return $this;
    }
    
    public function addProvider(Zend_Tool_Provider_Abstract $provider)
    {
        $this->_manifest->addProvider($provider);
        return $this;
    }
    
    public function addAction(Zend_Tool_Provider_Action $action)
    {
        $this->_manifest->addAction($action);
        return $this;
    }
   
    
}