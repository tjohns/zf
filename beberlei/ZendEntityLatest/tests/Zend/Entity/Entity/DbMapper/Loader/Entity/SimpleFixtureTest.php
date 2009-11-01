<?php

class Zend_Entity_DbMapper_Loader_Entity_SimpleFixtureTest extends Zend_Entity_DbMapper_Loader_TestCase
{
    public function getLoaderClassName()
    {
        return "Zend_Db_Mapper_Loader_Entity";
    }

    public function getFixtureClassName()
    {
        return "Zend_Entity_Fixture_SimpleFixtureDefs";
    }

    public function getLoader()
    {
        return $this->createLoader();
    }

    public function testCreateEntityFromState_SimpleEntity_StateIsInjected()
    {
        $loader = $this->getLoader();
        $state = $this->fixture->getDummyDataState();

        $entity = $loader->createEntityFromState($state, $this->mappings["Zend_TestEntity1"]);

        $this->assertEquals($state, $entity->getState());
    }

    public function testCreateEntityFromState_ReturnExistingIdentityMapMatches()
    {
        $loader = $this->getLoader();
        $state = $this->fixture->getDummyDataState();

        $entity = new Zend_TestEntity1();

        $this->identityMap->addObject("Zend_TestEntity1", 1, $entity);

        $createdEntity = $loader->createEntityFromState($state, $this->mappings["Zend_TestEntity1"]);

        $this->assertSame($entity, $createdEntity);
    }

    public function testProcessResultsetInEntityMode()
    {
        $loader = $this->getLoader();
        $row = $this->fixture->getDummyDataRow();
        $state = $this->fixture->getDummyDataState();

        $resultSet = array($row);

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity(Zend_Entity_Fixture_SimpleFixtureDefs::TEST_A_CLASS, "a");
        $rsm->addProperty("a", "a_id", "id");
        $rsm->addProperty("a", "a_property", "property");

        $collection = $loader->processResultset($resultSet, $rsm);

        $this->assertType('array', $collection);
        $this->assertEquals(1, count($collection));

        $entity = $collection[0];
        $this->assertEquals($state, $entity->getState());
    }

    public function testProcsesResultset_AnyWeirdColumnsNames_MappedToProperties()
    {
        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity("Zend_TestEntity1", "a")
            ->addProperty("a", "hello", "id")
            ->addProperty("a", "world", "property");

        $rows = array(
            array('hello' => 1, 'world' => 'foo'),
        );

        $loader = $this->getLoader();
        $result = $loader->processResultset($rows, $rsm);

        $this->assertEquals(1, count($rsm));
        $this->assertEquals(1, $result[0]->id);
        $this->assertEquals('foo', $result[0]->property);
    }

    public function testProcessResultset_RootEntitiesOnlyReturnedOnce()
    {
        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity("Zend_TestEntity1", "a")
            ->addProperty("a", "id", "id")
            ->addProperty("a", "property", "property");

        $rows = array(
            array('id' => 1, 'property' => 'foo'),
            array('id' => 1, 'property' => 'foo'),
            array('id' => 1, 'property' => 'foo'),
        );

        $loader = $this->getLoader();
        $result = $loader->processResultset($rows, $rsm);

        $this->assertEquals(1, count($rsm));
    }

    /**
     * @group entityperformance
     */
    public function testPerformance_Create10000Entities_InLessThan2Seconds()
    {
        $this->markTestSkipped();

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity("Zend_TestEntity1", "a")
            ->addProperty("a", "id", "id")
            ->addProperty("a", "property", "property");

        $rows = array();
        for($i = 0; $i < 10000; $i++) {
            $rows[] = array('id' => $i, 'property' => 'foo');
        }

        $loader = $this->getLoader();

        $this->startPerformanceMeasuring();
        
        $result = $loader->processResultset($rows, $rsm);

        $this->assertTookNoLongerThan(2);
        $this->assertEquals(10000, count($result));
    }
}