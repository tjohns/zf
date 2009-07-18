<?php

class Zend_Entity_Definition_OneToOneRelationTest extends Zend_Entity_Definition_RelationTest
{
    /**
     * @return Zend_Entity_Definition_OneToOneRelation
     */
    public function createRelation()
    {
        return new Zend_Entity_Definition_OneToOneRelation(self::TEST_PROPERTY);
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

    public function testCompileOneToOne()
    {
        $primaryKey = new Zend_Entity_Definition_PrimaryKey("id");

        $relatedDef = $this->createEntityDefinitionMock();
        $relatedDef->expects($this->once())
                   ->method('getPrimaryKey')
                   ->will($this->returnValue($primaryKey));

        $defMock = $this->createEntityResourceMock();
        $defMock->expects($this->once())
                ->method('getDefinitionByEntityName')
                ->with($this->equalTo(self::TEST_CLASS))
                ->will($this->returnValue($relatedDef));

        $relation = $this->createRelation();
        $relation->setClass(self::TEST_CLASS);
        $relation->compile($this->createEntityDefinitionMock(), $defMock);

        $this->assertEquals("id", $relation->getMappedBy());
    }
}