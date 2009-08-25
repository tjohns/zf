<?php

class Zend_Entity_Mapper_Loader_Entity_ManyToManyFixtureTest extends Zend_Entity_Mapper_Loader_TestCase
{
    public function getLoaderClassName()
    {
        return "Zend_Entity_Mapper_Loader_Entity";
    }

    public function getFixtureClassName()
    {
        return "Zend_Entity_Fixture_ManyToManyDefs";
    }

    /**
     * @return Zend_Entity_Mapper_Loader_LoaderAbstract
     */
    public function getClassALoader()
    {
        return $this->createLoader($this->fixture->getEntityDefinition(Zend_Entity_Fixture_ManyToManyDefs::TEST_A_CLASS));
    }

    /**
     * @return array
     */
    public function loadEntityAAndGetState()
    {
        $entity = new Zend_TestEntity1;
        $loader = $this->getClassALoader();
        $row = array(Zend_Entity_Fixture_ManyToManyDefs::TEST_A_ID_COLUMN => 1);
        
        $loader->loadRow($entity, $row, $this->mappings["Zend_TestEntity1"]);
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
        $def = $this->fixture->getEntityDefinition('Zend_TestEntity1');
        $def->getPropertyByName('manytomany')->setFetch('select');

        $entityState = $this->loadEntityAAndGetState();
        $elements = $entityState[Zend_Entity_Fixture_ManyToManyDefs::TEST_A_MANYTOMANY];

        $this->assertType('Zend_Entity_Collection', $elements);
    }
}