<?php

abstract class Zend_Entity_Definition_RelationTest extends Zend_Entity_Definition_TestCase
{
    /**
     * @return Zend_Entity_Definition_AbstractRelation
     */
    abstract public function createRelation();

    public function testDefaultFetchStrategyIsLazy()
    {
        $relDef = $this->createRelation();

        $this->assertEquals(Zend_Entity_Definition_Property::FETCH_LAZY, $relDef->getFetch());
    }

    public function testClassPublicProperty()
    {
        $relDef = $this->createRelation();
        $relDef->setClass("Foo");

        $this->assertEquals("Foo", $relDef->class);
    }

    public function testFetchPublicProperty()
    {
        $relDef = $this->createRelation();
        $relDef->setFetch(Zend_Entity_Definition_Property::FETCH_SELECT);

        $this->assertEquals(Zend_Entity_Definition_Property::FETCH_SELECT, $relDef->fetch);
    }

    public function testSetFetchStrategyToSelect()
    {
        $relDef = $this->createRelation();
        $relDef->setFetch(Zend_Entity_Definition_Property::FETCH_SELECT);

        $this->assertEquals(Zend_Entity_Definition_Property::FETCH_SELECT, $relDef->getFetch());
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

        $this->assertEquals(Zend_Entity_Definition_Property::CASCADE_NONE, $relDef->getCascade());
    }

    public function testCascadePublicProperty()
    {
        $relDef = $this->createRelation();

        $this->assertEquals(Zend_Entity_Definition_Property::CASCADE_NONE, $relDef->cascade);
        $relDef->setCascade(Zend_Entity_Definition_Property::CASCADE_SAVE);
        $this->assertEquals(Zend_Entity_Definition_Property::CASCADE_SAVE, $relDef->cascade);
    }

    public function testSetCascadeSave()
    {
        $relDef = $this->createRelation();
        $relDef->setCascade(Zend_Entity_Definition_Property::CASCADE_SAVE);

        $this->assertEquals(Zend_Entity_Definition_Property::CASCADE_SAVE, $relDef->getCascade());
    }

    public function testSetCascadeDelete()
    {
        $relDef = $this->createRelation();
        $relDef->setCascade(Zend_Entity_Definition_Property::CASCADE_DELETE);

        $this->assertEquals(Zend_Entity_Definition_Property::CASCADE_DELETE, $relDef->getCascade());
    }

    public function testSetCascadeAll()
    {
        $relDef = $this->createRelation();
        $relDef->setCascade(Zend_Entity_Definition_Property::CASCADE_ALL);

        $this->assertEquals(Zend_Entity_Definition_Property::CASCADE_ALL, $relDef->getCascade());
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

    public function testNotFoundDefaultsToNull()
    {
        $relation = $this->createRelation();

        $this->assertEquals(Zend_Entity_Definition_Property::NOTFOUND_EXCEPTION, $relation->getNotFound());
    }

    public function testSetNotFoundException()
    {
        $relation = $this->createRelation();
        $relation->setNotFound(Zend_Entity_Definition_Property::NOTFOUND_EXCEPTION);

        $this->assertEquals(Zend_Entity_Definition_Property::NOTFOUND_EXCEPTION, $relation->getNotFound());
    }

    public function testSetNotFoundNull()
    {
        $relation = $this->createRelation();
        $relation->setNotFound(Zend_Entity_Definition_Property::NOTFOUND_NULL);

        $this->assertEquals(Zend_Entity_Definition_Property::NOTFOUND_NULL, $relation->getNotFound());
    }

    public function testSetNotFoundUnknownValueThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $relation = $this->createRelation();
        $relation->setNotFound("foo");
    }

    public function testNotFoundPublicProperty()
    {
        $relation = $this->createRelation();

        $this->assertEquals(Zend_Entity_Definition_Property::NOTFOUND_EXCEPTION, $relation->notFound);
        $relation->setNotFound(Zend_Entity_Definition_Property::NOTFOUND_NULL);
        $this->assertEquals(Zend_Entity_Definition_Property::NOTFOUND_NULL, $relation->notFound);
    }
}