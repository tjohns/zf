<?php

require_once dirname(__FILE__)."/../../../TestHelper.php";

class Zend_Doctrine_Import_SchemaTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $manager = Doctrine_Manager::getInstance();
        $modelLoading = $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Zend_Doctrine_Core::MODEL_LOADING_ZEND);
    }

    public function testBuildTwoModularModels()
    {
        $builder = $this->getMock('Doctrine_Import_Builder', array('setOptions', 'setOption', 'setTargetPath', 'buildRecord'));
        $builder->expects($this->at(0))
                ->method('setOptions')
                ->with($this->isType('array'));
        $builder->expects($this->at(1))
                ->method('setTargetPath')
                ->with($this->equalTo(dirname(__FILE__)."/_files/default"));
        $builder->expects($this->at(2))
                ->method('buildRecord')
                ->with($this->isType('array'));
        $builder->expects($this->at(3))
                ->method('setTargetPath')
                ->with($this->equalTo(dirname(__FILE__)."/_files/addressbook"));
        $builder->expects($this->at(4))
                ->method('buildRecord')
                ->with($this->isType('array'));

        $import = new Zend_Doctrine_Import_SchemaMock();
        $import->setBuilder($builder);

        $import->importSchema(dirname(__FILE__)."/_files/TwoModules.yml", 'yml');
    }

    public function testListenerIsNotified()
    {
        $builder = $this->getMock('Doctrine_Import_Builder');
        $listener = $this->getMock('Zend_Doctrine_Import_ImportListener');
        $listener->expects($this->at(0))
                ->method('notifyRecordBuilt')
                ->with($this->equalTo('Model_User'), $this->equalTo('default'));
        $listener->expects($this->at(1))
                ->method('notifyRecordBuilt')
                ->with($this->equalTo('Addressbook_Model_Contact'), $this->equalTo('addressbook'));
        $listener->expects($this->at(2))
                 ->method('notifyImportCompleted');

        $import = new Zend_Doctrine_Import_SchemaMock();
        $import->setBuilder($builder);
        $import->setListener($listener);

        $import->importSchema(dirname(__FILE__)."/_files/TwoModules.yml", 'yml');
    }

    public function testUnknownFile_ThrowsException()
    {
        $this->setExpectedException('Doctrine_Import_Exception');
        
        $builder = $this->getMock('Doctrine_Import_Builder', array('setOptions', 'setOption', 'setTargetPath', 'buildRecord'));
        $builder->expects($this->never())
                ->method('setOptions');

        $import = new Zend_Doctrine_Import_SchemaMock();
        $import->setBuilder($builder);

        $import->importSchema(dirname(__FILE__)."/_files/UnknownFile.yml", 'yml');
    }

    public function testUnknownModule_ThrowsException()
    {
        $this->setExpectedException('Zend_Doctrine_DoctrineException');

        $builder = $this->getMock('Doctrine_Import_Builder');

        $import = new Zend_Doctrine_Import_SchemaMock();
        $import->setBuilder($builder);

        $import->importSchema(dirname(__FILE__)."/_files/UnknownModule.yml", 'yml');
    }

    public function testInvalidZendModel_ThrowsException()
    {
        $this->setExpectedException('Zend_Doctrine_DoctrineException', 'Found an invalid model class Adressbook_Foo which is not following the required Zend style, i.e Model_ClassName for the default module or ModuleName_Model_ClassName for the models in non-default modules.');

        $builder = $this->getMock('Doctrine_Import_Builder');

        $import = new Zend_Doctrine_Import_SchemaMock();
        $import->setBuilder($builder);

        $import->importSchema(dirname(__FILE__)."/_files/InvalidZendModel.yml", 'yml');
    }

    public function testNonZendModelLoadingStyle()
    {
        $manager = Doctrine_Manager::getInstance();
        $modelLoading = $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, 92941024); // something invalid

        $this->setExpectedException('Zend_Doctrine_DoctrineException', "Can't use Zend_Doctrine_Schema with Doctrine_Core::ATTR_MODEL_LOADING not equal to 4 (Zend).");

        $import = new Zend_Doctrine_Import_SchemaMock();
        $import->importSchema(dirname(__FILE__)."/_files/TwoModules.yml", 'yml');
    }
}

class Zend_Doctrine_Import_SchemaMock extends Zend_Doctrine_Import_Schema
{
    protected function _initModules()
    {
        $this->_defaultModule = "default";
        $this->_modules = array(
            "addressbook" => dirname(__FILE__)."/_files/addressbook",
            "default" => dirname(__FILE__)."/_files/default",
        );
    }
}