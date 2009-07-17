<?php

require_once "ArrayTest.php";
require_once "XmlSerializerTest.php";

class Zend_Entity_StateTransformer_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Entity Mapper StateTransformer AllTests');
        $suite->addTestSuite('Zend_Entity_StateTransformer_ArrayTest');
        $suite->addTestSuite('Zend_Entity_StateTransformer_XmlSerializerTest');

        return $suite;
    }
}