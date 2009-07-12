<?php

class Zend_Entity_Mapper_Loader_Basic_ManyToManyFixtureTest extends Zend_Entity_Mapper_Loader_TestCase
{
    /**
     * @var Zend_Entity_Fixture_ManyToManyDefs
     */
    private $fixture = null;

    public function createLoader($def)
    {
        return new Zend_Entity_Mapper_Loader_Basic($def);
    }

    /**
     * @return Zend_Entity_Mapper_Loader_Interface
     */
    public function getClassALoader()
    {
        return $this->createLoader($this->fixture->getEntityDefinition(Zend_Entity_Fixture_ManyToManyDefs::TEST_A_CLASS));
    }

    /**
     * @return Zend_Entity_Mapper_Loader_Interface
     */
    public function getClassBLoader()
    {
        return $this->createLoader($this->fixture->getEntityDefinition(Zend_Entity_Fixture_ManyToManyDefs::TEST_B_CLASS));
    }

    public function setUp()
    {
        $this->fixture = new Zend_Entity_Fixture_ManyToManyDefs();
    }

    /**
     * @return array
     */
    public function loadEntityAAndGetState()
    {
        $entity = new Zend_TestEntity1;
        $loader = $this->getClassALoader();
        $row = array(Zend_Entity_Fixture_ManyToManyDefs::TEST_A_ID_COLUMN => 1);

        $this->entityManager = $this->createEntityManager();
        $this->entityManager->setMetadataFactory(
            $this->fixture->getResourceMap()
        );
        $loader->loadRow($entity, $row, $this->entityManager);
        $entityState = $entity->getState();

        return $entityState;
    }

    public function testLoadRowEntityWithCollection()
    {
        $entityState = $this->loadEntityAAndGetState();

        $this->assertTrue(isset($entityState[Zend_Entity_Fixture_ManyToManyDefs::TEST_A_ID]));
        $this->assertTrue(isset($entityState[Zend_Entity_Fixture_ManyToManyDefs::TEST_A_MANYTOMANY]));
        $this->assertLazyLoad($entityState[Zend_Entity_Fixture_ManyToManyDefs::TEST_A_MANYTOMANY]);
    }

    public function testLoadRowLazyLoadCollectionHasCallbackWithSelectStatement()
    {
        $entityState = $this->loadEntityAAndGetState();

        $callback = $this->readAttribute($entityState[Zend_Entity_Fixture_ManyToManyDefs::TEST_A_MANYTOMANY], '_callback');
        $this->assertType('Zend_Entity_Query_AbstractQuery', $callback[0]);
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
}