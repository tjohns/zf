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
}