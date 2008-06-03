<?php

abstract class Zend_Tool_Provider_ZfProject_ProfileSet_ProfileSetAbstract
{
    /**
     * @var Zend_Registry
     */
    protected $_parameters = null;
    
    public function __construct()
    {
        $this->_parameters = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
    }
    
    public function __get($name)
    {
        return $this->_parameters->offsetGet($name);
    }
    
    public function __isset($name)
    {
        return $this->_parameters->offsetIsset($name);
    }
    
    public function __unset($name)
    {
        return $this->_parameters->offsetUnset($name);
    }

    public function __set($name, $value)
    {
        $this->_parameters->offsetSet($name, $value);
        return $this;
    }
    
}

