<?php

class Zend_Tool_Endpoint_Request
{

    /**
     * @var Zend_Tool_Endpoint_Inflector_Interface
     */
    protected $_inflector = null;
    
    protected $_providerNameRaw = null;
    protected $_providerName = null;
    protected $_providerSeparator = '.';
    protected $_providerSpecialtyRaw = null;
    protected $_providerSpecialty = null;
    protected $_actionNameRaw = null;
    protected $_actionName = null;
    protected $_actionParametersRaw = array();
    protected $_actionParameters = array();
    protected $_providerParametersRaw = array();
    protected $_providerParameters = array();
    
    public function setInflector(Zend_Tool_Endpoint_Inflector_Interface $inflector)
    {
        $this->_inflector = $inflector;
        return $this;
    }
    
    public function setFullProviderName($fullProviderName)
    {
        if (strpos($fullProviderName, $this->_providerSeparator) !== false) {
            list($providerName, $providerSpecialty) = explode('.', $fullProviderName);
        } else {
            $providerName = $fullProviderName;
            $providerSpecialty = null;
        }
        
        $this->setProviderName($providerName);
        if ($providerSpecialty) {
            $this->setProviderSpecialty($providerSpecialty);
        }
        
        return $this;
    }
    
    public function setProviderName($providerName)
    {
        $this->_providerNameRaw = $providerName;
        $this->_providerName = $this->_inflector->providerNameForTool($providerName);
        return $this;
    }
    
    public function getProviderNameRaw()
    {
        return $this->_providerNameRaw;
    }
    
    public function getProviderName()
    {
        return $this->_providerName;
    }
    
    public function setProviderSeparator($providerSeparator)
    {
        $this->_providerSeparator = $providerSeparator;
        return $this;
    }
    
    public function getProviderSeparator()
    {
        return $this->_providerSeparator;
    }
    
    public function setProviderSpecialty($providerSpecialty)
    {
        $this->_providerSpecialtyRaw = $providerSpecialty;
        $this->_providerSpecialty = $this->_inflector->actionNameForTool($providerSpecialty);
        return $this;
    }
    
    public function getProviderSpecialtyRaw()
    {
        return $this->_providerSpecialtyRaw;
    }

    public function getProviderSpecialty()
    {
        return $this->_providerSpecialty;
    }
    
    public function setActionName($actionName)
    {
        $this->_actionNameRaw = $actionName;
        $this->_actionName = $this->_inflector->actionNameForTool($actionName);
        return $this;
    }
    
    public function getActionNameRaw()
    {
        return $this->_actionNameRaw;
    }
    
    public function getActionName()
    {
        return $this->_actionName;
    }
    
    public function setActionParameter($parameterName, $parameterValue)
    {
        $this->_actionParametersRaw[$parameterName] = $parameterValue;
        $this->_actionParameters[$this->_inflector->parameterNameForTool($parameterName)] = $parameterValue;
        return $this;
    }
    
    public function getActionParametersRaw()
    {
        return $this->_actionParametersRaw;
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
        $this->_providerParametersRaw[$parameterName] = $parameterValue;
        $this->_providerParameters[$this->_inflector->parameterNameForTool($parameterName)] = $parameterValue;
        return $this;
    }
    
    public function getParametersRaw()
    {
        return $this->_providerParametersRaw;
    }
    
    public function getProviderParameters()
    {
        return $this->_providerParameters;
    }
    
    public function getProviderParameter($parameterName)
    {
        return (isset($this->_providerParameters[$parameterName])) ? $this->_providerParameters[$parameterName] : null;
    }
    
}