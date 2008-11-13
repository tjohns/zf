<?php

require_once 'ZendL/Reflection/Method.php';



/**
 * 
 * @group ZendL_Reflection
 * @group ZendL_Reflection_Method
 */
class ZendL_Reflection_MethodTest extends PHPUnit_Framework_TestCase
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
        $method = new ZendL_Reflection_Method('ZendL_Reflection_TestSampleClass2', 'getProp1');
        $this->assertEquals(get_class($method->getDeclaringClass()), 'ZendL_Reflection_Class');
    }
    
    public function testParemeterReturn()
    {
        $method = new ZendL_Reflection_Method('ZendL_Reflection_TestSampleClass2', 'getProp2');
        $parameters = $method->getParameters();
        $this->assertEquals(count($parameters), 2);
        $this->assertEquals(get_class(array_shift($parameters)), 'ZendL_Reflection_Parameter');
    }
    
    public function testStartLine()
    {
        $reflectionMethod = new ZendL_Reflection_Method('ZendL_Reflection_TestSampleClass5', 'doSomething');
        
        $this->assertEquals($reflectionMethod->getStartLine(), 105);
        $this->assertEquals($reflectionMethod->getStartLine(true), 89);
    }
    
}

