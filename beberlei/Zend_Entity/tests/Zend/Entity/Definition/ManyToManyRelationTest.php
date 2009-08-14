<?php

class Zend_Entity_Definition_ManyToManyRelationTest extends Zend_Entity_Definition_RelationTest
{
    /**
     * @return Zend_Entity_Definition_OneToOneRelation
     */
    public function createRelation()
    {
        return new Zend_Entity_Definition_ManyToManyRelation(self::TEST_PROPERTY);
    }
}