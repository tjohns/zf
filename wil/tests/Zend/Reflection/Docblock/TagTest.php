<?php

require_once 'Zend/Reflection/File.php';

/**
 * 
 * @group Zend_Reflection
 * @group Zend_Reflection_Docblock
 * @group Zend_Reflection_Docblock_Tag
 */
class Zend_Reflection_Docblock_TagTest extends PHPUnit_Framework_TestCase
{
    

    static protected $_sampleClassFileRequired = false;
    protected $_factory;
    
    public function setup()
    {
        if (self::$_sampleClassFileRequired === false) {
            $fileToRequire = dirname(dirname(__FILE__)) . '/_files/TestSampleClass.php';
            require_once $fileToRequire;
            self::$_sampleClassFileRequired = true;
        }
        $this->_factory;
    }
    
    public function testTagDescription()
    {
        $classReflection = $factory->createClass('Zend_Reflection_TestSampleClass5');

        $authorTag = $classReflection->getDocblock()->getTag('author');
        $this->assertEquals($authorTag->getDescription(), 'Ralph Schindler <ralph.schindler@zend.com>');
    }
    
}
