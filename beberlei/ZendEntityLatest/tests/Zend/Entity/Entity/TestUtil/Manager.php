<?php

class Zend_Entity_TestUtil_Manager extends Zend_Entity_Manager
{
    public $transaction;
    public $mapper;

    public function getTransaction()
    {
        if($this->transaction == null) {
            $this->transaction = new Zend_Entity_TestUtil_Transaction();
        }
        return $this->transaction;
    }

    public function getMapper()
    {
        if($this->mapper == null) {
            $this->mapper = new Zend_Entity_TestUtil_Mapper();
        }
        return $this->mapper;
    }

    public function setMapper(Zend_Entity_MapperAbstract $mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }
}