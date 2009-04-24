<?php

class Zend_Entity_Mapper_Definition_OneToOneRelationTest extends Zend_Entity_Mapper_Definition_RelationTest
{
    /**
     * @return Zend_Entity_Mapper_Definition_OneToOneRelation
     */
    public function createRelation()
    {
        return new Zend_Entity_Mapper_Definition_OneToOneRelation(self::TEST_PROPERTY);
    }

    public function testNotFoundDefaultsToNull()
    {
        $relation = $this->createRelation();
        
        $this->assertEquals(Zend_Entity_Mapper_Definition_Property::NOTFOUND_EXCEPTION, $relation->getNotFound());
    }

    public function testSetNotFoundException()
    {
        $relation = $this->createRelation();
        $relation->setNotFound(Zend_Entity_Mapper_Definition_Property::NOTFOUND_EXCEPTION);

        $this->assertEquals(Zend_Entity_Mapper_Definition_Property::NOTFOUND_EXCEPTION, $relation->getNotFound());
    }

    public function testSetNotFoundNull()
    {
        $relation = $this->createRelation();
        $relation->setNotFound(Zend_Entity_Mapper_Definition_Property::NOTFOUND_NULL);

        $this->assertEquals(Zend_Entity_Mapper_Definition_Property::NOTFOUND_NULL, $relation->getNotFound());
    }

    public function testSetNotFoundUnknownValueThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $relation = $this->createRelation();
        $relation->setNotFound("foo");
    }
}