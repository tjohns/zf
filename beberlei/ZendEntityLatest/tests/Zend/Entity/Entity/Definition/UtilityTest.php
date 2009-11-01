<?php

class Zend_Entity_Definition_UtilityTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Zend_Entity_Definition_Utility::setDefinitionLoader(null);
    }

    public function tearDown()
    {
        Zend_Entity_Definition_Utility::setDefinitionLoader(null);
    }

    public function testSetGetLoader()
    {
        $loader = $this->createPluginLoader();
        Zend_Entity_Definition_Utility::setDefinitionLoader($loader);
        $this->assertEquals($loader, Zend_Entity_Definition_Utility::getDefinitionLoader());
    }

    public function testLoadDefinitionWithUnknownClassNameThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $className = "MyEntity_UnknownClassName";
        $loader = $this->createPluginLoaderThatReturnsClassOnLoad($className);
        Zend_Entity_Definition_Utility::setDefinitionLoader($loader);

        Zend_Entity_Definition_Utility::loadDefinition($className, "foo");
    }

    public function testLoadDefinitionHasNonPropertyInterfaceClassThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $className = "stdClass";
        $loader = $this->createPluginLoaderThatReturnsClassOnLoad($className);
        Zend_Entity_Definition_Utility::setDefinitionLoader($loader);

        Zend_Entity_Definition_Utility::loadDefinition($className, "foo");
    }

    public function createPluginLoader()
    {
        return $this->getMock('Zend_Loader_PluginLoader');
    }

    public function createPluginLoaderThatReturnsClassOnLoad($class)
    {
        $loader = $this->createPluginLoader();
        $loader->expects($this->once())
               ->method('load')
               ->will($this->returnValue($class));
        return $loader;
    }
}