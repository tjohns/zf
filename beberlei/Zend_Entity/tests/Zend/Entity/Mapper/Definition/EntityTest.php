<?php

class Zend_Entity_Mapper_Definition_EntityTest extends Zend_Entity_Mapper_Definition_TestCase
{
    public function testConstructEntityWithClassName()
    {
        $entityDef = new Zend_Entity_Mapper_Definition_Entity(self::TEST_CLASS);
        $this->assertEquals(self::TEST_CLASS, $entityDef->getClass());
    }

    public function testSetEntityClassWithMethod()
    {
        $entityDef = new Zend_Entity_Mapper_Definition_Entity(self::TEST_CLASS);
        $entityDef->setClass(self::TEST_CLASS2);

        $this->assertEquals(self::TEST_CLASS2, $entityDef->getClass());
    }

    public function testConstructEntitySetsDefaultTableName()
    {
        $entityDef = new Zend_Entity_Mapper_Definition_Entity(self::TEST_CLASS);

        $this->assertEquals(self::TEST_CLASS, $entityDef->getTable());
    }

    public function testSetGetTable()
    {
        $entityDef = new Zend_Entity_Mapper_Definition_Entity(self::TEST_CLASS);
        $entityDef->setTable(self::TEST_TABLE);
        
        $this->assertEquals(self::TEST_TABLE, $entityDef->getTable());
    }

    public function testAddPropertyViaCallIntercept()
    {
        $entityDef = new Zend_Entity_Mapper_Definition_Entity(self::TEST_CLASS);
        $entityDef->addProperty(self::TEST_PROPERTY);

        $propertyName = $entityDef->getPropertyByName(self::TEST_PROPERTY)->getPropertyName();
        $this->assertEquals(self::TEST_PROPERTY, $propertyName);
    }

    public function testGetProperties()
    {
        $entityDef = new Zend_Entity_Mapper_Definition_Entity(self::TEST_CLASS);
        $entityDef->addProperty(self::TEST_PROPERTY);

        $this->assertEquals(1, count($entityDef->getProperties()));
    }

    public function testAddingPropertyThatAlreadyExistsThrowsEception()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $entityDef = new Zend_Entity_Mapper_Definition_Entity(self::TEST_CLASS);
        $entityDef->addProperty(self::TEST_PROPERTY);
        $entityDef->addProperty(self::TEST_PROPERTY);
    }

    public function testCallOnlyInterceptsAddMethodsOtherwiseException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $entityDef = new Zend_Entity_Mapper_Definition_Entity(self::TEST_CLASS);
        $entityDef->asdf();
    }

    public function testAddingPrimaryKeyPropertyIsAccessibleViaGetPrimaryKey()
    {
        $entityDef = new Zend_Entity_Mapper_Definition_Entity(self::TEST_CLASS);
        $entityDef->addPrimaryKey(self::TEST_PROPERTY);

        $primaryKey = $entityDef->getPrimaryKey();
        $this->assertNotNull($primaryKey);
        $this->assertEquals(self::TEST_PROPERTY, $primaryKey->getPropertyName());
    }

    public function testSetGetLoaderClass()
    {
        $entityDef = new Zend_Entity_Mapper_Definition_Entity(self::TEST_CLASS);
        $entityDef->setLoaderClass(self::TEST_CLASS2);

        $this->assertEquals(self::TEST_CLASS2, $entityDef->getLoaderClass());
    }

    public function testSetGetPersisterClass()
    {
        $entityDef = new Zend_Entity_Mapper_Definition_Entity(self::TEST_CLASS);
        $entityDef->setPersisterClass(self::TEST_CLASS2);

        $this->assertEquals(self::TEST_CLASS2, $entityDef->getPersisterClass());
    }

    public function testAddRelationAccessibleWithGetRelations()
    {
        $entityDef = new Zend_Entity_Mapper_Definition_Entity(self::TEST_CLASS);
        $entityDef->addOneToOneRelation(self::TEST_PROPERTY);

        $relation = $entityDef->getRelations();
        $this->assertEquals(1, count($relation));
        $this->assertTrue(isset($relation[self::TEST_PROPERTY]));
        $relationPropertyName = $relation[self::TEST_PROPERTY]->getPropertyName();
        $this->assertEquals(self::TEST_PROPERTY, $relationPropertyName);
    }

    public function testAddRelationAccessibleByPropertyName()
    {
        $entityDef = new Zend_Entity_Mapper_Definition_Entity(self::TEST_CLASS);
        $entityDef->addOneToOneRelation(self::TEST_PROPERTY);

        $relation = $entityDef->getPropertyByName(self::TEST_PROPERTY);
        $this->assertEquals(self::TEST_PROPERTY, $relation->getPropertyName());
    }

    public function testAccessNonExistingPropertyThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $entityDef = new Zend_Entity_Mapper_Definition_Entity(self::TEST_CLASS);
        $entityDef->getPropertyByName(self::TEST_PROPERTY);
    }

    public function testAddTableExtensionAccessibleByPropertyName()
    {
        $entityDef = new Zend_Entity_Mapper_Definition_Entity(self::TEST_CLASS);
        $entityDef->addJoin(self::TEST_TABLE);

        $extension = $entityDef->getPropertyByName(self::TEST_TABLE);
    }

    public function testGetDefaultStateTransformerClass()
    {
        $entityDef = new Zend_Entity_Mapper_Definition_Entity(self::TEST_CLASS);
        $this->assertEquals("Zend_Entity_Mapper_StateTransformer_Array", $entityDef->getStateTransformerClass());
    }

    /**
     * @dataProvider dataSetStateTransformerClass
     * @param <type> $class
     * @param <type> $expected
     */
    public function testSetStateTransformerClass($class, $expected)
    {
        $entityDef = new Zend_Entity_Mapper_Definition_Entity(self::TEST_CLASS);
        $entityDef->setStateTransformerClass($class);
        $this->assertEquals($expected, $entityDef->getStateTransformerClass());
    }

    static public function dataSetStateTransformerClass()
    {
        return array(
            array('MyStateTransformer', 'MyStateTransformer'),
            array('Array', 'Zend_Entity_Mapper_StateTransformer_Array'),
            array('Property', 'Zend_Entity_Mapper_StateTransformer_Property'),
            array('Reflection', 'Zend_Entity_Mapper_StateTransformer_Reflection'),
        );
    }
}