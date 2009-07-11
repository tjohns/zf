<?php

require_once dirname(__FILE__)."/../../TestHelper.php";

class Zend_Entity_MetadataFactory_CodeTest extends PHPUnit_Framework_TestCase
{
    const ENTITY_DIRECTORY = 'path/';
    const ENTITY_NAME = 'TestEntity';
    const ENTITY_NAME_WITH_NAMESPACE = 'Test\Entity';
    const ENTITY_PATH = 'path/TestEntity.php';
    const INVALID_ENTITY_NAME = '6%53&';

    public function testUnknownEntityDefinitionFileShouldThrowException()
    {
        $this->setExpectedException(
            "Zend_Entity_Exception",
            "Definition file 'invalidpath/UnknownEntity.php' for entity 'UnknownEntity' does not exist!"
        );

        $map = new Zend_Entity_MetadataFactory_Code("invalidpath");
        $map->getDefinitionByEntityName("UnknownEntity");
    }

    public function testFileNotReturningDefinitionShouldThrowException()
    {
        $entity  = "mapper".md5(time());

        $this->setExpectedException(
            "Zend_Entity_Exception",
            "Definition file of entity ".$entity." does not return a entity definition."
        );

        $tempnam = $entity.".php";
        touch(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $tempnam);
        $map = new Zend_Entity_MetadataFactory_Code(sys_get_temp_dir());
        $map->getDefinitionByEntityName($entity);
    }

    public function testCorrectDefinitionFileShouldReturnDefinitionObject()
    {
        $map = new Zend_Entity_MetadataFactory_Code(dirname(__FILE__)."/../Fixture/");
        $def = $map->getDefinitionByEntityName("EmptyEntityDefinition");
        $this->assertTrue($def instanceof Zend_Entity_Mapper_Definition_Entity);
    }

    public function testResourceMapShouldCacheReturnedDefinitions()
    {
        $map = new Zend_Entity_MetadataFactory_Code(dirname(__FILE__)."/../Fixture/");
        $def1 = $map->getDefinitionByEntityName("EmptyEntityDefinition");
        $def2 = $map->getDefinitionByEntityName("EmptyEntityDefinition");
        $this->assertEquals($def1, $def2);
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

        $mapMock = $this->getMock('Zend_Entity_MetadataFactory_Code', array('loadDefinitionFile', 'assertPathExists'), array($directory));
        $mapMock->expects($this->once())
                ->method('loadDefinitionFile')
                ->with($path, $entityName)
                ->will($this->returnValue($defMock));
        return $mapMock;
    }

    public function testResourceMapShouldIssueCompileLoadedDefinition()
    {
        $defMock = $this->getEntityDefinitionMock();
        $mapMock = $this->getDefinitionMapMock($defMock);

        $definition = $mapMock->getDefinitionByEntityName(self::ENTITY_NAME);
        $this->assertEquals($defMock, $definition);
    }

    public function testInvalidEntityNameThrowsException()
    {
        $this->setExpectedException(
            "Zend_Entity_Exception",
            "Trying to load invalid entity name '".self::INVALID_ENTITY_NAME."'. Only ".Zend_Entity_MetadataFactory_Code::INVALID_ENTITY_NAME_PATTERN." are allowed."
        );

        $map = new Zend_Entity_MetadataFactory_Code(self::ENTITY_PATH);
        $map->getDefinitionByEntityName(self::INVALID_ENTITY_NAME);
    }

    public function testNamespaceSeparatorIsValidEntityNameChar()
    {
        $defMock = $this->getEntityDefinitionMock(self::ENTITY_NAME_WITH_NAMESPACE);
        $map = $this->getDefinitionMapMock($defMock, self::ENTITY_NAME_WITH_NAMESPACE);
        $map->getDefinitionByEntityName(self::ENTITY_NAME_WITH_NAMESPACE);

        $this->assertEquals(self::ENTITY_NAME_WITH_NAMESPACE, $defMock->getClass());
    }
}