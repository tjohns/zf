<?php

require_once 'Zend/Reflection/Function.php';



/**
 * 
 * @group Zend_Reflection
 * @group Zend_Reflection_Function
 */
class Zend_Reflection_FunctionTest extends PHPUnit_Framework_TestCase
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
    
    public function testParemeterReturn()
    {
        $function = new Zend_Reflection_Function('array_splice');
        $parameters = $function->getParameters();
        $this->assertEquals(count($parameters), 4);
        $this->assertEquals(get_class(array_shift($parameters)), 'Zend_Reflection_Parameter');
    }
    
    public function testFunctionDocblockReturn()
    {
        $function = new Zend_Reflection_Function('zend_reflection_test_sample_function6');
        $this->assertEquals(get_class($function->getDocblock()), 'Zend_Reflection_Docblock');
    }
    
}

