<?php

class Zend_Entity_Mapper_Loader_ArrayTest extends Zend_Entity_Mapper_Loader_TestCase
{
    public function getLoaderClassName()
    {
        return "Zend_Entity_Mapper_Loader_Array";
    }

    public function setUp()
    {
        $this->fixture = new Zend_Entity_Fixture_SimpleFixtureDefs();
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

        $array = $loader->processResultset($resultSet, $this->createEntityManager(), Zend_Entity_Manager::FETCH_ARRAY);

        $this->assertTrue(is_array($array));
        $this->assertEquals(1, count($array));

        $this->assertEquals($state, $array[0]);
    }
}