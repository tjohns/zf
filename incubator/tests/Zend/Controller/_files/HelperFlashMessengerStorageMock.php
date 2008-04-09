<?php

class HelperFlashMessengerStorageMock extends Zend_Session_Namespace
{
    private $_mockStorage;
    
    /**
     * __construct() - 
     *
     * @param mixed $name
     */
    public function __construct($namespace = 'Default')
    {
        $this->_mockStorage = array($namespace=>'');
    }
    
    /**
     * getIterator() - Provided by IteratorAggregate interface
     *
     * @param mixed $prop
     * @return array
     */
    public function getIterator()
    {
        return new ArrayObject($this->_mockStorage);
    }
    
    /**
     * setExpirationHops() - Expected for mocking purposes 
     *
     * @param mixed $prop
     * @return array
     */
    public function setExpirationHops($hops, $variables = null, $hopCountOnUsageOnly = false)
    {
        // mocking method
    }
    
    /**
     * __get() - Wrapper to read from mock storage
     *
     * @param mixed $prop
     * @return array
     */
    public function &__get($prop){
        return $this->_mockStorage[$prop];
    }
    
    /**
     * __set() - Wrapper to write into mock storage
     *
     * @param mixed $prop
     * @param mixed $value
     * @return array
     */
    public function  __set($prop,$value)
    {
        $this->_mockStorage[$prop] = $value;
    }
    
    /**
     * __isset() -
     *
     * @param mixed $prop
     * @return bool
     */
    public function __isset($prop)
    {
        return isset($this->_mockStorage[$prop]);
    }
    
    /**
     * __unset() -
     *
     * @param mixed $prop
     * @return void
     */
    public function __unset($prop)
    {
        unset($this->_mockStorage[$prop]);
    }
}