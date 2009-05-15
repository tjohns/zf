<?php

class Zend_Entity_Mapper_Loader_Basic_ManyToManyFixtureTest extends Zend_Entity_Mapper_Loader_ManyToManyFixture
{
    public function createLoader($def)
    {
        return new Zend_Entity_Mapper_Loader_Basic($def);
    }

    public function setUp()
    {
        $this->resourceMap = new Zend_Entity_Resource_Testing();
        $this->resourceMap->addDefinition( $this->createClassBDefinition() );
        $this->resourceMap->addDefinition( $this->createClassADefinition() );
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

    public function testLoadRowEntityWithCollection()
    {
        $entityState = $this->loadEntityAAndGetState();

        $this->assertTrue(isset($entityState[self::TEST_A_ID]));
        $this->assertTrue(isset($entityState[self::TEST_A_MANYTOMANY]));
        $this->assertLazyLoad($entityState[self::TEST_A_MANYTOMANY]);
    }

    public function testLoadRowLazyLoadCollectionHasCallbackWithSelectStatement()
    {
        $entityState = $this->loadEntityAAndGetState();

        $callback = $this->readAttribute($entityState[self::TEST_A_MANYTOMANY], '_callback');
        $this->assertEquals($this->entityManager, $callback[0]);
        $this->assertEquals("find", $callback[1]);

        $callbackArgs = $this->readAttribute($entityState[self::TEST_A_MANYTOMANY], '_callbackArguments');
        $this->assertEquals(self::TEST_B_CLASS, $callbackArgs[0]);
        $this->assertTrue($callbackArgs[1] instanceof Zend_Db_Select);
    }

    public function testLoadRowLazyLoadCollectionSelectStatementIsBuildCorrectly()
    {
        $entityState = $this->loadEntityAAndGetState();

        $callbackArgs = $this->readAttribute($entityState[self::TEST_A_MANYTOMANY], '_callbackArguments');
        $select = $callbackArgs[1];

        $this->assertEquals(
            "SELECT table_b.b_id FROM table_b
 INNER JOIN manytomany_table ON manytomany_table.b_fkey = table_b.b_id WHERE (manytomany_table.a_fkey = 1)",
            (string)$select
        );
    }
}