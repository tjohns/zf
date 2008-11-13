<?php

require_once 'ZendL/Reflection/File.php';



/**
 * 
 * @group ZendL_Reflection
 * @group ZendL_Reflection_File
 */
class ZendL_Reflection_FileTest extends PHPUnit_Framework_TestCase
{
    
    public function testConstructor()
    {
        $reflectionFile = new ZendL_Reflection_File(__FILE__);
        $this->assertEquals(get_class($reflectionFile), 'ZendL_Reflection_File');
        $this->assertEquals($reflectionFile->getFileName(), __FILE__);
        
        require_once 'Zend/Loader.php';
        $reflectionFile = new ZendL_Reflection_File('Zend/Loader.php');
        $this->assertEquals(get_class($reflectionFile), 'ZendL_Reflection_File');
        
        try {
            $nonExistentFile = 'Non/Existent/File.php';
            $reflectionFile = new ZendL_Reflection_File($nonExistentFile);
            $this->fail('Exception should have been thrown');
        } catch (ZendL_Reflection_Exception $e) {
            $this->assertEquals($e->getMessage(), 'File ' . $nonExistentFile . ' must be required before it can be reflected.');
        }
    }
    
    public function testClassReturns()
    {
        $fileToRequire = dirname(__FILE__) . '/_files/TestSampleClass.php';
        require_once $fileToRequire;
        $reflectionFile = new ZendL_Reflection_File($fileToRequire);
        
        $this->assertEquals(get_class($reflectionFile), 'ZendL_Reflection_File');
        $this->assertEquals(count($reflectionFile->getClasses()), 5);
        $this->assertEquals(get_class($reflectionFile->getClass('ZendL_Reflection_TestSampleClass2')), 'ZendL_Reflection_Class');
        
        try {
            $nonExistentClass = 'Some_Non_Existent_Class';
            $reflectionFile->getClass($nonExistentClass);
            $this->fail('Exception should have been thrown');
        } catch (ZendL_Reflection_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Class by name ' . $nonExistentClass . ' not found.');
        }
        
    }
    
}