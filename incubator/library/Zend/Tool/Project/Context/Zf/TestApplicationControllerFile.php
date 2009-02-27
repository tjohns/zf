<?php

class Zend_Tool_Project_Context_Zf_TestApplicationControllerFile extends Zend_Tool_Project_Context_Filesystem_File
{
    
    protected $_forControllerName = '';
    
    public function getName()
    {
        return 'TestApplicationControllerFile';
    }
    
    public function init()
    {
        $this->_forControllerName = $this->_resource->getAttribute('forControllerName');
        $this->_filesystemName = ucfirst($this->_forControllerName) . 'ControllerTest.php';
        parent::init();
    }
    
    public function getContents()
    {

        $filter = new Zend_Filter_Word_DashToCamelCase();
        
        $className = $filter->filter($this->_forControllerName) . 'ControllerTest';
        
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
