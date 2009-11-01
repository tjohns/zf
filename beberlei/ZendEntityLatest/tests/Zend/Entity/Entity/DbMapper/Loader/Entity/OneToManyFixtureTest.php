<?php

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';
require_once 'Zend/Entity/Fixture/Entities.php';

class Zend_Entity_DbMapper_Loader_Entity_OneToManyFixtureTest extends Zend_Entity_DbMapper_Loader_TestCase
{
    private $_entity;

    public function getLoaderClassName()
    {
        return "Zend_Db_Mapper_Loader_Entity";
    }

    public function getFixtureClassName()
    {
        return "Zend_Entity_Fixture_OneToManyDefs";
    }

    /**
     * @return array
     */
    public function loadEntityAAndGetState()
    {
        $this->_entity = new Zend_TestEntity1;
        $loader = $this->createLoader();
        $state = array(Zend_Entity_Fixture_OneToManyDefs::TEST_A_ID => 1);

        $loader->loadState($this->_entity, $state, $this->mappings["Zend_TestEntity1"]);
        $entityState = $this->_entity->getState();

        return $entityState;
    }

    public function loadEntityAAndGetSelectOfLazyLoadCollection()
    {
        $entityState = $this->loadEntityAAndGetState();

        $callbackArgs = $this->readAttribute($entityState[Zend_Entity_Fixture_OneToManyDefs::TEST_A_ONETOMANY], '_callback');
        $select = $callbackArgs[0];
        return $select;
    }

    public function testLoadRowEntityWithCollection()
    {
        $entityState = $this->loadEntityAAndGetState();

        $this->assertTrue(isset($entityState[Zend_Entity_Fixture_OneToManyDefs::TEST_A_ID]));
        $this->assertTrue(isset($entityState[Zend_Entity_Fixture_OneToManyDefs::TEST_A_ONETOMANY]));
        $this->assertLazyLoad($entityState[Zend_Entity_Fixture_OneToManyDefs::TEST_A_ONETOMANY]);
    }

    public function testStoresLoadedCollectionInTheIdentityMap()
    {
        $this->loadEntityAAndGetState();

        $collection = $this->identityMap->getRelatedObject($this->_entity, Zend_Entity_Fixture_OneToManyDefs::TEST_A_ONETOMANY);
        $this->assertTrue($collection instanceof Zend_Entity_Collection_Interface);
    }

    public function testLoadRowLazyLoadCollectionHasCallbackWithSelectStatement()
    {
        $entityState = $this->loadEntityAAndGetState();

        $callback = $this->readAttribute($entityState[Zend_Entity_Fixture_OneToManyDefs::TEST_A_ONETOMANY], '_callback');
        $this->assertType('Zend_Entity_Query_QueryAbstract', $callback[0]);
        $this->assertEquals("getResultList", $callback[1]);

        $callbackArgs = $this->readAttribute($entityState[Zend_Entity_Fixture_OneToManyDefs::TEST_A_ONETOMANY], '_callbackArguments');
        $this->assertEquals(array(), $callbackArgs);
    }

    public function testLoadRowLazyLoadCollectionSelectStatementIsBuildCorrectly()
    {
        $select = $this->loadEntityAAndGetSelectOfLazyLoadCollection();

        $this->assertEquals(
            "SELECT table_b.b_id, table_b.manytoone FROM table_b WHERE (table_b.b_fkey = 1)",
            (string)$select
        );
    }

    public function testLoadRowWithWhereClauseInOneToManyRelatedCollectionIsInSelectStatement()
    {
        $this->initFixture();

        $property = $this->fixture->getEntityPropertyDef(Zend_Entity_Fixture_OneToManyDefs::TEST_A_CLASS, Zend_Entity_Fixture_OneToManyDefs::TEST_A_ONETOMANY);
        $property->setWhere("table_b.foo = 'bar'");

        $select = $this->loadEntityAAndGetSelectOfLazyLoadCollection();

        $this->assertEquals(
            "SELECT table_b.b_id, table_b.manytoone FROM table_b WHERE (table_b.b_fkey = 1) AND (table_b.foo = 'bar')",
            (string)$select
        );
    }

    public function testLoadRowWithOrderByClauseInOneToManyRelatedCollectionIsInSelectStatement()
    {
        $this->initFixture();

        $property = $this->fixture->getEntityPropertyDef(Zend_Entity_Fixture_OneToManyDefs::TEST_A_CLASS, Zend_Entity_Fixture_OneToManyDefs::TEST_A_ONETOMANY);
        $property->setOrderBy("table_b.foo ASC");

        $select = $this->loadEntityAAndGetSelectOfLazyLoadCollection();

        $this->assertEquals(
            "SELECT table_b.b_id, table_b.manytoone FROM table_b WHERE (table_b.b_fkey = 1) ORDER BY table_b.foo ASC",
            (string)$select
        );
    }

    public function createOneToManyJoinedEntityResultSetMapping()
    {
        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity('Zend_TestEntity1', 'a')
            ->addProperty('a', 'a_id', 'id')
            ->addJoinedEntity('Zend_TestEntity2', 'b', 'Zend_TestEntity1', 'onetomany')
            ->addProperty('b', 'b_id', 'id')
            ->addProperty('b', 'b_fkey', 'manytoone');
        return $rsm;
    }

    public function testProcessResultSet_WithOneRootEntityManyJoinedEntities()
    {
        $rows = array(
            array('a_id' => 1, 'b_id' => 1, 'b_fkey' => 1),
            array('a_id' => 1, 'b_id' => 2, 'b_fkey' => 1),
            array('a_id' => 1, 'b_id' => 3, 'b_fkey' => 1),
            array('a_id' => 1, 'b_id' => 4, 'b_fkey' => 1),
            array('a_id' => 1, 'b_id' => 5, 'b_fkey' => 1),
        );

        $rsm = $this->createOneToManyJoinedEntityResultSetMapping();

        $loader = $this->createLoader();

        $result = $loader->processResultset($rows, $rsm);
        $this->assertEquals(1, count($result));

        $theAEntity = $result[0];
        $this->assertNotType('Zend_Entity_LazyLoad_Collection', $theAEntity->onetomany);
        $this->assertEquals(5, count($theAEntity->onetomany));
    }

    public function testProcessResultSet_WithOneRoot_SchuffledJoinedEntities()
    {
        $rows = array(
            array('a_id' => 1, 'b_id' => 1, 'b_fkey' => 1),
            array('a_id' => 2, 'b_id' => 3, 'b_fkey' => 2),
            array('a_id' => 1, 'b_id' => 2, 'b_fkey' => 1),
            array('a_id' => 2, 'b_id' => 4, 'b_fkey' => 2),
            array('a_id' => 2, 'b_id' => 5, 'b_fkey' => 2),
        );

        $rsm = $this->createOneToManyJoinedEntityResultSetMapping();

        $loader = $this->createLoader();

        $result = $loader->processResultset($rows, $rsm);
        $this->assertEquals(2, count($result));
        $this->assertEquals(2, count($result[0]->onetomany), "A-Entity #1 should have 2 related B-Entities.");
        $this->assertEquals(3, count($result[1]->onetomany), "A-Entity #2 should have 3 related B-Entities.");
    }
}
