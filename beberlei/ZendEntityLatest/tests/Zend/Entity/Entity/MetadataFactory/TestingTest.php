<?php

require_once dirname(__FILE__)."/../../../TestHelper.php";

class Zend_Entity_MetadataFactory_TestingTest extends Zend_Entity_TestCase
{
    public function testDefinitionMapAddIsRetrievable()
    {
        $def = $this->createSampleEntityDefinition();
        $map = new Zend_Entity_MetadataFactory_Testing();
        $map->addDefinition($def);

        $this->assertSame($def, $map->getDefinitionByEntityName("Sample"));
    }

    public function testDefinitionMapThrowsExceptionIfEntityDefNotFound()
    {
        $this->setExpectedException("Zend_Entity_Exception");
        $map = new Zend_Entity_MetadataFactory_Testing();

        $map->getDefinitionByEntityName("UnknownEntity");
    }

    public function testGetMappingByEntityNames()
    {
        $def = $this->createSampleEntityDefinition("EntityName");
        $def->setProxyClass("EntityNameProxyClass");
        $def->setEntityName("en");

        $metadata = new Zend_Entity_MetadataFactory_Testing();
        $metadata->addDefinition($def);
        $metadata->transform('Zend_Db_Mapper_Mapping');

        $this->assertEquals("EntityName", $metadata["EntityName"]->class);
        $this->assertEquals("EntityName", $metadata["EntityNameProxyClass"]->class);
        $this->assertEquals("EntityName", $metadata["en"]->class);
    }

    public function testGetMapping_UnknownEntity_ThrowsException()
    {
        $metadata = new Zend_Entity_MetadataFactory_Testing();

        $this->setExpectedException("Zend_Entity_InvalidEntityException");

        $metadata["Foo"]->class;
    }

    public function testGetEntityName()
    {
        $def = $this->createSampleEntityDefinition("Zend_TestEntity1");
        $def->setProxyClass("Zend_TestEntity1Proxy");
        $def->setEntityName("en");

        $metadata = new Zend_Entity_MetadataFactory_Testing();
        $metadata->addDefinition($def);
        $metadata->transform('Zend_Db_Mapper_Mapping');

        $entityA = new Zend_TestEntity1();
        $entityB = new Zend_TestEntity1Proxy($this->createEntityManager(), 'Zend_TestEntity1', 1);

        $this->assertEquals("Zend_TestEntity1", $metadata->getEntityName($entityA));
        $this->assertEquals("Zend_TestEntity1", $metadata->getEntityName($entityB));
    }

    public function testTransform_PassesOptions_ToTransformer()
    {
        $defA = $this->createSampleEntityDefinition("Zend_TestEntity1");
        $defB = $this->createSampleEntityDefinition("Zend_TestEntity2");

        $metadata = new Zend_Entity_MetadataFactory_Testing();
        $metadata->addDefinition($defA);
        $metadata->addDefinition($defB);
        $metadata->transform('Zend_Entity_MetadataFactory_OptionsVisitor', array("foo" => "bar"));

        $this->assertEquals(array("foo" => "bar"), Zend_Entity_MetadataFactory_OptionsVisitor::$options);
        $this->assertEquals(2, Zend_Entity_MetadataFactory_OptionsVisitor::$called);
    }

    public function testGetEntityName_UnknownClass_ThrowsException()
    {
        $metadata = new Zend_Entity_MetadataFactory_Testing();

        $this->setExpectedException("Zend_Entity_InvalidEntityException");

        $metadata->getEntityName("Foo");
    }

    public function testArrayAccessSet_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $metadata = new Zend_Entity_MetadataFactory_Testing();
        $metadata["foo"] = "bar";
    }

    public function testArrayAccessUnset_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $metadata = new Zend_Entity_MetadataFactory_Testing();
        unset($metadata['foo']);
    }

    public function testDefaultIdGeneratorClassIsNullByDefaultForMapperSpecificConfigurations()
    {
        $metadata = new Zend_Entity_MetadataFactory_Testing();

        $this->assertNull($metadata->getDefaultIdGeneratorClass());
    }

    public function testSetGetDefaultIdGenerator()
    {
        $metadata = new Zend_Entity_MetadataFactory_Testing();

        $metadata->setDefaultIdGeneratorClass("stdClass");
        $this->assertEquals("stdClass", $metadata->getDefaultIdGeneratorClass());
    }

    public function testSetDefaultIdGenerator_ClassNotExists_ThrowsException()
    {
        $metadata = new Zend_Entity_MetadataFactory_Testing();

        $this->setExpectedException("Zend_Entity_Exception");

        $metadata->setDefaultIdGeneratorClass("UnknownFooClass");
    }
}

class Zend_Entity_MetadataFactory_OptionsVisitor implements Zend_Entity_Definition_MappingVisitor
{
    static public $options = array();
    static public $called = 0;
    public $classAlias = array();

    public function __construct($options)
    {
        self::$options = $options;
        self::$called++;
    }

    /**
     * Accept an entity definition
     *
     * @param Zend_Entity_Definition_Entity $entity
     * @param Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory
     */
    public function acceptEntity(Zend_Entity_Definition_Entity $entity, Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory)
    {
        
    }

    /**
     * Accept a property definition
     *
     * @param Zend_Entity_Definition_Property $property
     * @param Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory
     */
    public function acceptProperty(Zend_Entity_Definition_Property $property, Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory)
    {

    }

    /**
     * Helper function that finalizes the visitor process.
     *
     * @return void
     */
    public function finalize()
    {

    }
}