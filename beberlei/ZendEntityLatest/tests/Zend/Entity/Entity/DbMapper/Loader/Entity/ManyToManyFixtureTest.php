<?php

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';
require_once 'Zend/Entity/Fixture/Entities.php';

class Zend_Entity_DbMapper_Loader_Entity_ManyToManyFixtureTest extends Zend_Entity_DbMapper_Loader_TestCase
{
    private $_entity;

    public function getLoaderClassName()
    {
        return "Zend_Db_Mapper_Loader_Entity";
    }

    public function getFixtureClassName()
    {
        return "Zend_Entity_Fixture_ManyToManyDefs";
    }

    /**
     * @return array
     */
    public function loadEntityAAndGetState()
    {
        $loader = $this->createLoader();
        
        $this->_entity = new Zend_TestEntity1;
        $state = array(Zend_Entity_Fixture_ManyToManyDefs::TEST_A_ID => 1);
        
        $loader->loadState($this->_entity, $state, $this->mappings["Zend_TestEntity1"]);
        $entityState = $this->_entity->getState();

        return $entityState;
    }

    public function testLoadRowEntityWithCollection()
    {
        $entityState = $this->loadEntityAAndGetState();

        $this->assertTrue(isset($entityState[Zend_Entity_Fixture_ManyToManyDefs::TEST_A_ID]));
        $this->assertTrue(isset($entityState[Zend_Entity_Fixture_ManyToManyDefs::TEST_A_MANYTOMANY]));
        $this->assertLazyLoad($entityState[Zend_Entity_Fixture_ManyToManyDefs::TEST_A_MANYTOMANY]);
    }

    public function testStoresLoadedCollectionInTheIdentityMap()
    {
        $this->loadEntityAAndGetState();

        $collection = $this->identityMap->getRelatedObject($this->_entity, Zend_Entity_Fixture_ManyToManyDefs::TEST_A_MANYTOMANY);
        $this->assertTrue($collection instanceof Zend_Entity_Collection_Interface);
    }

    public function testLoadRowLazyLoadCollectionHasCallbackWithSelectStatement()
    {
        $entityState = $this->loadEntityAAndGetState();

        $callback = $this->readAttribute($entityState[Zend_Entity_Fixture_ManyToManyDefs::TEST_A_MANYTOMANY], '_callback');
        $this->assertType('Zend_Entity_Query_QueryAbstract', $callback[0]);
        $this->assertEquals("getResultList", $callback[1]);

        $callbackArgs = $this->readAttribute($entityState[Zend_Entity_Fixture_ManyToManyDefs::TEST_A_MANYTOMANY], '_callbackArguments');
        $this->assertEquals(array(), $callbackArgs);
    }

    public function testLoadRowLazyLoadCollectionSelectStatementIsBuildCorrectly()
    {
        $entityState = $this->loadEntityAAndGetState();

        $callback = $this->readAttribute($entityState[Zend_Entity_Fixture_ManyToManyDefs::TEST_A_MANYTOMANY], '_callback');
        $select = $callback[0];

        $this->assertEquals(
            "SELECT table_b.b_id FROM table_b\n".
            " INNER JOIN manytomany_table ON manytomany_table.b_fkey = table_b.b_id WHERE (manytomany_table.a_fkey = 1)",
            (string)$select
        );
    }

    public function testLoadRow_ReturnsCollection()
    {
        $this->initFixture();

        $def = $this->fixture->getEntityDefinition('Zend_TestEntity1');
        $def->getPropertyByName('manytomany')->setFetch('select');

        $entityState = $this->loadEntityAAndGetState();
        $elements = $entityState[Zend_Entity_Fixture_ManyToManyDefs::TEST_A_MANYTOMANY];

        $this->assertType('Zend_Entity_Collection', $elements);
    }
}
