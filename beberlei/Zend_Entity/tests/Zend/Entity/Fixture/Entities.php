<?php

class Zend_TestEntity1 implements Zend_Entity_Interface
{
    protected $state;

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

class Zend_TestEntity2 extends Zend_TestEntity1
{
    
}