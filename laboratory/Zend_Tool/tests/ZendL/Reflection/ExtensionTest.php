<?php

require_once 'ZendL/Reflection/Extension.php';


/**
 * 
 * @group ZendL_Reflection
 * @group ZendL_Reflection_Extension
 */
class ZendL_Reflection_ExtensionTest extends PHPUnit_Framework_TestCase
{
    
    public function testClassReturn()
    {
        $extension = new ZendL_Reflection_Extension('Reflection');
        $extensionClasses = $extension->getClasses();
        $this->assertEquals(get_class(array_shift($extensionClasses)), 'ZendL_Reflection_Class');
    }
    
    public function testFunctionReturn()
    {
        $extension = new ZendL_Reflection_Extension('Spl');
        $extensionFunctions = $extension->getFunctions();
        $this->assertEquals(get_class(array_shift($extensionFunctions)), 'ZendL_Reflection_Function');
    }
}

