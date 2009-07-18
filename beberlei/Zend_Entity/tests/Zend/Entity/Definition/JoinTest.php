<?php

class Zend_Entity_Definition_JoinTest extends Zend_Entity_Definition_TestCase
{
    public function testCreateJoinSetsPropertyAndTableName()
    {
        $joinDef = $this->createJoinDefinition(self::TEST_PROPERTY);
        $this->assertEquals(self::TEST_PROPERTY, $joinDef->getPropertyName());
        $this->assertEquals(self::TEST_PROPERTY, $joinDef->getTable());
    }

    public function testSetPropertyName()
    {
        $joinDef = $this->createJoinDefinition(self::TEST_PROPERTY);
        $joinDef->setPropertyName(self::TEST_PROPERTY2);

        $this->assertEquals(self::TEST_PROPERTY2, $joinDef->getPropertyName());
    }

    public function testSetGetTable()
    {
        $joinDef = $this->createJoinDefinition(self::TEST_PROPERTY);
        $joinDef->setTable(self::TEST_TABLE);

        $this->assertEquals(self::TEST_TABLE, $joinDef->getTable());
    }

    public function testSetGetKey()
    {
        $joinDef = $this->createJoinDefinition(self::TEST_PROPERTY);
        $joinDef->setKey(self::TEST_PROPERTY2);

        $this->assertEquals(self::TEST_PROPERTY2, $joinDef->getKey());
    }

    public function testSetGetOptional()
    {
        $joinDef = $this->createJoinDefinition(self::TEST_PROPERTY);

        $joinDef->setOptional(true);
        $this->assertTrue($joinDef->getOptional());

        $joinDef->setOptional(false);
        $this->assertFalse($joinDef->getOptional());
    }

    public function testAddPropertiesAndGet()
    {
        $joinDef = $this->createJoinDefinition(self::TEST_PROPERTY);
        $joinDef->addProperty(self::TEST_PROPERTY);
        $joinDef->addProperty(self::TEST_PROPERTY2);

        $properties = $joinDef->getProperties();
        $this->assertEquals(2, count($properties));

        $this->assertTrue(isset($properties[self::TEST_PROPERTY]));
        $this->assertEquals(self::TEST_PROPERTY, $properties[self::TEST_PROPERTY]->getPropertyName());
        $this->assertTrue(isset($properties[self::TEST_PROPERTY2]));
        $this->assertEquals(self::TEST_PROPERTY2, $properties[self::TEST_PROPERTY2]->getPropertyName());
    }

    public function testAddPropertyAndGetByPropertyName()
    {
        $joinDef = $this->createJoinDefinition(self::TEST_PROPERTY);
        $joinDef->addProperty(self::TEST_PROPERTY);

        $property = $joinDef->getPropertyByName(self::TEST_PROPERTY);
        $this->assertEquals(self::TEST_PROPERTY, $property->getPropertyName());
    }

    public function testGetPropertyByNameNonExistantThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $joinDef = $this->createJoinDefinition(self::TEST_PROPERTY);
        $joinDef->getPropertyByName(self::TEST_PROPERTY2);
    }

    public function testAddPropertyCallInterceptFirstArgumentNonStringThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $joinDef = $this->createJoinDefinition(self::TEST_PROPERTY);
        $joinDef->addProperty(array());
    }

    public function testAddPropertyCallInterceptSecondArgumentArrayIsPassedToNewProperty()
    {
        $joinDef = $this->createJoinDefinition(self::TEST_PROPERTY);
        $joinDef->addProperty(self::TEST_PROPERTY2, array('columnName' => 'foo'));

        $property = $joinDef->getPropertyByName(self::TEST_PROPERTY2);
        $this->assertEquals('foo', $property->getColumnName());
    }
}