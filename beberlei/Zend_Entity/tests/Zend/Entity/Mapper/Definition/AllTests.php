<?php

require_once dirname(__FILE__)."/../../../TestHelper.php";

require_once "FormulaTest.php";
require_once "PropertyTest.php";

class Zend_Entity_Mapper_Definition_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend_Entity_Mapper_Definition Tests');
        $suite->addTestSuite('Zend_Entity_Mapper_Definition_FormulaTest');
        $suite->addTestSuite('Zend_Entity_Mapper_Definition_PropertyTest');

        return $suite;
    }
}