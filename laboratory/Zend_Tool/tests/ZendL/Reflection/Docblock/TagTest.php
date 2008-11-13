<?php

require_once 'ZendL/Reflection/File.php';

/**
 * 
 * @group ZendL_Reflection
 * @group ZendL_Reflection_Docblock
 * @group ZendL_Reflection_Docblock_Tag
 */
class ZendL_Reflection_Docblock_TagTest extends PHPUnit_Framework_TestCase
{
    

    static protected $_sampleClassFileRequired = false;
    
    public function setup()
    {
        if (self::$_sampleClassFileRequired === false) {
            $fileToRequire = dirname(dirname(__FILE__)) . '/_files/TestSampleClass.php';
            require_once $fileToRequire;
            self::$_sampleClassFileRequired = true;
        }
    }
    
    public function testTagDescription()
    {
        $classReflection = new ZendL_Reflection_Class('ZendL_Reflection_TestSampleClass5');

        $authorTag = $classReflection->getDocblock()->getTag('author');
        $this->assertEquals($authorTag->getDescription(), 'Ralph Schindler <ralph.schindler@zend.com>');
    }
    
}
