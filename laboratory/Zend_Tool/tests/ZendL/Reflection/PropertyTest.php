<?php

require_once 'ZendL/Reflection/Property.php';



/**
 * 
 * @group ZendL_Reflection
 * @group ZendL_Reflection_Property
 */
class ZendL_Reflection_PropertyTest extends PHPUnit_Framework_TestCase
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
        $property = new ZendL_Reflection_Property('ZendL_Reflection_TestSampleClass2', '_prop1');
        $this->assertEquals(get_class($property->getDeclaringClass()), 'ZendL_Reflection_Class');
    }
    
    
}

