<?php

class Zend_Tool_Project_Context_Zf_TestLibraryFile extends Zend_Tool_Project_Context_Filesystem_File
{
    
    protected $_forClassName = '';
    
    public function getName()
    {
        return 'TestLibraryFile';
    }
    
    public function init()
    {
        $this->_forClassName = $this->_resource->getAttribute('forClassName');
        $this->_filesystemName = ucfirst(ltrim(strrchr($this->_forClassName, '_'), '_')) . 'Test.php';
        parent::init();
    }
    
    public function getContents()
    {

        $filter = new Zend_Filter_Word_DashToCamelCase();
        
        $className = $filter->filter($this->_forClassName) . 'Test';
        
        $codeGenFile = new Zend_CodeGenerator_Php_File(array(
            'requiredFiles' => array(
                'PHPUnit/Framework/TestCase.php'
                ),
            'classes' => array(
                new Zend_CodeGenerator_Php_Class(array(
                    'name' => $className,
                    'extendedClass' => 'PHPUnit_Framework_TestCase',
                    'methods' => array(
                        new Zend_CodeGenerator_Php_Method(array(
                            'name' => 'setUp',
                            'body' => '        /* Setup Routine */'
                            )),
                        new Zend_CodeGenerator_Php_Method(array(
                            'name' => 'tearDown',
                            'body' => '        /* Tear Down Routine */'
                            ))
                        )
                    ))
                )
            ));

        return $codeGenFile->generate();
    }
    
}