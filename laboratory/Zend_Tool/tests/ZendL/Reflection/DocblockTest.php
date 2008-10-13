<?php

require_once 'ZendL/Reflection/File.php';

/**
 * 
 * @group ZendL_Reflection
 * @group ZendL_Reflection_Docblock
 */
class ZendL_Reflection_DocblockTest extends PHPUnit_Framework_TestCase
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
        $classReflection = new ZendL_Reflection_Class('ZendL_Reflection_TestSampleClass5');
        $this->assertEquals($classReflection->getDocblock()->getShortDescription(), 'TestSampleClass5 Docblock Short Desc');
    }
    
    public function testDocblockLongDescription()
    {
        $classReflection = new ZendL_Reflection_Class('ZendL_Reflection_TestSampleClass5');
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
        $classReflection = new ZendL_Reflection_Class('ZendL_Reflection_TestSampleClass5');
        
        $this->assertEquals(count($classReflection->getDocblock()->getTags()), 1);
        $this->assertEquals(count($classReflection->getDocblock()->getTags('author')), 1);
        
        $this->assertEquals($classReflection->getDocblock()->getTag('version'), false);
        
        $this->assertEquals($classReflection->getMethod('doSomething')->getDocblock()->hasTag('return'), true);
        
        $returnTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('return');
        $this->assertEquals(get_class($returnTag), 'ZendL_Reflection_Docblock_Tag_Return');
        $this->assertEquals($returnTag->getType(), 'mixed');
    }
    
    public function testDocblockLines()
    {
        $classReflection = new ZendL_Reflection_Class('ZendL_Reflection_TestSampleClass5');
        
        $classDocblock = $classReflection->getDocblock();
        
        $this->assertEquals($classDocblock->getStartLine(), 76);
        $this->assertEquals($classDocblock->getEndLine(), 86);
        
    }
    
    public function testDocblockContents()
    {
        $classReflection = new ZendL_Reflection_Class('ZendL_Reflection_TestSampleClass5');
        
        $classDocblock = $classReflection->getDocblock();
        
        $expectedContents = <<<EOS
TestSampleClass5 Docblock Short Desc

This is a long description for 
the docblock of this class, it
should be longer than 3 lines.
It indeed is longer than 3 lines
now.

@author Ralph Schindler <ralph.schindler@zend.com>

EOS;
        
        $this->assertEquals($classDocblock->getContents(), $expectedContents);
        
    }
    
}