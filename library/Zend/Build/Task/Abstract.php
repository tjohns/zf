<?php

abstract class Zend_Build_Task_Abstract
{
    protected $_parameters = array();

    protected $_executingTarget = null;
    
    abstract public function execute();
    
    public function setParameter($name, $value)
    {
        $this->_parameters[$name] = $value;
    }
    
    public function getParameter($name)
    {
        return (isset($this->_parameters[$name])) ? $this->_parameters[$name] : null;
    }
    
    public function setExecutingTarget(Zend_Build_Target_Abstract $target)
    {
        $this->_executingTarget = $target;
        return $this;
    }
    
    public function unsetExecutingTarget()
    {
        $this->_executingTarget = null;
    }
    
    public function getExecutingTarget()
    {
        return $this->_executingTarget;
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
