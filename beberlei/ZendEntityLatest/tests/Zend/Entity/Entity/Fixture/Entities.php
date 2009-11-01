<?php

abstract class Zend_TestEntityAbstract implements Zend_Entity_Interface
{
    protected $state;

    public function __set($name, $value)
    {
        $this->state[$name] = $value;
    }

    public function __get($name)
    {
        return $this->state[$name];
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState(array $state)
    {
        $this->state = $state;
    }

    public function __call($method, $args)
    {
        $suffix = substr($method, 3);
        if(substr($method, 0, 3) == "get") {
            return $this->state[$suffix];
        } else if(substr($method, 0, 3) == "set") {
            $this->state[$suffix] = $args[0];
        }
    }
}

class Zend_TestEntity1 extends Zend_TestEntityAbstract
{
    static public function create()
    {
        return new Zend_TestEntity1();
    }
}

class Zend_TestEntity2 extends Zend_TestEntityAbstract 
{
    public $constructorCalled = false;

    public function __construct()
    {
        $this->constructorCalled = true;
    }
}

class Zend_TestEntityHasFinal extends Zend_TestEntityAbstract
{
    final public function doSomething()
    {
        
    }
}

/**
 * THIS CODE WAS AUTOMATICALLY CREATED AND MIGHT BE AUTOMATICALLY REGENERATED
 * CHANGES TO THIS CODE CAN BE LOST!
 */
class Zend_TestEntity1Proxy extends Zend_TestEntity1 implements Zend_Entity_LazyLoad_Proxy
{

    private $_entityManager = null;

    final public function __construct(Zend_Entity_Manager_Interface $entityManager, $entityName, $id)
    {
        $this->_entityManager = $entityManager;
        $entityManager->getIdentityMap()->addObject($entityName, $id, $this);
    }

    final private function __lazyLoad()
    {
        if($this->_entityManager !== null) {
            $this->_entityManager->refresh($this);
            $this->_entityManager = null;
        }
    }

    final public function entityWasLoaded()
    {
        return ($this->_entityManager===null);
    }

    final public function __set($name, $value)
    {
        $this->__lazyLoad();
        return parent::__set($name, $value);
    }

    final public function __get($name)
    {
        $this->__lazyLoad();
        return parent::__get($name);
    }

    final public function getState()
    {
        $this->__lazyLoad();
        return parent::getState();
    }

    final public function __call($method, $args)
    {
        $this->__lazyLoad();
        return parent::__call($method, $args);
    }

}