<?php

class Zend_Entity_Definition_ManyToOneRelationTest extends Zend_Entity_Definition_RelationTest
{
    /**
     * @return Zend_Entity_Definition_OneToOneRelation
     */
    public function createRelation()
    {
        return new Zend_Entity_Definition_ManyToOneRelation(self::TEST_PROPERTY);
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

    public function testCompileManyToOneSetsColumnIfUnset()
    {
        $relatedDef = $this->getMock('Zend_Entity_Definition_Entity');
        $relatedDef->expects($this->exactly(1))
                   ->method('getPrimaryKey')
                   ->will($this->returnValue(new Zend_Entity_Definition_PrimaryKey("foo")));
        $resourceMock = $this->createEntityResourceMock();
        $resourceMock->expects($this->exactly(1))
                     ->method('getDefinitionByEntityName')
                     ->with(self::TEST_CLASS)
                     ->will($this->returnValue($relatedDef));

        $relation = $this->createRelation();
        $relation->setClass(self::TEST_CLASS);
        $this->assertNull($relation->getColumnName());
        $relation->compile($this->createEntityDefinitionMock(), $resourceMock);
        $this->assertEquals(self::TEST_PROPERTY, $relation->getColumnName());

        $this->assertEquals("foo", $relation->getMappedBy());
    }
}