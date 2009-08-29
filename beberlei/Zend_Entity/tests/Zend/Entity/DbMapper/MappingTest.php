<?php

class Zend_Entity_DbMapper_MappingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Db_Mapper_Mapping
     */
    private $mapping = null;
    private $metadataFactory = null;

    public function setUp()
    {
        $this->mapping = new Zend_Db_Mapper_Mapping();
        $this->metadataFactory = $this->getMock('Zend_Entity_MetadataFactory_Interface');
    }

    public function testAcceptEntity_WithoutPrimaryKey_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $entity = new Zend_Entity_Definition_Entity("foo");

        $this->mapping->acceptEntity($entity, $this->metadataFactory);
    }

    public function testAcceptEntity_KeepsClass()
    {
        $entity = new Zend_Entity_Definition_Entity("foo");
        $entity->addPrimaryKey("id");
        $this->mapping->acceptEntity($entity, $this->metadataFactory);

        $this->assertEquals("foo", $this->mapping->class);
    }

    public function testAcceptEntity_UseClassAsTable_IfTableNotDefined()
    {
        $entity = new Zend_Entity_Definition_Entity("foo");
        $entity->addPrimaryKey("id");
        $this->mapping->acceptEntity($entity, $this->metadataFactory);

        $this->assertEquals("foo", $this->mapping->table);
    }

    public function testAcceptEntity_KeepsTable()
    {
        $entity = new Zend_Entity_Definition_Entity("foo", array("table" => "bar"));
        $entity->addPrimaryKey("id");
        $this->mapping->acceptEntity($entity, $this->metadataFactory);

        $this->assertEquals("bar", $this->mapping->table);
    }

    public function testAcceptEntity_KeepsSchema()
    {
        $entity = new Zend_Entity_Definition_Entity("foo", array("table" => "bar", "schema" => "baz"));
        $entity->addPrimaryKey("id");
        $this->mapping->acceptEntity($entity, $this->metadataFactory);

        $this->assertEquals("baz", $this->mapping->schema);
    }

    public function testAcceptEntity_InstantiatesStateTransformerClass()
    {
        $stateTransformerMock = $this->getMock(
            'Zend_Entity_StateTransformer_Abstract',
            array(),
            array(),
            'Zend_Entity_StateTransformer_InstantiateTransformerTestMock'
        );

        $entity = new Zend_Entity_Definition_Entity("foo", array(
            "stateTransformerClass" => "Zend_Entity_StateTransformer_InstantiateTransformerTestMock"
        ));
        $entity->addPrimaryKey("id");
        $this->mapping->acceptEntity($entity, $this->metadataFactory);

        $this->assertType('Zend_Entity_StateTransformer_InstantiateTransformerTestMock', $this->mapping->stateTransformer);
    }

    public function testAcceptEntity_InvalidStateTransformerClass_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $entity = new Zend_Entity_Definition_Entity("foo", array(
            "stateTransformerClass" => "stdClass"
        ));
        $entity->addPrimaryKey("id");
        $this->mapping->acceptEntity($entity, $this->metadataFactory);
    }

    public function testAcceptEntity_UnknownStateTransformerClass_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $entity = new Zend_Entity_Definition_Entity("foo", array(
            "stateTransformerClass" => "invalidClassName"
        ));
        $entity->addPrimaryKey("id");
        $this->mapping->acceptEntity($entity, $this->metadataFactory);
    }

    public function testFinalize_PassPropertyNames_ToStateTransformer()
    {
        $entity = new Zend_Entity_Definition_Entity("foo", array(
            "stateTransformerClass" => "Zend_Entity_DbMapper_TestStateTransformer"
        ));
        $entity->addPrimaryKey("id");
        $this->mapping->acceptProperty($entity->getPropertyByName("id"), $this->metadataFactory);
        $this->mapping->acceptEntity($entity, $this->metadataFactory);
        $this->mapping->finalize();

        $this->assertEquals(array("id"), $this->mapping->stateTransformer->_propertyNames);
    }

    public function testAcceptProperty_WithEmptyName_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $property = new Zend_Entity_Definition_Property(array());
        $this->mapping->acceptProperty($property, $this->metadataFactory);
    }

    public function testAcceptProperty_AddToPropertyNames()
    {
        $property = new Zend_Entity_Definition_Property("foo");
        $this->mapping->acceptProperty($property, $this->metadataFactory);

        $this->assertEquals(array("foo"), $this->mapping->propertyNames);
    }

    public function testAcceptProperty_AddToColumnNamePropertyMap()
    {
        $property = new Zend_Entity_Definition_Property("foo", array("columnName" => "bar"));
        $this->mapping->acceptProperty($property, $this->metadataFactory);

        $this->assertEquals(array("bar" => $property), $this->mapping->columnNameToProperty);
    }

    public function testAcceptProperty_ColumnNameNull_UsesPropertyNameAsValue()
    {
        $property = new Zend_Entity_Definition_Property("foo");
        $this->mapping->acceptProperty($property, $this->metadataFactory);

        $this->assertEquals(array("foo" => $property), $this->mapping->columnNameToProperty);
    }

    public function testAcceptPropertyPrimaryKey_KeepsInPrimaryKeyVariable()
    {
        $property = new Zend_Entity_Definition_PrimaryKey("foo");
        $this->mapping->acceptProperty($property, $this->metadataFactory);

        $this->assertEquals($property, $this->mapping->primaryKey);
    }

    public function testAcceptPropertyVersion_KeepsInVersionVariable()
    {
        $property = new Zend_Entity_Definition_Version("foo");

        $this->mapping->acceptProperty($property, $this->metadataFactory);

        $this->assertEquals($property, $this->mapping->versionProperty);
    }

    public function testAcceptRelationProperty_WithoutSpecifiedClass_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $relation = new Zend_Entity_Definition_ManyToOneRelation("foo");

        $this->mapping->acceptProperty($relation, $this->metadataFactory);
    }

    public function testAcceptRelationProperty_InverseRelation_MappedByNull_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $relation = new Zend_Entity_Definition_OneToOneRelation("foo", array("class" => "Bar", "inverse" => true));
        $foreignDef = new Zend_Entity_Definition_Entity("Bar");

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->mapping->acceptProperty($relation, $this->metadataFactory);
    }

    public function testAcceptRelationProperty_InverseRelation_MappedByPropertyNotExists_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $relation = new Zend_Entity_Definition_OneToOneRelation("foo", array("class" => "Bar", "inverse" => true, "mappedBy" => "bar"));
        $foreignDef = new Zend_Entity_Definition_Entity("Bar");

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->mapping->acceptProperty($relation, $this->metadataFactory);
    }

    public function testAcceptRelationProperty_OwningRelation_AutomaticallyDetectsUnsetMappedBy_FromForeignPrimaryKey()
    {
        $relation = new Zend_Entity_Definition_OneToOneRelation("foo", array("class" => "Bar", "inverse" => false));
        $foreignDef = new Zend_Entity_Definition_Entity("Bar");
        $foreignDef->addPrimaryKey("bar");

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->mapping->acceptProperty($relation, $this->metadataFactory);

        $this->assertEquals("bar", $this->mapping->columnNameToProperty["foo"]->getMappedBy());
    }

    public function testAcceptRelationProperty_OwningRelation_SpecifiedMappedBy_UnknownForeignProperty_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $relation = new Zend_Entity_Definition_OneToOneRelation("foo", array("class" => "Bar", "inverse" => false, "mappedBy" => "baz"));
        $foreignDef = new Zend_Entity_Definition_Entity("Bar");
        $foreignDef->addPrimaryKey("bar");

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->mapping->acceptProperty($relation, $this->metadataFactory);
    }

    public function testAcceptRelationProperty_OwningRelation_KeptInToOneRelations()
    {
        $relation = new Zend_Entity_Definition_OneToOneRelation("foo", array("class" => "Bar", "inverse" => false));
        $foreignDef = new Zend_Entity_Definition_Entity("Bar");
        $foreignDef->addPrimaryKey("bar");

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->mapping->acceptProperty($relation, $this->metadataFactory);

        $this->assertTrue(isset($this->mapping->toOneRelations["foo"]));
        $this->assertSame($relation, $this->mapping->toOneRelations["foo"]);
    }

    public function testAcceptRelationProperty_InverseRelation_NotKeptInToOneRelations()
    {
        $relation = new Zend_Entity_Definition_OneToOneRelation("foo", array("class" => "Bar", "inverse" => true, "mappedBy" => "bar"));
        $foreignDef = new Zend_Entity_Definition_Entity("Bar");
        $foreignDef->addPrimaryKey("bar");

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->mapping->acceptProperty($relation, $this->metadataFactory);

        $this->assertFalse(isset($this->mapping->toOneRelations["foo"]));
    }

    public function testAcceptRelationProperty_OwningRelation_KeptInColumnNameToPropertyMap()
    {
        $relation = new Zend_Entity_Definition_OneToOneRelation("foo", array("columnName" => "foo_id", "class" => "Bar", "inverse" => false));
        $foreignDef = new Zend_Entity_Definition_Entity("Bar");
        $foreignDef->addPrimaryKey("bar");

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->mapping->acceptProperty($relation, $this->metadataFactory);

        $this->assertTrue(isset($this->mapping->columnNameToProperty["foo_id"]));
        $this->assertEquals($this->mapping->columnNameToProperty["foo_id"], $relation);
    }

    public function testAcceptRelationProperty_InverseRelation_NotKeptInColumnNameToPropertyMap()
    {
        $relation = new Zend_Entity_Definition_OneToOneRelation("foo", array("columnName" => "foo_id", "class" => "Bar", "inverse" => true, "mappedBy" => "bar"));
        $foreignDef = new Zend_Entity_Definition_Entity("Bar");
        $foreignDef->addPrimaryKey("bar");

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->mapping->acceptProperty($relation, $this->metadataFactory);

        $this->assertFalse(isset($this->mapping->columnNameToProperty["foo_id"]));
    }

    public function testAcceptRelationProperty_OwningRelation_KeptInSqlColumnAliasMap()
    {
        $relation = new Zend_Entity_Definition_OneToOneRelation("foo", array("columnName" => "foo_id", "class" => "Bar", "inverse" => false));
        $foreignDef = new Zend_Entity_Definition_Entity("Bar");
        $foreignDef->addPrimaryKey("bar");

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->mapping->acceptProperty($relation, $this->metadataFactory);

        $this->assertTrue(isset($this->mapping->sqlColumnAliasMap["foo_id"]));
        $this->assertEquals("foo_id", $this->mapping->sqlColumnAliasMap["foo_id"]);
    }

    public function testAcceptCollectionProperty_WithoutRelation_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $collection = new Zend_Entity_Definition_Collection("foo");

        $this->mapping->acceptProperty($collection, $this->metadataFactory);
    }

    public function testAcceptCollectionProperty_FetchNull_UseRelationFetch()
    {
        $collection = new Zend_Entity_Definition_Collection("foo", array(
            "relation" => new Zend_Entity_Definition_OneToManyRelation("bar", array("class" => "Bar", "mappedBy" => "foo")),
            "key" => "bar_id",
        ));

        $foreignDef = new Zend_Entity_Definition_Entity("Bar");
        $foreignDef->addPrimaryKey("foo");

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->assertNull($collection->getFetch());
        $this->mapping->acceptProperty($collection, $this->metadataFactory);
        $this->assertEquals("lazy", $collection->getFetch());
    }

    public function testAcceptCollectionProperty_NoKey_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $collection = new Zend_Entity_Definition_Collection("foo", array(
            "relation" => new Zend_Entity_Definition_OneToManyRelation("bar", array("class" => "Bar", "mappedBy" => "foo")),
        ));

        $foreignDef = new Zend_Entity_Definition_Entity("Bar");
        $foreignDef->addPrimaryKey("foo");

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->mapping->acceptProperty($collection, $this->metadataFactory);
    }

    public function testAcceptArray_NoKey_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $array = new Zend_Entity_Definition_Array("foo", array(
            "table" => "elements",
            "mapkey" => "key",
            "element" => "value",
            "fetch" => null,
        ));

        $this->mapping->acceptProperty($array, $this->metadataFactory);
    }

    public function testAcceptArray_NoFetch_SetLazy()
    {
        $array = new Zend_Entity_Definition_Array("foo", array(
            "table" => "elements",
            "mapkey" => "key",
            "key" => "foo_id",
            "element" => "value",
            "fetch" => null,
        ));

        $this->assertNull($array->fetch);
        $this->mapping->acceptProperty($array, $this->metadataFactory);

        $this->assertEquals("lazy", $array->fetch);
    }

    public function testAcceptArray_WithoutTable_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $array = new Zend_Entity_Definition_Array("foo", array(
            "mapkey" => "key",
            "key" => "foo_id",
            "element" => "value"
        ));

        $this->mapping->acceptProperty($array, $this->metadataFactory);
    }

    public function testAcceptArray_WithoutMapKey_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $array = new Zend_Entity_Definition_Array("foo", array(
            "table" => "elements",
            "key" => "foo_id",
            "element" => "value"
        ));

        $this->mapping->acceptProperty($array, $this->metadataFactory);
    }

    public function testAcceptArray_WithoutElement_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $array = new Zend_Entity_Definition_Array("foo", array(
            "table" => "elements",
            "mapKey" => "key",
            "key" => "foo_id",
        ));

        $this->mapping->acceptProperty($array, $this->metadataFactory);
    }

    public function attachDefinitionToMetadataFactory($entityDef)
    {
        $this->metadataFactory->expects($this->any())
                              ->method('getDefinitionByEntityName')
                              ->with($this->equalTo($entityDef->getClass()))
                              ->will($this->returnValue($entityDef));
    }
}

class Zend_Entity_DbMapper_TestStateTransformer extends Zend_Entity_StateTransformer_Array
{
    public $_propertyNames = array();
}
