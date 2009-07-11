<?php

require_once "ArrayTest.php";

class Zend_Entity_Mapper_StateTransformer_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Entity Mapper StateTransformer AllTests');
        $suite->addTestSuite('Zend_Entity_Mapper_StateTransformer_ArrayTest');

        return $suite;
    }
}