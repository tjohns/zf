<?php

require_once 'ZendL/Reflection/File.php';

/**
 * 
 * @group ZendL_Reflection
 * @group ZendL_Reflection_Docblock
 * @group ZendL_Reflection_Docblock_Tag
 * @group ZendL_Reflection_Docblock_Tag_Param
 */
class ZendL_Reflection_Docblock_Tag_ParamTest extends PHPUnit_Framework_TestCase
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
        $classReflection = new ZendL_Reflection_Class('ZendL_Reflection_TestSampleClass5');

        $paramTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('param');
        $this->assertEquals($paramTag->getType(), 'int');
    }
    
    public function testVariableName()
    {
        $classReflection = new ZendL_Reflection_Class('ZendL_Reflection_TestSampleClass5');

        $paramTag = $classReflection->getMethod('doSomething')->getDocblock()->getTag('param');
        $this->assertEquals($paramTag->getVariableName(), '$one');
    }
    
}
    