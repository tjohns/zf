<?php

abstract class Zend_Entity_Mapper_Definition_RelationTest extends Zend_Entity_Mapper_Definition_TestCase
{
    abstract public function createRelation();

    public function testDefaultFetchStrategyIsLazy()
    {
        $relDef = $this->createRelation();

        $this->assertEquals(Zend_Entity_Mapper_Definition_Property::FETCH_LAZY, $relDef->getFetch());
    }

    public function testSetFetchStrategyToSelect()
    {
        $relDef = $this->createRelation();
        $relDef->setFetch(Zend_Entity_Mapper_Definition_Property::FETCH_SELECT);

        $this->assertEquals(Zend_Entity_Mapper_Definition_Property::FETCH_SELECT, $relDef->getFetch());
    }

    public function testSetFetchStrategyToInvalidNameThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");
        $relDef = $this->createRelation();
        $relDef->setFetch("foo");
    }

    public function testGetCascadeDefaultsToNone()
    {
        $relDef = $this->createRelation();

        $this->assertEquals(Zend_Entity_Mapper_Definition_Property::CASCADE_NONE, $relDef->getCascade());
    }

    public function testSetCascadeSave()
    {
        $relDef = $this->createRelation();
        $relDef->setCascade(Zend_Entity_Mapper_Definition_Property::CASCADE_SAVE);

        $this->assertEquals(Zend_Entity_Mapper_Definition_Property::CASCADE_SAVE, $relDef->getCascade());
    }

    public function testSetCascadeDelete()
    {
        $relDef = $this->createRelation();
        $relDef->setCascade(Zend_Entity_Mapper_Definition_Property::CASCADE_DELETE);

        $this->assertEquals(Zend_Entity_Mapper_Definition_Property::CASCADE_DELETE, $relDef->getCascade());
    }

    public function testSetCascadeAll()
    {
        $relDef = $this->createRelation();
        $relDef->setCascade(Zend_Entity_Mapper_Definition_Property::CASCADE_ALL);

        $this->assertEquals(Zend_Entity_Mapper_Definition_Property::CASCADE_ALL, $relDef->getCascade());
    }

    public function testSetCascadeInvalidThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $relDef = $this->createRelation();
        $relDef->setCascade("foo");
    }

    public function testCompileRelationWithoutClassThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $relDef = $this->createRelation();
        $relDef->setClass(null);

        $relDef->compile($this->createEntityDefinitionMock(), $this->createEntityResourceMock());
    }

    public function testSetGetColumnName()
    {
        $relDef = $this->createRelation();
        $relDef->setColumnName(self::TEST_PROPERTY2);

        $this->assertEquals(self::TEST_PROPERTY2, $relDef->getColumnName());
    }
}