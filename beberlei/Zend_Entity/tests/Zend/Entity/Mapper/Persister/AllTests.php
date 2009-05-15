<?php

require_once "SimpleSaveTest.php";

class Zend_Entity_Mapper_Persister_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend_Entity_Mapper_Persister Tests');
        $suite->addTestSuite('Zend_Entity_Mapper_Persister_SimpleSaveTest');

        return $suite;
    }
}