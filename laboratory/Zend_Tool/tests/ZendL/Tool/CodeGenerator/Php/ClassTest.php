<?php

require_once 'ZendL/Tool/CodeGenerator/Php/Class.php';

/**
 * 
 * @group ZendL_Tool_CodeGenerator_Php
 */
class ZendL_Tool_CodeGenerator_Php_ClassTest extends PHPUnit_Framework_TestCase
{
    
    public function testConstruction()
    {
        $class = new ZendL_Tool_CodeGenerator_Php_Class();
        $this->isInstanceOf($class, 'ZendL_Tool_CodeGenerator_Php_Class');
    }
    
    public function testNameAccessors()
    {
        $codeGenClass = new ZendL_Tool_CodeGenerator_Php_Class();
        $codeGenClass->setName('TestClass');
        $this->assertEquals($codeGenClass->getName(), 'TestClass');

    }
    
    public function testClassDocblockAccessors()
    {
        $this->markTestSkipped();
    }
    
    public function testAbstractAccessors()
    {
        $codeGenClass = new ZendL_Tool_CodeGenerator_Php_Class();
        $this->assertFalse($codeGenClass->isAbstract());
        $codeGenClass->setAbstract(true);
        $this->assertTrue($codeGenClass->isAbstract());
    }
    
    public function testExtendedClassAccessors()
    {
        $codeGenClass = new ZendL_Tool_CodeGenerator_Php_Class();
        $codeGenClass->setExtendedClass('ExtendedClass');
        $this->assertEquals($codeGenClass->getExtendedClass(), 'ExtendedClass');
    }
    
    public function testImplementedInterfacesAccessors()
    {
        $codeGenClass = new ZendL_Tool_CodeGenerator_Php_Class();
        $codeGenClass->setImplementedInterfaces(array('Class1', 'Class2'));
        $this->assertEquals($codeGenClass->getImplementedInterfaces(), array('Class1', 'Class2'));
    }
    
    public function testPropertyAccessors()
    {

        $codeGenClass = new ZendL_Tool_CodeGenerator_Php_Class();
        $codeGenClass->setProperties(array(
            array('name' => 'propOne'),
            new ZendL_Tool_CodeGenerator_Php_Property(array('name' => 'propTwo'))
            ));

        $properties = $codeGenClass->getProperties();
        $this->assertEquals(count($properties), 2);
        $this->isInstanceOf(array_pop($properties), 'ZendL_Tool_CodeGenerator_Php_Property');
        
        $property = $codeGenClass->getProperty('propTwo');
        $this->isInstanceOf($property, 'ZendL_Tool_CodeGenerator_Php_Property');
        $this->assertEquals($property->getName(), 'propTwo');
        
        // add a new property
        $codeGenClass->setProperty(array('name' => 'prop3'));
        $this->assertEquals(count($codeGenClass->getProperties()), 3);
        
        try {
            // add a property by a same name
            $codeGenClass->setProperty(array('name' => 'prop3'));
            $this->fail('ZendL_Tool_CodeGenerator_Php_Exception should have been thrown.');
        } catch (Exception $e) {
            $this->isInstanceOf($e, 'ZendL_Tool_CodeGenerator_Php_Exception');
        }
        
        
    }
    
    public function testMethodAccessors()
    {
        $codeGenClass = new ZendL_Tool_CodeGenerator_Php_Class();
        $codeGenClass->setMethods(array(
            array('name' => 'methodOne'),
            new ZendL_Tool_CodeGenerator_Php_Method(array('name' => 'methodTwo'))
            ));

        $methods = $codeGenClass->getMethods();
        $this->assertEquals(count($methods), 2);
        $this->isInstanceOf(array_pop($methods), 'ZendL_Tool_CodeGenerator_Php_Method');
        
        $method = $codeGenClass->getMethod('methodOne');
        $this->isInstanceOf($method, 'ZendL_Tool_CodeGenerator_Php_Method');
        $this->assertEquals($method->getName(), 'methodOne');
        
        // add a new property
        $codeGenClass->setMethod(array('name' => 'methodThree'));
        $this->assertEquals(count($codeGenClass->getMethods()), 3);
        
        try {
            // add a property by a same name
            $codeGenClass->setMethod(array('name' => 'methodThree'));
            $this->fail('ZendL_Tool_CodeGenerator_Php_Exception should have been thrown.');
        } catch (Exception $e) {
            $this->isInstanceOf($e, 'ZendL_Tool_CodeGenerator_Php_Exception');
        }
    }
    
    public function testToString()
    {
        $codeGenClass = new ZendL_Tool_CodeGenerator_Php_Class(array(
            'abstract' => true,
            'name' => 'SampleClass',
            'extendedClass' => 'ExtendedClassName',
            'implementedInterfaces' => array('Iterator', 'Traversable')
            ));
            
        $expectedOutput = <<<EOS
abstract class SampleClass extends ExtendedClassName implements Iterator, Traversable
{


}

EOS;
            
        $this->assertEquals($codeGenClass->toString(), $expectedOutput);
    }

}
