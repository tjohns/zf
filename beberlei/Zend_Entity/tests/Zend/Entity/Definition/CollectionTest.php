<?php

class Zend_Entity_Definition_CollectionTest extends Zend_Entity_Definition_TestCase
{
    public function testCreateCollectionPopulatesPropertyName()
    {
        $colDef = new Zend_Entity_Definition_Collection(self::TEST_PROPERTY);

        $this->assertEquals(self::TEST_PROPERTY, $colDef->getPropertyName());
    }

    public function testSetGetPropertyName()
    {
        $colDef = new Zend_Entity_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setPropertyName(self::TEST_PROPERTY2);

        $this->assertEquals(self::TEST_PROPERTY2, $colDef->getPropertyName());
    }

    public function testSetGetTable()
    {
        $colDef = new Zend_Entity_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setTable(self::TEST_TABLE);

        $this->assertEquals(self::TEST_TABLE, $colDef->getTable());
    }

    public function testTablePublicProperty()
    {
        $colDef = new Zend_Entity_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setTable(self::TEST_TABLE);

        $this->assertEquals(self::TEST_TABLE, $colDef->table);
    }

    public function testSetGetKey()
    {
        $colDef = new Zend_Entity_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setKey(self::TEST_PROPERTY2);

        $this->assertEquals(self::TEST_PROPERTY2, $colDef->getKey());
    }

    public function testKeyPublicProperty()
    {
        $colDef = new Zend_Entity_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setKey(self::TEST_PROPERTY2);

        $this->assertEquals(self::TEST_PROPERTY2, $colDef->key);
    }

    public function testSetGetWhereClause()
    {
        $colDef = new Zend_Entity_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setWhere("foo");

        $this->assertEquals("foo", $colDef->getWhere());
    }

    public function testWhereClausePublicProperty()
    {
        $colDef = new Zend_Entity_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setWhere("foo");

        $this->assertEquals("foo", $colDef->whereRestriction);
    }

    public function testSetGetOrderBy()
    {
        $colDef = new Zend_Entity_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setOrderBy("foo");

        $this->assertEquals("foo", $colDef->getOrderBy());
    }

    public function testOrderByPublicProperty()
    {
        $colDef = new Zend_Entity_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setOrderBy("foo");

        $this->assertEquals("foo", $colDef->orderByRestriction);
    }

    public function testGetDefaultInverse()
    {
        $colDef = new Zend_Entity_Definition_Collection(self::TEST_PROPERTY);

        $this->assertFalse($colDef->getInverse());
    }

    public function testGetSetInverse()
    {
        $colDef = new Zend_Entity_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setInverse(true);

        $this->assertTrue($colDef->getInverse());
    }

    public function testInversePublicProperty()
    {
        $colDef = new Zend_Entity_Definition_Collection(self::TEST_PROPERTY);

        $this->assertFalse($colDef->inverse);
        $colDef->setInverse(true);
        $this->assertTrue($colDef->inverse);
    }

    /**
     * @return Zend_Entity_Definition_Collection
     */
    protected function createCompileableCollection()
    {
        $colDef = new Zend_Entity_Definition_Collection(self::TEST_PROPERTY);
        $relationMock = $this->getMock('Zend_Entity_Definition_AbstractRelation', array(), array("propertyName"));
        $colDef->setRelation($relationMock);
        $colDef->setKey(self::TEST_PROPERTY2);

        return $colDef;
    }

    /**
     * @return Zend_Entity_MetadataFactory_Interface
     */
    protected function createEntityResourceMockWithDefitionByClassNameExpectsGetTable()
    {
        $entityDefinitionMock = $this->createEntityDefinitionMock();
        $entityDefinitionMock->expects($this->once())
                             ->method('getTable')
                             ->will($this->returnValue(self::TEST_TABLE));

        $mock = $this->createEntityResourceMock();
        $mock->expects($this->once())
             ->method('getDefinitionByEntityName')
             ->will($this->returnValue($entityDefinitionMock));

        return $mock;
    }
}