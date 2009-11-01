<?php

class Zend_Entity_DbMapper_Loader_SingleScalarTest
    extends Zend_Entity_DbMapper_Loader_TestCase
{

    public function getLoaderClassName() {
        return "Zend_Db_Mapper_Loader_SingleScalar";
    }

    public function getFixtureClassName() {
        return "Zend_Entity_Fixture_SimpleFixtureDefs";
    }

    public function testResultHasNotExactlyOneRowThrowsException()
    {
        $this->setExpectedException("Zend_Entity_NonUniqueResultException");

        $loader = $this->createLoader();

        $rows = array( array("foo" => "bar"), array("foo" => "baz") );

        $result = $loader->processResultset($rows, new Zend_Entity_Query_ResultSetMapping());
    }

    public function testResultHasNotExactlyOneRowColumnThrowsException()
    {
        $this->setExpectedException("Zend_Entity_NonUniqueResultException");

        $loader = $this->createLoader();

        $rows = array( array("foo" => "bar", "bar" => "baz") );

        $result = $loader->processResultset($rows, new Zend_Entity_Query_ResultSetMapping());
    }

    public function testResultSingleScalar()
    {
        $loader = $this->createLoader();

        $rows = array( array("foo" => "bar") );

        $result = $loader->processResultset($rows, new Zend_Entity_Query_ResultSetMapping());

        $this->assertEquals("bar", $result);
    }
}