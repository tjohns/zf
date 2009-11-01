<?php

require_once dirname(__FILE__)."/../../../TestHelper.php";

require_once "EntityGeneratorTest.php";

class Zend_Entity_Tool_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite("Zend_Entity Tools");

        $suite->addTestSuite('Zend_Entity_Tool_EntityGeneratorTest');

        return $suite;
    }
}
