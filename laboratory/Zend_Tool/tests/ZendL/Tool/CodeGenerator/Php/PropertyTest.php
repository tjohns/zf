<?php

require_once 'ZendL/Tool/CodeGenerator/Php/Property.php';

/**
 * 
 * @group ZendL_Tool_CodeGenerator_Php
 */
class ZendL_Tool_CodeGenerator_Php_PropertyTest extends PHPUnit_Framework_TestCase
{
    
    public function testConstructor()
    {
        $codeGenProperty = new ZendL_Tool_CodeGenerator_Php_Property();
        $this->isInstanceOf($codeGenProperty, 'ZendL_Tool_CodeGenerator_Php_Property');
    }
    
    
    
}