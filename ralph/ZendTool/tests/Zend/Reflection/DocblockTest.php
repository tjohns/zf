<?php

require_once 'Zend/Reflection/File.php';

/**
 * 
 * @group Zend_Reflection
 * @group Zend_Reflection_Docblock
 */
class Zend_Reflection_DocblockTest extends PHPUnit_Framework_TestCase
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
    
    public function testDocblockShortDescription()
    {
        $classReflection = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass5');
        $this->assertEquals($classReflection->getDocblock()->getShortDescription(), 'TestSampleClass5 Docblock Short Desc');
    }
    
    public function testDocblockLongDescription()
    {
        $classReflection = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass5');
        $expectedOutput =<<<EOS
This is a long description for 
the docblock of this class, it
should be longer than 3 lines.
It indeed is longer than 3 lines
now.
EOS;

        $this->assertEquals($classReflection->getDocblock()->getLongDescription(), $expectedOutput);

    }
    
    public function testDocblockTags()
    {
        $classReflection = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass5');
        
        $this->assertEquals($classReflection->getMethod('doSomething')->getDocblock()->hasTag('return'), true);
        
        $returnTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('return');
        $this->assertEquals(get_class($returnTag), 'Zend_Reflection_Docblock_Tag_Return');
        $this->assertEquals($returnTag->getType(), 'mixed');
    }
    
    
}