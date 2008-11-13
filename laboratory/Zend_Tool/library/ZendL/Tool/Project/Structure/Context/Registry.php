<?php

class ZendL_Tool_Project_Structure_Context_Registry
{
    
    protected static $_instance = null;
    
    protected $_contexts = array();
    
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }

    protected function __construct()
    {        
    }
    
    public function addContextClass($contextClass)
    {
        $context = new $contextClass();
        return $this->addContext($context);
    }
    
    public function addContext(ZendL_Tool_Project_Structure_Context_Interface $context)
    {
        $this->_contexts[strtolower($context->getName())] = $context;
        return $this;
    }
    
    public function getContext($name)
    {
        if (!$this->hasContext($name)) {
            throw new ZendL_Tool_Project_Structure_Context_Exception('Context by name ' . $name . ' does not exist in the registry.');
        }
        
        return clone $this->_contexts[strtolower($name)];
    }
    
    public function hasContext($name)
    {
        return (($this->_contexts[strtolower($name)]) ? true : false);
    }
    
}