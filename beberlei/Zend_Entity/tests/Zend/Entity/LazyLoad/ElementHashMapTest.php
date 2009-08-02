<?php

class Zend_Entity_LazyLoad_ElementHashMapTest extends Zend_Entity_Collection_ElementHashMapTest
{
    public function createElementHashMap()
    {
        return new Zend_Entity_LazyLoad_ElementHashMap(
            array('Zend_Entity_LazyLoad_ElementHashMapTest', 'dataLazyCallback')
        );
    }

    static public function dataLazyCallback()
    {
        return array("foo" => "bar", "bar" => "baz");
    }

    public function testWasLoadedFromDatabase()
    {
        $hashMap = $this->createElementHashMap();
        $this->assertFalse($hashMap->__ze_wasLoadedFromDatabase());
        
        $foo = $hashMap["foo"];
        $this->assertTrue($hashMap->__ze_wasLoadedFromDatabase());
    }

    public function testInvalidCallbackGivenThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $hashMap = new Zend_Entity_LazyLoad_ElementHashMap("invalidCallback");
    }
}
