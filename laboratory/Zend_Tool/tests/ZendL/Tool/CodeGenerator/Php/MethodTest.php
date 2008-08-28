<?php

require_once 'ZendL/Tool/CodeGenerator/Php/Method.php';

/**
 * 
 * @group ZendL_Tool_CodeGenerator_Php
 */
class ZendL_Tool_CodeGenerator_Php_MethodTest extends PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $codeGenMethod = new ZendL_Tool_CodeGenerator_Php_Method();
        $this->isInstanceOf($codeGenMethod, 'ZendL_Tool_CodeGenerator_Php_Method');
    }
    
    public function testParameterAccessors()
    {
        $codeGen = new ZendL_Tool_CodeGenerator_Php_Method();
        $codeGen->setParameters(array(
            'one'
            ));
    }
    
}