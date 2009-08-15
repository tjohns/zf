<?php

class Zend_Entity_Definition_ArrayTest extends Zend_Entity_Definition_TestCase
{
    public function testSetGetMapKey()
    {
        $colDef = new Zend_Entity_Definition_Array(self::TEST_PROPERTY);
        $colDef->setMapKey("keyName");

        $this->assertEquals("keyName", $colDef->getMapKey());
    }

    public function testMapKeyPublicProperty()
    {
        $colDef = new Zend_Entity_Definition_Array(self::TEST_PROPERTY);
        $colDef->setMapKey("keyName");

        $this->assertEquals("keyName", $colDef->mapKey);
    }

    public function testSetGetElement()
    {
        $colDef = new Zend_Entity_Definition_Array(self::TEST_PROPERTY);
        $colDef->setElement("elementName");

        $this->assertEquals("elementName", $colDef->getElement());
    }

    public function testElementPublicProperty()
    {
        $colDef = new Zend_Entity_Definition_Array(self::TEST_PROPERTY);
        $colDef->setElement("elementName");

        $this->assertEquals("elementName", $colDef->element);
    }


    public function testSetMapKey_NonString_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $colDef = new Zend_Entity_Definition_Array(self::TEST_PROPERTY);
        $colDef->setMapKey(new stdClass());
    }

    public function testSetElement_NonString_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $colDef = new Zend_Entity_Definition_Array(self::TEST_PROPERTY);
        $colDef->setElement(new stdClass());
    }

    public function testSetGetTable()
    {
        $colDef = new Zend_Entity_Definition_Array(self::TEST_PROPERTY);
        $colDef->setTable(self::TEST_TABLE);

        $this->assertEquals(self::TEST_TABLE, $colDef->getTable());
    }

    public function testTablePublicProperty()
    {
        $colDef = new Zend_Entity_Definition_Array(self::TEST_PROPERTY);
        $colDef->setTable(self::TEST_TABLE);

        $this->assertEquals(self::TEST_TABLE, $colDef->table);
    }

    public function testSetGetKey()
    {
        $colDef = new Zend_Entity_Definition_Array(self::TEST_PROPERTY);
        $colDef->setKey(self::TEST_PROPERTY2);

        $this->assertEquals(self::TEST_PROPERTY2, $colDef->getKey());
    }

    public function testKeyPublicProperty()
    {
        $colDef = new Zend_Entity_Definition_Array(self::TEST_PROPERTY);
        $colDef->setKey(self::TEST_PROPERTY2);

        $this->assertEquals(self::TEST_PROPERTY2, $colDef->key);
    }

    public function testGetFetch_Default()
    {
        $colDef = new Zend_Entity_Definition_Collection(self::TEST_PROPERTY);
        $this->assertNull($colDef->getFetch());
    }

    public function testSetFetch()
    {
        $colDef = new Zend_Entity_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setFetch(Zend_Entity_Definition_Property::FETCH_SELECT);

        $this->assertEquals(
            Zend_Entity_Definition_Property::FETCH_SELECT,
            $colDef->getFetch()
        );
    }

    public function testFetchPublicProperty()
    {
        $colDef = new Zend_Entity_Definition_Collection(self::TEST_PROPERTY);

        $this->assertNull($colDef->fetch);
        $colDef->setFetch(Zend_Entity_Definition_Property::FETCH_SELECT);
        $this->assertEquals(Zend_Entity_Definition_Property::FETCH_SELECT, $colDef->fetch);
    }
}
