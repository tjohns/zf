<?php

require_once "SimpleSaveTest.php";
require_once "CollectionTest.php";
require_once "ArrayTest.php";

class Zend_Entity_DbMapper_Persister_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend_Entity Database Mapper Persister Tests');
        $suite->addTestSuite('Zend_Entity_DbMapper_Persister_SimpleSaveTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_Persister_CollectionTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_Persister_ArrayTest');

        return $suite;
    }
}