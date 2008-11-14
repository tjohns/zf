<?php

require_once 'Zend/Reflection/Property.php';



/**
 * 
 * @group Zend_Reflection
 * @group Zend_Reflection_Property
 */
class Zend_Reflection_PropertyTest extends PHPUnit_Framework_TestCase
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
        $property = new Zend_Reflection_Property('Zend_Reflection_TestSampleClass2', '_prop1');
        $this->assertEquals(get_class($property->getDeclaringClass()), 'Zend_Reflection_Class');
    }
    
    
}

