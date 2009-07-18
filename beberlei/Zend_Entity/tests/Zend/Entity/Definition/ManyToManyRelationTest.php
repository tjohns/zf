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

    public function testForeignKeyDefaultsToForeignClassColumnName()
    {
        $primaryKey = new Zend_Entity_Definition_PrimaryKey(self::TEST_PROPERTY2, array(
                'columnName' => self::TEST_COLUMN2
            ));

        $foreignDefMock = $this->createEntityDefinitionMock();
        $foreignDefMock->expects($this->exactly(2))
                       ->method('getPrimaryKey')
                       ->will($this->returnValue($primaryKey));

        $resourceMock = $this->createEntityResourceMock();
        $resourceMock->expects($this->exactly(2))
                     ->method('getDefinitionByEntityName')
                     ->will($this->returnValue($foreignDefMock));

        $relation = $this->createRelation();
        $relation->setClass(self::TEST_CLASS2);
        $this->assertNull($relation->getColumnName());

        $relation->compile($this->createEntityDefinitionMock(), $resourceMock);

        $this->assertEquals(self::TEST_COLUMN2, $relation->getColumnName());
    }
}