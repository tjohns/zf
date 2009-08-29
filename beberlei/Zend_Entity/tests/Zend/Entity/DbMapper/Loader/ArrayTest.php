<?php

class Zend_Entity_Mapper_Loader_ArrayTest extends Zend_Entity_Mapper_Loader_TestCase
{
    public function getLoaderClassName()
    {
        return "Zend_Entity_Mapper_Loader_Array";
    }

    public function getFixtureClassName()
    {
        return "Zend_Entity_Fixture_SimpleFixtureDefs";
    }

    public function getLoader()
    {
        return $this->createLoader($this->fixture->getEntityDefinition(Zend_Entity_Fixture_SimpleFixtureDefs::TEST_A_CLASS));
    }

    public function testProcessResultsetInArrayMode()
    {
        $loader = $this->getLoader();
        $row = $this->fixture->getDummyDataRow();
        $state = $this->fixture->getDummyDataState();

        $resultSet = array($row);

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity(Zend_Entity_Fixture_SimpleFixtureDefs::TEST_A_CLASS, "a");
        $rsm->addProperty("a", "a_id", "id");
        $rsm->addProperty("a", "a_property", "property");

        $array = $loader->processResultset($resultSet, $rsm);

        $this->assertTrue(is_array($array));
        $this->assertEquals(1, count($array));

        $this->assertEquals($state, $array[0]);
    }
}