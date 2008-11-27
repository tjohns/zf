<?php

class Zend_Tool_Framework_Endpoint_Request
{
    
    
    protected $_providerName = null;
    protected $_specialtyName = null;
    protected $_actionName = null;
    protected $_actionParameters = array();
    protected $_providerParameters = array();
    protected $_isPretend = false;
    protected $_isDispatchable = true;
        
    public function setProviderName($providerName)
    {
        $this->_providerName = $providerName;
        return $this;
    }
    
    public function getProviderName()
    {
        return $this->_providerName;
    }
    
    public function setSpecialtyName($specialtyName)
    {
        $this->_specialtyName = $specialtyName;
        return $this;
    }
    
    public function getSpecialtyName()
    {
        return $this->_specialtyName;
    }
    
    public function setActionName($actionName)
    {
        $this->_actionName = $actionName; 
        return $this;
    }
    
    public function getActionName()
    {
        return $this->_actionName;
    }
    
    public function setActionParameter($parameterName, $parameterValue)
    {
        $this->_actionParameters[$parameterName] = $parameterValue;
        return $this;
    }
    
    public function getActionParameters()
    {
        return $this->_actionParameters;
    }
    
    public function getActionParameter($parameterName)
    {
        return (isset($this->_actionParameters[$parameterName])) ? $this->_actionParameters[$parameterName] : null;
    }
    
    public function setProviderParameter($parameterName, $parameterValue)
    {
        $this->_providerParameters[$parameterName] = $parameterValue;
        return $this;
    }
    
    public function getProviderParameters()
    {
        return $this->_providerParameters;
    }
    
    public function getProviderParameter($parameterName)
    {
        return (isset($this->_providerParameters[$parameterName])) ? $this->_providerParameters[$parameterName] : null;
    }
    
    public function setPretend($pretend)
    {
    	$this->_isPretend = (bool) $pretend;
    }
    
    public function getPretend()
    {
    	return $this->_isPretend;
    }
    
    public function setDispatchable($dispatchable)
    {
        $this->_isDispatchable = (bool) $dispatchable;
    }
    
    /**
     * Is this request Dispatchable?
     *
     * @return bool
     */
    public function isDispatchable()
    {
        return $this->_isDispatchable;
    }
    
}