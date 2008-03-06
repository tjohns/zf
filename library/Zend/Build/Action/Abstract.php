<?php

abstract class Zend_Build_Action_Abstract
{
    
    protected $_parameters = array();
    
    public function setParameters(Array $parameters = array())
    {
        $this->_parameters = $parameters;
    }
    
    public function getParameters()
    {
        return $this->_parameters;
    }
    
    public function getParameter($name)
    {
        
    }
    
    abstract public function validate();
    abstract public function execute();
    
}