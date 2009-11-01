<?php

require_once "ListenerTest.php";

class Zend_Entity_Event_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Entity Event Tests');
        $suite->addTestSuite('Zend_Entity_Event_ListenerTest');

        return $suite;
    }
}