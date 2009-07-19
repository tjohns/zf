<?php

class Zend_Entity_Mapper_Loader_Basic_OneToManyFixtureTest extends Zend_Entity_Mapper_Loader_TestCase
{
    protected $entityManager = null;

    /**
     *
     * @var Zend_Entity_Fixture_OneToManyDefs
     */
    protected $fixture;

    public function createLoader($def)
    {
        return new Zend_Entity_Mapper_Loader_Basic($def);
    }

    /**
     * @return Zend_Entity_Mapper_Loader_Interface
     */
    public function getClassALoader()
    {
        return $this->createLoader($this->fixture->getEntityDefinition(Zend_Entity_Fixture_OneToManyDefs::TEST_A_CLASS));
    }

    public function setUp()
    {
        $this->fixture = new Zend_Entity_Fixture_OneToManyDefs();
    }

    /**
     * @return array
     */
    public function loadEntityAAndGetState()
    {
        $entity = new Zend_TestEntity1;
        $loader = $this->getClassALoader();
        $row = array(Zend_Entity_Fixture_OneToManyDefs::TEST_A_ID_COLUMN => 1);

        $this->entityManager = $this->createEntityManager();
        $this->entityManager->setMetadataFactory(
            $this->fixture->getResourceMap()
        );
        $loader->loadRow($entity, $row, $this->entityManager);
        $entityState = $entity->getState();

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
        $property = $this->fixture->getEntityPropertyDef(Zend_Entity_Fixture_OneToManyDefs::TEST_A_CLASS, Zend_Entity_Fixture_OneToManyDefs::TEST_A_ONETOMANY);
        $property->setOrderBy("table_b.foo ASC");

        $select = $this->loadEntityAAndGetSelectOfLazyLoadCollection();

        $this->assertEquals(
            "SELECT table_b.b_id, table_b.manytoone FROM table_b WHERE (table_b.b_fkey = 1) ORDER BY table_b.foo ASC",
            (string)$select
        );
    }
}