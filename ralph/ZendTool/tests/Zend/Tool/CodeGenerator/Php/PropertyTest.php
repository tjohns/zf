<?php

require_once 'Zend/Tool/CodeGenerator/Php/Property.php';

/**
 * 
 * @group Zend_Tool_CodeGenerator_Php
 */
class Zend_Tool_CodeGenerator_Php_PropertyTest extends PHPUnit_Framework_TestCase
{
    
    public function testConstructor()
    {
        $codeGenProperty = new Zend_Tool_CodeGenerator_Php_Property();
        $this->isInstanceOf($codeGenProperty, 'Zend_Tool_CodeGenerator_Php_Property');
    }
    
    
    
}