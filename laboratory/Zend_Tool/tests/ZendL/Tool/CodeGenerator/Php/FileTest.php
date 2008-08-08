<?php

require_once 'ZendL/Tool/CodeGenerator/Php/File.php';
require_once 'ZendL/Reflection/File.php';

/**
 * 
 * @group ZendL_Tool_CodeGenerator_Php
 * @group ZendL_Tool_CodeGenerator_Php_File
 */
class ZendL_Tool_CodeGenerator_Php_FileTest extends PHPUnit_Framework_TestCase
{
    
    public function testConstruction()
    {
        $file = new ZendL_Tool_CodeGenerator_Php_File();
        $this->assertEquals(get_class($file), 'ZendL_Tool_CodeGenerator_Php_File');
    }
    
    public function testToString()
    {
        $codeGenFile = new ZendL_Tool_CodeGenerator_Php_File(array(
            'requiredFiles' => array('SampleClass.php'),
            'class' => array(
                'abstract' => true,
                'name' => 'SampleClass',
                'extendedClass' => 'ExtendedClassName',
                'implementedInterfaces' => array('Iterator', 'Traversable')            
                )
            ));

            
        $expectedOutput = <<<EOS
<?php

require_once 'SampleClass.php';

abstract class SampleClass extends ExtendedClassName implements Iterator, Traversable
{


}



EOS;

        $this->assertEquals($codeGenFile->generate(), $expectedOutput);
    }
    
    public function testFromReflection()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'UnitFile');
        
        $codeGenFile = new ZendL_Tool_CodeGenerator_Php_File(array(
            'class' => array(
                'name' => 'SampleClass'            
                )
            ));
        
        file_put_contents($tempFile, $codeGenFile->generate());

        require_once $tempFile;

        $codeGenFileFromDisk = ZendL_Tool_CodeGenerator_Php_File::fromReflection(new ZendL_Reflection_File($tempFile));
        
        unlink($tempFile);
        
        $this->assertEquals(get_class($codeGenFileFromDisk), 'ZendL_Tool_CodeGenerator_Php_File');
        $this->assertEquals(count($codeGenFileFromDisk->getClasses()), 1);
        
    }
    
    public function testFromReflectionFile()
    {
        $file = dirname(__FILE__) . '/_files/TestSampleSingleClass.php';
        
        require_once $file;
        $codeGenFileFromDisk = ZendL_Tool_CodeGenerator_Php_File::fromReflection(new ZendL_Reflection_File($file));
        
        echo $codeGenFileFromDisk->generate();
        
        var_dump($codeGenFileFromDisk->isSourceDirty());
        die();
    }

}
