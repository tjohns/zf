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
}