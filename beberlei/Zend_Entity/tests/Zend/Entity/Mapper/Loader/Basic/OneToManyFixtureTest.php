<?php

class Zend_Entity_Mapper_Loader_Basic_OneToManyFixtureTest extends Zend_Entity_Mapper_Loader_OneToManyFixture
{
    protected $entityManager = null;

    public function createLoader($def)
    {
        return new Zend_Entity_Mapper_Loader_Basic($def);
    }

    /**
     * @return array
     */
    public function loadEntityAAndGetState()
    {
        $entity = new Zend_TestEntity1;
        $loader = $this->getClassALoader();
        $row = array(self::TEST_A_ID_COLUMN => 1);

        $this->entityManager = $this->createEntityManager();
        $loader->loadRow($entity, $row, $this->entityManager);
        $entityState = $entity->getState();

        return $entityState;
    }

    public function loadEntityAAndGetSelectOfLazyLoadCollection()
    {
        $entityState = $this->loadEntityAAndGetState();

        $callbackArgs = $this->readAttribute($entityState[self::TEST_A_ONETOMANY], '_callbackArguments');
        $select = $callbackArgs[1];
        return $select;
    }

    public function testLoadRowEntityWithCollection()
    {
        $entityState = $this->loadEntityAAndGetState();

        $this->assertTrue(isset($entityState[self::TEST_A_ID]));
        $this->assertTrue(isset($entityState[self::TEST_A_ONETOMANY]));
        $this->assertLazyLoad($entityState[self::TEST_A_ONETOMANY]);
    }

    public function testLoadRowLazyLoadCollectionHasCallbackWithSelectStatement()
    {
        $entityState = $this->loadEntityAAndGetState();

        $callback = $this->readAttribute($entityState[self::TEST_A_ONETOMANY], '_callback');
        $this->assertEquals($this->entityManager, $callback[0]);
        $this->assertEquals("find", $callback[1]);

        $callbackArgs = $this->readAttribute($entityState[self::TEST_A_ONETOMANY], '_callbackArguments');
        $this->assertEquals(self::TEST_B_CLASS, $callbackArgs[0]);
        $this->assertTrue($callbackArgs[1] instanceof Zend_Db_Select);
    }

    public function testLoadRowLazyLoadCollectionSelectStatementIsBuildCorrectly()
    {
        $select = $this->loadEntityAAndGetSelectOfLazyLoadCollection();

        $this->assertEquals(
            "SELECT table_b.b_id FROM table_b WHERE (table_b.b_fkey = 1)",
            (string)$select
        );
    }

    public function testLoadRowWithWhereClauseInOneToManyRelatedCollectionIsInSelectStatement()
    {
        $this->resourceMap->getDefinitionByEntityName(self::TEST_A_CLASS)->getPropertyByName(self::TEST_A_ONETOMANY)->setWhere("table_b.foo = 'bar'");

        $select = $this->loadEntityAAndGetSelectOfLazyLoadCollection();

        $this->assertEquals(
            "SELECT table_b.b_id FROM table_b WHERE (table_b.b_fkey = 1) AND (table_b.foo = 'bar')",
            (string)$select
        );
    }

    public function testLoadRowWithOrderByClauseInOneToManyRelatedCollectionIsInSelectStatement()
    {
        $this->resourceMap->getDefinitionByEntityName(self::TEST_A_CLASS)->getPropertyByName(self::TEST_A_ONETOMANY)->setOrderBy("table_b.foo ASC");

        $select = $this->loadEntityAAndGetSelectOfLazyLoadCollection();

        $this->assertEquals(
            "SELECT table_b.b_id FROM table_b WHERE (table_b.b_fkey = 1) ORDER BY table_b.foo ASC",
            (string)$select
        );
    }
}