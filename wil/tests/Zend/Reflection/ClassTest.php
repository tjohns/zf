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
require_once 'Zend/Reflection/Class.php';

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @group Zend_Reflection
 * @group Zend_Reflection_Class
 */
class Zend_Reflection_ClassTest extends PHPUnit_Framework_TestCase
{

    static protected $_sampleClassFileRequired = false;
    protected $_factory;
    
    public function setup()
    {
        // ensure we are only required this file once per runtime
        if (self::$_sampleClassFileRequired === false) {
            $fileToRequire = dirname(__FILE__) . '/_files/TestSampleClass.php';
            require_once $fileToRequire;
            self::$_sampleClassFileRequired = true;
        }
    }
    
    public function testConstructor() {
        $factory = new Zend_Reflection_Factory();
        $reflection1 = $factory->createClass('Zend_Reflection_TestSampleClass2');
        $this->assertType('Zend_Reflection_Class', $reflection1);

        $reflection2 = new Zend_Reflection_Class('Zend_Reflection_TestSampleClass2');
        $this->assertType('Zend_Reflection_Class', $reflection2);
        
        // Make sure both instantiation methods return the same thing
        $this->assertEquals($reflection1, $reflection2);
    }

    public function testMethodReturns()
    {
        $factory = new Zend_Reflection_Factory();
        $reflectionClass = $factory->createClass('Zend_Reflection_TestSampleClass2');
        
        $methodByName = $reflectionClass->getMethod('getProp1');
        $this->assertEquals(get_class($methodByName), 'Zend_Reflection_Method');
        
        $methodsAll = $reflectionClass->getMethods();
        $this->assertEquals(count($methodsAll), 3);
        
        $firstMethod = array_shift($methodsAll);
        $this->assertEquals($firstMethod->getName(), 'getProp1');
    }
    
    public function testPropertyReturns()
    {
        $factory = new Zend_Reflection_Factory();
        $reflectionClass = $factory->createClass('Zend_Reflection_TestSampleClass2');
        
        $propertyByName = $reflectionClass->getProperty('_prop1');
        $this->assertEquals(get_class($propertyByName), 'Zend_Reflection_Property');
        
        $propertiesAll = $reflectionClass->getProperties();
        $this->assertEquals(count($propertiesAll), 2);
        
        $firstProperty = array_shift($propertiesAll);
        $this->assertEquals($firstProperty->getName(), '_prop1');
    }
    
    public function testParentReturn()
    {
        $factory = new Zend_Reflection_Factory();
        $reflectionClass = $factory->createClass('Zend_Reflection_TestSampleClass');
        
        $parent = $reflectionClass->getParentClass();
        $this->assertEquals(get_class($parent), 'Zend_Reflection_Class');
        $this->assertEquals($parent->getName(), 'ArrayObject');
    }
    
    public function testInterfaceReturn()
    {
        $factory = new Zend_Reflection_Factory();
        $reflectionClass = $factory->createClass('Zend_Reflection_TestSampleClass4');
        
        $interfaces = $reflectionClass->getInterfaces();
        $this->assertEquals(count($interfaces), 1);
        
        $interface = array_shift($interfaces);
        $this->assertEquals($interface->getName(), 'Zend_Reflection_TestSampleClassInterface');
        
    }
    
    public function testGetContentsReturnsContents()
    {
        $factory = new Zend_Reflection_Factory();
        $reflectionClass = $factory->createClass('Zend_Reflection_TestSampleClass2');
        $target = <<<EOS
{
    
    protected \$_prop1 = null;
    protected \$_prop2 = null;
    
    public function getProp1()
    {
        return \$this->_prop1;
    }
    
    public function getProp2(\$param1, Zend_Reflection_TestSampleClass \$param2)
    {
        return \$this->_prop2;
    }
    
    public function getIterator()
    {
        return array();
    }
    
}

EOS;
        $this->assertEquals($target, $reflectionClass->getContents());
    }
    
    public function testStartLine()
    {
        $factory = new Zend_Reflection_Factory();
        $reflectionClass = $factory->createClass('Zend_Reflection_TestSampleClass5');
        
        $this->assertEquals($reflectionClass->getStartLine(), 87);
        $this->assertEquals($reflectionClass->getStartLine(true), 76);
    }
    

    public function testGetDeclaringFileReturnsFilename()
    {
        $factory = new Zend_Reflection_Factory();
        $reflectionClass = $factory->createClass('Zend_Reflection_TestSampleClass2');
        $this->assertContains('TestSampleClass.php', $reflectionClass->getDeclaringFile()->getFileName()); //ns(, $reflectionClass->getDeclaringFile());
    }
    
}