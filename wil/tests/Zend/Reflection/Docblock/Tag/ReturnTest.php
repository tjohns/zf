<?php

require_once 'Zend/Reflection/File.php';

require_once 'Zend/Reflection/Factory.php';

/**
 * 
 * @group Zend_Reflection
 * @group Zend_Reflection_Docblock
 * @group Zend_Reflection_Docblock_Tag
 * @group Zend_Reflection_Docblock_Tag_Return
 */
class Zend_Reflection_Docblock_Tag_ReturnTest extends PHPUnit_Framework_TestCase
{
    
    static protected $_sampleClassFileRequired = false;
    
    public function setup()
    {
        if (self::$_sampleClassFileRequired === false) {
            $fileToRequire = dirname(dirname(dirname(__FILE__))) . '/_files/TestSampleClass.php';
            require_once $fileToRequire;
            self::$_sampleClassFileRequired = true;
        }
    }
    
    public function testType()
    {
        $factory = new Zend_Reflection_Factory();
        $classReflection = $factory->createClass('Zend_Reflection_TestSampleClass5');

        $paramTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('return');
        $this->assertEquals($paramTag->getType(), 'mixed');
    }
    
}
    