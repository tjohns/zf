<?php

abstract class Zend_Entity_Compliance_MappingTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Entity_MappingAbstract
     */
    protected $mapping = null;

    /**
     * @var Zend_Entity_MetadataFactory_FactoryAbstract
     */
    protected $metadataFactory = null;

    /**
     * Return the storage engine specific mapping that is tested for compliance.
     *
     * @return Zend_Entity_MappingAbstract
     */
    abstract public function createMapping();

    public function setUp()
    {
        $this->mapping = $this->createMapping();
        $this->metadataFactory = $this->getMock('Zend_Entity_MetadataFactory_FactoryAbstract');
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

    public function testAcceptEntity_AddClassToAliasMap()
    {
        $entity = new Zend_Entity_Definition_Entity("foo");
        $entity->addPrimaryKey("id");
        $this->mapping->acceptEntity($entity, $this->metadataFactory);

        $this->assertArrayHasKey('foo', $this->mapping->classAlias);
    }

    public function testAcceptEntity_AddProxyClassToAliasMap()
    {
        $entity = new Zend_Entity_Definition_Entity("foo");
        $entity->addPrimaryKey("id");
        $this->mapping->acceptEntity($entity, $this->metadataFactory);

        $this->assertArrayHasKey('fooProxy', $this->mapping->classAlias);
    }

    public function testAcceptEntity_AddEntityNameToAliasMap()
    {
        $entity = new Zend_Entity_Definition_Entity("foo");
        $entity->addPrimaryKey("id");
        $entity->setEntityName("bar");
        $this->mapping->acceptEntity($entity, $this->metadataFactory);

        $this->assertArrayHasKey('bar', $this->mapping->classAlias);
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

    public function testFinalize_PassTargetClassDetails_ToStateTransformer()
    {
        $stateTransformer = $this->getMock('Zend_Entity_StateTransformer_Abstract');
        $stateTransformer->expects($this->once())
                         ->method('setTargetClass')
                         ->with($this->equalTo('Foo'), $this->equalTo(array('bar', 'baz')));

        $this->mapping->class = "Foo";
        $this->mapping->stateTransformer = $stateTransformer;
        $this->mapping->newProperties = array('bar' => null, 'baz' => null);
        $this->mapping->finalize();
    }

    public function testAcceptProperty_WithEmptyName_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $property = new Zend_Entity_Definition_Property(array());
        $this->mapping->acceptProperty($property, $this->metadataFactory);
    }

    public function testAcceptProperty_AddToColumnNamePropertyMap()
    {
        $property = new Zend_Entity_Definition_Property("foo", array("columnName" => "bar"));
        $this->mapping->acceptProperty($property, $this->metadataFactory);

        $this->assertEquals(array("bar" => "foo"), $this->mapping->columnNameToProperty);
    }

    public function testAcceptProperty_ColumnNameNull_UsesPropertyNameAsValue()
    {
        $propertyName = "foo";
        $property = new Zend_Entity_Definition_Property($propertyName);
        $this->mapping->acceptProperty($property, $this->metadataFactory);

        $this->assertEquals(array("foo" => "foo"), $this->mapping->columnNameToProperty);
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

        $propertyName = $this->mapping->columnNameToProperty["foo"];
        $this->assertEquals("bar", $this->mapping->newProperties[$propertyName]->getMappedBy());
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
        $relationName = "foo";
        $relation = new Zend_Entity_Definition_OneToOneRelation($relationName, array("columnName" => "foo_id", "class" => "Bar", "inverse" => false));
        $foreignDef = new Zend_Entity_Definition_Entity("Bar");
        $foreignDef->addPrimaryKey("bar");

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->mapping->acceptProperty($relation, $this->metadataFactory);

        $this->assertTrue(isset($this->mapping->columnNameToProperty["foo_id"]));
        $this->assertEquals($this->mapping->columnNameToProperty["foo_id"], $relationName);
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
            "relation" => new Zend_Entity_Definition_OneToManyRelation(array("class" => "Bar", "mappedBy" => "foo")),
            "key" => "bar_id",
            "table" => "foo_bar",
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
            "relation" => new Zend_Entity_Definition_OneToManyRelation(array("class" => "Bar", "mappedBy" => "foo")),
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

    public function testAcceptNoCascadeToOneRelationOmitted()
    {
        $relName = "rel";
        $relation = new Zend_Entity_Definition_OneToOneRelation(
            $relName, array(
                "cascade" => array(),
                "class" => "Zend_TestEntity1"
            )
        );

        $foreignDef = new Zend_Entity_Definition_Entity("Zend_TestEntity1");
        $foreignDef->addPrimaryKey("id");

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->mapping->acceptProperty($relation, $this->metadataFactory);

        $this->assertEquals(array(), $this->mapping->cascade);
    }

    public function testAcceptCascadeToOneRelation()
    {
        $relName = "rel";
        $relation = new Zend_Entity_Definition_OneToOneRelation(
            $relName, array(
                "cascade" => Zend_Entity_Definition_Property::CASCADE_SAVE,
                "class" => "Zend_TestEntity1"
            )
        );

        $foreignDef = new Zend_Entity_Definition_Entity("Zend_TestEntity1");
        $foreignDef->addPrimaryKey("id");

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->mapping->acceptProperty($relation, $this->metadataFactory);

        $this->assertEquals(
            array($relName => array("relation", array(Zend_Entity_Definition_Property::CASCADE_SAVE))),
            $this->mapping->cascade
        );
    }

    public function testAcceptNoCascadeToManyCollectionIsOmitted()
    {
        $colName = "col";
        $colDef = new Zend_Entity_Definition_Collection(
            $colName, array(
                "relation" => new Zend_Entity_Definition_OneToManyRelation(
                    array(
                        "cascade" => array(),
                        "class" => "Zend_TestEntity1",
                        "mappedBy" => "foreign_id",
                    )
                 ),
                 "key" => "owner_id",
                 "table" => "foo_bar",
            )
        );

        $foreignDef = new Zend_Entity_Definition_Entity("Zend_TestEntity1");
        $foreignDef->addProperty("foreign_id");

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->mapping->acceptProperty($colDef, $this->metadataFactory);

        $this->assertEquals(array(), $this->mapping->cascade);
    }

    public function testAcceptCascadeToManyCollection()
    {
        $colName = "col";
        $colDef = new Zend_Entity_Definition_Collection(
            $colName, array(
                "relation" => new Zend_Entity_Definition_OneToManyRelation(
                    array(
                        "cascade" => Zend_Entity_Definition_Property::CASCADE_ALL,
                        "class" => "Zend_TestEntity1",
                        "mappedBy" => "foreign_id",
                    )
                 ),
                 "key" => "owner_id",
                 "table" => "foo_bar",
            )
        );

        $foreignDef = new Zend_Entity_Definition_Entity("Zend_TestEntity1");
        $foreignDef->addPrimaryKey("id");
        $foreignDef->addProperty("foreign_id");

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->mapping->acceptProperty($colDef, $this->metadataFactory);

        $this->assertEquals(
            array($colName => array("collection", array(Zend_Entity_Definition_Property::CASCADE_ALL))),
            $this->mapping->cascade
        );
    }

    public function testAcceptCollectionOwningOneToMany_ButMappedByRelationIsOwning_SetInverse()
    {
        $colName = "col";
        $colDef = new Zend_Entity_Definition_Collection(
            $colName, array(
                "relation" => new Zend_Entity_Definition_OneToManyRelation(
                    array(
                        "class" => "Zend_TestEntity1",
                        "mappedBy" => "foreign_id",
                        "inverse" => false,
                    )
                 ),
                 "key" => "owner_id",
                 "table" => "foo_bar",
            )
        );

        $foreignDef = new Zend_Entity_Definition_Entity("Zend_TestEntity1");
        $foreignDef->addPrimaryKey("id");
        $foreignDef->addManyToOneRelation("foreign_id", array("class" => "Zend_TestEntity2"));

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->mapping->acceptProperty($colDef, $this->metadataFactory);

        $this->assertTrue($colDef->relation->inverse);
    }

    public function testAcceptCollectionOwningRelation_MappedByCollectionIsOwning_ThrowsException()
    {
        $colName = "col";
        $colDef = new Zend_Entity_Definition_Collection(
            $colName, array(
                "relation" => new Zend_Entity_Definition_ManyToManyRelation(
                    array(
                        "class" => "Zend_TestEntity1",
                        "mappedBy" => "foreign_id",
                        "inverse" => false,
                    )
                 ),
                 "key" => "owner_id",
                 "table" => "foo_bar",
            )
        );

        $foreignDef = new Zend_Entity_Definition_Entity("Zend_TestEntity1");
        $foreignDef->addPrimaryKey("id");
        $foreignDef->addCollection("foreign_id", array(
                'relation' => new Zend_Entity_Definition_ManyToManyRelation(
                    array(
                        "class" => "Zend_TestEntity2",
                        "mappedBy" => "relation",
                        "inverse" => false,
                    )
                ),
                 "key" => "owner_id",
                 "table" => "foo_bar",
            )
        );

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->setExpectedException("Zend_Entity_Exception");
        $this->mapping->acceptProperty($colDef, $this->metadataFactory);
    }

    public function testAcceptCollectionUnidirectionalyOneToMany_CollectionTableIsForeignEntityTable_ThrowsException()
    {
        $collissionTableName = "foo_bar";

        $colName = "col";
        $colDef = new Zend_Entity_Definition_Collection(
            $colName, array(
                "relation" => new Zend_Entity_Definition_OneToManyRelation(
                    array(
                        "class" => "Zend_TestEntity1",
                        "inverse" => false,
                    )
                 ),
                 "key" => "owner_id",
                 "table" => $collissionTableName,
            )
        );

        $foreignDef = new Zend_Entity_Definition_Entity("Zend_TestEntity1");
        $foreignDef->setTable($collissionTableName);
        $foreignDef->addPrimaryKey("id");

        $this->attachDefinitionToMetadataFactory($foreignDef);

        $this->setExpectedException("Zend_Entity_Exception");
        $this->mapping->acceptProperty($colDef, $this->metadataFactory);
    }
}