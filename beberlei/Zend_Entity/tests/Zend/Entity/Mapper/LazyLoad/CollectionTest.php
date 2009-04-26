<?php

class Zend_Entity_Mapper_LazyLoad_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testCreateCollectionWithValidCallbackDoesNotThrowException()
    {
        $validCallback = 'substr';
        $collection = new Zend_Entity_Mapper_LazyLoad_Collection($validCallback, array());
    }

    public function testCreatecollectionWithInvalidCallbackThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $invalidCallback = array('invalidClass', 'invalidMethod');
        $collection = new Zend_Entity_Mapper_LazyLoad_Collection($invalidCallback, array());
    }
}