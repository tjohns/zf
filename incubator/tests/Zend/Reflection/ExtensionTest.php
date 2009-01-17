<?php

require_once 'Zend/Reflection/Extension.php';


/**
 * 
 * @group Zend_Reflection
 * @group Zend_Reflection_Extension
 */
class Zend_Reflection_ExtensionTest extends PHPUnit_Framework_TestCase
{
    
    public function testClassReturn()
    {
        $extension = new Zend_Reflection_Extension('Reflection');
        $extensionClasses = $extension->getClasses();
        $this->assertEquals(get_class(array_shift($extensionClasses)), 'Zend_Reflection_Class');
    }
    
    public function testFunctionReturn()
    {
        $extension = new Zend_Reflection_Extension('Spl');
        $extensionFunctions = $extension->getFunctions();
        $this->assertEquals(get_class(array_shift($extensionFunctions)), 'Zend_Reflection_Function');
    }
}

