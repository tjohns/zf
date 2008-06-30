<?php

require_once 'Zend/Tool/CodeGenerator/Php/Method.php';

/**
 * 
 * @group Zend_Tool_CodeGenerator_Php
 */
class Zend_Tool_CodeGenerator_Php_MethodTest extends PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $codeGenMethod = new Zend_Tool_CodeGenerator_Php_Method();
        $this->isInstanceOf($codeGenMethod, 'Zend_Tool_CodeGenerator_Php_Method');
    }
    
    public function testParameterAccessors()
    {
        $codeGen = new Zend_Tool_CodeGenerator_Php_Method();
        $codeGen->setParameters(array(
            'one'
            ));
    }
    
}