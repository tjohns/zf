<?php

require_once dirname(__FILE__)."/../../TestHelper.php";

class Zend_Entity_MetadataFactory_CodeTest extends PHPUnit_Framework_TestCase
{
    const ENTITY_DIRECTORY = 'path/';
    const ENTITY_NAME = 'TestEntity';
    const ENTITY_NAME_WITH_NAMESPACE = 'Test\Entity';
    const ENTITY_PATH = 'path/TestEntity.php';
    const INVALID_ENTITY_NAME = '6%53&';

    public function testInvalidCodePath_ThrowsException()
    {
        $this->setExpectedException(
            "Zend_Entity_InvalidEntityException"
        );

        $metadataFactory = new Zend_Entity_MetadataFactory_Code(dirname(__FILE__)."/_files/invalid");
        $metadataFactory->getDefinitionByEntityName(self::ENTITY_NAME);
    }

    public function testUnknownEntityDefinitionFile_ThrowsException()
    {
        $this->setExpectedException(
            "Zend_Entity_InvalidEntityException",
            "The entity 'UnknownEntity' is unknown."
        );

        $metadataFactory = new Zend_Entity_MetadataFactory_Code(dirname(__FILE__)."/_files/empty");
        $metadataFactory->getDefinitionByEntityName("UnknownEntity");
    }

    public function testFileNotReturningDefinition_ThrowsException()
    {
        $this->setExpectedException(
            "Zend_Entity_InvalidEntityException",
            "Definition file of entity 'NotReturningDef' does not return a entity definition."
        );

        $metadataFactory = new Zend_Entity_MetadataFactory_Code(dirname(__FILE__)."/_files/noreturn/");
        $metadataFactory->getDefinitionByEntityName('NotReturningDef');
    }

    public function testCorrectDefinitionFileShouldReturnDefinitionObject()
    {
        $metadataFactory = new Zend_Entity_MetadataFactory_Code(dirname(__FILE__)."/_files/empty");
        $def = $metadataFactory->getDefinitionByEntityName("EmptyEntityDefinition");
        $this->assertTrue($def instanceof Zend_Entity_Mapper_Definition_Entity);
    }

    public function testMetadataFactoryShouldCacheReturnedDefinitions()
    {
        $metadataFactory = new Zend_Entity_MetadataFactory_Code(dirname(__FILE__)."/_files/empty");
        $def1 = $metadataFactory->getDefinitionByEntityName("EmptyEntityDefinition");
        $def2 = $metadataFactory->getDefinitionByEntityName("EmptyEntityDefinition");
        $this->assertEquals($def1, $def2);
    }

    static public function dataGetEntityDefinitionNames()
    {
        $integrationTests = dirname(__FILE__)."/../IntegrationTest";

        return array(
            array($integrationTests."/Clinic/Definition/", array("Clinic_Bed", "Clinic_Occupancy", "Clinic_Patient", "Clinic_Station")),
            array($integrationTests."/University/Definitions/", array("ZendEntity_Course", "ZendEntity_Professor", "ZendEntity_Student")),
        );
    }

    /**
     * @dataProvider dataGetEntityDefinitionNames
     * @param string $path
     * @param array $containedEntites
     */
    public function testGetEntitiyDefinitionNames($path, $containedEntites)
    {
        $metadataFactory = new Zend_Entity_MetadataFactory_Code($path);
        $entityNames = $metadataFactory->getDefinitionEntityNames();

        $this->assertEquals(count($containedEntites), count($entityNames));
        foreach($entityNames AS $entityName) {
            $this->assertTrue(in_array($entityName, $containedEntites));
        }
    }
}
