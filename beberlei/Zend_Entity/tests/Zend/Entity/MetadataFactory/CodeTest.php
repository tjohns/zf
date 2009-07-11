<?php

require_once dirname(__FILE__)."/../../TestHelper.php";

class Zend_Entity_MetadataFactory_CodeTest extends PHPUnit_Framework_TestCase
{
    const ENTITY_DIRECTORY = 'path/';
    const ENTITY_NAME = 'TestEntity';
    const ENTITY_NAME_WITH_NAMESPACE = 'Test\Entity';
    const ENTITY_PATH = 'path/TestEntity.php';
    const INVALID_ENTITY_NAME = '6%53&';

    public function testUnknownEntityDefinitionFile_ThrowsException()
    {
        $this->setExpectedException(
            "Zend_Entity_Exception",
            "Definition file 'invalidpath/UnknownEntity.php' for entity 'UnknownEntity' does not exist!"
        );

        $metadataFactory = new Zend_Entity_MetadataFactory_Code("invalidpath");
        $metadataFactory->getDefinitionByEntityName("UnknownEntity");
    }

    public function testFileNotReturningDefinition_ThrowsException()
    {
        $entity  = "mapper".md5(time());

        $this->setExpectedException(
            "Zend_Entity_Exception",
            "Definition file of entity ".$entity." does not return a entity definition."
        );

        $tempnam = $entity.".php";
        touch(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $tempnam);
        $metadataFactory = new Zend_Entity_MetadataFactory_Code(sys_get_temp_dir());
        $metadataFactory->getDefinitionByEntityName($entity);
    }

    public function testCorrectDefinitionFileShouldReturnDefinitionObject()
    {
        $metadataFactory = new Zend_Entity_MetadataFactory_Code(dirname(__FILE__)."/../Fixture/");
        $def = $metadataFactory->getDefinitionByEntityName("EmptyEntityDefinition");
        $this->assertTrue($def instanceof Zend_Entity_Mapper_Definition_Entity);
    }

    public function testMetadataFactoryShouldCacheReturnedDefinitions()
    {
        $metadataFactory = new Zend_Entity_MetadataFactory_Code(dirname(__FILE__)."/../Fixture/");
        $def1 = $metadataFactory->getDefinitionByEntityName("EmptyEntityDefinition");
        $def2 = $metadataFactory->getDefinitionByEntityName("EmptyEntityDefinition");
        $this->assertEquals($def1, $def2);
    }

    public function testMetadataFactoryShouldIssueCompileLoadedDefinition()
    {
        $defMock = $this->getEntityDefinitionMock();
        $metadataMock = $this->getDefinitionMapMock($defMock);

        $definition = $metadataMock->getDefinitionByEntityName(self::ENTITY_NAME);
        $this->assertEquals($defMock, $definition);
    }

    public function testInvalidEntityNameThrowsException()
    {
        $this->setExpectedException(
            "Zend_Entity_Exception",
            "Trying to load invalid entity name '".self::INVALID_ENTITY_NAME."'. Only ".Zend_Entity_MetadataFactory_Code::INVALID_ENTITY_NAME_PATTERN." are allowed."
        );

        $metadataFactory = new Zend_Entity_MetadataFactory_Code(self::ENTITY_PATH);
        $metadataFactory->getDefinitionByEntityName(self::INVALID_ENTITY_NAME);
    }

    public function testNamespaceSeparatorIsValidEntityNameChar()
    {
        $defMock = $this->getEntityDefinitionMock(self::ENTITY_NAME_WITH_NAMESPACE);
        $metadataFactory = $this->getDefinitionMapMock($defMock, self::ENTITY_NAME_WITH_NAMESPACE);
        $metadataFactory->getDefinitionByEntityName(self::ENTITY_NAME_WITH_NAMESPACE);

        $this->assertEquals(self::ENTITY_NAME_WITH_NAMESPACE, $defMock->getClass());
    }


    protected function getEntityDefinitionMock($entityName=self::ENTITY_NAME)
    {
        $defMock = $this->getMock('Zend_Entity_Mapper_Definition_Entity', array(), array($entityName));
        $defMock->expects($this->once())
                ->method('compile');
        $defMock->expects($this->any())
                ->method('getClass')
                ->will($this->returnValue($entityName));
        return $defMock;
    }

    protected function getDefinitionMapMock($defMock=null, $entityName=self::ENTITY_NAME, $directory=self::ENTITY_DIRECTORY)
    {
        if($defMock == null) {
            $defMock = $this->getEntityDefinitionMock($entityName);
        }

        $ds = DIRECTORY_SEPARATOR;
        $path = str_replace($ds.$ds, $ds, $directory."/".$entityName.".php");

        $metadataMock = $this->getMock('Zend_Entity_MetadataFactory_Code', array('loadDefinitionFile', 'assertPathExists'), array($directory));
        $metadataMock->expects($this->once())
                ->method('loadDefinitionFile')
                ->with($path, $entityName)
                ->will($this->returnValue($defMock));
        return $metadataMock;
    }
}
