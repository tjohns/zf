<?php

class Zend_Entity_LazyLoad_ArrayTest extends Zend_Entity_Collection_ArrayTest
{
    public function createElementHashMap()
    {
        $data = array(
            array("key" => "foo", "value" => "bar"),
            array("key" => "bar", "value" => "baz"),
        );

        $select = $this->getMock('Zend_Db_Select', array(), array(), '', false);
        $select->expects($this->any())
               ->method('query')
               ->will($this->returnValue(Zend_Test_DbStatement::createSelectStatement($data)));

        return new Zend_Entity_LazyLoad_Array(
            $select, "key", "value"
        );
    }

    public function testWasLoadedFromDatabase()
    {
        $hashMap = $this->createElementHashMap();
        $this->assertFalse($hashMap->__ze_wasLoadedFromDatabase());
        
        $this->assertEquals("baz", $hashMap["bar"]);
        $this->assertTrue($hashMap->__ze_wasLoadedFromDatabase());
    }
}
