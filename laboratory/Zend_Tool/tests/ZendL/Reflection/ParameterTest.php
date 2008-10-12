<?php

require_once 'ZendL/Reflection/Parameter.php';



/**
 * 
 * @group ZendL_Reflection
 * @group ZendL_Reflection_Parameter
 */
class ZendL_Reflection_ParameterTest extends PHPUnit_Framework_TestCase
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
        $parameter = new ZendL_Reflection_Parameter(array('ZendL_Reflection_TestSampleClass2', 'getProp2'), 0);
        $this->assertEquals(get_class($parameter->getDeclaringClass()), 'ZendL_Reflection_Class');
    }
    
    public function testClassReturn()
    {
        $parameter = new ZendL_Reflection_Parameter(array('ZendL_Reflection_TestSampleClass2', 'getProp2'), 'param2');
        $this->assertEquals(get_class($parameter->getClass()), 'ZendL_Reflection_Class');
    }
    
    public function testTypeReturn()
    {
        $parameter = new ZendL_Reflection_Parameter(array('ZendL_Reflection_TestSampleClass5', 'doSomething'), 'two');
        $this->assertEquals($parameter->getType(), 'int');
    }
    
}

