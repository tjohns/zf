<?php

class Zend_Entity_Mapper_Definition_CollectionTest extends Zend_Entity_Mapper_Definition_TestCase
{
    public function testCreateCollectionPopulatesPropertyName()
    {
        $colDef = new Zend_Entity_Mapper_Definition_Collection(self::TEST_PROPERTY);

        $this->assertEquals(self::TEST_PROPERTY, $colDef->getPropertyName());
    }

    public function testSetGetPropertyName()
    {
        $colDef = new Zend_Entity_Mapper_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setPropertyName(self::TEST_PROPERTY2);

        $this->assertEquals(self::TEST_PROPERTY2, $colDef->getPropertyName());
    }

    public function testSetGetTable()
    {
        $colDef = new Zend_Entity_Mapper_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setTable(self::TEST_TABLE);

        $this->assertEquals(self::TEST_TABLE, $colDef->getTable());
    }

    public function testSetGetKey()
    {
        $colDef = new Zend_Entity_Mapper_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setKey(self::TEST_PROPERTY2);

        $this->assertEquals(self::TEST_PROPERTY2, $colDef->getKey());
    }

    public function testSetGetWhereClause()
    {
        $colDef = new Zend_Entity_Mapper_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setWhere("foo");

        $this->assertEquals("foo", $colDef->getWhere());
    }

    public function testCollectionCompileRequiresARelationThrowsExceptionOtherwise()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $colDef = new Zend_Entity_Mapper_Definition_Collection(self::TEST_PROPERTY);
        $colDef->compile($this->createEntityDefinitionMock(), $this->createEntityResourceMock());
    }

    public function testCollectionCompileSetsTableFromRelationIfNoneIsset()
    {
        $colDef = $this->createCompileableCollection();
        $this->assertNull($colDef->getTable());

        $colDef->compile($this->createEntityDefinitionMock(), $this->createEntityResourceMockWithDefitionByClassNameExpectsGetTable());
        $this->assertEquals(self::TEST_TABLE, $colDef->getTable());
    }

    public function testCollectionCompileRequiresKeyFieldNotNull()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $colDef = $this->createCompileableCollection();
        $colDef->setKey(null);

        $colDef->compile($this->createEntityDefinitionMock(), $this->createEntityResourceMockWithDefitionByClassNameExpectsGetTable());
    }

    /**
     * @return Zend_Entity_Mapper_Definition_Collection
     */
    protected function createCompileableCollection()
    {
        $colDef = new Zend_Entity_Mapper_Definition_Collection(self::TEST_PROPERTY);
        $relationMock = $this->getMock('Zend_Entity_Mapper_Definition_Relation');
        $colDef->setRelation($relationMock);
        $colDef->setKey(self::TEST_PROPERTY2);

        return $colDef;
    }

    /**
     * @return Zend_Entity_Resource_Interface
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