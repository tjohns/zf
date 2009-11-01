<?php

class Zend_Entity_DbMapper_Loader_RefreshTest extends Zend_Entity_DbMapper_Loader_TestCase
{
    public function getLoaderClassName()
    {
        return "Zend_Db_Mapper_Loader_Refresh";
    }

    public function getFixtureClassName()
    {
        return "Zend_Entity_Fixture_SimpleFixtureDefs";
    }

    public function createRsm()
    {
        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity('Zend_TestEntity1', 'a')
            ->addProperty('a', 'a_id', 'id')
            ->addProperty('a', 'a_property', 'property');

        return $rsm;
    }

    public function testRefreshManagedEntity()
    {
        $loader = $this->createLoader();

        $entityA = new Zend_TestEntity1();
        $this->identityMap->addObject('Zend_TestEntity1', 1, $entityA);

        $rsm = $this->createRsm();

        $rows = array(
            array('a_id' => '1', 'a_property' => 'Foo'),
        );

        $loader->processResultset($rows, $rsm);

        $this->assertEquals(1, $entityA->id);
        $this->assertEquals("Foo", $entityA->property);
    }

    public function testRefreshMultipleManagedEntities()
    {
        $loader = $this->createLoader();

        $entityA = new Zend_TestEntity1();
        $this->identityMap->addObject('Zend_TestEntity1', 1, $entityA);
        $entityB = new Zend_TestEntity1();
        $this->identityMap->addObject('Zend_TestEntity1', 2, $entityB);

        $rsm = $this->createRsm();

        $rows = array(
            array('a_id' => '1', 'a_property' => 'Foo'),
            array('a_id' => '2', 'a_property' => 'Bar'),
        );

        $loader->processResultset($rows, $rsm);

        $this->assertEquals(1, $entityA->id);
        $this->assertEquals("Foo", $entityA->property);
        $this->assertEquals(2, $entityB->id);
        $this->assertEquals("Bar", $entityB->property);
    }

    public function testNoRootEntityGiven_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Query_InvalidResultSetMappingException");

        $loader = $this->createLoader();

        $loader->processResultset(array(), new Zend_Entity_Query_ResultSetMapping());
    }

    public function testSeveralRootEntitiesGiven_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Query_InvalidResultSetMappingException");

        $loader = $this->createLoader();

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity("Foo")->addEntity("Bar");

        $loader->processResultset(array(), $rsm);
    }
}