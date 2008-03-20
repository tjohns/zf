<?php

abstract class Zend_Build_Resource_Abstract
{

    protected $_parameters = array();
    
    public function __construct()
    {
        $this->init();
    }
    
    public function init()
    {
        
    }
    
    public function setParameters(Array $parameters = array())
    {
        $this->_parameters = $parameters;
    }
    
    public function setParameter($name, $value)
    {
        $this->_parameters[$name] = $value;
        return $this;
    }
    
    public function getParameters()
    {
        return $this->_parameters;
    }
    
    public function getParameter($name)
    {
        return (isset($this->_parameters[$name])) ? $this->_parameters[$name] : null; 
    }
    
    abstract public function validate();

    public function execute($actionName)
    {
        return $this->{$actionName}();
    }
    
}
