<?php

class Zend_Entity_Definition_ManyToOneRelationTest extends Zend_Entity_Definition_RelationTest
{
    /**
     * @return Zend_Entity_Definition_ManyToOneRelation
     */
    public function createRelation()
    {
        return new Zend_Entity_Definition_ManyToOneRelation(self::TEST_PROPERTY);
    }
}