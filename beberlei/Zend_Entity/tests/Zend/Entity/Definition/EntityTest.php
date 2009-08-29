<?php

class Zend_Entity_Definition_EntityTest extends Zend_Entity_Definition_TestCase
{
    public function testConstructEntityWithClassName()
    {
        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        $this->assertEquals(self::TEST_CLASS, $entityDef->getClass());
    }

    public function testEntityClassNamePublicProperty()
    {
        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        $this->assertEquals(self::TEST_CLASS, $entityDef->class);
    }

    public function testSetEntityClassWithMethod()
    {
        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        $entityDef->setClass(self::TEST_CLASS2);

        $this->assertEquals(self::TEST_CLASS2, $entityDef->getClass());
    }

    public function testConstructEntitySetsDefaultTableName()
    {
        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);

        $this->assertEquals(self::TEST_CLASS, $entityDef->getTable());
    }

    public function testSetGetTable()
    {
        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        $entityDef->setTable(self::TEST_TABLE);
        
        $this->assertEquals(self::TEST_TABLE, $entityDef->getTable());
    }

    public function testGetDefaultSchema()
    {
        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);

        $this->assertNull($entityDef->schema);
    }

    public function testSetGetSchema()
    {
        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        $entityDef->setSchema("foo");

        $this->assertEquals("foo", $entityDef->schema);
        $this->assertEquals("foo", $entityDef->getSchema());
    }

    public function testTablePublicProperty()
    {
        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        
        $this->assertEquals(self::TEST_CLASS, $entityDef->table);
        $entityDef->setTable(self::TEST_TABLE);
        $this->assertEquals(self::TEST_TABLE, $entityDef->table);
    }

    public function testAddPropertyViaCallIntercept()
    {
        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        $entityDef->addProperty(self::TEST_PROPERTY);

        $propertyName = $entityDef->getPropertyByName(self::TEST_PROPERTY)->getPropertyName();
        $this->assertEquals(self::TEST_PROPERTY, $propertyName);
    }

    public function testGetProperties()
    {
        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        $entityDef->addProperty(self::TEST_PROPERTY);

        $this->assertEquals(1, count($entityDef->getProperties()));
    }

    public function testAddingPropertyThatAlreadyExistsThrowsEception()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        $entityDef->addProperty(self::TEST_PROPERTY);
        $entityDef->addProperty(self::TEST_PROPERTY);
    }

    public function testCallOnlyInterceptsAddMethodsOtherwiseException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        $entityDef->asdf();
    }

    public function testAddingPrimaryKeyPropertyIsAccessibleViaGetPrimaryKey()
    {
        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        $entityDef->addPrimaryKey(self::TEST_PROPERTY);

        $primaryKey = $entityDef->getPrimaryKey();
        $this->assertNotNull($primaryKey);
        $this->assertEquals(self::TEST_PROPERTY, $primaryKey->getPropertyName());
    }

    public function testAddRelationAccessibleWithGetProperties()
    {
        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        $entityDef->addOneToOneRelation(self::TEST_PROPERTY);

        $properties = $entityDef->getProperties();
        $this->assertEquals(1, count($properties));
        $this->assertTrue(isset($properties[self::TEST_PROPERTY]));
        $relationPropertyName = $properties[self::TEST_PROPERTY]->getPropertyName();
        $this->assertEquals(self::TEST_PROPERTY, $relationPropertyName);
    }

    public function testAddRelationAccessibleByPropertyName()
    {
        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        $entityDef->addOneToOneRelation(self::TEST_PROPERTY);

        $relation = $entityDef->getPropertyByName(self::TEST_PROPERTY);
        $this->assertEquals(self::TEST_PROPERTY, $relation->getPropertyName());
    }

    public function testAccessNonExistingPropertyThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        $entityDef->getPropertyByName(self::TEST_PROPERTY);
    }

    public function testAddTableExtensionAccessibleByPropertyName()
    {
        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        $entityDef->addCollection(self::TEST_TABLE);

        $extension = $entityDef->getPropertyByName(self::TEST_TABLE);
    }

    public function testGetDefaultStateTransformerClass()
    {
        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        $this->assertEquals("Zend_Entity_StateTransformer_Array", $entityDef->getStateTransformerClass());
    }

    /**
     * @dataProvider dataSetStateTransformerClass
     * @param string $class
     * @param string $expected
     */
    public function testSetStateTransformerClass($class, $expected)
    {
        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        $entityDef->setStateTransformerClass($class);
        $this->assertEquals($expected, $entityDef->getStateTransformerClass());
    }

    static public function dataSetStateTransformerClass()
    {
        return array(
            array('MyStateTransformer', 'MyStateTransformer'),
            array('Array', 'Zend_Entity_StateTransformer_Array'),
            array('Property', 'Zend_Entity_StateTransformer_Property'),
            array('Reflection', 'Zend_Entity_StateTransformer_Reflection'),
        );
    }

    public function testGetEntityNameDefaultsToClass()
    {
        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        $this->assertEquals(self::TEST_CLASS, $entityDef->getEntityName());
    }

    public function testGetEntityName()
    {
        $entityDef = new Zend_Entity_Definition_Entity(self::TEST_CLASS);
        $entityDef->setEntityName(self::TEST_CLASS2);
        $this->assertEquals(self::TEST_CLASS2, $entityDef->getEntityName());
    }

    public function testVisitEntityDefinition()
    {
        $entityDef = new Zend_Entity_Definition_TestingEntity(self::TEST_CLASS);

        $propertyA = new Zend_Entity_Definition_Property("foo");
        $propertyB = new Zend_Entity_Definition_Property("bar");

        $entityDef->addElement("foo", $propertyA);
        $entityDef->addElement("bar", $propertyB);

        $visitorMock = $this->getMock('Zend_Entity_Definition_MappingVisitor', array('acceptProperty', 'acceptEntity', 'finalize'));
        $visitorMock->expects($this->at(0))
                    ->method('acceptEntity')
                    ->with($this->equalTo($entityDef));
        $visitorMock->expects($this->at(1))
                    ->method('acceptProperty')
                    ->with($this->equalTo($propertyA));
        $visitorMock->expects($this->at(2))
                    ->method('acceptProperty')
                    ->with($this->equalTo($propertyB));
        $visitorMock->expects($this->at(3))
                    ->method('finalize');

        $entityDef->visit($visitorMock, $this->getMock('Zend_Entity_MetadataFactory_Interface'));
    }

    public function testSetGetStateTransformer()
    {
        $stateTransformer = new Zend_Entity_StateTransformer_Array();
        $entityDef = new Zend_Entity_Definition_Entity("classA");
        $entityDef->setStateTransformer($stateTransformer);

        $this->assertSame($stateTransformer, $entityDef->getStateTransformer());
    }

    public function testIsVersioned_Default()
    {
        $entityDef = new Zend_Entity_Definition_Entity("classA");
        $this->assertNull($entityDef->getVersionProperty());
    }

    public function testIsVersioned()
    {
        $entityDef = new Zend_Entity_Definition_Entity("classA");
        $entityDef->addVersion("foo");

        $this->assertType('Zend_Entity_Definition_Version', $entityDef->getVersionProperty());
    }

    public function testAddProperty_FirstArgumentNotString_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $entityDef = new Zend_Entity_Definition_Entity("classA");
        $entityDef->addProperty(array());
    }

    public function testGetDefaultChangePolicy_IsPassthrough()
    {
        $entityDef = new Zend_Entity_Definition_Entity("classA");

        $this->assertEquals(Zend_Entity_Definition_Entity::CHANGEPOLICY_PASSTHROUGH_EXPLICIT, $entityDef->getChangePolicy());
    }

    public function testSetGetChangePolicy()
    {
        $entityDef = new Zend_Entity_Definition_Entity("classA");

        $entityDef->setChangePolicy(Zend_Entity_Definition_Entity::CHANGEPOLICY_PASSTHROUGH_EXPLICIT);
        $this->assertEquals(Zend_Entity_Definition_Entity::CHANGEPOLICY_PASSTHROUGH_EXPLICIT, $entityDef->getChangePolicy());
    }
}

class Zend_Entity_Definition_TestingEntity extends Zend_Entity_Definition_Entity
{
    public function setPrimaryKey($propertyName, Zend_Entity_Definition_PrimaryKey $pk)
    {
        $this->primaryKey = $pk;
        $this->_properties[$propertyName] = $pk;
    }

    public function addElement($propertyName, Zend_Entity_Definition_Property $property)
    {
        $this->_properties[$propertyName] = $property;
    }
}