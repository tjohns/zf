<?php

require_once dirname(__FILE__)."/../../../TestHelper.php";

require_once "CollectionTest.php";
require_once "EntityTest.php";
require_once "ElementHashMapTest.php";
require_once "GeneratorTest.php";
require_once "GeneratorImplTest.php";

class Zend_Entity_LazyLoad_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend_Entity_LazyLoad Tests');
        $suite->addTestSuite('Zend_Entity_LazyLoad_CollectionTest');
        $suite->addTestSuite('Zend_Entity_LazyLoad_EntityTest');
        $suite->addTestSuite('Zend_Entity_LazyLoad_ArrayTest');
        $suite->addTestSuite('Zend_Entity_LazyLoad_GeneratorTest');
        $suite->addTestSuite('Zend_Entity_LazyLoad_GeneratorImplTest');

        return $suite;
    }
}