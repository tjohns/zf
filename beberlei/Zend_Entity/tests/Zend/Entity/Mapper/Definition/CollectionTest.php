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

    public function testDefaultFetchStrategyIsLazy()
    {
        $colDef = new Zend_Entity_Mapper_Definition_Collection(self::TEST_PROPERTY);

        $this->assertEquals(Zend_Entity_Mapper_Definition_Property::FETCH_LAZY, $colDef->getFetch());
    }

    public function testSetFetchStrategyToSelect()
    {
        $colDef = new Zend_Entity_Mapper_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setFetch(Zend_Entity_Mapper_Definition_Property::FETCH_SELECT);

        $this->assertEquals(Zend_Entity_Mapper_Definition_Property::FETCH_SELECT, $colDef->getFetch());
    }

    public function testSetFetchStrategyToJoin()
    {
        $colDef = new Zend_Entity_Mapper_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setFetch(Zend_Entity_Mapper_Definition_Property::FETCH_JOIN);

        $this->assertEquals(Zend_Entity_Mapper_Definition_Property::FETCH_JOIN, $colDef->getFetch());
    }

    public function testSetFetchStrategyToInvalidNameThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");
        $colDef = new Zend_Entity_Mapper_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setFetch("foo");
    }

    public function testGetCascadeDefaultsToNone()
    {
        $colDef = new Zend_Entity_Mapper_Definition_Collection(self::TEST_PROPERTY);
        
        $this->assertEquals(Zend_Entity_Mapper_Definition_Property::CASCADE_NONE, $colDef->getCascade());
    }

    public function testSetCascadeSave()
    {
        $colDef = new Zend_Entity_Mapper_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setCascade(Zend_Entity_Mapper_Definition_Property::CASCADE_SAVE);

        $this->assertEquals(Zend_Entity_Mapper_Definition_Property::CASCADE_SAVE, $colDef->getCascade());
    }

    public function testSetCascadeDelete()
    {
        $colDef = new Zend_Entity_Mapper_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setCascade(Zend_Entity_Mapper_Definition_Property::CASCADE_DELETE);

        $this->assertEquals(Zend_Entity_Mapper_Definition_Property::CASCADE_DELETE, $colDef->getCascade());
    }

    public function testSetCascadeAll()
    {
        $colDef = new Zend_Entity_Mapper_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setCascade(Zend_Entity_Mapper_Definition_Property::CASCADE_ALL);

        $this->assertEquals(Zend_Entity_Mapper_Definition_Property::CASCADE_ALL, $colDef->getCascade());
    }

    public function testSetCascadeInvalidThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $colDef = new Zend_Entity_Mapper_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setCascade("foo");
    }

    public function testSetGetWhereClause()
    {
        $colDef = new Zend_Entity_Mapper_Definition_Collection(self::TEST_PROPERTY);
        $colDef->setWhere("foo");

        $this->assertEquals("foo", $colDef->getWhere());
    }

    public function testCollectionRequiresARelationThrowsExceptionOtherwise()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $colDef = new Zend_Entity_Mapper_Definition_Collection(self::TEST_PROPERTY);
        $colDef->compile($this->createEntityDefinitionMock(), $this->createEntityResourceMock());
    }
}