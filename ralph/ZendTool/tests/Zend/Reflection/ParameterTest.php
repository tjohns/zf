<?php

require_once 'Zend/Reflection/Parameter.php';



/**
 * 
 * @group Zend_Reflection
 * @group Zend_Reflection_Parameter
 */
class Zend_Reflection_ParameterTest extends PHPUnit_Framework_TestCase
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
        $parameter = new Zend_Reflection_Parameter(array('Zend_Reflection_TestSampleClass2', 'getProp2'), 0);
        $this->assertEquals(get_class($parameter->getDeclaringClass()), 'Zend_Reflection_Class');
    }
    
    public function testClassReturn()
    {
        $parameter = new Zend_Reflection_Parameter(array('Zend_Reflection_TestSampleClass2', 'getProp2'), 'param2');
        $this->assertEquals(get_class($parameter->getClass()), 'Zend_Reflection_Class');
    }
    
    public function testTypeReturn()
    {
        $parameter = new Zend_Reflection_Parameter(array('Zend_Reflection_TestSampleClass5', 'doSomething'), 'two');
        $this->assertEquals($parameter->getType(), 'int');
    }
    
}

