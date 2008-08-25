<?php

require_once 'ZendL/Reflection/Function.php';



/**
 * 
 * @group ZendL_Reflection
 * @group ZendL_Reflection_Function
 */
class ZendL_Reflection_FunctionTest extends PHPUnit_Framework_TestCase
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
        $function = new ZendL_Reflection_Function('array_splice');
        $parameters = $function->getParameters();
        $this->assertEquals(count($parameters), 4);
        $this->assertEquals(get_class(array_shift($parameters)), 'ZendL_Reflection_Parameter');
    }
    
    public function testFunctionDocblockReturn()
    {
        $function = new ZendL_Reflection_Function('zend_reflection_test_sample_function6');
        $this->assertEquals(get_class($function->getDocblock()), 'ZendL_Reflection_Docblock');
    }
    
}

