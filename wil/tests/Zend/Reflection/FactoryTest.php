<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see TestHelper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/** requires */
require_once 'Zend/Reflection/Factory.php';

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @group Zend_Reflection
 * @group Zend_Reflection_Extension
 */
class Zend_Reflection_FactoryTest extends PHPUnit_Framework_TestCase
{
    private $testProperty = null;
    
    public function testCreateClass()
    {
        $factory = new Zend_Reflection_Factory();
        $clazz = $factory->createClass('Zend_Reflection_FactoryTest');
        $this->assertType('Zend_Reflection_Class', $clazz, "Factory returned wrong class.");
        $this->assertEquals('Zend_Reflection_FactoryTest', $clazz->getName(), "Factory returned a class with the wrong name.");
    }

    public function testCreateDocblock()
    {
        $factory = new Zend_Reflection_Factory();
        $function = $factory->createFunction('testFunction');
        $docblock = $factory->createDocblock($function);
        $this->assertType('Zend_Reflection_Docblock', $docblock, "Factory returned wrong class.");
        $this->assertTrue($docblock->hasTag('param'));
        $this->assertEquals(1, sizeof($docblock->getTags()));
    }
    
    public function testCreateExtension()
    {
        $factory = new Zend_Reflection_Factory();
        $extension = $factory->createExtension('standard');
        $this->assertType('Zend_Reflection_Extension', $extension, "Factory returned wrong class.");
        $this->assertEquals('standard', $extension->getName(), "Factory returned an extension with the wrong name.");
    }
    
    public function testCreateFile()
    {
        $factory = new Zend_Reflection_Factory();
        $file = $factory->createFile(__FILE__);
        $this->assertType('Zend_Reflection_File', $file, "Factory returned wrong class.");
        $this->assertEquals(__FILE__, $file->getFileName(), "Factory return file with the wrong name.");
    }
    
    public function testCreateFunction()
    {
        $factory = new Zend_Reflection_Factory();
        $functionName = 'testFunction';
        $function = $factory->createFunction($functionName);
        $this->assertType('Zend_Reflection_Function', $function, "Factory returned wrong class.");
        $this->assertEquals($functionName, $function->getName(), "Factory return function with the wrong name.");
    }
    
    public function testCreateMethod()
    {
        $factory = new Zend_Reflection_Factory();
        $methodName = 'testCreateMethod';
        $method = $factory->createMethod(get_class($this), $methodName);
        $this->assertType('Zend_Reflection_Method', $method, "Factory returned wrong class.");
        $this->assertEquals($methodName, $method->getName(), "Factory return a method with the wrong name.");
    }
    
    public function testCreateParameter()
    {
        $factory = new Zend_Reflection_Factory();
        $parameter = $factory->createParameter('testFunction', 'test');
        $this->assertType('Zend_Reflection_Parameter', $parameter, "Factory returned wrong class.");
        $this->assertEquals('test', $parameter->getName(), "Factory return a parameter with the wrong name.");
    }
    
    public function testCreateProperty()
    {
        $factory = new Zend_Reflection_Factory();
        $property = $factory->createProperty(get_class($this), 'testProperty');
        $this->assertType('Zend_Reflection_Property', $property, "Factory returned wrong class.");
        $this->assertEquals('testProperty', $property->getName(), "Factory return a property with the wrong name.");
    }
}

/**
 * @param test
 */
function testFunction($test) {
}
