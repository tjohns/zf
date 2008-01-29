<?php

abstract class Zend_Build_Task_Resource_Abstract
{
    protected $_parameters = array();

    abstract public function getName();

    public function setParameter($name, $value)
    {
        $this->_parameters[$name] = $value;
        return $this;
    }

    public function getParameter($name, $value)
    {
        return (isset($this->_parameters[$name])) ? $this->_parameters[$name] : null;
    }

    public function getSupportedActions()
    {
        foreach (get_class_methods($this) as $classMethod) {
            
        }
        return array();
    }

    public function implementsActionName($actionName)
    {
        $implementsActionName = method_exists($this, $actionName . 'Action');
        return $implementsAction;
    }

    public function satisfyDependencies()
    {}
    
    public function setup()
    {}
    
    public function rollback()
    {}
    
    public function cleanup()
    {}

}
