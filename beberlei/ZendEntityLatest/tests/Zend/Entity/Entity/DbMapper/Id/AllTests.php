<?php

require_once "AutoIncrementTest.php";
require_once "SequenceTest.php";

class Zend_Db_Mapper_Id_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend_Entity_Definition_Id Tests');
        $suite->addTestSuite('Zend_Db_Mapper_Id_AutoIncrementTest');
        $suite->addTestSuite('Zend_Db_Mapper_Id_SequenceTest');

        return $suite;
    }
}