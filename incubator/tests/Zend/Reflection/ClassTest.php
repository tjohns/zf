<?php

require_once 'Zend/Reflection/Class.php';



/**
 * 
 * @group Zend_Reflection
 * @group Zend_Reflection_Class
 */
class Zend_Reflection_ClassTest extends PHPUnit_Framework_TestCase
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
        
        $reflectionClass = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass2');
        
        $methodByName = $reflectionClass->getMethod('getProp1');
        $this->assertEquals(get_class($methodByName), 'Zend_Reflection_Method');
        
        $methodsAll = $reflectionClass->getMethods();
        $this->assertEquals(count($methodsAll), 3);
        
        $firstMethod = array_shift($methodsAll);
        $this->assertEquals($firstMethod->getName(), 'getProp1');
    }
    
    public function testPropertyReturns()
    {
        $reflectionClass = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass2');
        
        $propertyByName = $reflectionClass->getProperty('_prop1');
        $this->assertEquals(get_class($propertyByName), 'Zend_Reflection_Property');
        
        $propertiesAll = $reflectionClass->getProperties();
        $this->assertEquals(count($propertiesAll), 2);
        
        $firstProperty = array_shift($propertiesAll);
        $this->assertEquals($firstProperty->getName(), '_prop1');
    }
    
    public function testParentReturn()
    {
        $reflectionClass = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass');
        
        $parent = $reflectionClass->getParentClass();
        $this->assertEquals(get_class($parent), 'Zend_Reflection_Class');
        $this->assertEquals($parent->getName(), 'ArrayObject');
        
    }
    
    public function testInterfaceReturn()
    {
        $reflectionClass = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass4');
        
        $interfaces = $reflectionClass->getInterfaces();
        $this->assertEquals(count($interfaces), 1);
        
        $interface = array_shift($interfaces);
        $this->assertEquals($interface->getName(), 'Zend_Reflection_TestSampleClassInterface');
        
    }
    
    public function testStartLine()
    {
        $reflectionClass = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass5');
        
        $this->assertEquals($reflectionClass->getStartLine(), 87);
        $this->assertEquals($reflectionClass->getStartLine(true), 76);
    }
    
}