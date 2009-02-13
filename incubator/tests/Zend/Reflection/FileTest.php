<?php

require_once 'Zend/Reflection/File.php';



/**
 * 
 * @group Zend_Reflection
 * @group Zend_Reflection_File
 */
class Zend_Reflection_FileTest extends PHPUnit_Framework_TestCase
{
    
    public function testConstructor()
    {
        $reflectionFile = new Zend_Reflection_File(__FILE__);
        $this->assertEquals(get_class($reflectionFile), 'Zend_Reflection_File');
        $this->assertEquals($reflectionFile->getFileName(), __FILE__);
        
        require_once 'Zend/Loader.php';
        $reflectionFile = new Zend_Reflection_File('Zend/Loader.php');
        $this->assertEquals(get_class($reflectionFile), 'Zend_Reflection_File');
        
        try {
            $nonExistentFile = 'Non/Existent/File.php';
            $reflectionFile = new Zend_Reflection_File($nonExistentFile);
            $this->fail('Exception should have been thrown');
        } catch (Zend_Reflection_Exception $e) {
            $this->assertEquals($e->getMessage(), 'File ' . $nonExistentFile . ' must be required before it can be reflected.');
        }
    }
    
    public function testClassReturns()
    {
        $fileToRequire = dirname(__FILE__) . '/_files/TestSampleClass.php';
        require_once $fileToRequire;
        $reflectionFile = new Zend_Reflection_File($fileToRequire);
        
        $this->assertEquals(get_class($reflectionFile), 'Zend_Reflection_File');
        $this->assertEquals(count($reflectionFile->getClasses()), 5);
        $this->assertEquals(get_class($reflectionFile->getClass('Zend_Reflection_TestSampleClass2')), 'Zend_Reflection_Class');
        
        try {
            $nonExistentClass = 'Some_Non_Existent_Class';
            $reflectionFile->getClass($nonExistentClass);
            $this->fail('Exception should have been thrown');
        } catch (Zend_Reflection_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Class by name ' . $nonExistentClass . ' not found.');
        }
        
    }
    
}