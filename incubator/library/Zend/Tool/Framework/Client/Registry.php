<?php

class Zend_Tool_Framework_Client_Registry //extends ArrayObject 
{

    /**
     * Registry object provides storage for shared objects.
     * @var Zend_Registry
     */
    protected static $_instance = null;

    protected $_client      = null;
    protected $_dispatcher  = null;
    protected $_request     = null;
    protected $_response    = null;
    
    
    /**
     * Retrieves the default registry instance.
     *
     * @return Zend_Tool_Framework_Client_Registry
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    public function getClient()
    {
        return $this->_client;
    }
    
    public function getDispatcher()
    {
        return $this->_dispatcher;
    }
        
    public function getRequest()
    {
        return $this->_request;
    }
    
    public function getResponse()
    {
        return $this->_response;
    }
    
    public function __get($name)
    {
        if (method_exists($this, 'get' . $name)) {
            return $this->{'get' . $name}();
        } elseif (property_exists($this, '_' . $name)) {
            return $this->{'_' . $name};
        } else {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception('Property ' . $name . ' was not located in this registry.');
        }
    }
    
    public function __set($name, $value)
    {
        if (method_exists($this, 'set' . $name)) {
            $this->{'set' . $name}($value);
            return;
        } elseif (property_exists($this, '_' . $name)) {
            $this->{'_' . $name} = $value;
            return;
        } else {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception('Property ' . $name . ' was not located in this registry.');            
        }
    }
    
}
