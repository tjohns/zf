<?php

class Zend_Entity_TestManagerMock extends Zend_Entity_Manager
{
    public function addMapper($className, Zend_Entity_Mapper_Abstract $mapper)
    {
        $this->_entityMappers[$className] = $mapper;
    }
}