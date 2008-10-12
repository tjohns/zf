<?php

require_once 'ZendL/Reflection/Class.php';



/**
 * 
 * @group ZendL_Reflection
 * @group ZendL_Reflection_Class
 */
class ZendL_Reflection_ClassTest extends PHPUnit_Framework_TestCase
{

    static protected $_sampleClassFileRequired = false;
    
    public function setup()
    {
        if (self::$_sampleClassFileRequired === false) {
            $fileToRequire = dirname(__FILE__) . '/_files/TestSampleClass.php';
            require_once $fileToRequire;
            self::$_sampleClassFileRequired = true;
        }
    }
    
    public function testMethodReturns()
    {
        
        $reflectionClass = new ZendL_Reflection_Class('ZendL_Reflection_TestSampleClass2');
        
        $methodByName = $reflectionClass->getMethod('getProp1');
        $this->assertEquals(get_class($methodByName), 'ZendL_Reflection_Method');
        
        $methodsAll = $reflectionClass->getMethods();
        $this->assertEquals(count($methodsAll), 3);
        
        $firstMethod = array_shift($methodsAll);
        $this->assertEquals($firstMethod->getName(), 'getProp1');
    }
    
    public function testPropertyReturns()
    {
        $reflectionClass = new ZendL_Reflection_Class('ZendL_Reflection_TestSampleClass2');
        
        $propertyByName = $reflectionClass->getProperty('_prop1');
        $this->assertEquals(get_class($propertyByName), 'ZendL_Reflection_Property');
        
        $propertiesAll = $reflectionClass->getProperties();
        $this->assertEquals(count($propertiesAll), 2);
        
        $firstProperty = array_shift($propertiesAll);
        $this->assertEquals($firstProperty->getName(), '_prop1');
    }
    
    public function testParentReturn()
    {
        $reflectionClass = new ZendL_Reflection_Class('ZendL_Reflection_TestSampleClass');
        
        $parent = $reflectionClass->getParentClass();
        $this->assertEquals(get_class($parent), 'ZendL_Reflection_Class');
        $this->assertEquals($parent->getName(), 'ArrayObject');
        
    }
    
    public function testInterfaceReturn()
    {
        $reflectionClass = new ZendL_Reflection_Class('ZendL_Reflection_TestSampleClass4');
        
        $interfaces = $reflectionClass->getInterfaces();
        $this->assertEquals(count($interfaces), 1);
        
        $interface = array_shift($interfaces);
        $this->assertEquals($interface->getName(), 'ZendL_Reflection_TestSampleClassInterface');
        
    }
    
    public function testStartLine()
    {
        $reflectionClass = new ZendL_Reflection_Class('ZendL_Reflection_TestSampleClass5');
        
        $this->assertEquals($reflectionClass->getStartLine(), 87);
        $this->assertEquals($reflectionClass->getStartLine(true), 76);
    }
    
}