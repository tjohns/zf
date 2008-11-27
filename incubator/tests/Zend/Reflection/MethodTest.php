<?php

require_once 'Zend/Reflection/Method.php';



/**
 * 
 * @group Zend_Reflection
 * @group Zend_Reflection_Method
 */
class Zend_Reflection_MethodTest extends PHPUnit_Framework_TestCase
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
    
    public function testDeclaringClassReturn()
    {
        $method = new Zend_Reflection_Method('Zend_Reflection_TestSampleClass2', 'getProp1');
        $this->assertEquals(get_class($method->getDeclaringClass()), 'Zend_Reflection_Class');
    }
    
    public function testParemeterReturn()
    {
        $method = new Zend_Reflection_Method('Zend_Reflection_TestSampleClass2', 'getProp2');
        $parameters = $method->getParameters();
        $this->assertEquals(count($parameters), 2);
        $this->assertEquals(get_class(array_shift($parameters)), 'Zend_Reflection_Parameter');
    }
    
    public function testStartLine()
    {
        $reflectionMethod = new Zend_Reflection_Method('Zend_Reflection_TestSampleClass5', 'doSomething');
        
        $this->assertEquals($reflectionMethod->getStartLine(), 105);
        $this->assertEquals($reflectionMethod->getStartLine(true), 89);
    }
    
}

