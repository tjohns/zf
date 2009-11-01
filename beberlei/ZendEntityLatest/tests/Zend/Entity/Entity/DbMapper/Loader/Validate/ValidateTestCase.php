<?php

abstract class Zend_Entity_DbMapper_Loader_Validate_ValidateTestCase
    extends Zend_Entity_DbMapper_Loader_TestCase
{
    public function getFixtureClassName()
    {
        return "Zend_Entity_Fixture_SimpleFixtureDefs";
    }

    public function testMissingColumnThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Query_InvalidResultSetMappingException");

        $loader = $this->createLoader();

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity("Zend_TestEntity1", "e")
            ->addProperty("e", "a_id", "id")
            ->addProperty("e", "a_property", "property");

        $rows = array(
            array("a_id" => 1),
        );

        $loader->processResultset($rows, $rsm);
    }

    public function testInvalidEntityNameThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Query_InvalidResultSetMappingException");

        $loader = $this->createLoader();

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity("Zend_TestEntity12", "e")
            ->addProperty("e", "a_id", "id")
            ->addProperty("e", "a_property", "property");

        $rows = array(
            array("a_id" => 1, "a_property" => "foo"),
        );

        $loader->processResultset($rows, $rsm);
    }

    public function testInvalidPropertyThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Query_InvalidResultSetMappingException");

        $loader = $this->createLoader();

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity("Zend_TestEntity1", "e")
            ->addProperty("e", "a_id", "id")
            ->addProperty("e", "a_property", "propertyxyz");

        $rows = array(
            array("a_id" => 1, "a_property" => "foo"),
        );

        $loader->processResultset($rows, $rsm);
    }

    public function testMissingScalarValue()
    {
        $this->setExpectedException("Zend_Entity_Query_InvalidResultSetMappingException");
        
        $loader = $this->createLoader();

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity("Zend_TestEntity1", "e")
            ->addProperty("e", "a_id", "id")
            ->addProperty("e", "a_property", "property")
            ->addScalar("foo");

        $rows = array(
            array("a_id" => 1, "a_property" => "foo"),
        );

        $loader->processResultset($rows, $rsm);
    }

    /**
     * @group ZFINC-58
     */
    public function testNullScalarIsNotDetectedAsMissing()
    {
        $loader = $this->createLoader();

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity("Zend_TestEntity1", "e")
            ->addProperty("e", "a_id", "id")
            ->addProperty("e", "a_property", "property")
            ->addScalar("foo");

        $rows = array(
            array("a_id" => 1, "a_property" => "foo", "foo" => null),
        );

        $loader->processResultset($rows, $rsm);
    }

    /**
     * @group ZFINC-58
     */
    public function testNullFieldIsNotDetectedAsMissing()
    {
        $loader = $this->createLoader();

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity("Zend_TestEntity1", "e")
            ->addProperty("e", "a_id", "id")
            ->addProperty("e", "a_property", "property");

        $rows = array(
            array("a_id" => 1, "a_property" => null),
        );

        $result = $loader->processResultset($rows, $rsm);

        $this->assertEquals(1, count($result));
    }
}